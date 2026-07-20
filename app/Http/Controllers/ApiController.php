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
        $userId = auth()->id();
        
        if ($userId) {
            $countries = DB::table('countries')
                ->leftJoin('watchlists', function($join) use ($userId) {
                    $join->on('countries.id', '=', 'watchlists.country_id')
                         ->where('watchlists.user_id', '=', $userId);
                })
                ->select('countries.id', 'countries.name', 'countries.iso_code', DB::raw('IF(watchlists.id IS NOT NULL, 1, 0) as is_favorite'))
                ->orderBy('countries.name', 'asc')
                ->get();
        } else {
            $countries = DB::table('countries')
                ->select('id', 'name', 'iso_code', DB::raw('0 as is_favorite'))
                ->orderBy('name', 'asc')
                ->get();
        }
            
        return response()->json($countries);
    }

    /**
     * GET /api/country/{iso}
     * Get full detailed metrics for a single country.
     */
    public function country($iso): JsonResponse
    {
        set_time_limit(60);
        $c = DB::table('countries')->where('iso_code', $iso)->first();
        if (!$c) return response()->json(['error' => 'Not found'], 404);

        // Update / Enrich data dynamically with World Bank API
        $wb = $this->apiService->getWorldBankData($c->iso_code);
        
        $gdp = $wb['gdp'] ?? $c->gdp;
        $inflation = $wb['inflation'] ?? $c->inflation;
        $population = $wb['population'] ?? $c->population;

        // Fetch REST Countries data
        $restCountries = $this->apiService->getRestCountriesData($c->iso_code, $c->name);
        $region = $restCountries['region'] ?? $c->region;
        $language = $restCountries['language'] ?? $c->language;

        // Save back changes if updated
        if ($gdp != $c->gdp || $inflation != $c->inflation || $region != $c->region || $language != $c->language) {
            DB::table('countries')->where('id', $c->id)->update([
                'gdp' => $gdp,
                'inflation' => $inflation,
                'population' => $population,
                'region' => $region,
                'language' => $language,
                'updated_at' => now(),
            ]);
        }

        // Get weather for capitals using precise coordinates from external JSON
        $coords = $this->apiService->getCapitalCoordinates($c->iso_code);
        if ($coords[0] === 0.0 && $coords[1] === 0.0 && isset($restCountries['lat']) && $restCountries['lat'] !== 0.0) {
            $coords = [$restCountries['lat'], $restCountries['lon']];
        }
        $weather = $this->apiService->getWeather($coords[0], $coords[1]);

        // Get currency rate against USD
        $fx = 1.0;
        if ($c->currency_code !== 'USD') {
            $fx = $this->apiService->getExchangeRate('USD', $c->currency_code);
        }

        // Calculate live score
        $scoreDetails = $this->riskEngine->calculate(
            $weather['storm_risk'],
            $inflation * 10,
            rand(10, 40),
            rand(15, 60)
        );

        $userId = auth()->id();
        $is_favorite = false;
        if ($userId) {
            $is_favorite = DB::table('watchlists')
                ->where('user_id', $userId)
                ->where('country_id', $c->id)
                ->exists();
        }

        return response()->json([
            'id' => $c->id,
            'name' => $c->name,
            'iso_code' => $c->iso_code,
            'is_favorite' => $is_favorite,
            'currency' => $c->currency_code,
            'exchange_rate' => $fx,
            'region' => $region,
            'language' => $language,
            'population' => $population,
            'gdp' => $gdp,
            'inflation' => $inflation,
            'export' => $wb['export'] ?? null,
            'import' => $wb['import'] ?? null,
            'weather' => $weather,
            'coords' => $coords,
            'risk' => $scoreDetails
        ]);
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

        return response()->json($query->limit(500)->get());
    }

    /**
     * GET /api/marine
     * Retrieve marine weather data for given coordinates.
     */
    public function marine(Request $request): JsonResponse
    {
        $lat = (float) $request->query('lat', 0);
        $lon = (float) $request->query('lon', 0);
        
        $marineData = $this->apiService->getMarineData($lat, $lon);
        return response()->json($marineData);
    }

    /**
     * GET /api/news
     * GNews integration + sentiment engine analysis.
     */
    public function news(Request $request): JsonResponse
    {
        $countryCode = $request->query('country', 'ID');
        $country = DB::table('countries')->where('iso_code', $countryCode)->first();
        
        // 1. Check database cache
        $cachedNews = DB::table('news_cache')
            ->where('country_code', $countryCode)
            ->where('created_at', '>=', now()->subHours(2))
            ->get();

        if ($cachedNews->isNotEmpty()) {
            $result = $cachedNews->map(function($news) {
                $total = $news->positive_count + $news->negative_count;
                // Avoid division by zero
                $totalDiv = $total == 0 ? 1 : $total;
                
                return [
                    'title' => $news->title,
                    'description' => $news->description,
                    'url' => $news->url,
                    'image' => $news->image,
                    'source' => $news->source,
                    'published_at' => $news->published_at,
                    'sentiment' => $news->sentiment_result,
                    'positive_score' => $news->positive_count,
                    'negative_score' => $news->negative_count,
                    'positive_pct' => round(($news->positive_count / $totalDiv) * 100),
                    'negative_pct' => round(($news->negative_count / $totalDiv) * 100)
                ];
            });
            return response()->json($result);
        }
        
        $q = 'economy OR trade OR business';
        if ($country) {
            $q .= ' "' . $country->name . '"';
        }

        $articles = $this->apiService->getGNews($q);
        $result = [];

        // Clear old cache for this country
        DB::table('news_cache')->where('country_code', $countryCode)->delete();

        foreach ($articles as $art) {
            $title = $art['title'] ?? '';
            $desc = $art['description'] ?? '';
            $url = $art['url'] ?? '#';
            $image = $art['image'] ?? null;
            $source = $art['source']['name'] ?? 'News';
            $pubAt = \Carbon\Carbon::parse($art['publishedAt'] ?? now())->toDateTimeString();
            
            // Lexicon sentiment analysis
            $analysis = $this->sentimentEngine->analyze($title . ' ' . $desc);

            // 3. Store in DB
            DB::table('news_cache')->insert([
                'country_code' => $countryCode,
                'title' => $title,
                'description' => $desc,
                'url' => $url,
                'image' => $image,
                'source' => $source,
                'sentiment_result' => $analysis['sentiment'],
                'positive_count' => $analysis['positive_score'],
                'negative_count' => $analysis['negative_score'],
                'published_at' => $pubAt,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $result[] = [
                'title' => $title,
                'description' => $desc,
                'url' => $url,
                'image' => $image,
                'source' => $source,
                'published_at' => $pubAt,
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

    /**
     * POST /api/country/{iso}/favorite
     * Toggle the favorite status of a country.
     */
    public function toggleFavorite($iso): JsonResponse
    {
        $c = DB::table('countries')->where('iso_code', $iso)->first();
        if (!$c) return response()->json(['error' => 'Not found'], 404);

        $userId = auth()->id();
        if (!$userId) return response()->json(['error' => 'Unauthenticated'], 401);

        $exists = DB::table('watchlists')
            ->where('user_id', $userId)
            ->where('country_id', $c->id)
            ->first();

        $newStatus = false;
        if ($exists) {
            DB::table('watchlists')->where('id', $exists->id)->delete();
        } else {
            DB::table('watchlists')->insert([
                'user_id' => $userId,
                'country_id' => $c->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $newStatus = true;
        }

        return response()->json([
            'iso_code' => $iso,
            'is_favorite' => $newStatus
        ]);
    }

    private function getCentroid(string $iso): array
    {
        // Coordinates for the capital city of each country
        $capitals = [
            'ID' => [-6.2088, 106.8456], // Jakarta
            'DE' => [52.5200, 13.4050],  // Berlin
            'CN' => [39.9042, 116.4074], // Beijing
            'AU' => [-35.2809, 149.1300], // Canberra
            'US' => [38.9072, -77.0369], // Washington, D.C.
            'JP' => [35.6762, 139.6503], // Tokyo
            'SG' => [1.3521, 103.8198],  // Singapore
            'KR' => [37.5665, 126.9780], // Seoul
            'IN' => [28.6139, 77.2090],  // New Delhi
            'GB' => [51.5074, -0.1278],  // London
        ];

        return $capitals[$iso] ?? [0.0, 0.0];
    }
}
