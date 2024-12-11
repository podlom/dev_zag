<?php

namespace Aimix\Shop\app\Observers;

use Aimix\Shop\app\Models\BrandCategory;

class BrandCategoryObserver
{
    public function saving(BrandCategory $category){
      if($category->original) {
        $category->image = $category->original->image;
        $category->is_popular = $category->original->is_popular;
        $category->is_active = $category->original->is_active;
      } 

      $category->translations()->update([
        'image' => $category->image,
        'is_popular' => $category->is_popular,
        'is_active' => $category->is_active
      ]);
    }
    
    public function deleted(BrandCategory $category)
    {
        $category->brands()->update(['category_id' => null]);
        $category->translations()->each(function($translation) {
            $translation->delete();
        });
    }

    public function created(BrandCategory $category)
    {
        if($category->original && $category->original->brands->count()) {
            foreach($category->original->brands as $faq) {
                $faq->translations()->where('language_abbr', $category->language_abbr)->update(['category_id' => $category->id]);
            }
        }
    }
}
