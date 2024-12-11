<?php

namespace Aimix\Promotion\app\Http\Controllers\Admin;

use Aimix\Promotion\app\Http\Requests\PromotionRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

use Backpack\LangFileManager\app\Models\Language;

/**
 * Class PromotionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class PromotionCrudController extends CrudController
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
        $this->crud->setModel('Aimix\Promotion\app\Models\Promotion');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/promotion');
        $this->crud->setEntityNameStrings('акцию', 'акции');
        
        if(config('aimix.aimix.enable_languages')) {
          $this->languages = Language::getActiveLanguagesNames();
          $this->default_language = Language::where('default', 1)->first()->abbr;
          
          $this->crud->query = $this->crud->query->withoutGlobalScopes();
          $this->crud->model->clearGlobalScopes();
        }
    }

    protected function setupListOperation()
    {
        // TODO: remove setFromDb() and manually define Columns, maybe Filters
        // $this->crud->setFromDb();

        $this->crud->addFilter([ 
          'type' => 'simple',
          'name' => 'parsed',
          'label'=> 'Спарсенные'
        ],
        false,
        function() {
          $this->crud->addClause('where', 'is_parsed', '1'); 
        });

          
        $this->crud->addFilter([ 
          'type' => 'simple',
          'name' => 'unactive',
          'label'=> 'Неактивные'
        ],
        false,
        function() {
          $this->crud->addClause('where', 'is_active', '0'); 
        });
        
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
        
        $this->crud->addColumns([
          [
            'name' => 'title',
            'label' => 'Заголовок',
          ],
          [
            'name' => 'image',
            'label' => 'Изображение',
            'type' => 'image',
          ],
          [
            'name' => 'desc',
            'label' => 'Описание',
          ]
        ]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(PromotionRequest::class);

        if(\Route::current()->parameter('id'))
          $this->crud->getEntry(\Route::current()->parameter('id'));
          
        // TODO: remove setFromDb() and manually define Fields
        // $this->crud->setFromDb();

        
        if(config('aimix.promotion.enable_product_promotions') && (!$this->crud->entry || !$this->crud->entry->original)) {
          $this->crud->addField([
            'name' => 'is_active',
            'label' => 'Активна',
            'type' => 'boolean',
          ]);
        }

        if(config('aimix.aimix.enable_languages')) {

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
            'model' => 'promotion', // site.com/admin/{model}
          ]);
        }
        $this->crud->addField([
          'name' => 'title',
          'label' => 'Заголовок',
        ]);

        $this->crud->addField([
          'name' => 'slug',
          'label' => 'URL',
          'prefix' => url('/promotions').'/',
          'hint' => 'По умолчанию будет сгенерирован из заголовка.'
        ]);

        if(config('aimix.promotion.enable_product_promotions') && (!$this->crud->entry || !$this->crud->entry->original)) {
          $this->crud->addField([
            'name' => 'product_id',
            'label' => 'Объект',
            'type' => 'select2',
            'entity' => 'product',
            'attribute' => 'name', 
            'model' => "Aimix\Shop\app\Models\Product",
            'options' => function($query) {
              return $query->where('language_abbr', $this->default_language)->get();
            }
          ]);
        }

        if(!$this->crud->entry || !$this->crud->entry->original){
          $this->crud->addField([
            'name' => 'image',
            'label' => 'Изображение',
            'type' => 'browse',
          ]);

          $this->crud->addField([
            'name' => ['start', 'end'],
            'label' => 'Дата проведения',
            'type' => 'date_range',
            'default' => [now()->format('Y-m-d'), now()->addMonth()->format('Y-m-d')], // default values for start_date & end_date
            'date_range_options' => [
                // options sent to daterangepicker.js
                'timePicker' => false,
                'locale' => ['format' => 'DD-MM-YYYY']
            ]
          ]);
        }

        $this->crud->addField([
          'name' => 'desc',
          'label' => 'Описание',
          'type' => 'ckeditor',
          'attributes' => [
            'rows' => 7,
          ]
        ]);
        
        $this->crud->addField([
          'name' => 'link',
          'label' => 'Ссылка',
        ]);        
        
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
