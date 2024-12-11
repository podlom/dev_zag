<?php

namespace App\Observers;

use App\Models\FaqCategory;

class FaqCategoryObserver
{
    public function deleted(FaqCategory $faqcategory)
    {
        $faqcategory->questions()->update(['category_id' => null]);
        $faqcategory->translations()->each(function($translation) {
            $translation->delete();
        });
    }

    public function created(FaqCategory $faqcategory)
    {
        if($faqcategory->original && $faqcategory->original->questions->count()) {
            foreach($faqcategory->original->questions as $faq) {
                $faq->translations()->where('language_abbr', $faqcategory->language_abbr)->update(['category_id' => $faqcategory->id]);
            }
        }
    }

    public function saving(FaqCategory $faqcategory)
    {
        if($faqcategory->original) {
            $faqcategory->is_active = $faqcategory->original->is_active;
        }

        if($faqcategory->translations->count()) {
            $faqcategory->translations()->update([
                'is_active' => $faqcategory->is_active
            ]);
        }
    }
}
