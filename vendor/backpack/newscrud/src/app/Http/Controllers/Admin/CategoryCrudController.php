<?php

namespace Backpack\NewsCRUD\app\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\NewsCRUD\app\Http\Requests\CategoryRequest;

use Backpack\LangFileManager\app\Models\Language;
use Backpack\NewsCRUD\app\Models\Category;

class CategoryCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ReorderOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Aimix\Shop\app\Http\Controllers\Admin\Operations\TranslateOperation;

    private $languages = ['ru'];
    private $default_language = 'ru';
    private $parent_categories;
    private $ids = [1,14,5,26,12,27,13,28];

    public function setup()
    {
        CRUD::setModel("Backpack\NewsCRUD\app\Models\Category");
        CRUD::setRoute(config('backpack.base.route_prefix', 'admin').'/category');
        CRUD::setEntityNameStrings('тему', 'темы');
        
        $this->crud->model->clearGlobalScopes();

        $this->languages = Language::getActiveLanguagesNames();
        $this->parent_categories = Category::whereIn('id', $this->ids)->where('language_abbr', 'ru')->pluck('name', 'id')->toArray();

        $this->default_language = Language::where('default', 1)->first()->abbr;
        
        $this->crud->query = $this->crud->query->withoutGlobalScopes();
        
        $this->crud->model->clearGlobalScopes();

        // $this->crud->addClause('whereIn', 'parent_id', $this->ids);
        $this->crud->query->where(function($query) {
          $query->whereIn('parent_id', $this->ids)->orWhereIn('id', $this->ids);
        });
    }

    protected function setupListOperation()
    {
      $this->crud->orderBy('name');
      
      if(!request('parent'))
        $this->crud->query->where('parent_id', '!=', null);
      
      $this->crud->addFilter([
        'name'  => 'language_abbr',
        'type'  => 'select2',
        'label' => 'Язык'
      ], function () {
        return $this->languages;
      }, function ($value) { // if the filter is active
        $this->crud->addClause('where', 'language_abbr', $value);
      });

      $this->crud->addFilter([
        'name'  => 'parent_id',
        'type'  => 'select2',
        'label' => 'Категория'
      ], function () {
        return $this->parent_categories;
      }, function ($value) { // if the filter is active
        $this->crud->query->where(function($query) use ($value) {
          $query->where('parent_id', $value)->orWhere('parent_id', Category::find($value)->translations->first()->id);
        });
      });

      $this->crud->addFilter([
        'name' => 'parent',
        'type' => 'simple',
        'label' => 'Показать категории',
      ],
      false,
      function() {
        $this->crud->query->where('parent_id', null);
        $this->crud->denyAccess('delete');
      });
      
      $this->crud->addColumn([
          'name' => 'language_abbr',
          'label' => 'Язык',
        ]);

      $this->crud->addColumn([
        'name' => 'name',
        'label' => 'Название',
      ]);
          
      CRUD::addColumn([
          'label' => 'Категория',
          'type' => 'select',
          'name' => 'parent_id',
          'entity' => 'parent',
          'attribute' => 'name',
      ]);

      $this->crud->addColumn([
        'name' => 'is_active',
        'label' => 'Активна',
        'type' => 'check',
      ]);
    }

    protected function setupShowOperation()
    {
        return $this->setupListOperation();
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(CategoryRequest::class);

        if(\Route::current()->parameter('id')) {
          $this->crud->getEntry(\Route::current()->parameter('id'));
        }

        
        $this->crud->addField([
          'name' => 'is_active',
          'label' => 'Активна',
          'type' => 'boolean',
        ]);

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
          'model' => 'category',
        ]);
        
        if(!$this->crud->entry || (!$this->crud->entry->original && !in_array($this->crud->entry->id,$this->ids))){
          $this->crud->addField([
            'name' => 'parent_id',
            'label' => 'Категория',
            'type' => 'select2_from_array',
            'options' => $this->parent_categories,
            'allows_null' => true,
          ]);
        }

        CRUD::addField([
            'name' => 'name',
            'label' => 'Name',
        ]);
        CRUD::addField([
            'name' => 'slug',
            'label' => 'Slug (URL)',
            'type' => 'text',
            'hint' => 'Will be automatically generated from your name, if left empty.',
            // 'disabled' => 'disabled'
        ]);

        if(!$this->crud->entry || !$this->crud->entry->original){
        CRUD::addField([
          'name' => 'image',
          'label' => 'Изображение',
          'type' => 'browse',
          'hint' => 'Будет использоваться для записей, которым не добавлено изображение.'
        ]);
        }

        CRUD::addField([
          'name' => 'meta_title',
          'label' => 'Meta title',
        ]);

        CRUD::addField([
          'name' => 'meta_desc',
          'label' => 'Meta description',
          'type' => 'textarea',
          'attributes' => [
            'rows' => 6
          ]
      ]);

      CRUD::addField([
        'name' => 'seo_text',
        'label' => 'Seo текст',
        'type' => 'ckeditor'
    ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function setupReorderOperation()
    {
        CRUD::set('reorder.label', 'name');
        CRUD::set('reorder.max_level', 2);
    }
}
