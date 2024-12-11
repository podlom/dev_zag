<?php

namespace Aimix\Shop\app\Http\Controllers\Admin;

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
    
    private $languages = ['ru'];
    
    private $categories;
    private $brands;
    private $categories_by_lang;
    private $current_category;
    private $current_language;
    
    public function setup()
    {
        $this->crud->setModel('Aimix\Shop\app\Models\Product');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/product');
        $this->crud->setEntityNameStrings('товар', 'товары');
        
        if(config('aimix.shop.enable_brands')) {
          $this->brands = Brand::NoEmpty()->pluck('name', 'id')->toArray();
        }
        $this->current_category = \Request::input('category_id')? \Request::input('category_id') : null;

        if(config('aimix.aimix.enable_languages')) {
          $this->languages = Language::getActiveLanguagesNames();

          $this->current_language = \Request::input('language_abbr')? \Request::input('language_abbr') : null;
          
          $this->crud->query = $this->crud->query->withoutGlobalScopes();
          
          $this->crud->model->clearGlobalScopes();
          
        }
        
        $this->categories = Category::withoutGlobalScopes()->NoEmpty()->pluck('name', 'id')->toArray();
        
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
        // TODO: remove setFromDb() and manually define Columns, maybe Filters
        // $this->crud->setFromDb();
        $this->crud->addFilter([
          'name' => 'category_id',
          'label' => 'Категория',
          'type' => 'select2',
        ], function(){
          return $this->categories;
        }, function($value){
          $this->crud->addClause('where', 'category_id', $value);
        });
        
      if(config('aimix.shop.enable_brands')) {
        $this->crud->addFilter([
          'name' => 'brand_id',
          'label' => 'Производитель',
          'type' => 'select2'
        ], function(){
          return $this->brands;
        }, function($value){
          $this->crud->addClause('where', 'brand_id', $value);
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
          ]);
        }
        
        $this->crud->addColumn([
          'name' => 'name',
          'label' => 'Название',
        ]);
        
        $this->crud->addColumn([
          'name' => 'category_id',
          'label' => 'Категория',
          'type' => 'select',
          'entity' => 'category',
          'attribute' => 'name',
          'model' => 'Aimix\Shop\app\Models\Category',
          'options'   => (function ($query) {
              return $query->withoutGlobalScopes()->get();
          }),
        ]);
        
      if(config('aimix.shop.enable_brands')) {
        $this->crud->addColumn([
          'name' => 'brand_id',
          'label' => 'Производитель',
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
      ]);
    }
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(ProductRequest::class);
        $this->categories_by_lang = Category::withoutGlobalScopes()->doesnthave('children');
        $language_in_url = (boolean) $this->current_language;

        if(\Route::current()->parameter('id'))
          $this->current_language = $this->crud->getEntry(\Route::current()->parameter('id'))->language_abbr;

        $this->current_language = \Request::input('language_abbr')? \Request::input('language_abbr') : $this->current_language;

        if(config('aimix.aimix.enable_languages')) {
          if($this->current_language) {
            $this->categories_by_lang = $this->categories_by_lang->where('language_abbr', $this->current_language);
          } else {
            $this->categories_by_lang = $this->categories_by_lang->where('language_abbr', array_key_first($this->languages));
          }
        }

        $this->categories_by_lang = $this->categories_by_lang->pluck('name', 'id')->toArray();

        $this->crud->attributes = Category::withoutGlobalScopes()->find(array_key_first($this->categories_by_lang))->attributes->keyBy('id');

        $this->crud->attributes = \Route::current()->parameter('id') && !$language_in_url? Category::withoutGlobalScopes()->find($this->crud->getEntry(\Route::current()->parameter('id'))->category_id)->attributes : $this->crud->attributes;

        if($this->current_category)
          $this->crud->attributes = Category::withoutGlobalScopes()->find($this->current_category)->attributes->keyBy('id');

        // TODO: remove setFromDb() and manually define Fields
        // $this->crud->setFromDb();
      if(config('aimix.aimix.enable_languages')) {
        $this->crud->addField([
          'name' => 'language_abbr',
          'label' => 'Язык',
          'type' => 'select2_from_array',
          'options' => $this->languages,
          'value' => $this->current_language ?? $this->current_language,
          'attributes' => [
            'onchange' => 'window.location.search += "&language_abbr=" + this.value'
          ]
        ]);
      }
        
        
        $this->crud->addField([
          'name' => 'is_active',
          'label' => 'Активен',
          'type' => 'boolean',
          'default' => '1',
          'tab' => 'Характеристики',
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
          'name' => 'name',
          'label' => 'Название',
          'tab' => 'Характеристики',
        ]);
        
        $this->crud->addField([
          'name' => 'slug',
          'label' => 'URL',
          'prefix' => url('/products').'/',
          'hint' => 'По умолчанию будет сгенерирован из названия.',
          'tab' => 'Характеристики',
        ]);
        
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
        
        // $this->crud->addField([
        //   'name' => 'category_id',
        //   'label' => 'Категория',
        //   'type' => 'select2',
        //   'entity' => 'category',
        //   'attribute' => 'name',
        //   'model' => 'Aimix\Shop\app\Models\Category',
        //   'value' => $this->current_category,
        //   'attributes' => [
        //     'onchange' => 'window.location.search += "&category_id=" + this.value'
        //   ]
        // ]);

        
        
      if(config('aimix.shop.enable_brands')) {
        $this->crud->addField([
          'name' => 'brand_id',
          'label' => 'Производитель',
          'type' => 'select2',
          'entity' => 'brand',
          'attribute' => 'name',
          'model' => 'Aimix\Shop\app\Models\Brand',
          'tab' => 'Характеристики',
        ]);
      }
        
      if(config('aimix.shop.enable_is_hit')) {
        $this->crud->addField([
          'name' => 'is_hit',
          'label' => 'Хит',
          'type' => 'boolean',
          'tab' => 'Характеристики',
        ]);
      }

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
        
      $this->crud->addField([
        'name' => 'meta_title',
        'label' => 'Заголовок (meta)',
        'hint' => 'По умолчанию будет использовано название',
        'tab' => 'Meta',
      ]);

        $this->crud->addField([
          'name' => 'meta_description',
          'label' => 'Описание (meta)',
          'tab' => 'Meta',
        ]);
        
        $this->crud->addField([
          'name' => 'mod',
          'label' => 'Модификации',
          'type' => 'modification',
          'tab' => 'Характеристики',
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();

        // $this->crud->attributes = $this->current_category? Category::withoutGlobalScopes()->find($this->current_category)->attributes: ($this->crud->getEntry(\Route::current()->parameter('id'))? $this->crud->getEntry(\Route::current()->parameter('id'))->category->attributes : null);
    }
}
