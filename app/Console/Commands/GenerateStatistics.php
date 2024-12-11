<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Aimix\Shop\app\Models\Product;
use Aimix\Shop\app\Models\Modification;

class GenerateStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistics:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate statistics';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      (new Product)->clearGlobalScopes();

      $data = [];
      $region_id = '11'; // Киевская область
      $types = [
          'Земельный участок',
          'Вилла',
          'Квадрекс',
          'Дуплекс',
          'Коттедж',
          'Таунхаус',
          'Эллинг'
      ];

      // По расстоянию от Киева
      $values = [
          '1-5 км' => [1, 5],
          '5-10 км' => [5.01, 10],
          '10-20 км' => [10.01, 20],
          '20-30 км' => [20.01, 30],
          '>30 км' => [30.01, PHP_INT_MAX]
      ];
      foreach($values as $key => $val) {
          $products = Product::active()
                             ->where('products.category_id', 1)
                             ->distinct('products.id')
                             ->select('products.*')
                             ->join('modifications', 'modifications.product_id', '=', 'products.id')
                             ->where('modifications.is_default', 0)
                             ->where('products.extras->is_frozen', 0)
                             ->where('modifications.price', '>', 0)
                             ->where('products.language_abbr', 'ru')
                             ->where('products.address->region', $region_id)
                             ->whereBetween('products.extras->distance', $val);
                             
          $data['Расстояние от Киева'][$key]['Количество'] = $products->count();

          foreach($types as $type) {
              $price = Modification::withoutGlobalScopes()
                                   ->where('modifications.is_default', 0)
                                   ->where('modifications.price', '!=', 0)
                                   ->join('products', 'products.id', '=', 'modifications.product_id')
                                   ->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')
                                   ->where('attribute_modification.attribute_id', 1)
                                   ->whereJsonContains('attribute_modification.value', $type)
                                   ->where('products.category_id', 1)
                                   ->where('products.address->region', $region_id)
                                   ->whereBetween('products.extras->distance', $val)
                                   ->where('products.extras->is_frozen', 0)
                                   ->avg('price');

              $data['Расстояние от Киева'][$key]['Цены'][$type] = round($price);
          }
      }

      // По размеру участка
      $values = [
          '1-3 сотки' => [0.01, 3],
          '3-5 соток' => [3.01, 5],
          '5-10 соток' => [5.01, 10],
          '10-20 соток' => [10.01, 20],
          '>20 соток' => [20.01, PHP_INT_MAX]
      ];
      
      foreach($values as $key => $val) {
          $products = Product::active()
                             ->where('products.category_id', 1)
                             ->distinct('products.id')
                             ->select('products.*')
                             ->join('modifications', 'modifications.product_id', '=', 'products.id')
                             ->where('modifications.is_default', 0)
                             ->where('products.extras->is_frozen', 0)
                             ->where('products.language_abbr', 'ru')
                             ->where('products.address->region', $region_id)
                             ->where(function($q) use ($val) {
                                 $q->whereBetween('products.extras->area_cottage', $val)
                                   ->orWhereBetween('products.extras->area_townhouse', $val)
                                   ->orWhereBetween('products.extras->area_duplex', $val)
                                   ->orWhereBetween('products.extras->area_quadrex', $val);
                             });
                             
          $data['Размер участка'][$key]['Количество'] = $products->count();

            $price = Modification::withoutGlobalScopes()
                                ->where('modifications.is_default', 0)
                                ->where('modifications.price', '!=', 0)
                                ->join('products', 'products.id', '=', 'modifications.product_id')
                                ->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')
                                ->where('attribute_modification.attribute_id', 1)
                                ->whereJsonContains('attribute_modification.value', 'Земельный участок')
                                ->where('products.category_id', 1)
                                ->where('products.address->region', $region_id)
                                ->where('products.extras->is_frozen', 0)
                                ->where(function($q) use ($val) {
                                    $q->whereBetween('products.extras->area_cottage', $val)
                                    ->orWhereBetween('products.extras->area_townhouse', $val)
                                    ->orWhereBetween('products.extras->area_duplex', $val)
                                    ->orWhereBetween('products.extras->area_quadrex', $val);
                                })
                                ->avg('price');

            $data['Размер участка'][$key]['Цены']['Земельный участок'] = round($price);
      }

      // По площади домовладений
      $values = [
          '<50 кв.м' => [1, 50],
          '50-100 кв.м' => [50.01, 100],
          '100-150 кв.м' => [100.01, 150],
          '150-200 кв.м' => [150.01, 200],
          '200-300 кв.м' => [200.01, 300],
          '>300 кв.м' => [300.01, PHP_INT_MAX]
      ];

      
      foreach($values as $key => $val) {
          $products = Product::active()
                             ->where('products.category_id', 1)
                             ->distinct('products.id')
                             ->select('products.*')
                             ->join('modifications', 'modifications.product_id', '=', 'products.id')
                             ->join('attribute_modification', function($join) use ($val) {
                                  $join->on('attribute_modification.modification_id', '=', 'modifications.id')
                                       ->where('attribute_modification.attribute_id', 4)
                                       ->where('attribute_modification.value', '>=', (int)$val[0])
                                       ->where('attribute_modification.value', '<=', (int)$val[1]);
                              })
                             ->where('products.extras->is_frozen', 0)
                             ->where('modifications.is_default', 0)
                             ->where('products.language_abbr', 'ru')
                             ->where('products.address->region', $region_id);
                             
          $data['Площадь домовладений'][$key]['Количество'] = $products->count();
          
          foreach($types as $type) {
              if($type === 'Земельный участок')
                  continue;

              $price = Modification::withoutGlobalScopes()
                                   ->where('modifications.is_default', 0)
                                   ->where('modifications.price', '!=', 0)
                                   ->join('products', 'products.id', '=', 'modifications.product_id')
                                   ->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')
                                   ->where('attribute_modification.attribute_id', 1)
                                   ->join('attribute_modification as area', function($join) use ($val) {
                                      $join->on('area.modification_id', '=', 'modifications.id')
                                           ->where('area.attribute_id', 4)
                                           ->where('area.value', '>=', (int)$val[0])
                                           ->where('area.value', '<=', (int)$val[1]);
                                   })
                                   ->whereJsonContains('attribute_modification.value', $type)
                                   ->where('products.category_id', 1)
                                   ->where('products.address->region', $region_id)
                                   ->where('products.extras->is_frozen', 0)
                                   ->avg('price');

              $data['Площадь домовладений'][$key]['Цены'][$type] = round($price);
          }
      }

      // По площади застройки участка
      $values = [
          '0,1-1 га' => [0.1, 1],
          '1-5 га' => [1.01, 5],
          '5-10 га' => [5.01, 10],
          '10-20 га' => [10.01, 20],
          '20-30 га' => [20.01, 30],
          '30-50 га' => [30.01, 50],
          '>50 га' => [50.01, PHP_INT_MAX]
      ];
      
      foreach($values as $key => $val) {
          $products = Product::active()
                             ->where('products.category_id', 1)
                             ->distinct('products.id')
                             ->select('products.*')
                             ->join('modifications', 'modifications.product_id', '=', 'products.id')
                             ->where('modifications.is_default', 0)
                             ->where('products.extras->is_frozen', 0)
                             ->where('products.language_abbr', 'ru')
                             ->where('products.address->region', $region_id)
                             ->whereBetween('products.extras->area', $val);
                             
          $data['Площадь застройки'][$key]['Количество'] = $products->count();

          foreach($types as $type) {
              $price = Modification::withoutGlobalScopes()
                                  ->where('modifications.is_default', 0)
                                  ->where('modifications.price', '!=', 0)
                                  ->join('products', 'products.id', '=', 'modifications.product_id')
                                  ->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')
                                  ->where('attribute_modification.attribute_id', 1)
                                  ->whereBetween('products.extras->area', $val)
                                  ->whereJsonContains('attribute_modification.value', $type)
                                  ->where('products.category_id', 1)
                                  ->where('products.address->region', $region_id)
                                  ->where('products.extras->is_frozen', 0)
                                  ->avg('price');
              
              $data['Площадь застройки'][$key]['Цены'][$type] = round($price);
          }
      }

      // По стоимости
      $values = [
          '<10 тыс. грн' => [0.1, 10000],
          '10-15 тыс. грн' => [10001, 15000],
          '15-20 тыс. грн' => [15001, 20000],
          '20-25 тыс. грн' => [20001, 25000],
          '25-30 тыс. грн' => [25001, 30000],
          '30-40 тыс. грн' => [30001, 40000],
          '>40' => [40001, PHP_INT_MAX]
      ];
      
      foreach($values as $key => $val) {
          $products = Product::active()
                             ->where('products.category_id', 1)
                             ->distinct('products.id')
                             ->select('products.*')
                             ->join('modifications', 'modifications.product_id', '=', 'products.id')
                             ->where('modifications.is_default', 0)
                             ->where('products.extras->is_frozen', 0)
                             ->where('products.language_abbr', 'ru')
                             ->where('products.address->region', $region_id)
                             ->whereBetween('modifications.price', $val);
                             
          $data['Стоимость'][$key]['Количество'] = $products->count();
      }

      // По материалам строительства
      $values = array_flip(__('attributes.wall_materials'));
      
      
      foreach($values as $key => $val) {
          $products = Product::active()
                             ->where('products.category_id', 1)
                             ->distinct('products.id')
                             ->select('products.*')
                             ->join('modifications', 'modifications.product_id', '=', 'products.id')
                             ->where('modifications.is_default', 0)
                             ->where('products.extras->is_frozen', 0)
                             ->where('products.language_abbr', 'ru')
                             ->where('products.address->region', $region_id)
                             ->where('products.extras->wall_material', $val);
                             
          $data['Материалы строительства'][$key]['Количество'] = $products->count();

          foreach($types as $type) {
              if($type === 'Земельный участок')
                  continue;
          
              $price = Modification::withoutGlobalScopes()
                                  ->where('modifications.is_default', 0)
                                  ->where('modifications.price', '!=', 0)
                                  ->join('products', 'products.id', '=', 'modifications.product_id')
                                  ->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')
                                  ->where('attribute_modification.attribute_id', 1)
                                  ->where('products.extras->wall_material', $val)
                                  ->whereJsonContains('attribute_modification.value', $type)
                                  ->where('products.category_id', 1)
                                  ->where('products.address->region', $region_id)
                                  ->where('products.extras->is_frozen', 0)
                                  ->avg('price');
              
              $data['Материалы строительства'][$key]['Цены'][$type] = round($price);
          }
      }

      \Storage::disk('public')->put('statistics.json', json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
}
