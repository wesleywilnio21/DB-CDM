<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AppSetting;

class AppSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'org_name' => 'CETIYA DHAMMA MANGGALA',
            'org_address' => 'Jl. Taman Sunter Indah A3/33 Sunter Jaya, Jakarta Utara 14350 – Indonesia',
            'org_phone' => 'Telp. (021) 65300211 Fax. (021) 65300211 WA. 085103728801',
            'org_tagline' => '"SELALU BERUSAHA BERBUAT KEBAJIKAN SEBANYAK MUNGKIN UNTUK DIRI SENDIRI DAN ORANG LAIN"',
            'org_city_default' => 'Jakarta',
        ];

        foreach ($settings as $key => $value) {
            AppSetting::set($key, $value);
        }
    }
}
