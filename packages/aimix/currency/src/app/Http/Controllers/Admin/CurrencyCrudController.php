<?php

namespace Aimix\Currency\app\Http\Controllers\Admin;

use Aimix\Currency\app\Http\Requests\CurrencyRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CurrencyCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class CurrencyCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        $this->crud->setModel('Aimix\Currency\app\Models\Currency');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/currency');
        $this->crud->setEntityNameStrings('currency', 'currencies');
    }

    protected function setupListOperation()
    {
        // TODO: remove setFromDb() and manually define Columns, maybe Filters
        // $this->crud->setFromDb();
        
        $this->crud->addColumn([
          'name' => 'name',
          'label' => 'Название',
        ]);
        
        $this->crud->addColumn([
          'name' => 'abbr',
          'label' => 'Аббревиатура',
        ]);
        
        $this->crud->addColumn([
          'name' => 'is_default',
          'label' => 'Основная',
          'type' => 'boolean'
        ]);
        
        $this->crud->addColumn([
          'name' => 'exchange_rate',
          'label' => 'Курс',
        ]);
        
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(CurrencyRequest::class);

        // TODO: remove setFromDb() and manually define Fields
        // $this->crud->setFromDb();
        
        $this->crud->addFields([
          [
            'name' => 'name',
            'label' => 'Название',
          ],
          [
            'name' => 'abbr',
            'label' => 'Аббревиатура',
          ],
          [
            'name' => 'is_default',
            'label' => 'Основная',
            'type' => 'boolean',
          ],
          [
            'name' => 'exchange_rate',
            'label' => 'Курс',
            'type' => 'number',
            'attributes' => [
              'step' => 0.01
            ],
            'default' => '1.00',
          ],
          [
            'name' => 'symbol',
            'label' => 'Знак',
          ],
          [
            'name' => 'unicode',
            'label' => 'Unicode'
          ]
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
