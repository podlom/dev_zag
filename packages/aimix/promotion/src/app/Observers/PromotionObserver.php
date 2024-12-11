<?php

namespace Aimix\Promotion\app\Observers;

use Aimix\Promotion\app\Models\Promotion;

class PromotionObserver
{
  public function saving(Promotion $promotion){
    (new Promotion)->clearGlobalScopes();
    if($promotion->original) {
      $promotion->image = $promotion->original->image;
      $promotion->start = $promotion->original->start;
      $promotion->end = $promotion->original->end;
      $promotion->is_parsed = $promotion->original->is_parsed;
      $promotion->is_active = $promotion->original->is_active;
    } 

    $promotion->translations()->update([
      'image' => $promotion->image,
      'start' => $promotion->start,
      'end' => $promotion->end,
      'is_parsed' => $promotion->is_parsed,
      'is_active' => $promotion->is_active,
    ]);

    foreach($promotion->translations as $item) {
      $item->product()->associate($promotion->product->translations->where('language_abbr', $item->language_abbr)->first())->save();
    }

    if($promotion->original) {
      $promotion->product()->associate($promotion->original->product->translations->where('language_abbr', $promotion->language_abbr)->first());
    }
  }

    public function deleted(Promotion $promotion)
    {
        $promotion->translations()->delete();
    }
}