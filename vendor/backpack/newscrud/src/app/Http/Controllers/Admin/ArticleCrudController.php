<?php

namespace Backpack\NewsCRUD\app\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\NewsCRUD\app\Http\Requests\ArticleRequest;

use Backpack\LangFileManager\app\Models\Language;
use Backpack\NewsCRUD\app\Models\Category;
use Backpack\NewsCRUD\app\Models\Article;
use Backpack\NewsCRUD\app\Models\Tag;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\ArticleRegion;

class ArticleCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CloneOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\BulkCloneOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\BulkDeleteOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Aimix\Shop\app\Http\Controllers\Admin\Operations\TranslateOperation;
    
    private $languages = ['ru'];
    private $current_language;
    private $categories_by_lang;
    private $current_category;
    private $regions;
    private $default_language = 'ru';
    private $themes;
    private $years;
    private $statuses = [
      'PUBLISHED' => '<span><i class="la la-check-circle"></i></span>',
      'DRAFTED' => '<span><i class="la la-circle"></i></span>'
    ];
    private $statuses_filter = [
      'PUBLISHED' => 'Опубликовано',
      'DRAFTED' => 'Не опубликовано'
    ];
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel("Backpack\NewsCRUD\app\Models\Article");
        $this->crud->setRoute(config('backpack.base.route_prefix', 'admin').'/article');
        $this->crud->setEntityNameStrings('статью', 'статьи');

        $this->current_category = \Request::input('category_id')? \Request::input('category_id') : null;
        $this->default_language = Language::where('default', 1)->first()->abbr;
        
        $this->languages = Language::getActiveLanguagesNames();
        
          (new ArticleRegion)->clearGlobalScopes();
          $this->crud->model->clearGlobalScopes();
          $this->themes = Category::has('parent')->where('language_abbr', 'ru')->whereIn('parent_id', [5,26])->orderBy('name')->pluck('name', 'id')->toArray();
          $this->regions = ArticleRegion::where('language_abbr', 'ru')->pluck('name', 'region_id')->toArray();
          $this->crud->query->whereHas('category', function($q) {
            $q->whereIn('parent_id', [5,26]);
          });

        if (!Cache::has('article_years')) {
          $expiresAt = Carbon::now()->addWeek();

          foreach(Article::pluck('id', 'date') as $date => $id) {
            $year = explode('-', $date)[0];
            $years[$year] = $year;
          }

          Cache::put('article_years', $years, $expiresAt);
        } else {
            $years = Cache::get('article_years');
        }

        $this->years = $years;
        krsort($this->years);
        $this->current_language = \Request::input('language_abbr')? \Request::input('language_abbr') : null;

        Category::first();
        Tag::first();
        
        $this->crud->query = $this->crud->query->withoutGlobalScopes();
        
        $this->crud->model->clearGlobalScopes();
        /*
        |--------------------------------------------------------------------------
        | LIST OPERATION
        |--------------------------------------------------------------------------
        */
        $this->crud->operation('list', function () {
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
              'name'  => 'region',
              'type'  => 'select2',
              'label' => 'Область'
            ], function () {
              return $this->regions;
            }, function ($value) { // if the filter is active
              $this->crud->addClause('where', 'region', $value);
            });

            $this->crud->addFilter([
              'name'  => 'category_id',
              'type'  => 'select2',
              'label' => 'Тема'
            ], function () {
              return $this->themes;
            }, function ($value) { // if the filter is active
              if($value == 0) {
                $this->crud->addClause('where', 'category_id', null);
              } else {
                $this->crud->addClause('where', function($q) use ($value) {
                  $q->where('category_id', $value)->orWhere('category_id', Category::find($value)->translations->first()->id);
                });
              }
            });

            $this->crud->addFilter([
              'name'  => 'year',
              'type'  => 'select2',
              'label' => 'Год публикации'
            ], function () {
              return $this->years;
            }, function ($value) { // if the filter is active
              $this->crud->addClause('where', 'date', '<', $value . '-12-31 23:59:59');
              $this->crud->addClause('where', 'date', '>', $value . '-01-01 00:00:00');
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
                'name' => 'date',
                'label' => 'Дата публикации',
                'type' => 'datetime',
            ]);
            $this->crud->addColumn([
              'name' => 'status',
              'type' => 'select_from_array',
              'options' => $this->statuses,
              'label' => 'Опубликовано',
          ]);
            // $this->crud->addColumn([
            //     'name' => 'featured',
            //     'label' => 'Популярное',
            //     'type' => 'boolean',
            // ]);
        });

        /*
        |--------------------------------------------------------------------------
        | CREATE & UPDATE OPERATIONS
        |--------------------------------------------------------------------------
        */
        $this->crud->operation(['create', 'update'], function () {
            $this->crud->setValidation(ArticleRequest::class);
            
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
          'model' => 'article',
        ]);

        if(!$this->crud->entry || !$this->crud->entry->original){
          $this->crud->addField([
            'name' => 'category_id',
            'label' => 'Категория (тема)',
            'type' => 'select2_from_array',
            'options' => $this->themes,
            'allows_null' => true,
          ]);

          $this->crud->addField([
            'name' => 'region',
            'label' => 'Область',
            'type' => 'select2_from_array',
            'options' => $this->regions,
            'allows_null' => false,
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

            if(!$this->crud->entry || !$this->crud->entry->original){
              $this->crud->addField([
                  'name' => 'date',
                  'label' => 'Дата публикации',
                  'type' => 'datetime_picker',
                  'default' => date('m/d/y H:i:s'),
                  'datetime_picker_options' => [
                      'format' => 'DD/MM/YYYY HH:mm:ss',
                      'language' => 'ru'
                  ],
              ]);
            }

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
              $this->crud->addField([
                  'label' => 'Теги',
                  'type' => 'select2_multiple',
                  'name' => 'tags', // the method that defines the relationship in your Model
                  'entity' => 'tags', // the method that defines the relationship in your Model
                  'attribute' => 'name', // foreign key attribute that is shown to user
                  'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
                  'options' => function($query) {
                    if($this->current_language)
                      return $query->withoutGlobalScopes()->where('language_abbr', $this->current_language)->get();
                    else
                      return $query->withoutGlobalScopes()->where('language_abbr', array_key_first($this->languages))->get();
                  }
              ]);
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
        });
    }
}
