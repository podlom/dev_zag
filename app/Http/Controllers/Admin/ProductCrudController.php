<?php

namespace App\Http\Controllers\Admin;

use Aimix\Shop\app\Http\Requests\ProductRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Aimix\Shop\app\Models\Category;
use Aimix\Shop\app\Models\Brand;
use Aimix\Shop\app\Models\Product;
use Aimix\Shop\app\Models\Modification;
use Aimix\Shop\app\Models\Attribute;
use Aimix\Shop\app\Models\AttributeModification;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Communication;
use App\Models\Infrastructure;

use Backpack\LangFileManager\app\Models\Language;

/**
 * Class ProductCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class ProductCrudController extends CrudController
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
    private $brands;
    private $categories_by_lang;
    private $brands_by_lang;
    private $current_category;
    private $current_language;
    private $communications;
    private $statuses;
    private $regions;
    private $areas;
    private $kyivdistricts;
    private $types;

    public function setup()
    {
        //   $start = microtime(true);
        //   dd(microtime(true) - $start);
        $this->crud->setModel('Aimix\Shop\app\Models\Product');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/product');
        $this->crud->setEntityNameStrings('объект', 'объекты');

        $this->crud->enableExportButtons();
        
        if(config('aimix.shop.enable_brands')) {
          $this->brands = Brand::where('category_id', 139)->pluck('name', 'id')->toArray();
        }
        $this->current_category = \Request::input('category_id')? \Request::input('category_id') : null;

        if(config('aimix.aimix.enable_languages')) {
          $this->languages = Language::getActiveLanguagesNames();
          $this->default_language = Language::where('default', 1)->first()->abbr;

          $this->current_language = \Request::input('language_abbr')? \Request::input('language_abbr') : null;
          
          $this->crud->query = $this->crud->query->withoutGlobalScopes();
          
          $this->crud->model->clearGlobalScopes();
          
        }
        
        $this->categories = Category::withoutGlobalScopes()->NoEmpty()->pluck('name', 'id')->toArray();
        $this->communications = __('attributes.communications');
        $this->statuses = __('main.product_statuses');
        $this->regions = \App\Region::where('language_abbr', 'ru')->pluck('name', 'region_id')->toArray();
        $this->areas = \App\Area::where('language_abbr', 'ru')->where('region_id', 11)->pluck('name', 'area_id')->toArray();
        $this->kyivdistricts = \App\Kyivdistrict::where('language_abbr', 'ru')->pluck('name', 'kyivdistrict_id')->toArray();

        if($this->current_category == 2) {
          $this->types = __('attributes.newbuild_types');
        } else {
          $this->types = Attribute::find(1)->values;
        }
        
        $this->crud->model->clearGlobalScopes();

    }

    public function clone($id)
    {
      $this->crud->hasAccessOrFail('clone');
      $this->crud->setOperation('clone');
      
      $product = Product::find($id);
      $mods = $product->modifications;
      
      $newProduct = new Product;
      $newProduct->language_abbr = $product->language_abbr;
      $newProduct->category_id = $product->category_id;
      $newProduct->brand_id = $product->brand_id;
      $newProduct->name = $product->name;
      $newProduct->description = $product->description;
      $newProduct->image = $product->image;
      $newProduct->short_description = $product->short_description;
      $newProduct->is_hit = $product->is_hit;
      $newProduct->is_new = $product->is_new;
      $newProduct->is_recommended = $product->is_recommended;
      $newProduct->is_active = $product->is_active;
      $newProduct->address = $product->address;
      $newProduct->extras = $product->extras;
      $newProduct->extras_translatable = $product->extras_translatable;
      $newProduct->meta_title = $product->meta_title;
      $newProduct->meta_description = $product->meta_description;
      $newProduct->save();
      
      foreach($mods as $mod) {
        $newMod = new Modification;
        $newMod->product_id = $newProduct->id;
        $newMod->code = $mod->code;
        $newMod->name = $mod->name;
        $newMod->price = $mod->price;
        $newMod->old_price = $mod->old_price;
        $newMod->images = $mod->images;
        $newMod->is_default = $mod->is_default;
        $newMod->is_active = $mod->is_active;
        $newMod->in_stock = $mod->in_stock;
        $newMod->extras = $mod->extras;
        $newMod->save();

        foreach($mod->attrs as $attr) {
          $newAttr = new AttributeModification;
          $newAttr->attribute_id = $attr->id;
          $newAttr->modification_id = $newMod->id;
          $newAttr->value = $attr->pivot->value;
          $newAttr->save();
        }
      }
    }

    protected function setupListOperation()
    {
      $this->crud->orderBy('name');
      $categories = [
        1 => [1,6],
        2 => [2,7]
      ];
      if(request()->has('category_id')) {
        $this->crud->query->whereIn('category_id', $categories[request('category_id')]);

        $this->crud->addFilter([
          'name' => 'type',
          'label' => 'Тип',
          'type' => 'select2'
        ], function(){
          return $this->types;
        }, function($value){
          if($this->current_category == 2)
            $this->crud->query->where('extras->newbuild_type', $value);
          else {
            $this->crud->query->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', $this->types[$value])->select('products.*');
          }
        });
      }

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
      
        $this->crud->addFilter([
          'name' => 'region',
          'label' => 'Область',
          'type' => 'select2'
        ], function(){
          return $this->regions;
        }, function($value){
          $this->crud->query->where('address->region', $value);
        });

          $this->crud->addFilter([
            'name' => 'area',
            'label' => 'Район',
            'type' => 'select2'
          ], function(){
            return $this->areas;
          }, function($value){
            $this->crud->query->where('address->area', $value);
          });

        
        $this->crud->addFilter([
          'name' => 'frozen',
          'label' => 'Замороженные',
          'type' => 'simple'
        ], false, function($value){
          $this->crud->query->where('products.extras->is_frozen', 1);
        });

      $this->crud->addFilter([
        'name' => 'status',
        'label' => 'Статус',
        'type' => 'select2',
      ], function(){
        return $this->statuses;
      }, function($value){
        $this->crud->query->where('products.extras->status', $value);
      });
        
      if(config('aimix.shop.enable_brands')) {
        $this->crud->addFilter([
          'name' => 'brand_id',
          'label' => 'Застройщик',
          'type' => 'select2'
        ], function(){
          return $this->brands;
        }, function($value){
          $brand_id = Brand::where('id', $value)->orWhere('original_id', $value)->pluck('id');
          $this->crud->query->whereIn('brand_id', $brand_id);
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
            $this->crud->addClause('where', 'products.language_abbr', $value);
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
        
      if(config('aimix.shop.enable_brands')) {
        $this->crud->addColumn([
          'name' => 'brand_id',
          'label' => 'Застройщик',
          'type' => 'select',
          'entity' => 'brand',
          'attribute' => 'name',
          'model' => 'Aimix\Shop\app\Models\Brand',
        ]);
      }

      if(config('aimix.shop.enable_product_rating')) {
        $this->crud->addColumn([
          'name' => 'rating',
          'label' => 'Рейтинг',
          'visibleInExport' => false,
          'visibleInTable' => false,
          'searchLogic' => function ($query, $column, $searchTerm) {
            $query->orWhere('old_id', $searchTerm);
          }
        ]);
      }

      $this->crud->addColumn([
        'name' => 'region',
        'type' => 'textarea',
        'label' => 'Область'
      ]);

      $this->crud->addColumn([
        'name' => 'area',
        'type' => 'closure',
        'label' => 'Район',
        'function' => function($entry) {
          return $entry->address['region'] == 29? $entry->kyivdistrict : $entry->area;
        }
      ]);

      $this->crud->addColumn([
        'name' => 'city',
        'type' => 'textarea',
        'label' => 'Город',
      ]);

      $this->crud->addColumn([
        'name' => 'phone',
        'type' => 'textarea',
        'label' => 'Телефон',
        'exportOnlyField' => true
      ]);

      $this->crud->addColumn([
        'name' => 'site',
        'type' => 'textarea',
        'label' => 'Сайт',
        'exportOnlyField' => true
      ]);
      
      $this->crud->addColumn([
        'name' => 'email',
        'type' => 'textarea',
        'label' => 'Email',
        'exportOnlyField' => true
      ]);

      $this->crud->addColumn([
        'name' => 'total_items',
        'label' => 'Количество',
        'exportOnlyField' => true
      ]);

      $this->crud->addColumn([
        'name' => 'type',
        'type' => 'textarea',
        'label' => 'Тип',
        'exportOnlyField' => true
      ]);

      $this->crud->addColumn([
        'name' => 'status_string',
        'type' => 'textarea',
        'label' => 'Состояние',
        'exportOnlyField' => true
      ]);
      
      $this->crud->addColumn([
        'name' => 'price',
        'type' => 'closure',
        'label' => 'Цена от',
        'exportOnlyField' => true,
        'function' => function($entry) {
          if($entry->type !== 'Участок' && $entry->price)
            return $entry->price . ' грн/кв.м';
        }
      ]);

      $this->crud->addColumn([
        'name' => 'max_price',
        'type' => 'closure',
        'label' => 'Цена до',
        'exportOnlyField' => true,
        'function' => function($entry) {
          if($entry->type !== 'Участок' && $entry->max_price)
            return $entry->max_price . ' грн/кв.м';
        }
      ]);

      $this->crud->addColumn([
        'name' => 'area_min',
        'type' => 'closure',
        'label' => 'Площадь от',
        'exportOnlyField' => true,
        'function' => function($entry) {
          if($entry->area_min)
            return $entry->area_min . ' кв.м';
        }
      ]);

      $this->crud->addColumn([
        'name' => 'area_max',
        'type' => 'closure',
        'label' => 'Площадь до',
        'exportOnlyField' => true,
        'function' => function($entry) {
          if($entry->area_max)
            return $entry->area_max . ' кв.м';
        }
      ]);

      if(request()->category_id == 1) {
        $this->crud->addColumn([
          'name' => 'statistics_price_plot',
          'type' => 'closure',
          'label' => 'Цена от (участок)',
          'exportOnlyField' => true,
          'function' => function($entry) {
            if($entry->statistics_price_plot)
              return $entry->statistics_price_plot . ' грн/сот';
          }
        ]);
        
        $this->crud->addColumn([
          'name' => 'statistics_price_plot_max',
          'type' => 'closure',
          'label' => 'Цена до (участок)',
          'exportOnlyField' => true,
          'function' => function($entry) {
            if($entry->statistics_price_plot_max)
              return $entry->statistics_price_plot_max . ' грн/сот';
          }
        ]);
        
        $this->crud->addColumn([
          'name' => 'area_min_plot',
          'type' => 'closure',
          'label' => 'Площадь от (участок)',
          'exportOnlyField' => true,
          'function' => function($entry) {
            if($entry->area_min_plot)
              return $entry->area_min_plot . ' сот';
          }
        ]);
  
        $this->crud->addColumn([
            'name' => 'area_max_plot',
            'type' => 'closure',
            'label' => 'Площадь до (участок)',
            'exportOnlyField' => true,
            'function' => function($entry) {
              if($entry->area_max_plot)
                return $entry->area_max_plot . ' сот';
            }
          ]);
        }
      }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(ProductRequest::class);

        $this->categories_by_lang = Category::withoutGlobalScopes();
        $this->brands_by_lang = Brand::withoutGlobalScopes()->where('category_id', 139);

        if(\Route::current()->parameter('id')) {
          $this->current_language = $this->crud->getEntry(\Route::current()->parameter('id'))->language_abbr;
          $this->current_category = $this->crud->getEntry(\Route::current()->parameter('id'))->category_id;
        }

        if(config('aimix.aimix.enable_languages')) {
            $this->categories_by_lang = $this->categories_by_lang->where('language_abbr', $this->default_language);
            $this->brands_by_lang = $this->brands_by_lang->where('language_abbr', $this->default_language);
        }

        $this->categories_by_lang = $this->categories_by_lang->pluck('name', 'id')->toArray();
        $this->brands_by_lang = $this->brands_by_lang->pluck('name', 'id')->toArray();

        $this->crud->attributes = Category::withoutGlobalScopes()->find(array_key_first($this->categories_by_lang))->attributes->keyBy('id');
        
        if($this->current_category)
          $this->crud->attributes = Category::withoutGlobalScopes()->find($this->current_category)->attributes->keyBy('id');
          
      if(config('aimix.aimix.enable_languages')) {
        $this->crud->addField([
          'name' => 'language_abbr',
          'label' => 'Язык',
          'default' => $this->default_language,
          'attributes' => [
            'readonly' => 'readonly',
          ],
          'tab' => 'Характеристики'
        ]);

        $this->crud->addField([
          'name' => 'original_id',
          'label' => 'Оригинал/перевод',
          'type' => 'translation',
          'model' => 'product', // site.com/admin/{model}
          'tab' => 'Характеристики'
        ]);
      }

      if(!$this->crud->entry || !$this->crud->entry->original){
	    
	    $this->crud->addField([
          'name' => 'no_parse',
          'label' => 'Не перезаписывать',
          'hint' => 'Если выбрано, то объект не будет перезаписываться повторно при парсинге',
          'type' => 'boolean',
          'tab' => 'Характеристики',
        ]);
        
        $this->crud->addField([
          'name' => 'is_active',
          'label' => 'Активен',
          'type' => 'boolean',
          'default' => '1',
          'tab' => 'Характеристики',
        ]);

        if(config('aimix.shop.enable_is_hit')) {
          $this->crud->addField([
            'name' => 'is_hit',
            'label' => 'Популярный',
            'type' => 'boolean',
            'tab' => 'Характеристики',
            'hint' => 'Популярные объекты отображаются на главной в блоке "Горящие предложения"'
          ]);
        }

        $this->crud->addField([
          'name' => 'created_at',
          'label' => 'Дата добавления',
          'type' => 'datetime_picker',
          'tab' => 'Характеристики',
          'default' => now()
        ]);

        $this->crud->addField([
          'name' => 'category_id',
          'label' => 'Категория',
          'type' => 'select2_from_array',
          'value' => $this->current_category ?? $this->current_category,
          'options' => $this->categories_by_lang,
          'attributes' => [
            'onchange' => 'window.location.search += "&category_id=" + this.value'
          ],
          'tab' => 'Характеристики',
        ]);

        $this->crud->addField([
          'name' => 'brand_id',
          'label' => 'Застройщик',
          'type' => 'select2_from_array',
          'options' => $this->brands_by_lang,
          'tab' => 'Характеристики',
          'allows_null' => true
        ]);
      }

        
        $this->crud->addField([
          'name' => 'name',
          'label' => 'Название',
          'tab' => 'Характеристики',
        ]);
        
        $this->crud->addField([
          'name' => 'slug',
          'label' => 'Slug',
          'hint' => 'По умолчанию будет сгенерирован из названия.',
          'tab' => 'Характеристики',
        ]);
        
        if(!$this->crud->entry || !$this->crud->entry->original){
          $this->crud->addField([
            'name' => 'image',
            'label' => 'Изображение',
            'type' => 'image',
            'upload' => true,
            'aspect_ratio' => 0, // set to 0 to allow any aspect ratio
            'crop' => false, // set to true to allow cropping, false to disable
            'hint' => 'Изображение, которое будет отображаться в каталоге',
            'tab' => 'Характеристики',
          ]);
        
        
        if(config('aimix.shop.enable_multiple_product_images')) {
          $this->crud->addField([
            'name' => 'images',
            'label' => 'Изображения',
            'type' => 'product_images',
            'tab' => 'Изображения',
          ]);
        }
      }

      $this->crud->addField([
        'name' => 'short_description',
        'label' => 'Краткое описание',
        'type' => 'ckeditor',
        'tab' => 'Характеристики',
      ]);

      $this->crud->addField([
        'name' => 'description',
        'label' => 'Описание',
        'type' => 'ckeditor',
        'tab' => 'Характеристики',
      ]);

      $this->crud->addField([
        'name' => 'parsed_desc',
        'label' => 'Описание (Лун)',
        'type' => 'ckeditor',
        'tab' => 'Характеристики',
      ]);
        
      if(!$this->crud->entry || !$this->crud->entry->original){

        $this->crud->addField([
          'name' => 'address',
          'type' => 'address_zagorodna',
          'tab' => 'Характеристики'
        ]);

        if(config('aimix.shop.enable_is_new')) {
          $this->crud->addField([
            'name' => 'is_new',
            'label' => 'Новинка',
            'type' => 'boolean',
            'tab' => 'Характеристики',
          ]);
        }

        if(config('aimix.shop.enable_is_recommended')) {
          $this->crud->addField([
            'name' => 'is_recommended',
            'label' => 'Рекомендуемый',
            'type' => 'boolean',
            'tab' => 'Характеристики',
          ]);
        }
      }

      $this->crud->addField([
        'name' => 'address_string',
        'label' => 'Адрес',
        'fake' => true,
        'store_in' => 'extras_translatable',
        'tab' => 'Характеристики'
      ]);
        
      if(config('aimix.shop.enable_product_promotions')) {
        $this->crud->addField([
          'name' => 'sales',
          'label' => 'Акции',
          'fake' => true,
          'type' => 'table',
          'store_in' => 'extras',
          'entity_singular' => 'акцию',
          'columns' => [
            'discount' => 'Скидка, руб.',
            'desc' => 'Описание',
          ],
          'tab' => 'Характеристики',
        ]);
      }
      // dd($this->crud->entry->status);
      if(!$this->crud->entry || !$this->crud->entry->original){
        if($this->current_category == 2) {
          $this->crud->addField([
            'name' => 'newbuild_type',
            'label' => 'Тип',
            'type' => 'select_from_array',
            'options' => __('attributes.newbuild_types'),
            'tab' => 'Характеристики',
            'fake' => true,
            'store_in' => 'extras',
          ]);
        }
        $this->crud->addField([
          'name' => 'status',
          'label' => 'Статус',
          'type' => 'select_status',
          'options' => $this->statuses,
          'tab' => 'Характеристики',
          'fake' => true,
          'store_in' => 'extras',
        ]);
  
        $this->crud->addField([
          'name' => 'is_frozen',
          'label' => 'Заморожено',
          'type' => 'boolean',
          'tab' => 'Характеристики',
          'fake' => true,
          'store_in' => 'extras',
        ]);

        $this->crud->addField([
          'name' => 'is_sold',
          'label' => 'Продано',
          'type' => 'boolean',
          'tab' => 'Характеристики',
        ]);

        $this->crud->addField([
          'name' => 'distance',
          'label' => 'Расстояние, км',
          'type' => 'number',
          'tab' => 'Характеристики',
          'fake' => true,
          'store_in' => 'extras',
        ]);

        if($this->current_category != 2) {
          $this->crud->addField([
            'name' => 'area',
            'label' => 'Площадь застройки',
            'type' => 'number',
            'attributes' => [
              'step' => 0.01
            ],
            'tab' => 'Характеристики',
            'fake' => true,
            'store_in' => 'extras',
            'value' => $this->crud->entry? $this->crud->entry->area_m2 : ''
          ]);

          $this->crud->addField([
            'name' => 'wall_material',
            'label' => 'Материал стен',
            'type' => 'select_from_array',
            'options' => __('attributes.wall_materials'),
            'allows_null' => true,
            'tab' => 'Характеристики',
            'fake' => true,
            'store_in' => 'extras',
          ]);

          $this->crud->addField([
            'name' => 'roof_material',
            'label' => 'Материал крыши',
            'type' => 'select_from_array',
            'options' => __('attributes.roof_materials'),
            'allows_null' => true,
            'tab' => 'Характеристики',
            'fake' => true,
            'store_in' => 'extras',
          ]);

          $this->crud->addField([
            'name' => 'area_cottage',
            'label' => 'Размер участка под коттедж',
            'tab' => 'Характеристики',
            'fake' => true,
            'store_in' => 'extras',
          ]);

          $this->crud->addField([
            'name' => 'area_townhouse',
            'label' => 'Придомовой участок таунхауса',
            'tab' => 'Характеристики',
            'fake' => true,
            'store_in' => 'extras',
          ]);

          $this->crud->addField([
            'name' => 'area_duplex',
            'label' => 'Придомовой участок дуплекса',
            'tab' => 'Характеристики',
            'fake' => true,
            'store_in' => 'extras',
          ]);

          $this->crud->addField([
            'name' => 'area_quadrex',
            'label' => 'Придомовой участок квадрекса',
            'tab' => 'Характеристики',
            'fake' => true,
            'store_in' => 'extras',
          ]);
        } else {
          // $this->crud->addField([
          //   'name' => 'technology',
          //   'label' => 'Технология строительства',
          //   'type' => 'select_from_array',
          //   'options' => __('attributes.technologies'),
          //   'tab' => 'Характеристики',
          //   'fake' => true,
          //   'store_in' => 'extras',
          // ]);

          $this->crud->addField([
            'name' => 'wall_material',
            'label' => 'Материал стен',
            'type' => 'select_from_array',
            'options' => __('attributes.wall_materials'),
            'tab' => 'Характеристики',
            'fake' => true,
            'store_in' => 'extras',
          ]);

          $this->crud->addField([
            'name' => 'floors',
            'label' => 'Количество этажей',
            'type' => 'number',
            'attributes' => [
              'min' => 1
            ],
            'tab' => 'Характеристики',
            'fake' => true,
            'store_in' => 'extras',
          ]);

          $this->crud->addField([
            'name' => 'flats_count',
            'label' => 'Количество квартир',
            'tab' => 'Характеристики',
            'fake' => true,
            'store_in' => 'extras',
          ]);
        }

        // $this->crud->addField([
        //   'name' => 'map',
        //   'label' => 'Html-код карты',
        //   'type' => 'textarea',
        //   'attributes' => [
        //     'rows' => 6
        //   ],
        //   'tab' => 'Характеристики',
        //   'fake' => true,
        //   'store_in' => 'extras',
        // ]);
        $this->crud->addField([
          'name' => 'youtube_video',
          'label' => 'Html-код видео с Youtube',
          'tab' => 'Характеристики',
          'fake' => true,
          'store_in' => 'extras',
        ]);

        $this->crud->addField([
          'name' => 'videos',
          'label' => 'Видео',
          'type' => 'browse_multiple',
          'upload' => true,
          'tab' => 'Характеристики',
          'fake' => true,
          'store_in' => 'extras',
        ]);
      }


      if($this->current_category != 2 && $this->current_category != 7) {
        $this->crud->addField([
          'name' => 'insulation',
          'label' => 'Утепление',
          'tab' => 'Характеристики',
          'fake' => true,
          'store_in' => 'extras_translatable',
        ]);

        $this->crud->addField([
          'name' => 'closed_area',
          'label' => 'Закрытая территория',
          'tab' => 'Характеристики',
          'fake' => true,
          'store_in' => 'extras_translatable',
        ]);
      } else {
        $this->crud->addField([
          'name' => 'class',
          'label' => 'Класс',
          'tab' => 'Характеристики',
          'fake' => true,
          'store_in' => 'extras_translatable',
        ]);

        $this->crud->addField([
          'name' => 'technology',
          'label' => 'Технология строительства',
          'tab' => 'Характеристики',
          'fake' => true,
          'store_in' => 'extras_translatable',
        ]);

        $this->crud->addField([
          'name' => 'insulation',
          'label' => 'Утепление',
          'tab' => 'Характеристики',
          'fake' => true,
          'store_in' => 'extras_translatable',
        ]);

        $this->crud->addField([
          'name' => 'ceilings',
          'label' => 'Высота потолков',
          'tab' => 'Характеристики',
          'fake' => true,
          'store_in' => 'extras_translatable',
        ]);

        $this->crud->addField([
          'name' => 'condition',
          'label' => 'Состояние квартиры',
          'tab' => 'Характеристики',
          'fake' => true,
          'store_in' => 'extras_translatable',
        ]);

        $this->crud->addField([
          'name' => 'closed_area',
          'label' => 'Закрытая территория',
          'tab' => 'Характеристики',
          'fake' => true,
          'store_in' => 'extras_translatable',
        ]);

        $this->crud->addField([
          'name' => 'parking',
          'label' => 'Паркинг',
          'tab' => 'Характеристики',
          'fake' => true,
          'store_in' => 'extras_translatable',
        ]);
      }

      $this->crud->addField([
        'name' => 'infrastructure',
        'label' => 'Инфраструктура',
        'tab' => 'Характеристики',
        'fake' => true,
        'store_in' => 'extras_translatable',
      ]);

      if(!$this->crud->entry || !$this->crud->entry->original){
        $this->crud->addField([
          'name' => 'communications',
          'label' => 'Коммуникации',
          'type' => 'select2_from_array',
          'options' => $this->communications,
          'allows_multiple' => true,
          'tab' => 'Характеристики',
          'fake' => true,
          'store_in' => 'extras',
        ]);

        $this->crud->addField([
          'name' => 'contacts',
          'label' => 'Контакты',
          'tab' => 'Характеристики',
          'fake' => true,
          'type' => 'ckeditor',
          'store_in' => 'extras',
        ]);

        $this->crud->addField([
          'name' => 'phone',
          'label' => 'Телефон',
          'tab' => 'Характеристики',
          'fake' => true,
          'store_in' => 'extras',
        ]);

        $this->crud->addField([
          'name' => 'email',
          'label' => 'Email',
          'type' => 'text',
          'tab' => 'Характеристики',
          'fake' => true,
          'store_in' => 'extras',
        ]);

        $this->crud->addField([
          'name' => 'site',
          'label' => 'Сайт',
          'tab' => 'Характеристики',
          'fake' => true,
          'store_in' => 'extras',
        ]);
      }

      $this->crud->addField([
        'name' => 'meta_title',
        'label' => 'Заголовок (meta)',
        'hint' => 'По умолчанию будет использовано название',
        'tab' => 'Характеристики',
      ]);

        $this->crud->addField([
        'name' => 'meta_description',
        'label' => 'Описание (meta)',
        'tab' => 'Характеристики',
      ]);

      $this->crud->addField([
        'name' => 'mod',
        'label' => 'Типовые проекты',
        'type' => 'modification',
        'tab' => 'Типовые проекты',
      ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();

        // $this->crud->attributes = $this->current_category? Category::withoutGlobalScopes()->find($this->current_category)->attributes: ($this->crud->getEntry(\Route::current()->parameter('id'))? $this->crud->getEntry(\Route::current()->parameter('id'))->category->attributes : null);
    }
}
