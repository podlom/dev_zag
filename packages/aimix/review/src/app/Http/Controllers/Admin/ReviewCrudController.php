<?php

namespace Aimix\Review\app\Http\Controllers\Admin;

use Aimix\Review\app\Http\Requests\ReviewRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Aimix\Review\app\Models\Review;
use Backpack\LangFileManager\app\Models\Language; // remove

/**
 * Class ReviewCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class ReviewCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Aimix\Shop\app\Http\Controllers\Admin\Operations\TranslateOperation; // remove

    private $models;
    private $languages = ['ru']; // remove
    private $default_language = 'ru'; // remove
    private $types; // remove

    public function setup()
    {
        $this->crud->setModel('Aimix\Review\app\Models\Review');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/review');
        $this->crud->setEntityNameStrings('отзыв', 'отзывы');
        
        $this->crud->model->clearGlobalScopes(); // remove
        if($this->crud->getCurrentEntry() && $this->crud->getCurrentEntry()->type === 'article' || (request()->has('type') && request('type') === 'article')) {
          $this->crud->setEntityNameStrings('комментарий', 'комментарии');
        }

        $this->models = [
          'Backpack\NewsCRUD\app\Models\Article' => 'статья',
          'Aimix\Shop\app\Models\Product' => 'объект',
          'Aimix\Shop\app\Models\Category' => 'категория',
          'Aimix\Shop\app\Models\Brand' => 'компания',
        ];
        $this->languages = Language::getActiveLanguagesNames(); // remove
        $this->default_language = Language::where('default', 1)->first()->abbr; // remove
        
        $this->current_language = \Request::input('language_abbr')? \Request::input('language_abbr') : null; // remove
        
        $this->crud->query = $this->crud->query->withoutGlobalScopes(); // remove
        $this->crud->model->clearGlobalScopes(); // remove
        $this->types = [
          'zagorodna' => 'Zagorodna.com',
          'realexpo' => 'Реал Экспо',
          'brand' => 'Компании',
          'newbuild' => 'Новостройки',
          'cottage' => 'Коттеджные городки'
        ];
    }

    protected function setupListOperation()
    {
        // TODO: remove setFromDb() and manually define Columns, maybe Filters
        
        $this->crud->addFilter([ // remove
          'name'  => 'language',
          'type'  => 'select2',
          'label' => 'Язык'
        ], function () {
          return $this->languages;
        }, function ($value) { // if the filter is active
          $this->crud->addClause('where', 'language_abbr', $value);
        });

        if(!request()->has('type') || request('type') !== 'article') {
          $this->crud->addClause('where', 'type', '!=', 'article');
          $this->crud->addFilter([ // remove
            'name'  => 'type',
            'type'  => 'select2',
            'label' => 'Тип'
          ], function () {
            return $this->types;
          }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'type', $value);
          });
        } else {
          $this->crud->addClause('where', 'type', request('type'));
        }

        $this->crud->addFilter([
          'type'  => 'simple',
          'name'  => 'is_moderated',
          'label' => 'Неактивные'
        ], 
        false, 
        function() { // if the filter is active
          $this->crud->addClause('where', 'is_moderated', 0);
        } );

        $this->crud->addColumn([ // remove
          'name' => 'language_abbr',
          'label' => 'Язык',
        ]);
        
        $this->crud->addColumn([
          'name' => 'is_moderated',
          'label' => 'Опубликовано',
          'type' => 'boolean'
        ]);

        
        $this->crud->addColumn([
          'name' => 'created_at',
          'label' => 'Дата',
        ]);
        
      if(config('aimix.review.enable_review_type')) {
        $this->crud->addColumn([
          'name' => 'type',
          'label' => 'Тип',
        ]);
      }
      
        $this->crud->addColumn([
          'name' => 'name',
          'label' => 'Имя',
        ]);
        
      if(config('aimix.review.enable_review_for_product')) {
        $label = 'Предмет отзыва';
        if(request()->has('type') && request('type') === 'article')
          $label = 'Статья';

        $this->crud->addColumn([
          'name' => 'reviewable',
          'label' => $label,
          'type' => 'closure',
          'function' => function($entry) {
            if($entry->type == 'zagorodna')
              return 'Zagorodna.com';
            elseif($entry->type == 'realexpo')
              return 'Реал Экспо';
              
            if(!$entry->reviewable_type)
              return null;

            if(!$entry->reviewable_type::find($entry->reviewable_id))
              return 'Запись была удалена';

            $name_or_title = $entry->reviewable_type::find($entry->reviewable_id)->name? $entry->reviewable_type::find($entry->reviewable_id)->name : $entry->reviewable_type::find($entry->reviewable_id)->title;

            return $this->models[$entry->reviewable_type] . ' <a href="' . $entry->reviewable_type::find($entry->reviewable_id)->link .'" target="_blank">' . $name_or_title . '</a>';
          },
        ]);
      }
        
      if(config('aimix.review.enable_rating')) {
        $this->crud->addColumn([
          'name' => 'rating',
          'label' => 'Оценка',
        ]);
      }
    }

    protected function setupCreateOperation()
    {
       // $this->crud->setValidation(ReviewRequest::class);
      //  if($this->crud->getCurrentEntry() && $this->crud->getCurrentEntry()->type === 'article') {
      //   $this->crud->route = $this->crud->route . '?type=article';
      // }

       if(\Route::current()->parameter('id'))
        $this->crud->getEntry(\Route::current()->parameter('id'));

        $this->crud->addField([ // remove
          'name' => 'language_abbr',
          'label' => 'Язык',
          'default' => $this->default_language,
          'attributes' => [
            'readonly' => 'readonly',
          ]
        ]);
  
        $this->crud->addField([ // remove
          'name' => 'translation_id',
          'label' => 'Оригинал/перевод',
          'type' => 'translation',
          'model' => 'review',
        ]);
    if(!$this->crud->entry || !$this->crud->entry->original){ // remove
        $this->crud->addField([
          'name' => 'is_moderated',
          'label' => 'Опубликовано',
          'type' => 'boolean'
        ]);

      if(!\Route::current()->parameter('id') || $this->crud->getCurrentEntry()->type === 'zagorodna' || $this->crud->getCurrentEntry()->type === 'realexpo') {
        $this->crud->addField([
          'name' => 'type',
          'label' => 'Тип',
          'type' => 'select2_from_array',
          'options' => [
            'zagorodna' => 'Zagorodna.com',
            'realexpo' => 'Реал Экспо'
          ]
        ]);
      }
        
      
      if(config('aimix.review.enable_review_type')) {
        // $this->crud->addField([
        //   'name' => 'type',
        //   'label' => 'Тип',
        //   'type' => 'select_from_array',
        //   'options' => [
        //     'text' => 'Текстовый',
        //     'video' => 'Видео'
        //   ]
        // ]);
      }
    }

    if(!$this->crud->entry || !$this->crud->entry->original){
      $this->crud->addField([
          'name' => 'created_at',
          'label' => 'Дата написания',
          'type' => 'date_picker',
          'default' => date('m/d/y'),
      ]);
  }
        
    $this->crud->addField([
      'name' => 'name',
      'label' => 'Имя',
    ]);
        
  if(!$this->crud->entry || !$this->crud->entry->original){ // remove
      if(config('aimix.review.enable_review_type')) {
        $this->crud->addField([
          'name' => 'file',
          'label' => 'Фото/видео',
          'type' => 'browse',
          'disc' => 'review',
        ]);
      } else {
        $this->crud->addField([
          'name' => 'file',
          'label' => 'Фото',
          'type' => 'browse',
          'disc' => 'review',
        ]);
      }
  }

      $this->crud->addField([
        'name' => 'profession',
        'label' => 'Профессия',
      ]);
        
      // if(config('aimix.review.enable_review_for_product')) {
      //   $this->crud->addField([
      //     'name' => 'product_id',
      //     'label' => 'Приобретённый товар',
      //     'type' => 'select2',
      //     'entity' => 'Product',
      //     'attribute' => 'name',
      //     'model' => "Aimix\Shop\app\Models\Product",
      //   ]);
      // }
    if(!$this->crud->entry || !$this->crud->entry->original){ // remove
      if(config('aimix.review.enable_rating')) {
        $this->crud->addField([
          'name' => 'rating',
          'label' => 'Оценка',
          'type' => 'number',
          'attributes' => [
            'max' => '10',
            'min' => '0'
          ]
        ]);
      }
    }

      if(config('aimix.review.enable_review_type')) {
        $this->crud->addField([
          'name' => 'text',
          'label' => 'Сообщение/html-код видео',
          'type' => 'textarea',
          'attributes' => [
            'rows' => '8'
          ]
        ]);
      } else {
        $this->crud->addField([
          'name' => 'text',
          'label' => 'Сообщение',
          'type' => 'ckeditor',
          'attributes' => [
            'rows' => '8'
          ]
        ]);
      }
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
