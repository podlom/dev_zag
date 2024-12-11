<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PollOptionRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

use Backpack\LangFileManager\app\Models\Language;

/**
 * Class PollOptionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PollOptionCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Aimix\Shop\app\Http\Controllers\Admin\Operations\TranslateOperation;

    private $languages = ['ru'];
    private $default_language = 'ru';
    private $questions = [];

    public function setup()
    {
        $this->crud->setModel('App\Models\PollOption');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/polloption');
        $this->crud->setEntityNameStrings('вариант ответа', 'варианты ответов');

        $this->languages = Language::getActiveLanguagesNames();
        $this->default_language = Language::where('default', 1)->first()->abbr;

        $this->questions = \App\Models\PollQuestion::withoutGlobalScopes()->NoEmpty()->where('language_abbr', 'ru')->pluck('title', 'id')->toArray();
        
        $this->crud->query = $this->crud->query->withoutGlobalScopes();
        $this->crud->model->clearGlobalScopes();
    }

    protected function setupListOperation()
    {
        $this->crud->orderBy('title');

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
            'name'  => 'question',
            'type'  => 'select2',
            'label' => 'Вопрос'
          ], function () {
            return $this->questions;
          }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'question_id', $value);
          });
          
          $this->crud->addColumn([
            'name' => 'language_abbr',
            'label' => 'Язык',
          ]);

        CRUD::addColumn([
            'name' => 'question_id',
            'label' => 'Вопрос',
            'type' => 'select',
            'entity' => 'question',
            'attribute' => 'title'
        ]);

        CRUD::addColumn([
            'name' => 'title',
            'label' => 'Текст'
        ]);

        CRUD::addColumn([
            'name' => 'is_active',
            'label' => 'Активен',
            'type' => 'check'
        ]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(PollOptionRequest::class);

        if(\Route::current()->parameter('id'))
            $this->crud->getEntry(\Route::current()->parameter('id'));

        if(!$this->crud->entry || !$this->crud->entry->original){
            CRUD::addField([
                'name' => 'is_active',
                'label' => 'Активен',
                'type' => 'boolean',
                'default' => 1
            ]);
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
            'name' => 'translation_id',
            'label' => 'Оригинал/перевод',
            'type' => 'translation',
            'model' => 'polloption',
        ]);

        if(!$this->crud->entry || !$this->crud->entry->original){
            CRUD::addField([
                'name' => 'question_id',
                'label' => 'Вопрос',
                'type' => 'select2',
                'entity' => 'question',
                'attribute' => 'title',
                'options' => function($q) {
                    return $q->where('language_abbr', 'ru')->get();
                }
            ]);
        }

        CRUD::addField([
            'name' => 'title',
            'label' => 'Текст'
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
