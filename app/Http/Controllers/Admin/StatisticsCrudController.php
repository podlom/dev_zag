<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StatisticsRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\Statistics;
use App\Region;
use Aimix\Shop\app\Models\Modification;

/**
 * Class StatisticsCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class StatisticsCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        $this->crud->setModel('App\Models\Statistics');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/statistics');
        $this->crud->setEntityNameStrings('статистику', 'статистика');
    }

    public function create()
    {
        $statistics = new Statistics;
        $statistics->type = 'cottage';

        $data = [];
        foreach(Region::get() as $region) {
            $price = Modification::where('price', '!=', 0)->select('modifications.*')->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonDoesntContain('attribute_modification.value', 'Земельный участок')->join('products', 'products.id', '=', 'modifications.product_id')->where('products.category_id', 1)->where('products.extras->is_frozen', 0)->where('products.is_active', 1)->where('products.address->region', $region->region_id)->avg('price');
            $data[$region->region_id] = $price? round($price) : $price;
        }
        $statistics->data = $data;
        $statistics->total = round(Modification::where('price', '!=', 0)->select('modifications.*')->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonDoesntContain('attribute_modification.value', 'Земельный участок')->join('products', 'products.id', '=', 'modifications.product_id')->where('products.category_id', 1)->where('products.extras->is_frozen', 0)->where('products.is_active', 1)->avg('price'));
        
        $statistics->save();

        $statistics = new Statistics;
        $statistics->type = 'newbuild';

        $data = [];
        foreach(Region::get() as $region) {
            $price = Modification::where('price', '!=', 0)->select('modifications.*')->join('products', 'products.id', '=', 'modifications.product_id')->where('products.category_id', 2)->where('products.extras->is_frozen', 0)->where('products.is_active', 1)->where('products.address->region', $region->region_id)->avg('price');
            $data[$region->region_id] = $price? round($price) : $price;
        }
        $statistics->data = $data;
        $statistics->total = round(Modification::where('price', '!=', 0)->select('modifications.*')->join('products', 'products.id', '=', 'modifications.product_id')->where('products.category_id', 2)->where('products.extras->is_frozen', 0)->where('products.is_active', 1)->avg('price'));

        $statistics->save();

        return back();
    }

    protected function setupListOperation()
    {
        // TODO: remove setFromDb() and manually define Columns, maybe Filters
        // $this->crud->setFromDb();

        CRUD::addColumn([
            'name' => 'created_at',
            'label' => 'Дата',
            'type' => 'date'
        ]);

        CRUD::addColumn([
            'name' => 'type',
            'label' => 'Тип',
            'type' => 'select_from_array',
            'options' => [
                'cottage' => 'Коттеджи',
                'newbuild' => 'Новостройки'
            ]
        ]);

        CRUD::addColumn([
            'name' => 'total',
            'label' => 'Украина',
            'style' => 'display:block;text-align:center'
        ]);

        foreach(Region::whereNotIn('region_id', [5,13])->get() as $region) {
            CRUD::addColumn([
                'name' => 'data_' . $region->region_id,
                'label' => $region->name,
                'type' => 'closure',
                'function' => function($entry) use ($region) {
                    return $entry->data[$region->region_id];
                },
                'style' => 'display:block;text-align:center'
            ]);
        }
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

}
