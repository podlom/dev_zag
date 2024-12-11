<?php

namespace Aimix\Banner\app\Http\Controllers\Admin;

use Aimix\Banner\app\Http\Requests\BannerRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

use Backpack\LangFileManager\app\Models\Language;

/**
 * Class BannerCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class BannerCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CloneOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ReorderOperation;
    use \Aimix\Shop\app\Http\Controllers\Admin\Operations\TranslateOperation;
    
    private $languages = 'ru';
    private $default_language = 'ru';

    public function setup()
    {
        $this->crud->setModel('Aimix\Banner\app\Models\Banner');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/banner');
        $this->crud->setEntityNameStrings('баннер', 'Баннеры');
        
        if(config('aimix.aimix.enable_languages')) {
          $this->languages = Language::getActiveLanguagesNames();
          $this->default_language = Language::where('default', 1)->first()->abbr;
          
          $this->crud->query = $this->crud->query->withoutGlobalScopes();
          $this->crud->model->clearGlobalScopes();
        }
    }

    protected function setupReorderOperation()
    {
        // define which model attribute will be shown on draggable elements 
        $this->crud->set('reorder.label', 'title');
        // define how deep the admin is allowed to nest the items
        // for infinite levels, set it to 0
        $this->crud->set('reorder.max_level', 1);
    }

    protected function setupListOperation()
    {
        // TODO: remove setFromDb() and manually define Columns, maybe Filters
        // $this->crud->setFromDb();
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
        
        $this->crud->addColumns([
            [
								'name' => 'title',
								'label' => 'Заголовок',
						],
						[
								'name' => 'image',
								'label' => 'Изображение',
								'type' => 'image'
						],
        ]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(BannerRequest::class);

        // TODO: remove setFromDb() and manually define Fields
				// $this->crud->setFromDb();
      if(config('aimix.aimix.enable_languages')) {
        // $this->crud->addField([
        //   'name' => 'language_abbr',
        //   'label' => 'Язык',
        //   'type' => 'select_from_array',
        //   'options' => $this->languages
        // ]);
        $this->crud->addField([
          'name' => 'language_abbr',
          'label' => 'Язык',
          'default' => $this->default_language,
          'attributes' => [
            'readonly' => 'readonly',
          ]
        ]);

        $this->crud->addField([
          'name' => 'original_id',
          'label' => 'Оригинал/Переводы',
          'type' => 'translation',
          'model' => 'banner',
        ]);
      }
				
				$this->crud->addFields([
					[
						'name' => 'title',
						'label' => 'Заголовок',
					],
					[
						'name' => 'image',
						'label' => 'Изображение',
            'type' => 'browse',
            'hint' => 'Рекомендованный размер 1115х620'
					],
					// [
					// 	'name' => 'short_desc',
					// 	'label' => 'Краткое описание',
					// ],
					[
						'name' => 'short_desc',
            'label' => 'Цена',
            'prefix' => 'от',
            'suffix' => 'грн/кв.м'
					],
					// [
					// 	'name' => 'desc',
					// 	'label' => 'Описание',
					// 	'type' => 'ckeditor',
          // ],
          [
						'name' => 'button_text',
						'label' => 'Текст на кнопке',
					],
					[
						'name' => 'link',
						'label' => 'Ссылка',
					],
				]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
