<?php

namespace Backpack\MenuCRUD\app\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\LangFileManager\app\Models\Language;

class MenuItemCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ReorderOperation;
    use \Aimix\Shop\app\Http\Controllers\Admin\Operations\TranslateOperation;

    private $languages = 'ru';
    private $default_language = 'ru';

    public function setup()
    {
        $this->crud->setModel("Backpack\MenuCRUD\app\Models\MenuItem");
        $this->crud->setRoute(config('backpack.base.route_prefix').'/menu-item');
        $this->crud->setEntityNameStrings('элемент', 'меню');

        $this->languages = Language::getActiveLanguagesNames();
        $this->default_language = Language::where('default', 1)->first()->abbr;
          
        $this->crud->query = $this->crud->query->withoutGlobalScopes();
        $this->crud->model->clearGlobalScopes();

        $this->crud->enableReorder('name', 3);

        $this->crud->operation('list', function () {
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
                'name' => 'name',
                'label' => 'Заголовок',
            ]);
            $this->crud->addColumn([
                'label' => 'Родительский элемент',
                'type' => 'select',
                'name' => 'parent_id',
                'entity' => 'parent',
                'attribute' => 'name',
                'model' => "\Backpack\MenuCRUD\app\Models\MenuItem",
            ]);
        });

        $this->crud->operation(['create', 'update'], function () {
            if(\Route::current()->parameter('id')) {
                $this->crud->getEntry(\Route::current()->parameter('id'));
                $lang = $this->crud->getEntry(\Route::current()->parameter('id'))->language_abbr;
            } else {
                $lang = 'ru';
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
                'model' => 'menu-item',
              ]);

            $this->crud->addField([
                'name' => 'name',
                'label' => 'Label',
            ]);
            if(!$this->crud->entry || !$this->crud->entry->original){
            $this->crud->addField([
                'label' => 'Parent',
                'type' => 'select',
                'name' => 'parent_id',
                'entity' => 'parent',
                'attribute' => 'name',
                'model' => "\Backpack\MenuCRUD\app\Models\MenuItem",
                'options' => function($q) use ($lang) {
                    return $q->whereIn('depth', [1,2])->where('language_abbr', $lang)->get();
                }
                
            ]);
            }

            $this->crud->addField([
                'name' => ['type', 'link', 'page_id'],
                'label' => 'Type',
                'type' => 'page_or_link',
                'page_model' => '\Backpack\PageManager\app\Models\Page',
            ]);
        });
    }
}
