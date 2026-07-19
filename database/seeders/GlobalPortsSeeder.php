<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GlobalPortsSeeder extends Seeder
{
    public function run(): void
    {
        $ports = [
            // Asia
            ['name' => 'Port of Chittagong', 'country' => 'Bangladesh', 'latitude' => 22.31, 'longitude' => 91.80, 'code' => 'BDCGP'],
            ['name' => 'Port of Karachi', 'country' => 'Pakistan', 'latitude' => 24.81, 'longitude' => 66.97, 'code' => 'PKKHI'],
            ['name' => 'Port of Colombo', 'country' => 'Sri Lanka', 'latitude' => 6.94, 'longitude' => 79.84, 'code' => 'LKCMB'],
            ['name' => 'Port of Manila', 'country' => 'Philippines', 'latitude' => 14.59, 'longitude' => 120.96, 'code' => 'PHMNL'],
            ['name' => 'Port of Laem Chabang', 'country' => 'Thailand', 'latitude' => 13.08, 'longitude' => 100.88, 'code' => 'THLCH'],
            ['name' => 'Port of Ho Chi Minh City', 'country' => 'Vietnam', 'latitude' => 10.76, 'longitude' => 106.66, 'code' => 'VNSGN'],
            ['name' => 'Port of Yangon', 'country' => 'Myanmar', 'latitude' => 16.76, 'longitude' => 96.15, 'code' => 'MMRGN'],
            ['name' => 'Port of Taipei', 'country' => 'Taiwan', 'latitude' => 25.14, 'longitude' => 121.38, 'code' => 'TWTPE'],
            
            // Middle East
            ['name' => 'Port of Jebel Ali', 'country' => 'United Arab Emirates', 'latitude' => 25.01, 'longitude' => 55.05, 'code' => 'AEJEA'],
            ['name' => 'Jeddah Islamic Port', 'country' => 'Saudi Arabia', 'latitude' => 21.48, 'longitude' => 39.16, 'code' => 'SAJED'],
            ['name' => 'Port of Salalah', 'country' => 'Oman', 'latitude' => 16.94, 'longitude' => 53.99, 'code' => 'OMSLL'],
            ['name' => 'Port of Haifa', 'country' => 'Israel', 'latitude' => 32.81, 'longitude' => 35.00, 'code' => 'ILHFA'],
            ['name' => 'Port of Shuwaikh', 'country' => 'Kuwait', 'latitude' => 29.35, 'longitude' => 47.92, 'code' => 'KWSWK'],
            ['name' => 'Hamad Port', 'country' => 'Qatar', 'latitude' => 25.01, 'longitude' => 51.57, 'code' => 'QAHMD'],
            ['name' => 'Port of Bandar Abbas', 'country' => 'Iran', 'latitude' => 27.14, 'longitude' => 56.06, 'code' => 'IRBND'],
            ['name' => 'Port of Aqaba', 'country' => 'Jordan', 'latitude' => 29.51, 'longitude' => 34.99, 'code' => 'JOAQJ'],

            // Europe
            ['name' => 'Port of Antwerp', 'country' => 'Belgium', 'latitude' => 51.27, 'longitude' => 4.35, 'code' => 'BEANR'],
            ['name' => 'Port of Le Havre', 'country' => 'France', 'latitude' => 49.48, 'longitude' => 0.10, 'code' => 'FRLEH'],
            ['name' => 'Port of Algeciras', 'country' => 'Spain', 'latitude' => 36.14, 'longitude' => -5.43, 'code' => 'ESALG'],
            ['name' => 'Port of Barcelona', 'country' => 'Spain', 'latitude' => 41.34, 'longitude' => 2.16, 'code' => 'ESBCN'],
            ['name' => 'Port of Genoa', 'country' => 'Italy', 'latitude' => 44.40, 'longitude' => 8.90, 'code' => 'ITGOA'],
            ['name' => 'Port of Piraeus', 'country' => 'Greece', 'latitude' => 37.94, 'longitude' => 23.63, 'code' => 'GRPIR'],
            ['name' => 'Port of Gdansk', 'country' => 'Poland', 'latitude' => 54.40, 'longitude' => 18.67, 'code' => 'PLGDN'],
            ['name' => 'Port of Gothenburg', 'country' => 'Sweden', 'latitude' => 57.69, 'longitude' => 11.87, 'code' => 'SEGOT'],
            ['name' => 'Port of Aarhus', 'country' => 'Denmark', 'latitude' => 56.15, 'longitude' => 10.22, 'code' => 'DKAAR'],
            ['name' => 'Port of Oslo', 'country' => 'Norway', 'latitude' => 59.90, 'longitude' => 10.73, 'code' => 'NOOSL'],
            ['name' => 'Port of Helsinki', 'country' => 'Finland', 'latitude' => 60.15, 'longitude' => 24.95, 'code' => 'FIHEL'],
            ['name' => 'Port of Dublin', 'country' => 'Ireland', 'latitude' => 53.34, 'longitude' => -6.21, 'code' => 'IEDUB'],
            ['name' => 'Port of Lisbon', 'country' => 'Portugal', 'latitude' => 38.70, 'longitude' => -9.16, 'code' => 'PTLIS'],
            ['name' => 'Port of Constanta', 'country' => 'Romania', 'latitude' => 44.13, 'longitude' => 28.64, 'code' => 'ROCND'],
            ['name' => 'Port of Istanbul', 'country' => 'Turkey', 'latitude' => 41.02, 'longitude' => 28.98, 'code' => 'TRIST'],

            // Americas
            ['name' => 'Port of Vancouver', 'country' => 'Canada', 'latitude' => 49.28, 'longitude' => -123.10, 'code' => 'CAVAN'],
            ['name' => 'Port of Montreal', 'country' => 'Canada', 'latitude' => 45.54, 'longitude' => -73.53, 'code' => 'CAMTR'],
            ['name' => 'Port of Veracruz', 'country' => 'Mexico', 'latitude' => 19.20, 'longitude' => -96.13, 'code' => 'MXVER'],
            ['name' => 'Port of Manzanillo', 'country' => 'Mexico', 'latitude' => 19.06, 'longitude' => -104.29, 'code' => 'MXZLO'],
            ['name' => 'Port of Buenos Aires', 'country' => 'Argentina', 'latitude' => -34.58, 'longitude' => -58.37, 'code' => 'ARBUE'],
            ['name' => 'Port of Valparaiso', 'country' => 'Chile', 'latitude' => -33.04, 'longitude' => -71.62, 'code' => 'CLVAP'],
            ['name' => 'Port of Callao', 'country' => 'Peru', 'latitude' => -12.05, 'longitude' => -77.14, 'code' => 'PECLL'],
            ['name' => 'Port of Guayaquil', 'country' => 'Ecuador', 'latitude' => -2.26, 'longitude' => -79.88, 'code' => 'ECGYE'],
            ['name' => 'Port of Buenaventura', 'country' => 'Colombia', 'latitude' => 3.89, 'longitude' => -77.02, 'code' => 'COBUN'],
            ['name' => 'Port of Montevideo', 'country' => 'Uruguay', 'latitude' => -34.90, 'longitude' => -56.20, 'code' => 'UYMVD'],
            ['name' => 'Port of Kingston', 'country' => 'Jamaica', 'latitude' => 17.96, 'longitude' => -76.79, 'code' => 'JMKIN'],
            ['name' => 'Port of San Juan', 'country' => 'Puerto Rico', 'latitude' => 18.45, 'longitude' => -66.10, 'code' => 'PRSJU'],

            // Africa
            ['name' => 'Port of Durban', 'country' => 'South Africa', 'latitude' => -29.87, 'longitude' => 31.02, 'code' => 'ZADUR'],
            ['name' => 'Port of Cape Town', 'country' => 'South Africa', 'latitude' => -33.90, 'longitude' => 18.43, 'code' => 'ZACPT'],
            ['name' => 'Port of Lagos (Apapa)', 'country' => 'Nigeria', 'latitude' => 6.44, 'longitude' => 3.36, 'code' => 'NGLOS'],
            ['name' => 'Port of Mombasa', 'country' => 'Kenya', 'latitude' => -4.05, 'longitude' => 39.65, 'code' => 'KEMBA'],
            ['name' => 'Port of Dar es Salaam', 'country' => 'Tanzania', 'latitude' => -6.83, 'longitude' => 39.29, 'code' => 'TzDAR'],
            ['name' => 'Port of Alexandria', 'country' => 'Egypt', 'latitude' => 31.18, 'longitude' => 29.88, 'code' => 'EGALY'],
            ['name' => 'Port Said', 'country' => 'Egypt', 'latitude' => 31.25, 'longitude' => 32.31, 'code' => 'EGPSD'],
            ['name' => 'Port of Casablanca', 'country' => 'Morocco', 'latitude' => 33.60, 'longitude' => -7.60, 'code' => 'MACAS'],
            ['name' => 'Port of Algiers', 'country' => 'Algeria', 'latitude' => 36.76, 'longitude' => 3.06, 'code' => 'DZALG'],
            ['name' => 'Port of Tema', 'country' => 'Ghana', 'latitude' => 5.63, 'longitude' => 0.01, 'code' => 'GHTEM'],
            ['name' => 'Port of Abidjan', 'country' => 'Ivory Coast', 'latitude' => 5.27, 'longitude' => -4.01, 'code' => 'CIABJ'],
            ['name' => 'Port of Dakar', 'country' => 'Senegal', 'latitude' => 14.68, 'longitude' => -17.42, 'code' => 'SNDKR'],
            ['name' => 'Port of Luanda', 'country' => 'Angola', 'latitude' => -8.79, 'longitude' => 13.24, 'code' => 'AOLAD'],

            // Oceania
            ['name' => 'Port of Auckland', 'country' => 'New Zealand', 'latitude' => -36.84, 'longitude' => 174.77, 'code' => 'NZAKL'],
            ['name' => 'Port of Tauranga', 'country' => 'New Zealand', 'latitude' => -37.66, 'longitude' => 176.17, 'code' => 'NZTRG'],
            ['name' => 'Port of Suva', 'country' => 'Fiji', 'latitude' => -18.13, 'longitude' => 178.42, 'code' => 'FJSVU'],
            ['name' => 'Port Moresby', 'country' => 'Papua New Guinea', 'latitude' => -9.46, 'longitude' => 147.16, 'code' => 'PGPOM']
        ];

        $count = 0;
        foreach ($ports as $p) {
            $existing = DB::table('ports')->where('name', $p['name'])->first();
            if (!$existing) {
                DB::table('ports')->insert(array_merge($p, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]));
                $count++;
            }
        }
        
        $this->command->info("Seeded {$count} new global ports.");
    }
}
