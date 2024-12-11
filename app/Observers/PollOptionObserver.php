<?php

namespace App\Observers;
use App\Models\PollOption;

class PollOptionObserver
{
    public function deleted(PollOption $option)
    {
        $option->translations()->delete();
    }

    public function saving(PollOption $option){
        if($option->original) {
            $option->is_active = $option->original->is_active;
        }

        if($option->translations->count()) {
            $option->translations()->update([
                'is_active' => $option->is_active,
            ]);
        }
    }

    public function updated(PollOption $option){
        foreach($option->translations as $item) {
            $item->question()->associate($option->question->translations->where('language_abbr', $item->language_abbr)->first())->save();
        }
    }

    public function created(PollOption $option){
        if($option->original) {
            $option->question()->associate($option->original->question->translations->where('language_abbr', $option->language_abbr)->first())->save();
        }
    }
}
