<?php

namespace Aimix\Shop\app\Observers;

use Aimix\Shop\app\Models\Category;

class CategoryObserver
{
    public function saving(Category $category){
      if($category->original) {
        $category->image = $category->original->image;
        $category->is_popular = $category->original->is_popular;
      } 

      $category->translations()->update([
        'image' => $category->image,
        'is_popular' => $category->is_popular
      ]);
    }
    
    public function deleted(Category $category)
    {
        // $category->products()->update(['category_id' => null]);
        $category->translations()->each(function($translation) {
            $translation->delete();
        });
    }

    // public function created(Category $category)
    // {
    //     if($category->original && $category->original->brands->count()) {
    //         foreach($category->original->products as $product) {
    //             $product->translations()->where('language_abbr', $category->language_abbr)->update(['category_id' => $category->id]);
    //         }
    //     }
    // }
}
