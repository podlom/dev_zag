<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CommunicationRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

use Backpack\LangFileManager\app\Models\Language;

/**
 * Class CommunicationCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CommunicationCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Aimix\Shop\app\Http\Controllers\Admin\Operations\TranslateOperation;

    private $languages = ['ru'];
    private $default_language = 'ru';

    public function setup()
    {
        $this->crud->setModel('App\Models\Communication');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/communication');
        $this->crud->setEntityNameStrings('communication', 'communications');
        
        if(config('aimix.aimix.enable_languages')) {
            $this->languages = Language::getActiveLanguagesNames();
            $this->default_language = Language::where('default', 1)->first()->abbr;
  
            $this->current_language = \Request::input('language_abbr')? \Request::input('language_abbr') : null;
            
            $this->crud->query = $this->crud->query->withoutGlobalScopes();
            
            $this->crud->model->clearGlobalScopes();
            
        }
    }

    protected function setupListOperation()
    {
        $this->crud->orderBy('name');

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
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(CommunicationRequest::class);

        // TODO: remove setFromDb() and manually define Fields
        $this->crud->setFromDb();
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
