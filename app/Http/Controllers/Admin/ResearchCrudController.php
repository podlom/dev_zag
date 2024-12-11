<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ResearchRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

use App\Models\Research;
use App\Notifications\ResearchCreated;
use App\Models\BackpackUser;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ResearchCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ResearchCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    private $types = [
        'individual' => 'Индивидуальное',
        'all' => 'Все рынки',
        'cottage' => 'Коттеджные городки',
        'realexpo' => 'Компания "РеалЭкспо"',
    ];

    private $statuses = [
        'new' => 'Новое',
        'in_process' => 'В работе',
        'completed' => 'Завершено',
    ];

    public function setup()
    {
        $this->crud->setModel('App\Models\Research');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/research');
        $this->crud->setEntityNameStrings('исследование', 'исследования');
    }

    protected function setupListOperation()
    {
        // TODO: remove setFromDb() and manually define Columns, maybe Filters
        // $this->crud->setFromDb();

        $this->crud->addFilter([
            'name'  => 'status',
            'type'  => 'select2',
            'label' => 'Статус',
          ], function () {
            return $this->statuses;
          }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'status', $value);
          });

        $this->crud->addFilter([
            'name'  => 'type',
            'type'  => 'select2',
            'label' => 'Тип',
          ], function () {
            return $this->types;
          }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'type', $value);
          });
        
        $this->crud->addColumn([
            'name' => 'status',
            'label' => 'Статус',
            'type' => 'select_from_array',
            'options' => $this->statuses,
        ]);

        $this->crud->addColumn([
            'name' => 'type',
            'label' => 'Тип',
            'type' => 'select_from_array',
            'options' => $this->types,
        ]);

        $this->crud->addColumn([
            'name' => 'region',
            'label' => 'Регион',
        ]);

        $this->crud->addColumn([
            'name' => 'organization',
            'label' => 'Организация',
        ]);

        $this->crud->addColumn([
            'name' => 'name',
            'label' => 'ФИО',
        ]);

        $this->crud->addColumn([
            'name' => 'phone',
            'label' => 'Телефон',
        ]);

        $this->crud->addColumn([
            'name' => 'email',
            'label' => 'Email',
        ]);
    }

    protected function setupShowOperation()
    {
        // TODO: remove setFromDb() and manually define Columns, maybe Filters
        // $this->crud->setFromDb();
        $this->crud->addColumn([
            'name' => 'status',
            'label' => 'Статус',
            'type' => 'select_from_array',
            'options' => $this->statuses,
        ]);
        
        $this->crud->addColumn([
            'name' => 'type',
            'label' => 'Тип',
            'type' => 'select_from_array',
            'options' => $this->types,
        ]);

        $this->crud->addColumn([
            'name' => 'theme',
            'label' => 'Тематика (цели, задачи, проблемы)',
            'type' => 'textarea',
        ]);

        $this->crud->addColumn([
            'name' => 'region',
            'label' => 'Регион',
        ]);

        $this->crud->addColumn([
            'name' => 'method',
            'label' => 'Методика',
        ]);

        $this->crud->addColumn([
            'name' => 'structure',
            'label' => 'Структура',
        ]);

        $this->crud->addColumn([
            'name' => 'info',
            'label' => 'Дополнительная информация',
            'type' => 'textarea',
        ]);

        $this->crud->addColumn([
            'name' => 'organization',
            'label' => 'Организация',
        ]);

        $this->crud->addColumn([
            'name' => 'name',
            'label' => 'ФИО',
        ]);

        $this->crud->addColumn([
            'name' => 'phone',
            'label' => 'Телефон',
        ]);

        $this->crud->addColumn([
            'name' => 'email',
            'label' => 'Email',
        ]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(ResearchRequest::class);

        // TODO: remove setFromDb() and manually define Fields
        // $this->crud->setFromDb();
    }

    protected function setupUpdateOperation()
    {
        // $this->setupCreateOperation();
        $this->crud->addField([
            'name' => 'status',
            'label' => 'Статус',
            'type' => 'select2_from_array',
            'options' => $this->statuses,
        ]);

        $this->crud->addField([
            'name' => 'type',
            'label' => 'Тип',
            'type' => 'select2_from_array',
            'options' => $this->types,
        ]);

        $this->crud->addField([
            'name' => 'theme',
            'label' => 'Тематика (цели, задачи, проблемы)',
            'type' => 'textarea',
            'attributes' => [
                'rows' => 6
            ]
        ]);

        $this->crud->addField([
            'name' => 'region',
            'label' => 'Регион',
        ]);

        $this->crud->addField([
            'name' => 'method',
            'label' => 'Методика',
        ]);

        $this->crud->addField([
            'name' => 'structure',
            'label' => 'Структура',
        ]);

        $this->crud->addField([
            'name' => 'info',
            'label' => 'Дополнительная информация',
            'type' => 'textarea',
            'attributes' => [
                'rows' => 6
            ]
        ]);

        $this->crud->addField([
            'name' => 'organization',
            'label' => 'Организация',
        ]);

        $this->crud->addField([
            'name' => 'name',
            'label' => 'ФИО',
        ]);

        $this->crud->addField([
            'name' => 'phone',
            'label' => 'Телефон',
        ]);

        $this->crud->addField([
            'name' => 'email',
            'label' => 'Email',
        ]);
    }

    public function create(ResearchRequest $request)
    {
        $input = $request->input();
        unset($input['_token']);

        $research = new Research;
        foreach($input as $key => $value) {
            $research[$key] = $value;
        }

        $research->save();

        $admins = BackpackUser::whereHas('roles', function(Builder $query) {
            $query->where('name', 'admin');
        })->get();

        foreach($admins as $admin) {
            $admin->notify(new ResearchCreated($research, 'admin'));
        }

        $research->notify(new ResearchCreated($research, 'user'));
        

        return back()->with('message', __('forms.success.research'))->with('type', 'success');
    }
}
