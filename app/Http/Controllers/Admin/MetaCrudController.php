<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MetaRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

use Backpack\LangFileManager\app\Models\Language;
use App\Models\Meta;
use App\Region;

/**
 * Class MetaCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MetaCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CloneOperation;
    use \Aimix\Shop\app\Http\Controllers\Admin\Operations\TranslateOperation;

    private $languages = ['ru'];
    private $default_language = 'ru';
    private $statuses = [];
    private $types = [];
    private $regions = [];
    private $cities = [];

    public function setup()
    {
        $this->crud->setModel('App\Models\Meta');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/meta');
        $this->crud->setEntityNameStrings('тексты', 'тексты');

        $this->languages = Language::getActiveLanguagesNames();
        $this->default_language = Language::where('default', 1)->first()->abbr;
        
        $this->crud->query = $this->crud->query->withoutGlobalScopes();
        
        $this->crud->model->clearGlobalScopes();

        $this->regions = Region::where('language_abbr', 'ru')->pluck('name', 'region_id')->toArray();
        $this->statuses = __('main.product_statuses');
        $this->statuses['frozen'] = 'Заморожено';
        $this->types = array_merge(__('attributes.cottage_types'), __('attributes.newbuild_types'));
        
        // $this->cities = Meta::pluck('city', 'city')->unique()->toArray();
    }

    public function clone($id)
    {
      $this->crud->hasAccessOrFail('clone');
      $this->crud->setOperation('clone');
      
      $meta = Meta::find($id);
      $new = $meta->replicate();
      $new->save();

      $meta->translations->each(function($item) use ($new) {
        $new_translation = $item->replicate();
        $new_translation->original_id = $new->id;
        $new_translation->save();
      });
    }

    protected function setupListOperation()
    {

        $this->crud->addFilter([
            'name'  => 'language',
            'type'  => 'select2',
            'label' => 'Язык'
          ], function () {
            return $this->languages;
          }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'language_abbr', $value);
          });

          $this->crud->addFilter([
            'name'  => 'region',
            'type'  => 'select2',
            'label' => 'Область',
            'attributes' => [
              'style' => 'width:150px'
            ],
          ], function () {
            return $this->regions;
          }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'address->region', $value);
          });

          $this->crud->addFilter([
            'name'  => 'is_map',
            'type'  => 'select2',
            'label' => 'Страница',
            'attributes' => [
              'style' => 'width:150px'
            ],
          ], function () {
            return ['Прекаталог','Карта'];
          }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'is_map', $value);
          });

          $this->crud->addColumn([
            'name' => 'language_abbr',
            'label' => 'Язык',
          ]);

          $this->crud->addColumn([
            'name' => 'is_map',
            'label' => 'Страница',
            'type' => 'closure',
            'function' => function($value) {
              return $value->is_map? 'Карта' : 'Прекаталог';
            }
          ]);

          $this->crud->addColumn([
            'name' => 'region',
            'label' => 'Область',
            'type' => 'closure',
            'function' => function($value) {
              return $value->address['region']? $this->regions[$value->address['region']] : null;
            }
          ]);

          $this->crud->addColumn([
            'name' => 'type',
            'label' => 'Тип',
            'type' => 'select_from_array',
            'options' => $this->types
          ]);

          $this->crud->addColumn([
            'name' => 'status',
            'label' => 'Статус',
            'type' => 'select_from_array',
            'options' => $this->statuses
          ]);

          $this->crud->addColumn([
            'name' => 'cottage_h1',
            'label' => 'Заголовок (коттеджные поселки)',
          ]);

          $this->crud->addColumn([
            'name' => 'newbuild_h1',
            'label' => 'Заголовок (новостройки)',
          ]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(MetaRequest::class);

        // TODO: remove setFromDb() and manually define Fields
        // $this->crud->setFromDb();
        if(\Route::current()->parameter('id')) {
            $this->crud->getEntry(\Route::current()->parameter('id'));
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
            'name' => 'original_id',
            'label' => 'Оригинал/Переводы',
            'type' => 'translation',
            'model' => 'meta',
          ]);

          if(!$this->crud->entry || !$this->crud->entry->original){
            $this->crud->addField(            [
              'name' => 'is_map',
              'type' => 'boolean',
              'label' => 'Использовать для карты'
            ]);

            $this->crud->addField(            [
              'name' => 'address',
              'type' => 'address_zagorodna',
            ]);
            
            $this->crud->addField([
                'name' => 'status',
                'label' => 'Статус',
                'type' => 'select2_from_array',
                'options' => $this->statuses,
                'allows_null' => true
            ]);

            $this->crud->addField([
                'name' => 'type',
                'label' => 'Тип',
                'type' => 'select_type',
                'options' => $this->types,
                'allows_null' => true
            ]);
          }

          $this->crud->addField([   // CustomHTML
            'name' => 'hint',
            'type' => 'custom_html',
            'value' => '<br><p>Переменные для вставки:</p>
            <p>{region} / {region_genitive} - область (Киевская/Киевской) / район (Шевченковский/Шевченковского) / нас. пункт (Киев/Киева)</p>
            <p>{type} / {type_genitive} / {type_plural} / {type_plural_genitive} - тип (таунхаус/таунхауса/таунхаусы/таунхаусов)</p>
            <p>{status_plural} - статус (строящиеся)</p>
            <p>В случае отсутствия значения переменная {status} будет проигнорирована, {region} / {region_genitive} = "Украина" / "Украины", {type} = "коттеджный поселок"/"новостройка"</p>
            ',
          ]);

          $this->crud->addField([ 
            'name' => 'cottage_h1',
            'label' => 'Заголовок h1',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Коттеджные поселки'
        ]);

            $this->crud->addField([ 
              'name' => 'cottage_meta_title',
              'label' => 'Meta title',
              'fake' => true,
              'store_in' => 'extras',
              'tab' => 'Коттеджные поселки'
          ]);
  
          $this->crud->addField([
              'name' => 'cottage_meta_desc',
              'label' => 'Meta description',
              'fake' => true,
              'store_in' => 'extras',
              'tab' => 'Коттеджные поселки'
          ]);
  
          $this->crud->addField([   // CustomHTML
              'name' => 'content_separator_3',
              'type' => 'custom_html',
              'value' => '<br><h2>Seo-текст</h2><hr>',
              'tab' => 'Коттеджные поселки'
          ]);
  
          $this->crud->addField([
              'name' => 'cottage_seo_title',
              'label' => 'Заголовок',
              'fake' => true,
              'store_in' => 'extras',
              'tab' => 'Коттеджные поселки'
          ]);
  
          $this->crud->addField([
              'name' => 'cottage_seo_text',
              'label' => 'Текст',
              'type' => 'ckeditor',
              'fake' => true,
              'store_in' => 'extras',
              'tab' => 'Коттеджные поселки'
          ]);

          $this->crud->addField([ 
            'name' => 'newbuild_h1',
            'label' => 'Заголовок h1',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Новостройки'
        ]);
  
          $this->crud->addField([ 
              'name' => 'newbuild_meta_title',
              'label' => 'Meta title',
              'fake' => true,
              'store_in' => 'extras',
              'tab' => 'Новостройки'
          ]);
  
          $this->crud->addField([
              'name' => 'newbuild_meta_desc',
              'label' => 'Meta description',
              'fake' => true,
              'store_in' => 'extras',
              'tab' => 'Новостройки'
          ]);
  
          $this->crud->addField([   // CustomHTML
              'name' => 'content_separator_4',
              'type' => 'custom_html',
              'value' => '<br><h2>Seo-текст</h2><hr>',
              'tab' => 'Новостройки'
          ]);
  
          $this->crud->addField([
              'name' => 'newbuild_seo_title',
              'label' => 'Заголовок',
              'fake' => true,
              'store_in' => 'extras',
              'tab' => 'Новостройки'
          ]);
  
          $this->crud->addField([
              'name' => 'newbuild_seo_text',
              'label' => 'Текст',
              'type' => 'ckeditor',
              'fake' => true,
              'store_in' => 'extras',
              'tab' => 'Новостройки'
          ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
