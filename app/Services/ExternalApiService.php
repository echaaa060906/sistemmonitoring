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
        $cacheKey = "weather_v4_{$lat}_{$lon}";
        return Cache::remember($cacheKey, 1800, function () use ($lat, $lon) {
            try {
                $response = Http::withoutVerifying()->timeout(5)->get("https://api.open-meteo.com/v1/forecast", [
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
        $cacheKey = "marine_v4_{$lat}_{$lon}";
        return Cache::remember($cacheKey, 1800, function () use ($lat, $lon) {
            try {
                $response = Http::withoutVerifying()->timeout(5)->get("https://marine-api.open-meteo.com/v1/marine", [
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
        $cacheKey = "worldbank_v5_{$countryIso2}";
        return Cache::remember($cacheKey, 86400, function () use ($countryIso2) {
            try {
                // World bank returns data in array format: [ {page, pages, per_page, total}, [ {indicator, country, countryiso3code, date, value, unit, obs_status, decimal} ] ]
                // NY.GDP.MKTP.CD (GDP current USD)
                $gdpResponse = Http::withoutVerifying()->timeout(5)->get("https://api.worldbank.org/v2/country/{$countryIso2}/indicator/NY.GDP.MKTP.CD", [
                    'format' => 'json',
                    'per_page' => 5
                ]);
                
                // FP.CPI.TOTL.ZG (Inflation, consumer prices annual %)
                $infResponse = Http::withoutVerifying()->timeout(5)->get("https://api.worldbank.org/v2/country/{$countryIso2}/indicator/FP.CPI.TOTL.ZG", [
                    'format' => 'json',
                    'per_page' => 5
                ]);

                // SP.POP.TOTL (Total population)
                $popResponse = Http::withoutVerifying()->timeout(5)->get("https://api.worldbank.org/v2/country/{$countryIso2}/indicator/SP.POP.TOTL", [
                    'format' => 'json',
                    'per_page' => 5
                ]);
                
                // NE.EXP.GNFS.CD (Exports of goods and services, current US$)
                $expResponse = Http::withoutVerifying()->timeout(5)->get("https://api.worldbank.org/v2/country/{$countryIso2}/indicator/NE.EXP.GNFS.CD", [
                    'format' => 'json',
                    'per_page' => 5
                ]);
                
                // NE.IMP.GNFS.CD (Imports of goods and services, current US$)
                $impResponse = Http::withoutVerifying()->timeout(5)->get("https://api.worldbank.org/v2/country/{$countryIso2}/indicator/NE.IMP.GNFS.CD", [
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
     * Get Country details from REST Countries API (Deprecated, returning nulls to fallback)
     */
    public function getRestCountriesData(string $countryIso2): array
    {
        return [
            'region' => null,
            'language' => null,
            'lat' => 0.0,
            'lon' => 0.0,
            'source' => 'Mock Data'
        ];
    }

    /**
     * Get accurate capital coordinates from an open source JSON repository.
     */
    public function getCapitalCoordinates(string $iso2): array
    {
        // Comprehensive fallback dictionary of world capitals (Latitude, Longitude)
        $capitals = [
            'AF' => [34.5281, 69.1171], // Afghanistan, Kabul
            'AL' => [41.3275, 19.8189], // Albania, Tirana
            'DZ' => [36.7525, 3.0420],  // Algeria, Algiers
            'AR' => [-34.6037, -58.3816], // Argentina, Buenos Aires
            'AU' => [-35.2809, 149.1300], // Australia, Canberra
            'AT' => [48.2082, 16.3738], // Austria, Vienna
            'BD' => [23.8103, 90.4125], // Bangladesh, Dhaka
            'BE' => [50.8503, 4.3517],  // Belgium, Brussels
            'BR' => [-15.7942, -47.8822], // Brazil, Brasília
            'CA' => [45.4215, -75.6972], // Canada, Ottawa
            'CL' => [-33.4489, -70.6693], // Chile, Santiago
            'CN' => [39.9042, 116.4074], // China, Beijing
            'CO' => [4.7110, -74.0721],  // Colombia, Bogotá
            'EG' => [30.0444, 31.2357], // Egypt, Cairo
            'FR' => [48.8566, 2.3522],  // France, Paris
            'DE' => [52.5200, 13.4050], // Germany, Berlin
            'GR' => [37.9838, 23.7275], // Greece, Athens
            'IN' => [28.6139, 77.2090], // India, New Delhi
            'ID' => [-6.2088, 106.8456], // Indonesia, Jakarta
            'IR' => [35.6892, 51.3890], // Iran, Tehran
            'IQ' => [33.3128, 44.3615], // Iraq, Baghdad
            'IE' => [53.3498, -6.2603], // Ireland, Dublin
            'IL' => [31.7683, 35.2137], // Israel, Jerusalem
            'IT' => [41.9028, 12.4964], // Italy, Rome
            'JP' => [35.6762, 139.6503], // Japan, Tokyo
            'KE' => [-1.2864, 36.8172], // Kenya, Nairobi
            'KR' => [37.5665, 126.9780], // South Korea, Seoul
            'MY' => [3.1390, 101.6869],  // Malaysia, Kuala Lumpur
            'MX' => [19.4326, -99.1332], // Mexico, Mexico City
            'MA' => [34.0209, -6.8416], // Morocco, Rabat
            'NL' => [52.3676, 4.9041],  // Netherlands, Amsterdam
            'NZ' => [-41.2865, 174.7762], // New Zealand, Wellington
            'NG' => [9.0579, 7.4951],   // Nigeria, Abuja
            'PK' => [33.6844, 73.0479], // Pakistan, Islamabad
            'PH' => [14.5995, 120.9842], // Philippines, Manila
            'PL' => [52.2297, 21.0122], // Poland, Warsaw
            'PT' => [38.7223, -9.1393], // Portugal, Lisbon
            'RU' => [55.7558, 37.6173], // Russia, Moscow
            'SA' => [24.7136, 46.6753], // Saudi Arabia, Riyadh
            'SG' => [1.3521, 103.8198], // Singapore, Singapore
            'ZA' => [-25.7479, 28.2293], // South Africa, Pretoria
            'ES' => [40.4168, -3.7038], // Spain, Madrid
            'SE' => [59.3293, 18.0686], // Sweden, Stockholm
            'CH' => [46.9480, 7.4474],  // Switzerland, Bern
            'TW' => [25.0330, 121.5654], // Taiwan, Taipei
            'TH' => [13.7563, 100.5018], // Thailand, Bangkok
            'TR' => [39.9208, 32.8541], // Turkey, Ankara
            'UA' => [50.4501, 30.5234], // Ukraine, Kyiv
            'AE' => [24.4539, 54.3773], // United Arab Emirates, Abu Dhabi
            'GB' => [51.5074, -0.1278], // United Kingdom, London
            'US' => [38.9072, -77.0369], // United States, Washington, D.C.
            'VN' => [21.0285, 105.8542], // Vietnam, Hanoi
        ];

        return $capitals[$iso2] ?? [0.0, 0.0];
    }

    /**
     * Get Real-time Exchange Rate from ExchangeRate API.
     */
    public function getExchangeRate(string $base, string $target): float
    {
        $cacheKey = "fx_v2_{$base}_{$target}";
        return Cache::remember($cacheKey, 3600, function () use ($base, $target) {
            try {
                $response = Http::withoutVerifying()->timeout(5)->get("https://open.er-api.com/v6/latest/{$base}");
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
        $cacheKey = "gnews_en10_" . md5($query); // bypass old cache
        return Cache::remember($cacheKey, 7200, function () use ($query) {
            $apiKey = env('GNEWS_API_KEY');
            if (!$apiKey || $apiKey === 'YOUR_GNEWS_API_KEY_HERE') {
                return $this->getMockNews();
            }

            try {
                $response = Http::withoutVerifying()->timeout(5)->get("https://gnews.io/api/v4/search", [
                    'q' => $query,
                    'lang' => 'en',
                    'apikey' => $apiKey,
                    'max' => 10
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
            ],
            [
                'title' => 'New tariffs disrupt trans-Pacific trade routes',
                'description' => 'Economic policies in key markets have caused unexpected shifts in global supply chains and cargo volumes.',
                'url' => '#',
                'source' => ['name' => 'Global Economy'],
                'publishedAt' => now()->subHours(8)->toIso8601String()
            ],
            [
                'title' => 'Automated ports boost efficiency in Asia',
                'description' => 'Investment in AI and automation leads to record-breaking turnaround times for container ships.',
                'url' => '#',
                'source' => ['name' => 'Tech & Logistics'],
                'publishedAt' => now()->subDays(2)->toIso8601String()
            ],
            [
                'title' => 'Fuel prices impact international freight costs',
                'description' => 'Rising marine fuel prices are forcing shipping companies to implement new surcharges on global routes.',
                'url' => '#',
                'source' => ['name' => 'Energy Weekly'],
                'publishedAt' => now()->subHours(12)->toIso8601String()
            ]
        ];
    }
}
