<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\FaqCategoryRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

use Backpack\NewsCRUD\app\Models\Category;
use Backpack\LangFileManager\app\Models\Language;
/**
 * Class FaqCategoryCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class FaqCategoryCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Aimix\Shop\app\Http\Controllers\Admin\Operations\TranslateOperation;

    private $languages = 'ru';
    private $default_language = 'ru';
    private $article_categories = [];
    
    public function setup()
    {
        $this->crud->setModel('App\Models\FaqCategory');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/faqcategory');
        $this->crud->setEntityNameStrings('категорию', 'категории');

        $this->languages = Language::getActiveLanguagesNames();
        $this->default_language = Language::where('default', 1)->first()->abbr;
        $this->article_categories = Category::withoutGlobalScopes()->where('language_abbr', 'ru')->whereIn('parent_id', [1,14,5,26,12,27,13,28])->pluck('name', 'id')->toArray();
          
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
        $this->crud->setValidation(FaqCategoryRequest::class);

        // TODO: remove setFromDb() and manually define Fields
        // $this->crud->setFromDb();
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
            ]
          ]);

          $this->crud->addField([
            'name' => 'translation_id',
            'label' => 'Оригинал/перевод',
            'type' => 'translation',
            'model' => 'faqcategory',
          ]);

        $this->crud->addField([
            'name' => 'name',
            'label' => 'Название',
        ]);

        $this->crud->addField([
            'name' => 'slug',
            'label' => 'URL',
            'prefix' => url('faq').'/',
            'hint' => 'По умолчанию будет сгенерирован из названия.'
        ]);

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
          
        if(!$this->crud->entry || !$this->crud->entry->original){
        $this->crud->addField([
            'name' => 'categories',
            'label' => 'Категории статей',
            'type' => 'select2_from_array',
            'options'   => $this->article_categories,
            'allows_multiple' => true,
            'allows_null' => true

        ]);
        }
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
