<?php

namespace Database\Seeders;

use App\Models\GlobalSettings;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    
    public function run()
    {
        $ins['site_name'] = 'Musee Musical';
        $ins['site_email'] = 'support@museemusical.in';
        $ins['site_mobile_no'] = '+91-9940046621';
        $ins['address'] = '73, Anna Salai, near Devi Theatre,<br> Mount Road, Border Thottam, Padupakkam, <br>Triplicane, Chennai, Tamil Nadu 600002';

        GlobalSettings::updateOrCreate(['id' => 1], $ins);
       
    }
}
