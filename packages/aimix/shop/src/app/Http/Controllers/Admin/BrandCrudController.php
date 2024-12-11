<?php

namespace Aimix\Shop\app\Http\Controllers\Admin;

use Aimix\Shop\app\Http\Requests\BrandRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Aimix\Shop\app\Models\BrandCategory as Category;

use Backpack\LangFileManager\app\Models\Language;

// use App\Models\Country;

/**
 * Class BrandCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class BrandCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CloneOperation;
    use \Aimix\Shop\app\Http\Controllers\Admin\Operations\TranslateOperation;
    
    private $languages = ['ru'];
    private $default_language = 'ru';
    
    private $categories;
    private $categories_by_lang;
    private $current_language;
    
    // private $countries;

    public function setup()
    {
        $this->crud->setModel('Aimix\Shop\app\Models\Brand');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/brand');
        $this->crud->setEntityNameStrings('компанию', 'компании');
        
        // $this->countries = Country::NoEmpty()->pluck('name', 'id')->toArray();
        $this->crud->enableExportButtons();
        
        if(config('aimix.aimix.enable_languages')) {
          $this->languages = Language::getActiveLanguagesNames();
          $this->default_language = Language::where('default', 1)->first()->abbr;

          $this->current_language = \Request::input('language_abbr')? \Request::input('language_abbr') : null;
          
          $this->crud->query = $this->crud->query->withoutGlobalScopes();

          $this->crud->model->clearGlobalScopes();
        }

        if(config('aimix.shop.enable_brand_categories')) {
          $this->categories = Category::withoutGlobalScopes()->where('language_abbr', 'ru')->NoEmpty()->pluck('name', 'id')->toArray();
          
          $this->crud->model->clearGlobalScopes();
        }
    }

    protected function setupListOperation()
    {
      $this->crud->orderBy('name');
      
        // $this->crud->addFilter([
        //   'name' => 'country_id',
        //   'label' => 'Страна',
        //   'type' => 'select2'
        // ], function(){
        //   return $this->countries;
        // }, function($value){
        //   $this->crud->addClause('where', 'country_id', $value);
        // });

        $this->crud->addFilter([ 
          'type' => 'simple',
          'name' => 'parsed',
          'label'=> 'Спарсенные'
        ],
        false,
        function() {
          $this->crud->addClause('where', 'brands.is_parsed', '1'); 
        });

        $this->crud->addFilter([ 
          'type' => 'simple',
          'name' => 'unactive',
          'label'=> 'Неактивные'
        ],
        false,
        function() {
          $this->crud->addClause('where', 'brands.is_active', '0'); 
        });

        if(config('aimix.shop.enable_brand_categories')) {
          $this->crud->addFilter([
            'name' => 'category_id',
            'label' => 'Категория',
            'type' => 'select2',
          ], function(){
            return $this->categories;
          }, function($value){
            $translation = Category::find($value)->translations->first();

            if($translation)
              $this->crud->addClause('whereIn', 'category_id', [$value, $translation->id]);
            else
              $this->crud->addClause('where', 'category_id', $value);
          });
        }

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
            'visibleInExport' => false,
            'visibleInTable' => true,
          ]);
        }

        $this->crud->addColumn([
          'name' => 'name',
          'label' => 'Название',
        ]);

        if(config('aimix.shop.enable_brand_categories')) {
          $this->crud->addColumn([
            'name' => 'category_id',
            'label' => 'Категория',
            'type' => 'select',
            'entity' => 'category',
            'attribute' => 'name',
            'model' => 'Aimix\Shop\app\Models\BrandCategory',
            'options'   => (function ($query) {
                return $query->withoutGlobalScopes()->get();
            }),
          ]);
        }

        $this->crud->addColumn([
          'name' => 'products',
          'type' => 'relationship_count',
          'label' => 'Объекты',
          'visibleInExport' => false,
          'visibleInTable' => true,
          'suffix' => ' объектов'
        ]);

        // $this->crud->addColumn([
        //   'name' => 'city',
        //   'type' => 'closure',
        //   'label' => 'Город',
        //   'exportOnlyField' => true,
        //   'function' => function($entry) {
        //     return $entry->city !== $entry->region? $entry->city . ', ' . $entry->region . ' область' : $entry->city;
        //   }
        // ]);

        $this->crud->addColumn([
          'name' => 'region',
          'type' => 'textarea',
          'label' => 'Область',
          'exportOnlyField' => true
        ]);

        $this->crud->addColumn([
          'name' => 'area',
          'type' => 'closure',
          'label' => 'Район',
          'exportOnlyField' => true,
          'function' => function($entry) {
            return $entry->address['region'] == 29? $entry->kyivdistrict : $entry->area;
          }
        ]);
  
        $this->crud->addColumn([
          'name' => 'city',
          'type' => 'textarea',
          'label' => 'Город',
          'exportOnlyField' => true,
        ]);

        $this->crud->addColumn([
          'name' => 'address_string',
          'type' => 'closure',
          'label' => 'Адрес',
          'exportOnlyField' => true,
          'function' => function($entry) {
            return isset($entry->extras_translatable['address_string'])? $entry->extras_translatable['address_string'] : '';
          }
        ]);
        
        $this->crud->addColumn([
          'name' => 'phone',
          'type' => 'closure',
          'label' => 'Телефон',
          'exportOnlyField' => true,
          'function' => function($entry) {
            return $entry->contacts['phone'];
          }
        ]);
        
        $this->crud->addColumn([
          'name' => 'email',
          'type' => 'closure',
          'label' => 'Email',
          'exportOnlyField' => true,
          'function' => function($entry) {
            return $entry->contacts['email'];
          }
        ]);
        
        $this->crud->addColumn([
          'name' => 'site',
          'type' => 'closure',
          'label' => 'Сайт',
          'exportOnlyField' => true,
          'function' => function($entry) {
            return $entry->contacts['site'];
          }
        ]);
          // $this->crud->addColumn([
          //     'name' => 'country_id',
          //     'label' => 'Страна',
          //     'type' => 'select',
          //     'entity' => 'country',
          //     'attribute' => 'name',
          //     'model' => 'App\Models\Country',
          // ]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(BrandRequest::class);

        // dd($this->crud->getCurrentEntry()->products()->pluck('is_active'));

        // TODO: remove setFromDb() and manually define Fields
        // $this->crud->setFromDb();
        $this->categories_by_lang = Category::withoutGlobalScopes();

        if(\Route::current()->parameter('id'))
          $this->current_language = $this->crud->getEntry(\Route::current()->parameter('id'))->language_abbr;

        // dd(json_decode($this->crud->entry->contacts['address']));
        if(config('aimix.aimix.enable_languages')) {
          if($this->current_language) {
            $this->categories_by_lang = $this->categories_by_lang->where('language_abbr', $this->current_language);
          } else {
            $this->categories_by_lang = $this->categories_by_lang->where('language_abbr', $this->default_language);
          }
        }

        $this->categories_by_lang = $this->categories_by_lang->pluck('name', 'id')->toArray();

      if(config('aimix.aimix.enable_languages')) {

        $this->crud->addField([
          'name' => 'language_abbr',
          'label' => 'Язык',
          'default' => $this->default_language,
          'attributes' => [
            'readonly' => 'readonly',
          ],
          'tab' => 'Основная информация'
        ]);

        $this->crud->addField([
          'name' => 'original_id',
          'label' => 'Оригинал/перевод',
          'type' => 'translation',
          'model' => 'brand', // site.com/admin/{model}
          'tab' => 'Основная информация'
        ]);
      }

      if(!$this->crud->entry || !$this->crud->entry->original){
        $this->crud->addField([
          'name' => 'is_active',
          'label' => 'Активен',
          'type' => 'boolean',
          'default' => '1',
          'tab' => 'Основная информация',
        ]);

        $this->crud->addField([
          'name' => 'created_at',
          'label' => 'Дата добавления',
          'type' => 'datetime_picker',
          'tab' => 'Основная информация',
          'default' => now()
        ]);
      }
      
      if(config('aimix.shop.enable_brand_categories') && (!$this->crud->entry || !$this->crud->entry->original)) {
        $this->crud->addField([
          'name' => 'category_id',
          'label' => 'Категория',
          'type' => 'select2_from_array',
          'options' => $this->categories_by_lang,
          'tab' => 'Основная информация'
        ]);

        $this->crud->addField([
          'name' => 'is_popular',
          'label' => 'Популярная',
          'type' => 'boolean',
          'tab' => 'Основная информация'
        ]);
      }
        
        $this->crud->addFields([
          [
            'name' => 'name',
            'label' => 'Название',
            'tab' => 'Основная информация'
          ],
          [
            'name' => 'slug',
            'label' => 'URL',
            'prefix' => url('/companies').'/',
            'hint' => 'По умолчанию будет сгенерирован из названия.',
            'type' => 'text',
            'tab' => 'Основная информация'
          ],
          [
            'name' => 'description',
            'label' => 'Описание',
            'type' => 'ckeditor',
            'tab' => 'Основная информация'
          ],
          [
            'name' => 'parsed_desc',
            'label' => 'Описание (Лун)',
            'type' => 'ckeditor',
            'tab' => 'Основная информация',
          ],
          [
            'name' => 'activity',
            'label' => 'Деятельность компании',
            'type' => 'table',
            'entity_singular' => 'деятельность', // used on the "Add X" button
            'columns' => [
                'text' => 'Деятельность',
            ],
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Основная информация'
          ],
          [
            'name' => 'videos',
            'label' => 'Видео',
            'upload' => true,
            'fake' => true,
            'store_in' => 'extras',
            'type' => 'browse_multiple',
            'tab' => 'Основная информация'
          ],
          [
            'name' => 'seo_title',
            'label' => 'SEO-заголовок',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Основная информация'
          ],
          [
            'name' => 'seo_desc',
            'label' => 'SEO-текст',
            'type' => 'ckeditor',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Основная информация'
          ],
        ]);

        if(!$this->crud->entry || !$this->crud->entry->original){
          $this->crud->addFields([
            [
              'name' => 'color',
              'label' => 'Фон',
              'type' => 'color_picker',
              'fake' => true,
              'store_in' => 'images',
              'tab' => 'Изображения',
              'color_picker_options' => ['format' => 'rgba'],
              'hint' => 'Фон для изображения на странице компании'
            ],
            [
              'name' => 'image',
              'label' => 'Изображение',
              'type' => 'browse',
              'upload' => true,
              'fake' => true,
              'store_in' => 'images',
              'tab' => 'Изображения'
            ],
            [
              'name' => 'logo',
              'label' => 'Логотип',
              'type' => 'browse',
              'upload' => true,
              'fake' => true,
              'store_in' => 'images',
              'tab' => 'Изображения'
            ],
            [
              'name' => 'business_card',
              'label' => 'Визитка',
              'type' => 'browse',
              'upload' => true,
              'fake' => true,
              'store_in' => 'images',
              'tab' => 'Изображения'
            ],
            [
              'name' => 'address',
              'type' => 'address_zagorodna',
              'tab' => 'Контакты'
            ]
          ]);
        }

            $this->crud->addField(
              [
                'name' => 'address_string',
                'label' => 'Адрес',
                'fake' => true,
                'store_in' => 'extras_translatable',
                'tab' => 'Контакты'
            ]);

            
        if(!$this->crud->entry || !$this->crud->entry->original){
            $this->crud->addFields([
            [
              'name' => 'phone',
              'label' => 'Телефон',
              'fake' => true,
              'store_in' => 'contacts',
              'tab' => 'Контакты'
            ],
            [
              'name' => 'email',
              'label' => 'Email',
              'fake' => true,
              'store_in' => 'contacts',
              'tab' => 'Контакты'
            ],
            [
              'name' => 'site',
              'label' => 'Сайт',
              'fake' => true,
              'store_in' => 'contacts',
              'tab' => 'Контакты'
            ],
            [
              'name' => 'fb',
              'label' => 'Ссылка на Facebook',
              'fake' => true,
              'store_in' => 'contacts',
              'tab' => 'Контакты'
            ],
            [
              'name' => 'inst',
              'label' => 'Ссылка на Instagram',
              'fake' => true,
              'store_in' => 'contacts',
              'tab' => 'Контакты'
            ],
            [
              'name' => 'map',
              'label' => 'Html-код карты',
              'type' => 'textarea',
              'attributes' => [
                'rows' => 6
              ],
              'fake' => true,
              'store_in' => 'contacts',
              'tab' => 'Контакты'
            ],
          ]);
        }
        
        $this->crud->addFields([
          [
            'name' => 'statistics',
            'label' => 'Статистика',
            'type' => 'table',
            'entity_singular' => 'option', // used on the "Add X" button
            'columns' => [
                'number' => 'Число',
                'text' => 'Текст',
            ],
            'max' => 3, // maximum rows allowed in the table
            'min' => 3, // minimum rows allowed in the table
            'fake' => true,
            'store_in' => 'extras_translatable',
            'tab' => 'Основная информация'
          ],
          [
            'name' => 'achievements',
            'label' => 'Достижения',
            'type' => 'achievements',
            // 'fake' => true,
            // 'store_in' => 'extras',
            'tab' => 'Достижения'
          ],
          [
            'name' => 'meta_title',
            'label' => 'Title',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Meta'
          ],
          [
            'name' => 'meta_desc',
            'label' => 'Description',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Meta'
          ],
          // [
          //   'name' => 'country_id',
          //   'label' => 'Страна',
          //   'type' => 'select2',
          //   'entity' => 'country',
          //   'attribute' => 'name',
          //   'model' => 'App\Models\Country',
          // ],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
