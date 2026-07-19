<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class FetchCountriesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-countries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch all countries from REST Countries API and insert into database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching countries from REST Countries API...');
        
        // Gunakan parameter fields agar response tidak terlalu besar (mencegah terpotong/dibatasi oleh server)
        $response = Http::timeout(60)->withoutVerifying()->get('https://restcountries.com/v3.1/all?fields=name,cca2,region,subregion,currencies,languages,population');
        
        $countries = [];
        if ($response->successful()) {
            $countries = $response->json();
        }

        // Fallback jika API utama diblokir atau hanya mengembalikan sedikit negara (masalah proxy/jaringan)
        if (!is_array($countries) || count($countries) < 10) {
            $this->info("Primary API returned " . count($countries) . " countries. Falling back to alternative data source...");
            $response = Http::timeout(60)->withoutVerifying()->get('https://raw.githubusercontent.com/mledoze/countries/master/countries.json');
            if ($response->successful()) {
                $countries = $response->json();
            }
        }

        if (is_array($countries) && count($countries) > 0) {
            $count = 0;
            
            file_put_contents(storage_path('logs/restcountries_dump.json'), json_encode($countries, JSON_PRETTY_PRINT));
            $this->info("Found " . count($countries) . " countries in API response. Response saved to storage/logs/restcountries_dump.json.");

            foreach ($countries as $index => $c) {
                $iso2 = $c['cca2'] ?? null;
                $name = $c['name']['common'] ?? null;
                $region = $c['region'] ?? null;
                $subregion = $c['subregion'] ?? '';
                
                $currencies = $c['currencies'] ?? [];
                $currencyCode = !empty($currencies) ? array_key_first($currencies) : 'USD';
                
                $languages = $c['languages'] ?? [];
                $language = !empty($languages) ? implode(', ', array_values($languages)) : 'N/A';
                
                $population = $c['population'] ?? 0;
                
                $fullRegion = $subregion ? "{$region} ({$subregion})" : $region;

                if ($iso2 && $name) {
                    $existing = DB::table('countries')->where('iso_code', $iso2)->first();
                    
                    if (!$existing) {
                        $countryId = DB::table('countries')->insertGetId([
                            'name' => $name,
                            'iso_code' => $iso2,
                            'currency_code' => $currencyCode,
                            'region' => $fullRegion,
                            'language' => $language,
                            'population' => $population,
                            'gdp' => 0,
                            'inflation' => 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        
                        // Seed basic risk score
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
                        $count++;
                    } else if ($existing->region === 'Data tidak tersedia' || $existing->language === 'Data tidak tersedia') {
                        DB::table('countries')->where('id', $existing->id)->update([
                            'region' => $fullRegion,
                            'language' => $language
                        ]);
                        $this->info("Fixed Region/Language for {$name}");
                    }
                }
            }
            $this->info("Successfully added {$count} new countries to the database.");
        } else {
            $this->error('Failed to fetch from REST Countries API.');
        }
    }
}
