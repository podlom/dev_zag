<?php

namespace Aimix\Gallery\app\Http\Controllers\Admin;

use Aimix\Gallery\app\Http\Requests\GalleryRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class GalleryCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class GalleryCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        $this->crud->setModel('Aimix\Gallery\app\Models\Gallery');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/gallery');
        $this->crud->setEntityNameStrings('галерею', 'галереи');
    }

    protected function setupListOperation()
    {
        // TODO: remove setFromDb() and manually define Columns, maybe Filters
        // $this->crud->setFromDb();

        $this->crud->addColumn([
            'name' => 'name',
            'label' => 'Название'
        ]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(GalleryRequest::class);

        // TODO: remove setFromDb() and manually define Fields
        // $this->crud->setFromDb();

        $this->crud->addField([
            'name' => 'name',
            'label' => 'Название',
        ]);
          
        $this->crud->addField([
            'name' => 'slug',
            'label' => 'URL',
            'prefix' => url('/products').'/',
            'hint' => 'По умолчанию будет сгенерирован из названия.'
        ]);

        $this->crud->addField([
            'name' => 'images',
            'label' => 'Изображения',
            'type' => 'gallery_images'
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
