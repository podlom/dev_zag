<?php

namespace App\Observers;

use App\Models\Faq;

class FaqObserver
{
    public function updated(Faq $faq){
        foreach($faq->translations as $item) {
            $item->category()->associate($faq->category->translations->where('language_abbr', $item->language_abbr)->first())->save();
        }
    }

    public function deleted(Faq $faq)
    {
        $faq->translations()->delete();
    }
}
