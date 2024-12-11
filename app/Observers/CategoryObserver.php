<?php

namespace App\Observers;

use Backpack\NewsCRUD\app\Models\Category;

class CategoryObserver
{
    public function deleted(Category $category)
    {
        $category->articles()->update(['category_id' => null]);
        $category->children()->update(['parent_id' => null]);
        $category->translations()->each(function($translation) {
            $translation->delete();
        });
    }

    public function saving(Category $category){
        (new Category)->clearGlobalScopes();
        if($category->original && $category->original->parent) {
          $category->parent_id = $category->original->parent->translations->where('language_abbr', $category->language_abbr)->first()->id;
        } 
        
        if($category->parent) {
            $category->translations()->update([
            'parent_id' => $category->parent->translations->where('language_abbr', 'uk')->first()? $category->parent->translations->where('language_abbr', 'uk')->first()->id : null,
            ]);
        } else {
            $category->translations()->update([
                'parent_id' => null
            ]);
        }

        if($category->original) {
            $category->image = $category->original->image;
            $category->is_active = $category->original->is_active;
            $category->faq_category_id = $category->original->faq_category && $category->original->faq_category->translations->count()? $category->original->faq_category->translations->first()->id : null;
        }

        if($category->translations->count()) {
            $category->translations()->update([
                'image' => $category->image,
                'is_active' => $category->is_active,
                'faq_category_id' => $category->faq_category && $category->faq_category->translations->count()? $category->faq_category->translations->first()->id : null
            ]);
        }
    }

    public function created(Category $category)
    {
        if($category->original && $category->original->articles->count()) {
            foreach($category->original->articles as $faq) {
                $faq->translations()->where('language_abbr', $category->language_abbr)->update(['category_id' => $category->id]);
            }
        }
    }
}
