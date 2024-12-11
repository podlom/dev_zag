<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\FaqRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

use Backpack\LangFileManager\app\Models\Language;
use App\Models\FaqCategory as Category;
/**
 * Class FaqCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class FaqCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Aimix\Shop\app\Http\Controllers\Admin\Operations\TranslateOperation;

    private $languages = ['ru'];
    private $categories;
    private $categories_by_lang;
    private $current_language;
    private $default_language = 'ru';

    public function setup()
    {
        $this->crud->setModel('App\Models\Faq');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/faq');
        $this->crud->setEntityNameStrings('вопрос', 'вопросы');

        $this->languages = Language::getActiveLanguagesNames();
        $this->default_language = Language::where('default', 1)->first()->abbr;

        $this->current_language = \Request::input('language_abbr')? \Request::input('language_abbr') : null;
        
        $this->crud->query = $this->crud->query->withoutGlobalScopes();
        
        $this->crud->model->clearGlobalScopes();

        $this->categories = Category::withoutGlobalScopes()->NoEmpty()->where('language_abbr', 'ru')->pluck('name', 'id')->toArray();
        
        $this->crud->model->clearGlobalScopes();
    }

    protected function setupListOperation()
    {
      $this->crud->orderBy('question');

        $this->crud->addFilter([
            'name' => 'category_id',
            'label' => 'Категория',
            'type' => 'select2',
          ], function(){
            return $this->categories;
          }, function($value){
            $this->crud->addClause('where', 'category_id', $value);
          });

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
            'name' => 'question',
            'label' => 'Вопрос',
          ]);

          $this->crud->addColumn([
            'name' => 'category_id',
            'label' => 'Категория',
            'type' => 'select',
            'entity' => 'category',
            'attribute' => 'name',
            'model' => 'App\Models\FaqCategory',
            'options'   => (function ($query) {
                return $query->withoutGlobalScopes()->get();
            }),
          ]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(FaqRequest::class);

        // TODO: remove setFromDb() and manually define Fields
        // $this->crud->setFromDb();

        $this->categories_by_lang = Category::withoutGlobalScopes();
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

        // $this->crud->addField([
        //     'name' => 'language_abbr',
        //     'label' => 'Язык',
        //     'type' => 'select2_from_array',
        //     'options' => $this->languages,
        //     'value' => $this->current_language ?? $this->current_language,
        //     'attributes' => [
        //       'onchange' => 'window.location.search += "&language_abbr=" + this.value'
        //     ]
        //   ]);

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
          'model' => 'faq',
        ]);
        
        if(!$this->crud->entry || !$this->crud->entry->original){
          $this->crud->addField([
            'name' => 'category_id',
            'label' => 'Категория',
            'type' => 'select2_from_array',
            'options' => $this->categories_by_lang,
          ]);
        }

          $this->crud->addField([
            'name' => 'question',
            'label' => 'Вопрос',
            
          ]);

          $this->crud->addField([
            'name' => 'answer',
            'label' => 'Ответ',
            'type' => 'ckeditor',
          ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
