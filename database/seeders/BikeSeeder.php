<?php

namespace Database\Seeders;

use App\Models\Bike;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bikes = [
            [
                'name' => 'Specialized Crux',
                'short_description' => 'Paзмep 54, нa рост 168-178. SRAM- Электроника',
                'img_url' => 'https://www.velodrom.cc/cdn/shop/files/specialized-crux-comp-complete-bike-gravel-sram-rival-gloss-dove-greymetallic-navy.webp?v=1722032437'
            ],
            [
                'name' => 'Specialized Diverge E5',
                'short_description' => 'Ручки grx810. Тормоза ХТ. Кассета m7000',
                'img_url' => 'https://assets.specialized.com/i/specialized/95423-70_DIVERGE-E5-BRCH-WHTMTN_HERO'
            ],
            [
                'name' => 'Giant Revolt M',
                'short_description' => 'Ручки grx810. Тормоза ХТ. Кассета m7000',
                'img_url' => 'https://www.summumbike.com/1027-thickbox_default/velo-de-gravel-giant-revolt-adv-1-taille-m.jpg'
            ],
            [
                'name' => 'Pardus Sport XL',
                'short_description' => 'Shimano 105. Гидравлическая группа р7000',
                'img_url' => 'https://all-bikes.ru/goods_img/_goods/a06152458d7ea54b161cace2cdd4d94e_large.jpg'
            ],
            [
                'name' => 'Boardman Team Carbon',
                'short_description' => 'Shimano 105',
                'img_url' => 'https://storage.googleapis.com/fm-coresites-assets/rcuk/wp-content/uploads/2011/06/Boardman_Road_TeamC_xl_two.jpg'
            ],
            [
                'name' => 'Cube Elite C68 Race 29',
                'short_description' => 'Shimano XTR. Пушка-гонка',
                'img_url' => 'https://static.cyclelab.eu/velos/cube/2007/highres/617400.jpg'
            ]
            
        ];
        Bike::insert($bikes);
//        DB::table('bikes')->insert($bikes);
    }
}
