<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SubscriptionRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\Subscription;

/**
 * Class SubscriptionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SubscriptionCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    private $types = [];

    public function setup()
    {
        $this->crud->setModel('App\Models\Subscription');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/subscription');
        $this->crud->setEntityNameStrings('подписку', 'подписки');

        $this->types = array_merge(__('attributes.cottage_types'), __('attributes.newbuild_types'));
    }

    protected function setupListOperation()
    {
        // TODO: remove setFromDb() and manually define Columns, maybe Filters
        // $this->crud->setFromDb();
        $this->crud->addColumn([
          'name' => 'email',
          'label' => 'Email'
        ]);

        $this->crud->addColumn([
          'name' => 'region',
          'label' => 'Область'
        ]);

        $this->crud->addColumn([
          'name' => 'radius',
          'label' => 'Радиус, км'
        ]);

        $this->crud->addColumn([
          'name' => 'news',
          'label' => 'Новости',
          'type' => 'boolean'
        ]);

        $this->crud->addColumn([
          'name' => 'adding',
          'label' => 'Добавление',
          'type' => 'boolean'
        ]);

        $this->crud->addColumn([
          'name' => 'status',
          'label' => 'Статус',
          'type' => 'boolean'
        ]);

        $this->crud->addColumn([
          'name' => 'price',
          'label' => 'Цена',
          'type' => 'boolean'
        ]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(SubscriptionRequest::class);

        // TODO: remove setFromDb() and manually define Fields
        // $this->crud->setFromDb();

        $this->crud->addField([
          'name' => 'email',
          'label' => 'Email'
        ]);

        $this->crud->addField([
          'name' => 'region',
          'label' => 'Область'
        ]);

        $this->crud->addField([
          'name' => 'radius',
          'label' => 'Радиус, км',
          'type' => 'number',
          'attributes' => [
            'min' => 0
          ]
        ]);

        $this->crud->addField([
          'name' => 'news',
          'label' => 'Новости',
          'type' => 'boolean'
        ]);

        $this->crud->addField([
          'name' => 'adding',
          'label' => 'Добавление новой недвижимости',
          'type' => 'boolean'
        ]);

        $this->crud->addField([
          'name' => 'status',
          'label' => 'Изменение статуса',
          'type' => 'boolean'
        ]);

        $this->crud->addField([
          'name' => 'price',
          'label' => 'Изменение цен',
          'type' => 'boolean'
        ]);

        $this->crud->addField([
          'name' => 'price',
          'label' => 'Изменение цен',
          'type' => 'boolean'
        ]);

        $this->crud->addField([
          'name' => 'types',
          'label' => 'Типы ',
          'type' => 'select2_from_array',
          'options' => $this->types,
          'allows_null' => true,
          'allows_multiple' => true,
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function subscribe(SubscriptionRequest $request)
    {
      $email = $request->input('subscription_email')? $request->input('subscription_email') : $request->input('subscription_email_product');
      
      $subscription = Subscription::where('email', $email);

      if($request->input('subscription_news'))
        $subscription = $subscription->where('news', 1);
      else
        $subscription = $subscription->where('news', 0);
        
      if($request->input('subscription_region'))
        $subscription = $subscription->where('region', $request->input('subscription_region'));

      $subscription = $subscription->first();
      $subscription = $subscription? $subscription : new Subscription;

      $subscription->email = $email;

      if($request->input('subscription_region'))
        $subscription->region = $request->input('subscription_region');

      if($request->input('subscription_news'))
        $subscription->news = $request->input('subscription_news');

      if($request->input('subscription_latlng'))
        $subscription->latlng = $request->input('subscription_latlng');
        
      if($request->input('subscription_adding'))
        $subscription->adding = $request->input('subscription_adding');

      if($request->input('subscription_price'))
        $subscription->price = $request->input('subscription_price');
        
      if($request->input('subscription_status'))
        $subscription->status = $request->input('subscription_status');

      if($request->input('subscription_types')) {
        if($subscription->types) {
          $types = $subscription->types;
          foreach($request->input('subscription_types') as $item) {
            if(!in_array($item, $subscription->types))
            $types[] = $item;
          }
          $subscription->types = $types;
        } else {
          $subscription->types = $request->input('subscription_types');
        }

      }

      if($request->input('subscription_radius') != null)
        $subscription->radius = $request->input('subscription_radius');

      $subscription->save();

      return back()->with('message', __('forms.success.subscription'))->with('type', 'success');
    }
}
