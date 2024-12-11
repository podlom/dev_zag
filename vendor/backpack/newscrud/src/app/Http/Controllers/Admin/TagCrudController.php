<?php

namespace Backpack\NewsCRUD\app\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\NewsCRUD\app\Http\Requests\TagRequest;

use Backpack\LangFileManager\app\Models\Language;

class TagCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Aimix\Shop\app\Http\Controllers\Admin\Operations\TranslateOperation;

    private $languages = ['ru'];
    private $default_language = 'ru';

    public function setup()
    {
        $this->crud->setModel("Backpack\NewsCRUD\app\Models\Tag");
        $this->crud->setRoute(config('backpack.base.route_prefix', 'admin').'/tag');
        $this->crud->setEntityNameStrings('метку', 'метки');
        // $this->crud->setFromDb();

        $this->languages = Language::getActiveLanguagesNames();

        $this->current_language = \Request::input('language_abbr')? \Request::input('language_abbr') : null;
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
            'label' => 'Название',
          ]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(TagRequest::class);

        $this->crud->addField([
          'name' => 'language_abbr',
          'label' => 'Язык',
          'default' => $this->default_language,
          'attributes' => [
            'readonly' => 'readonly',
          ],
        ]);

        $this->crud->addField([
          'name' => 'original_id',
          'label' => 'Оригинал/перевод',
          'type' => 'translation',
          'model' => 'tag',
        ]);

          $this->crud->addField([
            'name' => 'name',
            'label' => 'Название',
          ]);
    }

    protected function setupUpdateOperation()
    {
        $this->crud->setValidation(TagRequest::class);
        $this->setupCreateOperation();
    }
}
