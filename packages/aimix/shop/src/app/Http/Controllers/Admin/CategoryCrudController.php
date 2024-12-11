<?php

namespace Aimix\Shop\app\Http\Controllers\Admin;

use Aimix\Shop\app\Http\Requests\CategoryRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

use Backpack\LangFileManager\app\Models\Language;

/**
 * Class CategoryCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class CategoryCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Aimix\Shop\app\Http\Controllers\Admin\Operations\TranslateOperation;
    
    private $languages = 'ru';
    private $default_language = 'ru';

    public function setup()
    {
        $this->crud->setModel('Aimix\Shop\app\Models\Category');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/prod_category');
        $this->crud->setEntityNameStrings('категорию', 'категории');
        
        if(config('aimix.aimix.enable_languages')) {
          $this->languages = Language::getActiveLanguagesNames();
          $this->default_language = Language::where('default', 1)->first()->abbr;
          
          $this->crud->query = $this->crud->query->withoutGlobalScopes();
          $this->crud->model->clearGlobalScopes();
        }
        
    }

    protected function setupListOperation()
    {
        if(config('aimix.aimix.enable_languages')) {
          $this->crud->addFilter([
            'name'  => 'language',
            'type'  => 'select2',
            'label' => 'Язык'
          ], function () {
            return $this->languages;
          }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'language_abbr', $value);
          });
          
          $this->crud->addColumn([
            'name' => 'language_abbr',
            'label' => 'Язык',
          ]);
        }
        
        $this->crud->addColumn([
          'name' => 'name',
          'label' => 'Название',
        ]);

        if(config('aimix.shop.enable_parent_categories')) {
          $this->crud->addColumn([
            'name' => 'parent_id',
            'label' => 'Родительская категория',
            'type' => 'select',
            'entity' => 'parent',
            'attribute' => 'name',
            'model' => 'Aimix\Shop\app\Models\Category'
          ]);
        }

        $this->crud->addColumn([
          'name' => 'image',
          'label' => 'Изображение',
          'type' => 'image'
        ]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(CategoryRequest::class);

        if(\Route::current()->parameter('id'))
        $this->crud->getEntry(\Route::current()->parameter('id'));
        
        $this->crud->addField([
          'name' => 'language_abbr',
          'label' => 'Язык',
          'default' => $this->default_language,
          'attributes' => [
            'readonly' => 'readonly',
          ],
        ]);

        $this->crud->addField([
          'name' => 'original_id',
          'label' => 'Оригинал/перевод',
          'type' => 'translation',
          'model' => 'prod_category', // site.com/admin/{model}
        ]);
        
      if(config('aimix.shop.enable_parent_categories')) {
        $this->crud->addField([
          'name' => 'parent_id',
          'label' => "Родительская категория",
          'type' => 'select2',
          'entity' => 'parent', 
          'attribute' => 'name', 
          'model' => "Aimix\Shop\app\Models\Category",
          'options'   => (function ($query) {
            return $query->where('parent_id', null)->get();
          }),
        ]);
      }

        $this->crud->addFields([
          [
            'name' => 'name',
            'label' => 'Название',
          ],
          [
            'name' => 'slug',
            'label' => 'URL',
            'prefix' => url('/').'/',
            'hint' => 'По умолчанию будет сгенерирован из названия.'
          ],
        ]);

      if(!$this->crud->entry || !$this->crud->entry->original){
        $this->crud->addFields([
          [
            'name' => 'is_popular',
            'label' => 'Популярная',
            'type' => 'boolean'
          ],
          [
            'name' => 'image',
            'label' => 'Изображение',
            'type' => 'browse',
          ],
        ]);
      }

        $this->crud->addFields([
          [
            'name' => 'description',
            'label' => 'Описание',
            'type' => 'summernote',
            'options' => [
              'height' => '200px'
            ]
          ],
          [
            'name' => 'meta_title',
            'label' => 'Заголовок (meta)',
            'hint' => 'По умолчанию будет использовано название'
          ],
          [
            'name' => 'meta_desc',
            'label' => 'Описание (meta)',
          ],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
