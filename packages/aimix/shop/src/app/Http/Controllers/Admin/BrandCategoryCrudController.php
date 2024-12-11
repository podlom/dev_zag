<?php

namespace Aimix\Shop\app\Http\Controllers\Admin;

use Aimix\Shop\app\Http\Requests\BrandCategoryRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

use Backpack\LangFileManager\app\Models\Language;

/**
 * Class BrandCategoryCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class BrandCategoryCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Aimix\Shop\app\Http\Controllers\Admin\Operations\TranslateOperation;

    private $languages = 'ru';
    private $default_language = 'ru';

    public function setup()
    {
        $this->crud->setModel('Aimix\Shop\app\Models\BrandCategory');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/brandcategory');
        $this->crud->setEntityNameStrings('категорию', 'категории');

        $this->languages = Language::getActiveLanguagesNames();
        $this->default_language = Language::where('default', 1)->first()->abbr;
          
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
            'name' => 'image',
            'label' => 'Изображение',
            'type' => 'image'
        ]);

        $this->crud->addColumn([
            'name' => 'is_active',
            'label' => 'Активна',
            'type' => 'check'
        ]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(BrandCategoryRequest::class);

        if(\Route::current()->parameter('id'))
          $this->crud->getEntry(\Route::current()->parameter('id'));

        if(config('aimix.aimix.enable_languages')) {

          $this->crud->addField([
            'name' => 'language_abbr',
            'label' => 'Язык',
            'default' => $this->default_language,
            'attributes' => [
              'readonly' => 'readonly',
            ]
          ]);

          $this->crud->addField([
            'name' => 'original_id',
            'label' => 'Оригинал/перевод',
            'type' => 'translation',
            'model' => 'brandcategory', // site.com/admin/{model}
          ]);
        }

        if(!$this->crud->entry || !$this->crud->entry->original){
            $this->crud->addField([
                'name' => 'is_active',
                'label' => 'Активна',
                'type' => 'boolean'
            ]);
        }

        $this->crud->addField([
            'name' => 'name',
            'label' => 'Название',
        ]);
        
        $this->crud->addField([
            'name' => 'slug',
            'label' => 'Slug',
            'hint' => 'По умолчанию будет сгенерирован из названия.'
        ]);
        
        if(!$this->crud->entry || !$this->crud->entry->original){
            $this->crud->addField([
                'name' => 'image',
                'label' => 'Изображение',
                'type' => 'browse'
            ]);

            $this->crud->addField([
                'name' => 'is_popular',
                'label' => 'Популярная',
                'type' => 'boolean'
            ]);
        }

        $this->crud->addField([
            'name' => 'seo_separator',
            'type' => 'custom_html',
            'value' => '<br><h2>SEO</h2><hr>',
        ]);

        $this->crud->addField([
            'name' => 'seo_title',
            'label' => 'Заголовок',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'seo_desc',
            'label' => 'Текст',
            'type' => 'ckeditor',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'meta_separator',
            'type' => 'custom_html',
            'value' => '<br><h2>Meta</h2><hr>',
        ]);

        $this->crud->addField([
            'name' => 'meta_title',
            'label' => 'Title',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'meta_desc',
            'label' => 'Description',
            'fake' => true,
            'store_in' => 'extras',
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
