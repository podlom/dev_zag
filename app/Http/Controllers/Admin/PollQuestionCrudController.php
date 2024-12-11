<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PollQuestionRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

use Backpack\LangFileManager\app\Models\Language;

/**
 * Class PollQuestionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PollQuestionCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Aimix\Shop\app\Http\Controllers\Admin\Operations\TranslateOperation;

    private $languages = ['ru'];
    private $default_language = 'ru';
    private $types = [
        'all' => 'Все',
        'cottage' => 'Коттеджные городки',
        'newbuild' => 'Новостройки'
    ];

    public function setup()
    {
        $this->crud->setModel('App\Models\PollQuestion');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/pollquestion');
        $this->crud->setEntityNameStrings('вопрос', 'вопросы');

        $this->languages = Language::getActiveLanguagesNames();
        $this->default_language = Language::where('default', 1)->first()->abbr;
        
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
          
          $this->crud->addColumn([
            'name' => 'language_abbr',
            'label' => 'Язык',
          ]);

        CRUD::addColumn([
            'name' => 'title',
            'label' => 'Вопрос'
        ]);

        CRUD::addColumn([
            'name' => 'is_active',
            'label' => 'Активен',
            'type' => 'check'
        ]);

        CRUD::addColumn([
            'name' => 'is_multiple',
            'label' => 'Множественный выбор',
            'type' => 'check'
        ]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(PollQuestionRequest::class);

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
            'model' => 'pollquestion',
        ]);

        CRUD::addField([
            'name' => 'title',
            'label' => 'Вопрос'
        ]);

        if(!$this->crud->entry || !$this->crud->entry->original){
            CRUD::addField([
                'name' => 'type',
                'label' => 'Категория',
                'type' => 'select2_from_array',
                'options' => $this->types
            ]);

            CRUD::addField([
                'name' => 'is_multiple',
                'label' => 'Множественный выбор',
                'type' => 'boolean'
            ]);
        }

        $this->crud->addField([
            'name' => 'h1',
            'label' => 'Заголовок h1',
        ]);

        $this->crud->addField([
            'name' => 'meta_title',
            'label' => 'Заголовок (meta)',
        ]);
    
        $this->crud->addField([
            'name' => 'meta_desc',
            'label' => 'Описание (meta)',
        ]);

        $this->crud->addField([
            'name' => 'seo_text',
            'label' => 'Seo-текст',
            'type' => 'ckeditor',
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
