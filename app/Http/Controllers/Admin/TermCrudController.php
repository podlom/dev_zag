<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TermRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

use Backpack\LangFileManager\app\Models\Language;

/**
 * Class TermCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TermCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    private $languages = ['ru'];
    private $default_language = 'ru';

    public function setup()
    {
        $this->crud->setModel('App\Models\Term');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/term');
        $this->crud->setEntityNameStrings('термин', 'термины');

        $this->languages = Language::getActiveLanguagesNames();
        $this->default_language = Language::where('default', 1)->first()->abbr;
        
        $this->crud->query = $this->crud->query->withoutGlobalScopes();
        
        $this->crud->model->clearGlobalScopes();
    }

    protected function setupListOperation()
    {
        $this->crud->orderBy('name');

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
            'label' => 'Термин',
          ]);

          $this->crud->addColumn([
            'name' => 'definition',
            'label' => 'Определение',
          ]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(TermRequest::class);

        // TODO: remove setFromDb() and manually define Fields
        // $this->crud->setFromDb();

        $this->crud->addField([
            'name' => 'language_abbr',
            'label' => 'Язык',
            'default' => $this->default_language,
            'type' => 'select2_from_array',
            'options' => $this->languages
          ]);

          $this->crud->addField([
            'name' => 'name',
            'label' => 'Термин',
          ]);

          $this->crud->addField([
            'name' => 'definition',
            'label' => 'Определение',
            'type' => 'textarea',
            'attributes' => [
                'rows' => 8
            ]
          ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
