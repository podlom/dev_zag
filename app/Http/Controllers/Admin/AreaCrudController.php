<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AreaRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

use Backpack\LangFileManager\app\Models\Language;
use App\Region;
/**
 * Class AreaCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AreaCrudController extends CrudController
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
        $this->crud->setModel('App\Area');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/area');
        $this->crud->setEntityNameStrings('район', 'районы');

        $this->languages = Language::getActiveLanguagesNames();
        $this->default_language = Language::where('default', 1)->first()->abbr;
        $this->regions = Region::pluck('name', 'region_id')->toArray();
        
        $this->current_language = \Request::input('language_abbr')? \Request::input('language_abbr') : null;
        
        $this->crud->query = $this->crud->query->withoutGlobalScopes();
        $this->crud->model->clearGlobalScopes();
    }

    protected function setupListOperation()
    {
        $this->crud->orderBy('name');

        $this->crud->addFilter([
            'name'  => 'language',
            'type'  => 'select2',
            'label' => 'Язык'
          ], function () {
            return $this->languages;
          }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'language_abbr', $value);
          });

          $this->crud->addFilter([
            'name'  => 'region_id',
            'type'  => 'select2',
            'label' => 'Область'
          ], function () {
            return $this->regions;
          }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'region_id', $value);
          });
          
          $this->crud->addColumn([
            'name' => 'language_abbr',
            'label' => 'Язык',
          ]);

          $this->crud->addColumn([
            'name' => 'name',
            'label' => 'Название',
          ]);

          $this->crud->addColumn([
            'name'         => 'region',
            'type'         => 'select',
            'label'        => 'Область',
            'entity'    => 'region_admin',
            'attribute' => 'name',
            'model'     => App\Region::class,
         ]);

          $this->crud->addColumn([
            'name' => 'extras.cottage_h1',
            'label' => 'Заголовок (коттеджные городки)',
          ]);

          $this->crud->addColumn([
            'name' => 'extras.newbuild_h1',
            'label' => 'Заголовок (новостройки)',
          ]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(AreaRequest::class);

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
        'model' => 'area',
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

      // $this->crud->addField([
      //   'name' => 'region_id',
      //   'label' => 'Область',
      //   'type' => 'select2',
      //   'model' => '\App\Region',
      //   'attribute' => 'name',
      //   'entity' => 'region',
      //   'options' => function($query) {
      //     return $query->where('language_abbr', 'ru')->get();
      //   }
      // ]);

      $this->crud->addField([
        'name' => 'region_id',
        'label' => 'Область',
        'type' => 'zagorodna_region',
      ]);

      $this->crud->addField([
        'name' => 'cottage_h1',
        'label' => 'Заголовок',
        'fake' => true,
        'store_in' => 'extras',
        'tab' => 'Коттеджные городки'
      ]);

      $this->crud->addField([
        'name' => 'cottage_keywords',
        'label' => 'Ключевые слова',
        'fake' => true,
        'store_in' => 'extras',
        'tab' => 'Коттеджные городки'
      ]);

      $this->crud->addField([
        'name' => 'cottage_meta_title',
        'label' => 'Meta title',
        'fake' => true,
        'store_in' => 'extras',
        'tab' => 'Коттеджные городки'
      ]);

      $this->crud->addField([
        'name' => 'cottage_meta_desc',
        'label' => 'Meta description',
        'type' => 'textarea',
        'attributes' => [
            'rows' => 6,
        ],
        'fake' => true,
        'store_in' => 'extras',
        'tab' => 'Коттеджные городки'
      ]);

      $this->crud->addField([
        'name' => 'cottage_seo_text',
        'label' => 'Seo текст',
        'type' => 'ckeditor',
        'fake' => true,
        'store_in' => 'extras',
        'tab' => 'Коттеджные городки'
      ]);

      $this->crud->addField([
        'name' => 'newbuild_h1',
        'label' => 'Заголовок',
        'fake' => true,
        'store_in' => 'extras',
        'tab' => 'Новостройки'
      ]);

      $this->crud->addField([
        'name' => 'newbuild_keywords',
        'label' => 'Ключевые слова',
        'fake' => true,
        'store_in' => 'extras',
        'tab' => 'Новостройки'
      ]);

      $this->crud->addField([
        'name' => 'newbuild_meta_title',
        'label' => 'Meta title',
        'fake' => true,
        'store_in' => 'extras',
        'tab' => 'Новостройки'
      ]);

      $this->crud->addField([
        'name' => 'newbuild_meta_desc',
        'label' => 'Meta description',
        'type' => 'textarea',
        'attributes' => [
            'rows' => 6,
        ],
        'fake' => true,
        'store_in' => 'extras',
        'tab' => 'Новостройки'
      ]);

      $this->crud->addField([
        'name' => 'newbuild_seo_text',
        'label' => 'Seo текст',
        'type' => 'ckeditor',
        'fake' => true,
        'store_in' => 'extras',
        'tab' => 'Новостройки'
      ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
