<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ApplicationRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\Application;
use Backpack\NewsCRUD\app\Models\Article;

/**
 * Class ApplicationCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ApplicationCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    private $articles;

    public function setup()
    {
        $this->crud->setModel('App\Models\Application');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/application');
        $this->crud->setEntityNameStrings('заявку', 'заявки');
        
        $this->crud->enableExportButtons();

        $this->articles = Article::withoutGlobalScopes()->where('language_abbr', 'ru')->find(Application::pluck('article_id'))->pluck('title', 'id')->toArray();
    }

    protected function setupListOperation()
    {
        $this->crud->addFilter([
            'name'  => 'article_id',
            'type'  => 'select2',
            'label' => 'Мероприятие'
        ], function () {
            return $this->articles;
        }, function ($value) { // if the filter is active
            $this->crud->addClause('where', function($q) use ($value) {
                $q->where('article_id', $value);
            });
        });

        
        $this->crud->addFilter([
            'name'  => 'is_online',
            'type'  => 'select2',
            'label' => 'Онлайн/Офлайн'
        ], function () {
            return [
                0 => 'Офлайн',
                1 => 'Онлайн'
            ];
        }, function ($value) { // if the filter is active
            $this->crud->addClause('where', function($q) use ($value) {
                $q->where('is_online', $value);
            });
        });

        $this->crud->addColumn([
            'name' => 'article_id',
            'label' => 'Мероприятие',
            'type' => 'closure',
            'function' => function($entry) {
                return $entry->article? $entry->article->title : '';
            },
            'exportOnlyField' => true
            
        ]);

        $this->crud->addColumn([
            'name' => 'is_online',
            'label' => '',
            'type' => 'closure',
            'function' => function($entry) {
                return $entry->is_online? 'Онлайн' : 'Офлайн';
            }
        ]);

        $this->crud->addColumn([
            'name' => 'name',
            'label' => 'ФИО'
        ]);

        $this->crud->addColumn([
            'name' => 'organization',
            'label' => 'Организация'
        ]);

        $this->crud->addColumn([
            'name' => 'email',
            'label' => 'Email'
        ]);
        
        $this->crud->addColumn([
            'name' => 'phone',
            'label' => 'Телефон'
        ]);

        $this->crud->addColumn([
            'name' => 'extras_1',
            'label' => 'Должность',
            'type' => 'closure',
            'function' => function($entry) {
                return $entry->extras && $entry->extras['Должность']? $entry->extras['Должность'] : '';
            },
            'exportOnlyField' => true
        ]);
        
        $this->crud->addColumn([
            'name' => 'extras_1',
            'label' => 'Сфера деятельности',
            'type' => 'closure',
            'function' => function($entry) {
                return $entry->extras && $entry->extras['Сфера деятельности']? $entry->extras['Сфера деятельности'] : '';
            },
            'exportOnlyField' => true
        ]);
        
        $this->crud->addColumn([
            'name' => 'extras_2',
            'label' => 'Страна',
            'type' => 'closure',
            'function' => function($entry) {
                return $entry->extras && $entry->extras['Страна']? $entry->extras['Страна'] : '';
            },
            'exportOnlyField' => true
        ]);
        
        $this->crud->addColumn([
            'name' => 'extras_3',
            'label' => 'Область',
            'type' => 'closure',
            'function' => function($entry) {
                return $entry->extras && $entry->extras['Область']? $entry->extras['Область'] : '';
            },
            'exportOnlyField' => true
        ]);
        
        $this->crud->addColumn([
            'name' => 'extras_4',
            'label' => 'Город',
            'type' => 'closure',
            'function' => function($entry) {
                return $entry->extras && $entry->extras['Город']? $entry->extras['Город'] : '';
            },
            'exportOnlyField' => true
        ]);
        
        $this->crud->addColumn([
            'name' => 'extras_5',
            'label' => 'Почтовый индекс',
            'type' => 'closure',
            'function' => function($entry) {
                return $entry->extras && $entry->extras['Почтовый индекс']? $entry->extras['Почтовый индекс'] : '';
            },
            'exportOnlyField' => true
        ]);
        
        $this->crud->addColumn([
            'name' => 'extras_6',
            'label' => 'Улица',
            'type' => 'closure',
            'function' => function($entry) {
                return $entry->extras && $entry->extras['Улица']? $entry->extras['Улица'] : '';
            },
            'exportOnlyField' => true
        ]);
        
        $this->crud->addColumn([
            'name' => 'extras_7',
            'label' => 'Дом',
            'type' => 'closure',
            'function' => function($entry) {
                return $entry->extras && $entry->extras['Дом']? $entry->extras['Дом'] : '';
            },
            'exportOnlyField' => true
        ]);
        
        $this->crud->addColumn([
            'name' => 'extras_8',
            'label' => 'Квартира / офис',
            'type' => 'closure',
            'function' => function($entry) {
                return $entry->extras && $entry->extras['Квартира / офис']? $entry->extras['Квартира / офис'] : '';
            },
            'exportOnlyField' => true
        ]);
    }

    
    protected function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', false);
        (new Article)->clearGlobalScopes();
        $entry = $this->crud->getCurrentENtry();

        $this->crud->addColumn([
            'name' => 'article_id',
            'label' => 'Мероприятие',
            'type' => 'select',
            'entity' => 'article',
            'attribute' => 'title'
        ]);

        $this->crud->addColumn([
            'name' => 'name',
            'label' => 'ФИО'
        ]);

        $this->crud->addColumn([
            'name' => 'organization',
            'label' => 'Организация'
        ]);

        $this->crud->addColumn([
            'name' => 'email',
            'label' => 'Email'
        ]);

        $this->crud->addColumn([
            'name' => 'phone',
            'label' => 'Телефон'
        ]);

        $this->crud->addColumn([
            'name' => 'is_online',
            'label' => '',
            'type' => 'closure',
            'function' => function($entry) {
                return $entry->is_online? 'Онлайн' : 'Офлайн';
            }
        ]);

        if($entry && $entry->extras) {
            foreach($entry->extras as $key => $item) {
                $this->crud->addColumn([
                    'name' => $key,
                    'label' => $key,
                    'value' => $item
                ]);
            }
        }
    }

    public function create(ApplicationRequest $request)
    {
        $fields = $request->all();
        unset($fields['_token']);

        Application::create($fields);

        return back()->with('message', __('forms.success.application'))->with('type', 'review');
    }
}
