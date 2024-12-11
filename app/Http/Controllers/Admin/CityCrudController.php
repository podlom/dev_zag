<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CityRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;


use Backpack\LangFileManager\app\Models\Language;
use App\Region;

/**
 * Class CityCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CityCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Aimix\Shop\app\Http\Controllers\Admin\Operations\TranslateOperation;

    private $languages = ['ru'];
    private $default_language = 'ru';
    private $regions;

    public function setup()
    {
        $this->crud->setModel('App\City');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/city');
        $this->crud->setEntityNameStrings('населенный пункт', 'населенные пункты');

        $this->languages = Language::getActiveLanguagesNames();
        $this->default_language = Language::where('default', 1)->first()->abbr;
        $this->regions = Region::pluck('name', 'region_id')->toArray();
        
        $this->current_language = \Request::input('language_abbr')? \Request::input('language_abbr') : null;
        
        $this->crud->query = $this->crud->query->withoutGlobalScopes();
        $this->crud->model->clearGlobalScopes();
    }

    protected function setupListOperation()
    {
        $this->crud->orderBy('cities.name');
        
        $this->crud->addFilter([
            'name'  => 'language',
            'type'  => 'select2',
            'label' => 'Язык'
          ], function () {
            return $this->languages;
          }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'cities.language_abbr', $value);
          });

          $this->crud->addFilter([
            'name'  => 'region_id',
            'type'  => 'select2',
            'label' => 'Область'
          ], function () {
            return $this->regions;
          }, function ($value) { // if the filter is active
            $this->crud->query->distinct('cities.id')->join('areas', 'areas.area_id', '=', 'cities.area_id')->where('areas.region_id', $value)->select('cities.*');
          });
          
          $this->crud->addColumn([
            'name' => 'language_abbr',
            'label' => 'Язык',
            'searchLogic' => false
          ]);

          $this->crud->addColumn([
            'name' => 'name',
            'label' => 'Название',
            'searchLogic' => function ($query, $column, $searchTerm) {
              $query->orWhere('cities.name', 'like', '%'.$searchTerm.'%');
            }
          ]);

          $this->crud->addColumn([
            'name'         => 'region.name',
            'label'        => 'Область',
         ]);

        $this->crud->addColumn([
          'name'         => 'area',
          'type'         => 'select',
          'label'        => 'Район',
          'entity'    => 'area_admin',
          'attribute' => 'name',
          'model'     => App\Area::class,
          'searchLogic' => function ($query, $column, $searchTerm) {
            $ids = \App\Area::where('name', 'like', '%'.$searchTerm.'%')->pluck('area_id');
            $query->orWhereIn('cities.area_id', $ids);
          }
       ]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(CityRequest::class);

        if(\Route::current()->parameter('id'))
          $this->crud->getEntry(\Route::current()->parameter('id'));
          
        $this->crud->addField([
          'name' => 'language_abbr',
          'label' => 'Язык',
          'default' => $this->default_language,
          'attributes' => [
            'readonly' => 'readonly',
          ]
        ]);
  
        $this->crud->addField([
          'name' => 'translation_id',
          'label' => 'Оригинал/перевод',
          'type' => 'translation',
          'model' => 'city',
        ]);
  
        $this->crud->addField([
          'name' => 'name',
          'label' => 'Название',
        ]);

        $this->crud->addField([
          'name' => 'name_genitive',
          'label' => 'Название (родительный падеж)',
        ]);
  
        $this->crud->addField([
          'name' => 'slug',
          'label' => 'Slug',
        ]);

        $this->crud->addField([
          'name' => 'area_id',
          'label' => 'Район',
          'type' => 'zagorodna_area',
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
