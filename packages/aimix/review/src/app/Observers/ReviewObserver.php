<?php

namespace Aimix\Review\app\Observers;


use Aimix\Review\app\Models\Review;
use Aimix\Shop\app\Models\Product;


class ReviewObserver
{
  public function created(Review $review)
  {
    /* if ($review->language_abbr != 'ru') // remove from package
      return; */

    $product = $review->product;

    $this->updateProductRating($product);
  }

  public function saving(Review $review)
  { // remove from package
    if ($review->original) {
        $review->is_moderated = $review->original->is_moderated;
        $review->reviewable_type = $review->original->reviewable_type;
        // @ts edited on 2024-11-22
        // $review->reviewable_id = $review->original->reviewable? $review->original->reviewable->translations->where('language_abbr', $review->language_abbr)->first()->id : null;
        $review->reviewable_id = $review->original->reviewable? null : null;
        $review->type = $review->original->type;
        $review->file = $review->original->file;
        $review->rating = $review->original->rating;
        $review->email = $review->original->email;
    }

    if ($review->translations->count()) {
        $review->translations()->update([
            'is_moderated' => $review->is_moderated,
            'reviewable_type' => $review->reviewable_type,
            'reviewable_id' => $review->reviewable? $review->reviewable->translations->first()->id : null,
            'type' => $review->type,
            'file' => $review->file,
            'rating' => $review->rating,
            'email' => $review->email,
        ]);
    }

    if ($review->rating && ($review->type == 'cottage' || $review->type == 'newbuild') && $review->is_moderated && !$review->rating_added) {
      $review->rating_added = true;
      Product::where('id', $review->reviewable_id)->orWhere('original_id', $review->reviewable_id)->each(function($item) use ($review) {
        $item->update(['old_rating' => $item->old_rating + ($review->rating / 2), 'old_rating_count' => $item->old_rating_count + 1]);
      });
    }
}

  public function updated(Review $review)
  {
    if (!$review->reviewable || class_basename(get_class($review->reviewable)) != 'Product')
      return;

    $product = $review->reviewable;
    $productId = $product? $product->id : null;
    $originalProductId = $review->getOriginal()['reviewable_id'];

    if($originalProductId && $originalProductId != $productId) {
      $this->updateProductRating(Product::find($originalProductId));
    }

    $this->updateProductRating($product);
  }

  public function deleted(Review $review) {
    $product = $review->product;

    $this->updateProductRating($product);
  }

  public function updateProductRating($product) {

    if($product == null)
      return;

    $productReviews = $product->reviews->where('rating', '!=', null);
    $averageRating = 0;

    if(!count($productReviews)) {
      $product->rating = null;
      $product->save();
      return;
    }

    foreach($productReviews as $review) {
      $averageRating += $review->rating;
    }

    $averageRating = $averageRating / count($productReviews);
    $product->rating = round($averageRating, 1);
    $product->save();
  }

}
