<?php

namespace Aimix\Shop\app\Http\Controllers\Admin\Operations;

use Illuminate\Support\Facades\Route;
use Backpack\LangFileManager\app\Models\Language;
use Aimix\Shop\app\Models\Modification;
use Aimix\Shop\app\Models\AttributeModification;

trait TranslateOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupTranslateRoutes($segment, $routeName, $controller)
    {
        Route::get($segment.'/{id}/translate', [
            'as'        => $routeName.'.translate',
            'uses'      => $controller.'@translate',
            'operation' => 'translate',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupTranslateDefaults()
    {
        $this->crud->allowAccess('translate');

        $this->crud->operation('translate', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });
        $this->crud->operation('list', function () {
            // $this->crud->addButton('top', 'translate', 'view', 'crud::buttons.translate');
            $this->crud->addButton('line', 'translate', 'view', 'crud::buttons.translate', 'end');
        });
    }

    /**
     * Show the view for performing the operation.
     *
     * @return Response
     */
    public function translate($id)
    {
        $this->crud->hasAccessOrFail('translate');

        $model = get_class($this->crud->model);
        $item = $this->crud->getCurrentEntry();

        $languages = Language::getActiveLanguagesNames();
        unset($languages[$item->language_abbr]); // remove current language
        
        foreach($languages as $abbr => $lang) {
            if($item->translations->where('language_abbr', $abbr)->first()) // skip if this translation already exists
                continue;
            
            $newItem = new $model;
            $newItem->language_abbr = $abbr;
            $newItem->original_id = $item->id;

            if($item->category) {
                $translatedCategory = $item->category->translations->where('language_abbr', $abbr)->first();

                if(!$translatedCategory) { // check if category translation exists
                    \Alert::error('Сначала переведите категорию')->flash();
    
                    return back();
                }

                $newItem->category_id = $translatedCategory->id;
            }

            if($item->brand_id) {
                $translatedBrand = $item->brand->translations->where('language_abbr', $abbr)->first();

                if(!$translatedBrand) { // check if brand translation exists
                    \Alert::error('Сначала переведите компанию')->flash();
    
                    return back();
                }

                $newItem->brand_id = $translatedBrand->id;
            }

            if($item->product) {
                $translatedProduct = $item->product->translations->where('language_abbr', $abbr)->first();

                if(!$translatedProduct) { // check if product translation exists
                    \Alert::error('Сначала переведите объект')->flash();
    
                    return back();
                }

                $newItem->product_id = $translatedProduct->id;
            }

            $newItem->save();
        }
        
        
        // load the view
        // return view("crud::operations.translate", $this->data);

        \Alert::success('Перевод успешно создан')->flash();

        return back();
    }
}
