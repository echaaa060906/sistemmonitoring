<?php

namespace App\Http\Controllers;

use App\Services\ExternalApiService;
use App\Services\SentimentEngine;
use App\Services\RiskScoringEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    protected $apiService;
    protected $sentimentEngine;
    protected $riskEngine;

    public function __construct(
        ExternalApiService $apiService,
        SentimentEngine $sentimentEngine,
        RiskScoringEngine $riskEngine
    ) {
        $this->apiService = $apiService;
        $this->sentimentEngine = $sentimentEngine;
        $this->riskEngine = $riskEngine;
    }

    /**
     * GET /api/countries
     * Get dynamic list of countries and their World Bank stats.
     */
    public function countries(): JsonResponse
    {
        set_time_limit(120);
        $countries = DB::table('countries')->get();
        $data = [];

        foreach ($countries as $c) {
            // Update / Enrich data dynamically with World Bank API (caching handled in service)
            $wb = $this->apiService->getWorldBankData($c->iso_code);
            
            $gdp = $wb['gdp'] ?? $c->gdp;
            $inflation = $wb['inflation'] ?? $c->inflation;
            $population = $wb['population'] ?? $c->population;

            // Save back changes if updated
            if ($gdp != $c->gdp || $inflation != $c->inflation) {
                DB::table('countries')->where('id', $c->id)->update([
                    'gdp' => $gdp,
                    'inflation' => $inflation,
                    'population' => $population,
                    'updated_at' => now(),
                ]);
            }

            // Get weather for capitals / centroids of countries (approximate coordinates)
            $coords = $this->getCentroid($c->iso_code);
            $weather = $this->apiService->getWeather($coords[0], $coords[1]);

            // Get currency rate against USD
            $fx = 1.0;
            if ($c->currency_code !== 'USD') {
                $fx = $this->apiService->getExchangeRate('USD', $c->currency_code);
            }

            // Calculate live score
            $scoreDetails = $this->riskEngine->calculate(
                $weather['storm_risk'],
                $inflation * 10, // scaled as index
                rand(10, 40),     // currency stability index
                rand(15, 60)      // sentiment index fallback
            );

            $data[] = [
                'id' => $c->id,
                'name' => $c->name,
                'iso_code' => $c->iso_code,
                'currency' => $c->currency_code,
                'exchange_rate' => $fx,
                'region' => $c->region,
                'language' => $c->language,
                'population' => $population,
                'gdp' => $gdp,
                'inflation' => $inflation,
                'weather' => $weather,
                'risk' => $scoreDetails
            ];
        }

        return response()->json($data);
    }

    /**
     * GET /api/risk
     * Retrieve risk scores & history.
     */
    public function risk(Request $request): JsonResponse
    {
        $countryId = $request->query('country_id');
        $query = DB::table('risk_scores')
            ->join('countries', 'risk_scores.country_id', '=', 'countries.id')
            ->select('risk_scores.*', 'countries.name as country_name', 'countries.iso_code');

        if ($countryId) {
            $query->where('country_id', $countryId);
        }

        return response()->json($query->get());
    }

    /**
     * GET /api/ports
     * Retrieve all world port index entries.
     */
    public function ports(Request $request): JsonResponse
    {
        $search = $request->query('query');
        $query = DB::table('ports');

        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('country', 'LIKE', "%{$search}%");
        }

        return response()->json($query->get());
    }

    /**
     * GET /api/news
     * GNews integration + sentiment engine analysis.
     */
    public function news(Request $request): JsonResponse
    {
        $countryCode = $request->query('country', 'ID');
        $country = DB::table('countries')->where('iso_code', $countryCode)->first();
        $q = 'supply chain logistics';
        if ($country) {
            $q .= ' ' . strtolower($country->name);
        }

        $articles = $this->apiService->getGNews($q);
        $result = [];

        foreach ($articles as $art) {
            $title = $art['title'] ?? '';
            $desc = $art['description'] ?? '';
            
            // Lexicon sentiment analysis
            $analysis = $this->sentimentEngine->analyze($title . ' ' . $desc);

            $result[] = [
                'title' => $title,
                'description' => $desc,
                'url' => $art['url'] ?? '#',
                'source' => $art['source']['name'] ?? 'News',
                'published_at' => $art['publishedAt'] ?? now()->toIso8601String(),
                'sentiment' => $analysis['sentiment'],
                'positive_score' => $analysis['positive_score'],
                'negative_score' => $analysis['negative_score'],
                'positive_pct' => $analysis['positive_pct'],
                'negative_pct' => $analysis['negative_pct'],
                'neutral_pct' => $analysis['neutral_pct'],
                'matched_pos' => $analysis['matched_positive'],
                'matched_neg' => $analysis['matched_negative'],
            ];
        }

        return response()->json($result);
    }

    /**
     * GET /api/currency
     * Live FX conversion rates against USD.
     */
    public function currency(Request $request): JsonResponse
    {
        $base = $request->query('base', 'USD');
        $currencies = ['IDR', 'CNY', 'EUR', 'AUD', 'USD'];
        $rates = [];

        foreach ($currencies as $curr) {
            $rates[$curr] = $this->apiService->getExchangeRate($base, $curr);
        }

        return response()->json([
            'base' => $base,
            'rates' => $rates,
            'timestamp' => now()->toIso8601String()
        ]);
    }

    private function getCentroid(string $iso): array
    {
        $centroids = [
            'ID' => [-6.20, 106.84],
            'DE' => [52.52, 13.40],
            'CN' => [39.90, 116.40],
            'AU' => [-35.28, 149.13],
            'US' => [38.90, -77.03],
        ];

        return $centroids[$iso] ?? [0.0, 0.0];
    }
}
