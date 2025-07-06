<?php

namespace Database\Seeders;

use App\Enums\BikeCategory;
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
                'category' => BikeCategory::Gravel,
                'prices' => [
                    ['price' => 4500, 'period' => 'сутки'],
                    ['price' => 12000, 'period' => '3 дня'],
                    ['price' => 25000, 'period' => 'неделя'],
                    ['price' => 60000, 'period' => 'месяц'],
                ],
                'short_description' => 'Paзмep 54, нa рост 168-178. SRAM- Электроника',
                'img_url' => 'https://www.velodrom.cc/cdn/shop/files/specialized-crux-comp-complete-bike-gravel-sram-rival-gloss-dove-greymetallic-navy.webp?v=1722032437'
            ],
            [
                'name' => 'Specialized Diverge E5',
                'category' => BikeCategory::Gravel,
                'prices' => [
                    ['price' => 4500, 'period' => 'сутки'],
                    ['price' => 12000, 'period' => '3 дня'],
                    ['price' => 25000, 'period' => 'неделя'],
                    ['price' => 60000, 'period' => 'месяц'],
                ],
                'short_description' => 'Ручки grx810. Тормоза ХТ. Кассета m7000',
                'img_url' => 'https://assets.specialized.com/i/specialized/95423-70_DIVERGE-E5-BRCH-WHTMTN_HERO'
            ],
            [
                'name' => 'Giant Revolt M',
                'category' => BikeCategory::Gravel,
                'prices' => [
                    ['price' => 3000, 'period' => 'сутки'],
                    ['price' => 7500, 'period' => '3 дня'],
                    ['price' => 14000, 'period' => 'неделя'],
                    ['price' => 40000, 'period' => 'месяц'],
                ],
                'short_description' => 'Ручки grx810. Тормоза ХТ. Кассета m7000',
                'img_url' => 'https://www.sefiles.net/images/library/zoom/giant-revolt-advanced-pro-1-570972-1.png'
            ],
            [
                'name' => 'Pardus Sport XL',
                'category' => BikeCategory::Road,
                'prices' => [
                    ['price' => 2500, 'period' => 'сутки'],
                    ['price' => 6000, 'period' => '3 дня'],
                    ['price' => 12000, 'period' => 'неделя'],
                    ['price' => 30000, 'period' => 'месяц'],
                ],
                'short_description' => 'Shimano 105. Гидравлическая группа р7000',
                'img_url' => 'https://all-bikes.ru/goods_img/_goods/a06152458d7ea54b161cace2cdd4d94e_large.jpg'
            ],
            [
                'name' => 'Boardman Team Carbon',
                'category' => BikeCategory::Road,
                'prices' => [
                    ['price' => 2500, 'period' => 'сутки'],
                    ['price' => 6000, 'period' => '3 дня'],
                    ['price' => 12000, 'period' => 'неделя'],
                    ['price' => 30000, 'period' => 'месяц'],
                ],
                'short_description' => 'Shimano 105',
                'img_url' => 'https://storage.googleapis.com/fm-coresites-assets/rcuk/wp-content/uploads/2011/06/Boardman_Road_TeamC_xl_two.jpg'
            ],
            [
                'name' => 'Cube Elite C68 Race 29',
                'category' => BikeCategory::MTB,
                'prices' => [
                    ['price' => 3000, 'period' => 'сутки'],
                    ['price' => 7500, 'period' => '3 дня'],
                    ['price' => 14000, 'period' => 'неделя'],
                    ['price' => 40000, 'period' => 'месяц'],
                ],
                'short_description' => 'Shimano XTR. Пушка-гонка',
                'img_url' => 'https://static.cyclelab.eu/velos/cube/2007/highres/617400.jpg'
            ]
        
        ];
        
        foreach ($bikes as $bike) {
            Bike::create($bike);
        }
    }
}
