<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Kamus Kata Positif (Lexicon)
        $posWords = ['growth', 'increase', 'profit', 'stable', 'improve', 'strengthen', 'success', 'benefit', 'safe', 'clear'];
        foreach ($posWords as $w) {
            DB::table('positive_words')->insertOrIgnore([
                'word' => $w,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 2. Seed Kamus Kata Negatif (Lexicon)
        $negWords = ['war', 'crisis', 'inflation', 'delay', 'disaster', 'conflict', 'decline', 'risk', 'storm', 'disrupt'];
        foreach ($negWords as $w) {
            DB::table('negative_words')->insertOrIgnore([
                'word' => $w,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 3. Seed Countries
        $countries = [
            [
                'name' => 'Indonesia', 'iso_code' => 'ID', 'currency_code' => 'IDR',
                'region' => 'Asia', 'language' => 'Indonesian', 'population' => 275000000,
                'gdp' => 1319000000000, 'inflation' => 2.8, 'export_val' => 292000000000, 'import_val' => 230000000000
            ],
            [
                'name' => 'Germany', 'iso_code' => 'DE', 'currency_code' => 'EUR',
                'region' => 'Europe', 'language' => 'German', 'population' => 84000000,
                'gdp' => 4072000000000, 'inflation' => 1.9, 'export_val' => 1650000000000, 'import_val' => 1400000000000
            ],
            [
                'name' => 'China', 'iso_code' => 'CN', 'currency_code' => 'CNY',
                'region' => 'Asia', 'language' => 'Chinese', 'population' => 1412000000,
                'gdp' => 17960000000000, 'inflation' => 0.5, 'export_val' => 3590000000000, 'import_val' => 2680000000000
            ],
            [
                'name' => 'Australia', 'iso_code' => 'AU', 'currency_code' => 'AUD',
                'region' => 'Oceania', 'language' => 'English', 'population' => 26000000,
                'gdp' => 1675000000000, 'inflation' => 3.6, 'export_val' => 400000000000, 'import_val' => 350000000000
            ],
            [
                'name' => 'United States', 'iso_code' => 'US', 'currency_code' => 'USD',
                'region' => 'America', 'language' => 'English', 'population' => 333000000,
                'gdp' => 25460000000000, 'inflation' => 3.1, 'export_val' => 2060000000000, 'import_val' => 3240000000000
            ],
            [
                'name' => 'Japan', 'iso_code' => 'JP', 'currency_code' => 'JPY',
                'region' => 'Asia', 'language' => 'Japanese', 'population' => 125000000,
                'gdp' => 4230000000000, 'inflation' => 2.2, 'export_val' => 718000000000, 'import_val' => 900000000000
            ],
            [
                'name' => 'Singapore', 'iso_code' => 'SG', 'currency_code' => 'SGD',
                'region' => 'Asia', 'language' => 'English', 'population' => 5600000,
                'gdp' => 466000000000, 'inflation' => 4.8, 'export_val' => 515000000000, 'import_val' => 475000000000
            ],
        ];

        foreach ($countries as $c) {
            $existing = DB::table('countries')->where('name', $c['name'])->first();
            if (!$existing) {
                $countryId = DB::table('countries')->insertGetId(array_merge($c, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]));

                // Seed Risk Scores awal untuk setiap negara
                DB::table('risk_scores')->insert([
                    'country_id' => $countryId,
                    'weather_risk' => rand(15, 60),
                    'inflation_risk' => rand(10, 50),
                    'currency_risk' => rand(10, 40),
                    'news_sentiment_risk' => rand(20, 70),
                    'total_risk' => rand(20, 55),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        $ports = [
            // Indonesia (ID)
            ['name' => 'Tanjung Priok', 'country' => 'Indonesia', 'latitude' => -6.10, 'longitude' => 106.88, 'code' => 'IDTPP'],
            ['name' => 'Tanjung Perak', 'country' => 'Indonesia', 'latitude' => -7.19, 'longitude' => 112.73, 'code' => 'IDTPE'],
            ['name' => 'Belawan', 'country' => 'Indonesia', 'latitude' => 3.78, 'longitude' => 98.68, 'code' => 'IDBLW'],
            ['name' => 'Makassar Port', 'country' => 'Indonesia', 'latitude' => -5.13, 'longitude' => 119.41, 'code' => 'IDMAK'],
            ['name' => 'Batam Centre', 'country' => 'Indonesia', 'latitude' => 1.12, 'longitude' => 104.05, 'code' => 'IDBTH'],

            // Germany (DE)
            ['name' => 'Port of Hamburg', 'country' => 'Germany', 'latitude' => 53.54, 'longitude' => 9.99, 'code' => 'DEHAM'],
            ['name' => 'Bremerhaven', 'country' => 'Germany', 'latitude' => 53.55, 'longitude' => 8.57, 'code' => 'DEBRV'],
            ['name' => 'Wilhelmshaven', 'country' => 'Germany', 'latitude' => 53.52, 'longitude' => 8.11, 'code' => 'DEWVN'],
            ['name' => 'Rostock Port', 'country' => 'Germany', 'latitude' => 54.14, 'longitude' => 12.10, 'code' => 'DERSK'],

            // China (CN)
            ['name' => 'Shanghai Port', 'country' => 'China', 'latitude' => 31.23, 'longitude' => 121.47, 'code' => 'CNSHA'],
            ['name' => 'Port of Shenzhen', 'country' => 'China', 'latitude' => 22.54, 'longitude' => 114.05, 'code' => 'CNSZX'],
            ['name' => 'Ningbo-Zhoushan', 'country' => 'China', 'latitude' => 29.87, 'longitude' => 121.55, 'code' => 'CNNGB'],
            ['name' => 'Guangzhou Port', 'country' => 'China', 'latitude' => 23.10, 'longitude' => 113.45, 'code' => 'CNCAN'],
            ['name' => 'Qingdao Port', 'country' => 'China', 'latitude' => 36.08, 'longitude' => 120.32, 'code' => 'CNTAO'],
            ['name' => 'Tianjin Port', 'country' => 'China', 'latitude' => 38.98, 'longitude' => 117.73, 'code' => 'CNTXG'],

            // Australia (AU)
            ['name' => 'Port of Melbourne', 'country' => 'Australia', 'latitude' => -37.84, 'longitude' => 144.90, 'code' => 'AUMEL'],
            ['name' => 'Port of Sydney', 'country' => 'Australia', 'latitude' => -33.86, 'longitude' => 151.20, 'code' => 'AUSYD'],
            ['name' => 'Port of Brisbane', 'country' => 'Australia', 'latitude' => -27.38, 'longitude' => 153.17, 'code' => 'AUBNE'],
            ['name' => 'Fremantle Port', 'country' => 'Australia', 'latitude' => -32.05, 'longitude' => 115.74, 'code' => 'AUFRE'],
            ['name' => 'Port Hedland', 'country' => 'Australia', 'latitude' => -20.31, 'longitude' => 118.57, 'code' => 'AUPHE'],

            // United States (US)
            ['name' => 'Los Angeles Port', 'country' => 'United States', 'latitude' => 33.74, 'longitude' => -118.26, 'code' => 'USLAX'],
            ['name' => 'Long Beach Port', 'country' => 'United States', 'latitude' => 33.75, 'longitude' => -118.21, 'code' => 'USLGB'],
            ['name' => 'Port of New York & New Jersey', 'country' => 'United States', 'latitude' => 40.67, 'longitude' => -74.04, 'code' => 'USNYC'],
            ['name' => 'Port of Savannah', 'country' => 'United States', 'latitude' => 32.12, 'longitude' => -81.14, 'code' => 'USSAV'],
            ['name' => 'Port of Houston', 'country' => 'United States', 'latitude' => 29.73, 'longitude' => -95.26, 'code' => 'USHOU'],
            ['name' => 'Port of Seattle', 'country' => 'United States', 'latitude' => 47.61, 'longitude' => -122.34, 'code' => 'USSEA'],

            // Japan (JP)
            ['name' => 'Tokyo Port', 'country' => 'Japan', 'latitude' => 35.68, 'longitude' => 139.69, 'code' => 'JPTYO'],
            ['name' => 'Port of Yokohama', 'country' => 'Japan', 'latitude' => 35.45, 'longitude' => 139.66, 'code' => 'JPYOK'],
            ['name' => 'Port of Kobe', 'country' => 'Japan', 'latitude' => 34.68, 'longitude' => 135.21, 'code' => 'JPUKB'],
            ['name' => 'Port of Nagoya', 'country' => 'Japan', 'latitude' => 35.08, 'longitude' => 136.88, 'code' => 'JPNGO'],
            ['name' => 'Port of Osaka', 'country' => 'Japan', 'latitude' => 34.64, 'longitude' => 135.43, 'code' => 'JPOSA'],

            // Singapore (SG)
            ['name' => 'Port of Singapore', 'country' => 'Singapore', 'latitude' => 1.26, 'longitude' => 103.82, 'code' => 'SGSIN'],
            ['name' => 'Jurong Port', 'country' => 'Singapore', 'latitude' => 1.30, 'longitude' => 103.72, 'code' => 'SGJUR'],

            // South Korea (KR)
            ['name' => 'Port of Busan', 'country' => 'South Korea', 'latitude' => 35.10, 'longitude' => 129.04, 'code' => 'KRPUS'],
            ['name' => 'Port of Incheon', 'country' => 'South Korea', 'latitude' => 37.45, 'longitude' => 126.62, 'code' => 'KRINC'],

            // Netherlands (NL)
            ['name' => 'Port of Rotterdam', 'country' => 'Netherlands', 'latitude' => 51.95, 'longitude' => 4.05, 'code' => 'NLRTM'],
            ['name' => 'Port of Amsterdam', 'country' => 'Netherlands', 'latitude' => 52.40, 'longitude' => 4.80, 'code' => 'NLAMS'],

            // United Kingdom (GB)
            ['name' => 'Port of Felixstowe', 'country' => 'United Kingdom', 'latitude' => 51.95, 'longitude' => 1.31, 'code' => 'GBFXT'],
            ['name' => 'Port of Southampton', 'country' => 'United Kingdom', 'latitude' => 50.89, 'longitude' => -1.40, 'code' => 'GBSOU'],
            ['name' => 'Port of London', 'country' => 'United Kingdom', 'latitude' => 51.50, 'longitude' => 0.05, 'code' => 'GBLON'],

            // India (IN)
            ['name' => 'Jawaharlal Nehru Port (Nhava Sheva)', 'country' => 'India', 'latitude' => 18.94, 'longitude' => 72.95, 'code' => 'INNSA'],
            ['name' => 'Chennai Port', 'country' => 'India', 'latitude' => 13.08, 'longitude' => 80.29, 'code' => 'INMAA'],
            ['name' => 'Mundra Port', 'country' => 'India', 'latitude' => 22.74, 'longitude' => 69.70, 'code' => 'INMUN'],

            // Brazil (BR)
            ['name' => 'Port of Santos', 'country' => 'Brazil', 'latitude' => -23.97, 'longitude' => -46.29, 'code' => 'BRSSZ'],
            ['name' => 'Port of Rio de Janeiro', 'country' => 'Brazil', 'latitude' => -22.88, 'longitude' => -43.20, 'code' => 'BRRIO'],

            // Malaysia (MY)
            ['name' => 'Port Klang', 'country' => 'Malaysia', 'latitude' => 2.99, 'longitude' => 101.39, 'code' => 'MYPKG'],
            ['name' => 'Port of Tanjung Pelepas', 'country' => 'Malaysia', 'latitude' => 1.36, 'longitude' => 103.54, 'code' => 'MYTPP'],
            ['name' => 'Penang Port', 'country' => 'Malaysia', 'latitude' => 5.41, 'longitude' => 100.34, 'code' => 'MYPEN'],
        ];

        foreach ($ports as $p) {
            $existing = DB::table('ports')->where('name', $p['name'])->first();
            if (!$existing) {
                DB::table('ports')->insert(array_merge($p, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]));
            }
        }

        // 5. Seed Users
        DB::table('users')->insertOrIgnore([
            'name' => 'Admin Operator',
            'email' => 'admin@supplychain.com',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
