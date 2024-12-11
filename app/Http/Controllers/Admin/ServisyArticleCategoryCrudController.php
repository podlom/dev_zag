<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ServisyArticleCategoryRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\NewsCRUD\app\Models\Category;
use Backpack\LangFileManager\app\Models\Language;

/**
 * Class ServisyArticleCategoryCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ServisyArticleCategoryCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Aimix\Shop\app\Http\Controllers\Admin\Operations\TranslateOperation;

    private $languages = ['ru'];
    private $default_language = 'ru';
    private $ids = [369,370,371,372,373,374];

    public function setup()
    {
        $this->crud->setModel('Backpack\NewsCRUD\app\Models\Category');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/servisyarticlecategory');
        $this->crud->setEntityNameStrings('сервис', 'сервисы');
        
        $this->languages = Language::getActiveLanguagesNames();

        $this->default_language = Language::where('default', 1)->first()->abbr;
        
        $this->crud->query = $this->crud->query->withoutGlobalScopes();
        
        $this->crud->model->clearGlobalScopes();
        
        $this->crud->query->where(function($query) {
            $query->whereIn('parent_id', $this->ids)->orWhereIn('id', $this->ids);
          });
    }

    protected function setupListOperation()
    {
      $this->crud->orderBy('name');
      
      $this->crud->addFilter([
        'name'  => 'language_abbr',
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

          $this->crud->addColumn([
            'name' => 'name',
            'label' => 'Название',
          ]);

          $this->crud->addColumn([
            'name' => 'is_active',
            'label' => 'Активна',
            'type' => 'check',
          ]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(ServisyArticleCategoryRequest::class);

        if(\Route::current()->parameter('id'))
            $this->crud->getEntry(\Route::current()->parameter('id'));

        if(!$this->crud->entry || !$this->crud->entry->original){
          $this->crud->addField([
            'name' => 'is_active',
            'label' => 'Активна',
            'type' => 'boolean',
          ]);
        }
    
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
            'model' => 'servisyarticlecategory',
        ]);

        if(!$this->crud->entry || (!$this->crud->entry->original && !in_array($this->crud->entry->id,$this->ids))){
            $this->crud->addField([
                'name' => 'parent_id',
                'label' => 'Категория',
                'type' => 'hidden',
                'value' => 194,
            ]);
        }

        CRUD::addField([
            'name' => 'name',
            'label' => 'Название',
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

        CRUD::addField([
          'name' => 'content',
          'label' => 'Контент',
          'type' => 'ckeditor'
      ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
