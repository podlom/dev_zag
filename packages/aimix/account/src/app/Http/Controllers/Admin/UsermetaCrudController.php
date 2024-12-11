<?php

namespace Aimix\Account\app\Http\Controllers\Admin;

use Aimix\Account\app\Http\Requests\UsermetaRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class UsermetaCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class UsermetaCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        $this->crud->setModel('Aimix\Account\app\Models\Usermeta');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/usermeta');
        $this->crud->setEntityNameStrings('usermeta', 'usermetas');
    }

    protected function setupListOperation()
    {
        // TODO: remove setFromDb() and manually define Columns, maybe Filters
        // $this->crud->setFromDb();
        
        $this->crud->addColumn([
          'name' => 'user_id',
          'label' => 'User',
          'type' => 'user_link'
        ]);
        
        $this->crud->addColumn([
          'name' => 'lastname',
          'label' => 'Lastname',
        ]);
        
        $this->crud->addColumn([
          'name' => 'firstname',
          'label' => 'Firstname',
        ]);
        
        // $this->crud->addColumn([
        //   'name' => 'patronymic',
        //   'label' => 'Patronymic',
        // ]);
        
        // $this->crud->addColumn([
        //   'name' => 'gender',
        //   'label' => 'Gender'
        // ]);
        
        // $this->crud->addColumn([
        //   'name' => 'birthday',
        //   'label' => 'Birthday',
        // ]);
        
        $this->crud->addColumn([
          'name' => 'telephone',
          'label' => 'Telephone',
        ]);

      if(config('aimix.account.enable_bonus_system')) {
        $this->crud->addColumn([
          'name' => 'balance',
          'label' => 'Bonus balance',
          'type' => 'closure',
          'function' => function($entry) {
              if(!count($entry->transactions->where('is_completed', 1)))
                return;
                
              return '$'.$entry->transactions->where('is_completed', 1)->sortByDesc('created_at')->first()->balance;
          }
        ]);
      }
      
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(UsermetaRequest::class);

        // TODO: remove setFromDb() and manually define Fields
        // $this->crud->setFromDb();
        
        $this->crud->addFields([
          [
            'name' => 'user_id',
            'label' => 'User',
            'type' => 'select2',
            'entity' => 'user',
            'attribute' => 'name', 
            'model' => 'App\User',
          ],
          [
            'name' => 'firstname',
            'label' => 'Firstname',
          ],
          [
            'name' => 'lastname',
            'label' => 'Lastname',
          ],
          // [
          //   'name' => 'patronymic',
          //   'label' => 'Patronymic',
          // ],
          [
            'name' => 'gender',
            'label' => 'Gender',
            'type' => 'select2_from_array',
            'options' => [
              'male' => 'male',
              'female' => 'female'
            ],
            'allows_null' => true,
          ],
          [
            'name' => 'birthday',
            'label' => 'Birthday',
            'type' => 'date_picker'
          ],
          [
            'name' => 'telephone',
            'label' => 'Telephone',
          ],
          [
            'name' => 'address',
            'label' => 'Address',
          ],
          // [
          //   'name' => 'notification_email',
          //   'label' => 'Email for notifications',
          // ],
          // [
          //   'name' => 'files',
          //   'label' => 'Files',
          //   'type' => 'files_field'
          // ],
        ]);
        
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function setupShowOperation()
    {
      $this->crud->setValidation(UsermetaRequest::class);

      $this->crud->addColumn([
          'name' => 'user_id',
          'label' => 'User',
          'type' => 'user_link'
        ]);
      
      $this->crud->addColumn([
        'name' => 'firstname',
        'label' => 'Firstname',
      ]);

      $this->crud->addColumn([
        'name' => 'lastname',
        'label' => 'Lastname',
      ]);

      $this->crud->addColumn([
        'name' => 'extras',
        'label' => 'Prefered communication',
        'type' => 'usermeta_extras'
      ]);

      $this->crud->addColumn([
        'name' => 'telephone',
        'label' => 'Telephone',
      ]);

      $this->crud->addColumn([
        'name' => 'patronymic',
        'label' => 'Patronymic',
      ]);
      
      $this->crud->addColumn([
        'name' => 'gender',
        'label' => 'Gender'
      ]);
      
      $this->crud->addColumn([
        'name' => 'birthday',
        'label' => 'Birthday',
      ]);
    
      if(config('aimix.account.enable_referral_system')) {
        $this->crud->addColumn([
          'name' => 'referrer_id',
          'label' => 'Referrer',
          'entity' => 'referrer',
          'attribute' => 'firstname', 
          'model' => 'Aimix\Account\app\Models\Usermeta',
          'type' => 'select',
        ]);

        $this->crud->addColumn([
          'name' => 'referral_code',
          'label' => 'Referral code',
        ]);
        
        $this->crud->addColumn([
          'name' => 'referrals',
          'label' => 'Referrals',
          'type' => 'referrals_info'
        ]);
      }
      
      if(config('aimix.account.enable_bonus_system')) {
        $this->crud->addColumn([
          'name' => 'transactions',
          'label' => 'Transactions',
          'type' => 'transactions_info'
        ]);
      }
    }
}
