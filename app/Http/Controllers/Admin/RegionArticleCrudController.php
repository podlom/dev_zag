<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RegionArticleRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

use Backpack\LangFileManager\app\Models\Language;
use Backpack\NewsCRUD\app\Models\Category;
use Backpack\NewsCRUD\app\Models\Article;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

/**
 * Class RegionArticleCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class RegionArticleCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Aimix\Shop\app\Http\Controllers\Admin\Operations\TranslateOperation;

    private $statuses = [
        'PUBLISHED' => '<span><i class="la la-check-circle"></i></span>',
        'DRAFTED' => '<span><i class="la la-circle"></i></span>'
    ];
    private $statuses_filter = [
        'PUBLISHED' => 'Опубликовано',
        'DRAFTED' => 'Не опубликовано'
    ];
    private $languages = ['ru'];
    private $themes;
    private $ids = [202,203];

    public function setup()
    {
        $this->crud->setModel('Backpack\NewsCRUD\app\Models\Article');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/regionarticle');
        $this->crud->setEntityNameStrings('запись', 'записи');

        $this->current_category = \Request::input('category_id')? \Request::input('category_id') : null;
        $this->default_language = Language::where('default', 1)->first()->abbr;
        
        $this->languages = Language::getActiveLanguagesNames();
        $this->crud->model->clearGlobalScopes();
        $this->themes = Category::has('parent')->where('language_abbr', 'ru')->whereIn('parent_id', $this->ids)->orderBy('name')->pluck('name', 'id')->toArray();

        $this->crud->query->whereHas('category', function($q) {
            $q->whereIn('parent_id', $this->ids);
        });
        
        Category::first();
        
        $this->crud->query = $this->crud->query->withoutGlobalScopes();
        
        $this->crud->model->clearGlobalScopes();
    }

    protected function setupListOperation()
    {
        $this->crud->orderBy('title');

        $this->crud->addFilter([
          'name'  => 'language_abbr',
          'type'  => 'select2',
          'label' => 'Язык',
        ], function () {
          return $this->languages;
        }, function ($value) { // if the filter is active
          $this->crud->addClause('where', 'language_abbr', $value);
        });
        
        $this->crud->addFilter([
          'name'  => 'category_id',
          'type'  => 'select2',
          'label' => 'Тема'
        ], function () {
          return $this->themes;
        }, function ($value) { // if the filter is active
            $this->crud->addClause('where', function($q) use ($value) {
              $q->where('category_id', $value)->orWhere('category_id', Category::find($value)->translations->first()->id);
            });
        });

        $this->crud->addFilter([
          'name'  => 'status',
          'type'  => 'select2',
          'label' => 'Статус',
        ], function () {
          return $this->statuses_filter;
        }, function ($value) { // if the filter is active
          $this->crud->addClause('where', 'status', $value);
        });

        $this->crud->addColumn([
          'name' => 'language_abbr',
          'label' => 'Язык',
        ]);
      
        $this->crud->addColumn([
          'name' => 'title',
          'label' => 'Заголовок',
      ]);
      $this->crud->addColumn([
        'label' => 'Тема',
        'type' => 'select',
        'name' => 'category_id',
        'entity' => 'category',
        'attribute' => 'name',
      ]);
        $this->crud->addColumn([
          'name' => 'status',
          'type' => 'select_from_array',
          'options' => $this->statuses,
          'label' => 'Опубликовано',
      ]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(RegionArticleRequest::class);

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
            'model' => 'regionarticle',
        ]);
    
        if(!$this->crud->entry || !$this->crud->entry->original){
            $this->crud->addField([
                'name' => 'category_id',
                'label' => 'Тема',
                'type' => 'select2_from_array',
                // 'value' => $this->current_category ?? $this->current_category,
                'options' => $this->themes,
                'allows_null' => true,
            ]);
        }
    
        $this->crud->addField([
            'name' => 'title',
            'label' => 'Заголовок',
            'type' => 'text',
            'placeholder' => 'Your title here',
        ]);

        $this->crud->addField([
            'name' => 'slug',
            'label' => 'Slug (URL)',
            'type' => 'text',
            'hint' => 'Will be automatically generated from your title, if left empty.',
            // 'disabled' => 'disabled'
        ]);

        $this->crud->addField([
            'name' => 'short_desc',
            'label' => 'Краткое содержание',
            'type' => 'textarea',
            'attributes' => [
                'rows' => 4
            ]
        ]);

        $this->crud->addField([
            'name' => 'content',
            'label' => 'Содержание',
            'type' => 'ckeditor',
            'placeholder' => 'Your textarea text here',
        ]);
        
        $this->crud->addField([
        'name' => 'meta_title',
        'label' => 'Заголовок (meta)',
        ]);

        $this->crud->addField([
        'name' => 'meta_desc',
        'label' => 'Описание (meta)',
        ]);
        if(!$this->crud->entry || !$this->crud->entry->original){
            $this->crud->addField([
                'name' => 'image',
                'label' => 'Изображение',
                'type' => 'browse',
            ]);
            // $this->crud->addField([
            //     'label' => 'Теги',
            //     'type' => 'select2_multiple',
            //     'name' => 'tags', // the method that defines the relationship in your Model
            //     'entity' => 'tags', // the method that defines the relationship in your Model
            //     'attribute' => 'name', // foreign key attribute that is shown to user
            //     'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
            //     'options' => function($query) {
            //         return $query->withoutGlobalScopes()->where('language_abbr', 'ru')->get();
            //     }
            // ]);
            $this->crud->addField([
                'name' => 'status',
                'label' => 'Статус',
                'type' => 'select2_from_array',
                'options' => [
                    'PUBLISHED' => 'Опубликовано', 
                    'DRAFT' => 'Не опубликовано'
                ]
            ]);
            // $this->crud->addField([
            //     'name' => 'featured',
            //     'label' => 'Популярная новость',
            //     'type' => 'checkbox',
            // ]);
            $this->crud->addField([
                'name' => 'hide_from_index',
                'label' => 'Скрыть от индексации',
                'type' => 'checkbox',
            ]);
            $this->crud->addField([
              'name' => 'nofollow_links',
              'label' => 'Закрыть ссылки',
              'type' => 'checkbox',
          ]);
        }
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
