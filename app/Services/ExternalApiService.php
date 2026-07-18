<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ExternalApiService
{
    /**
     * Get Weather Data from Open-Meteo API.
     */
    public function getWeather(float $lat, float $lon): array
    {
        $cacheKey = "weather_v2_{$lat}_{$lon}";
        return Cache::remember($cacheKey, 1800, function () use ($lat, $lon) {
            try {
                $response = Http::timeout(5)->get("https://api.open-meteo.com/v1/forecast", [
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'current' => 'temperature_2m,wind_speed_10m,wind_gusts_10m,precipitation,visibility',
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $current = $data['current'] ?? [];
                    
                    $temp = $current['temperature_2m'] ?? 24;
                    $wind = $current['wind_speed_10m'] ?? 10;
                    $gusts = $current['wind_gusts_10m'] ?? $wind;
                    $precip = $current['precipitation'] ?? 0;
                    $visibility = $current['visibility'] ?? 10000;
                    
                    // Storm risk calculation based on gusts and precipitation
                    $stormRisk = ($gusts > 30 || $precip > 5) ? rand(60, 90) : rand(10, 45);

                    return [
                        'temp' => $temp,
                        'wind' => $wind,
                        'gusts' => $gusts,
                        'precipitation' => $precip,
                        'visibility' => $visibility,
                        'storm_risk' => $stormRisk,
                        'source' => 'Open-Meteo'
                    ];
                }
            } catch (\Exception $e) {
                // Fail-safe mock data
            }

            return [
                'temp' => rand(20, 30) + (rand(0, 9) / 10), // dynamic mock fallback
                'wind' => rand(5, 20),
                'gusts' => rand(10, 30),
                'precipitation' => 0.0,
                'visibility' => 10000,
                'storm_risk' => rand(10, 40),
                'source' => 'Mock Data (Offline)'
            ];
        });
    }

    /**
     * Get Marine Data from Open-Meteo API.
     */
    public function getMarineData(float $lat, float $lon): array
    {
        $cacheKey = "marine_v2_{$lat}_{$lon}";
        return Cache::remember($cacheKey, 1800, function () use ($lat, $lon) {
            try {
                $response = Http::timeout(5)->get("https://marine-api.open-meteo.com/v1/marine", [
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'current' => 'wave_height,ocean_current_velocity',
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $current = $data['current'] ?? [];
                    
                    return [
                        'wave_height' => $current['wave_height'] ?? 0,
                        'ocean_current_velocity' => $current['ocean_current_velocity'] ?? 0,
                        'source' => 'Open-Meteo Marine'
                    ];
                }
            } catch (\Exception $e) {
                // Fail-safe mock data
            }

            return [
                'wave_height' => 1.2,
                'ocean_current_velocity' => 0.5,
                'source' => 'Mock Data (Offline)'
            ];
        });
    }

    /**
     * Get Economic Indicators from World Bank API.
     */
    public function getWorldBankData(string $countryIso2): array
    {
        $cacheKey = "worldbank_v4_{$countryIso2}";
        return Cache::remember($cacheKey, 86400, function () use ($countryIso2) {
            try {
                // World bank returns data in array format: [ {page, pages, per_page, total}, [ {indicator, country, countryiso3code, date, value, unit, obs_status, decimal} ] ]
                // NY.GDP.MKTP.CD (GDP current USD)
                $gdpResponse = Http::timeout(5)->get("https://api.worldbank.org/v2/country/{$countryIso2}/indicator/NY.GDP.MKTP.CD", [
                    'format' => 'json',
                    'per_page' => 5
                ]);
                
                // FP.CPI.TOTL.ZG (Inflation, consumer prices annual %)
                $infResponse = Http::timeout(5)->get("https://api.worldbank.org/v2/country/{$countryIso2}/indicator/FP.CPI.TOTL.ZG", [
                    'format' => 'json',
                    'per_page' => 5
                ]);

                // SP.POP.TOTL (Total population)
                $popResponse = Http::timeout(5)->get("https://api.worldbank.org/v2/country/{$countryIso2}/indicator/SP.POP.TOTL", [
                    'format' => 'json',
                    'per_page' => 5
                ]);
                
                // NE.EXP.GNFS.CD (Exports of goods and services, current US$)
                $expResponse = Http::timeout(5)->get("https://api.worldbank.org/v2/country/{$countryIso2}/indicator/NE.EXP.GNFS.CD", [
                    'format' => 'json',
                    'per_page' => 5
                ]);
                
                // NE.IMP.GNFS.CD (Imports of goods and services, current US$)
                $impResponse = Http::timeout(5)->get("https://api.worldbank.org/v2/country/{$countryIso2}/indicator/NE.IMP.GNFS.CD", [
                    'format' => 'json',
                    'per_page' => 5
                ]);

                $gdp = null;
                $inflation = null;
                $population = null;
                $export = null;
                $import = null;

                $getFirstNonNull = function($response) {
                    if ($response->successful()) {
                        $data = $response->json();
                        if (isset($data[1]) && is_array($data[1])) {
                            foreach ($data[1] as $item) {
                                if (!is_null($item['value'])) {
                                    return $item['value'];
                                }
                            }
                        }
                    }
                    return null;
                };

                $gdp = $getFirstNonNull($gdpResponse);
                $inflation = $getFirstNonNull($infResponse);
                $population = $getFirstNonNull($popResponse);
                $export = $getFirstNonNull($expResponse);
                $import = $getFirstNonNull($impResponse);

                return [
                    'gdp' => $gdp,
                    'inflation' => $inflation ? round($inflation, 2) : null,
                    'population' => $population,
                    'export' => $export,
                    'import' => $import,
                    'source' => 'World Bank API'
                ];
            } catch (\Exception $e) {
                // Fail-safe mock data
            }

            return [
                'gdp' => null,
                'inflation' => null,
                'population' => null,
                'export' => null,
                'import' => null,
                'source' => 'Mock Data (Offline)'
            ];
        });
    }

    /**
     * Get Country details from REST Countries API.
     */
    public function getRestCountriesData(string $countryIso2): array
    {
        $cacheKey = "restcountries_v2_{$countryIso2}";
        return Cache::remember($cacheKey, 86400, function () use ($countryIso2) {
            try {
                $response = Http::timeout(5)->get("https://restcountries.com/v3.1/alpha/{$countryIso2}");
                if ($response->successful()) {
                    $data = $response->json();
                    if (is_array($data) && count($data) > 0) {
                        $country = $data[0];
                        
                        // Parse languages
                        $languages = $country['languages'] ?? [];
                        $langList = empty($languages) ? 'N/A' : implode(', ', array_values($languages));
                        
                        // Parse region
                        $region = $country['region'] ?? 'N/A';
                        $subregion = $country['subregion'] ?? '';
                        $fullRegion = $subregion ? "{$region} ({$subregion})" : $region;
                        
                        return [
                            'region' => $fullRegion,
                            'language' => $langList,
                            'source' => 'REST Countries API'
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Fallback
            }
            
            return [
                'region' => null,
                'language' => null,
                'source' => 'Mock Data'
            ];
        });
    }

    /**
     * Get Real-time Exchange Rate from ExchangeRate API.
     */
    public function getExchangeRate(string $base, string $target): float
    {
        $cacheKey = "fx_{$base}_{$target}";
        return Cache::remember($cacheKey, 3600, function () use ($base, $target) {
            try {
                $response = Http::timeout(5)->get("https://open.er-api.com/v6/latest/{$base}");
                if ($response->successful()) {
                    $data = $response->json();
                    return (double) ($data['rates'][$target] ?? 1.0);
                }
            } catch (\Exception $e) {
                // Fallback
            }

            // Defaults if API fails or rate is unavailable
            $rates = [
                'USD_CNY' => 7.2450,
                'USD_IDR' => 16340.00,
                'USD_EUR' => 0.9250,
                'USD_AUD' => 1.5120,
            ];

            return $rates["{$base}_{$target}"] ?? 1.0;
        });
    }

    /**
     * Get News Feed from GNews API.
     */
    public function getGNews(string $query = 'supply chain logistics'): array
    {
        $cacheKey = "gnews_en9_" . md5($query); // bypass old cache
        return Cache::remember($cacheKey, 7200, function () use ($query) {
            $apiKey = env('GNEWS_API_KEY');
            if (!$apiKey || $apiKey === 'YOUR_GNEWS_API_KEY_HERE') {
                return $this->getMockNews();
            }

            try {
                $response = Http::timeout(5)->get("https://gnews.io/api/v4/search", [
                    'q' => $query,
                    'lang' => 'en',
                    'apikey' => $apiKey,
                    'max' => 3
                ]);

                if ($response->successful()) {
                    return $response->json()['articles'] ?? [];
                }
                
                // If we reach here, it means the API request failed (e.g. wrong key, rate limit)
                // We should clear this cache immediately so it doesn't cache the failure
                Cache::forget($cacheKey);
                
                // You can temporarily uncomment the line below to debug what error GNews is giving
                // throw new \Exception('GNews API Error: ' . $response->body());

            } catch (\Exception $e) {
                Cache::forget($cacheKey);
                // Fallback to mock
            }

            return $this->getMockNews();
        });
    }

    /**
     * Mock News data for fallback offline / dev mode.
     */
    private function getMockNews(): array
    {
        return [
            [
                'title' => 'Global Inflation increases while exports decrease due to war and port delays',
                'description' => 'A geopolitical crisis in critical maritime trade lanes causes delay in ship arrivals worldwide, impacting currency values and supply chains.',
                'url' => '#',
                'source' => ['name' => 'Logistics Weekly'],
                'publishedAt' => now()->subHours(2)->toIso8601String()
            ],
            [
                'title' => 'Severe storm disrupt shipping lanes in South China Sea',
                'description' => 'Weather forecast issues red alert warning cargo ships of waves higher than 6 meters, forcing diversions.',
                'url' => '#',
                'source' => ['name' => 'Marine News'],
                'publishedAt' => now()->subHours(5)->toIso8601String()
            ],
            [
                'title' => 'Port congestion shows stable improve in Rotterdam and Hamburg',
                'description' => 'Container processing capacity boosts logistic efficiency, boosting quarterly profits for shipping lines.',
                'url' => '#',
                'source' => ['name' => 'Trade Journal'],
                'publishedAt' => now()->subDays(1)->toIso8601String()
            ]
        ];
    }
}
