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

        $ports = [
            ['name' => 'Tanjung Priok', 'country' => 'Indonesia', 'latitude' => -6.10, 'longitude' => 106.88, 'code' => 'IDTPP'],
            ['name' => 'Shanghai Port', 'country' => 'China', 'latitude' => 31.23, 'longitude' => 121.47, 'code' => 'CNSHA'],
            ['name' => 'Rotterdam Port', 'country' => 'Germany', 'latitude' => 51.90, 'longitude' => 4.48, 'code' => 'NLRTM'], // (didekatkan ke DE di region/rute)
            ['name' => 'Los Angeles Port', 'country' => 'United States', 'latitude' => 33.74, 'longitude' => -118.26, 'code' => 'USLAX'],
            ['name' => 'Tokyo Port', 'country' => 'Japan', 'latitude' => 35.68, 'longitude' => 139.69, 'code' => 'JPTYO'],
            ['name' => 'Port of Singapore', 'country' => 'Singapore', 'latitude' => 1.26, 'longitude' => 103.82, 'code' => 'SGSIN'],
            ['name' => 'Port of Busan', 'country' => 'South Korea', 'latitude' => 35.10, 'longitude' => 129.04, 'code' => 'KRPUS'],
            ['name' => 'Port of Antwerp', 'country' => 'Belgium', 'latitude' => 51.24, 'longitude' => 4.39, 'code' => 'BEANR'],
            ['name' => 'Port of Hamburg', 'country' => 'Germany', 'latitude' => 53.54, 'longitude' => 9.99, 'code' => 'DEHAM'],
            ['name' => 'Port of Melbourne', 'country' => 'Australia', 'latitude' => -37.84, 'longitude' => 144.90, 'code' => 'AUMEL'],
            ['name' => 'Port of Sydney', 'country' => 'Australia', 'latitude' => -33.86, 'longitude' => 151.20, 'code' => 'AUSYD'],
            ['name' => 'Port of Shenzhen', 'country' => 'China', 'latitude' => 22.54, 'longitude' => 114.05, 'code' => 'CNSZX'],
        ];

        foreach ($ports as $p) {
            DB::table('ports')->insert(array_merge($p, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
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
