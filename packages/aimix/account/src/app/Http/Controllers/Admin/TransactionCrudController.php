<?php

namespace Aimix\Account\app\Http\Controllers\Admin;

use Aimix\Account\app\Models\Transaction;
use Aimix\Account\app\Models\Usermeta;

use Aimix\Account\app\Http\Requests\TransactionRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class TransactionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TransactionCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    private $types;
    private $usermetas;

    public function setup()
    {
        $this->crud->setModel('Aimix\Account\app\Models\Transaction');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/transaction');
        $this->crud->setEntityNameStrings('transaction', 'transactions');

        $this->types = array_unique(Transaction::pluck('type', 'type')->toArray());
        $this->usermetas = array_unique(Usermeta::pluck('firstname', 'id')->toArray());
    }

    protected function setupListOperation()
    {
        // TODO: remove setFromDb() and manually define Columns, maybe Filters
        // $this->crud->setFromDb();
        $this->crud->addFilter([
            'name' => 'type',
            'label' => 'Type',
            'type' => 'select2'
          ], function(){
            return $this->types;
          }, function($value){
            $this->crud->addClause('where', 'type', $value);
          });

          $this->crud->addFilter([
            'name' => 'usermeta_id',
            'label' => 'User',
            'type' => 'select2'
          ], function(){
            return $this->usermetas;
          }, function($value){
            $this->crud->addClause('where', 'usermeta_id', $value);
          });

        $this->crud->addColumn([
            'name' => 'usermeta_id',
            'label' => 'User',
            'type' => 'usermeta_link'
        ]);
        
        $this->crud->addColumn([
            'name' => 'order_id',
            'label' => 'Order',
            'type' => 'order_link'
        ]);

        $this->crud->addColumn([
            'name' => 'type',
            'label' => 'Type'
        ]);

        $this->crud->addColumn([
            'name' => 'change',
            'label' => 'Amount',
            'prefix' => '$'
        ]);

        $this->crud->addColumn([
            'name' => 'is_completed',
            'label' => 'Completed',
            'type' => 'boolean'
        ]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(TransactionRequest::class);

        // TODO: remove setFromDb() and manually define Fields
        // $this->crud->setFromDb();
        
        $this->crud->addField([
            'name' => 'usermeta_id',
            'label' => 'User',
            'entity' => 'usermeta',
            'attribute' => 'firstname', 
            'model' => 'Aimix\Account\app\Models\Usermeta',
            'type' => 'select2',
        ]);
        
        $this->crud->addField([
            'name' => 'type',
            'label' => 'Type',
            'type' => 'select2_from_array',
            'options' => [
              'bonus' => 'bonus',
              'cashback' => 'cashback',
              'review' => 'review',
              'withdraw' => 'withdraw',
            ]
        ]);
        
        $this->crud->addField([
          'name' => 'is_completed',
          'label' => 'Completed',
          'type' => 'boolean'
        ]);
        
        $this->crud->addField([
          'name' => 'change',
          'label' => 'Amount',
          'type' => 'number',
          'attributes' => [
            'readonly' => true
          ]
        ]);
        
        $this->crud->addField([
          'name' => 'description',
          'label' => 'Description',
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
    
    protected function setupShowOperation()
    {
      $this->setupListOperation();
    }
}
