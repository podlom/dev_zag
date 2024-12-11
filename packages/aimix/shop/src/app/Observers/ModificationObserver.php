<?php

namespace Aimix\Shop\app\Observers;

use Aimix\Shop\app\Models\Modification;
use Aimix\Shop\app\Models\AttributeModification;

class ModificationObserver
{
  public function saved(Modification $modification) {
    if($modification->attributes_array && count($modification->attributes_array))
    $modification->attrs()->sync($modification->attributes_array);

    $modification->translations()->update([
      'images' => $modification->images,
      // 'layouts' => $modification->layouts,
      'price' => $modification->price,
      'old_price' => $modification->old_price,
      'is_active' => $modification->is_active,
      // 'is_pricehidden' => $modification->is_pricehidden,
      // 'in_stock' => $modification->in_stock,
      'code' => $modification->code,
    ]);
    
    if($modification->translations->count()) {
      $attrArray = [];
      
      foreach($modification->attrs as $attr) {
        $val = $attr->pivotValue;
        if(($attr->id == 7 || $attr->id == 6 || $attr->id == 8) && !$val)
          $val = 0;
          
          $attrArray[$attr->id] = ['value' => $val];
      }

      foreach($modification->translations() as $item) {
        $item->attrs()->sync($attrArray);
      }
    }

    $modification->refresh();
    
    if($modification->product && $modification->product->translations->count() && !$modification->translations->count() && count($modification->attrs)) {
      $product_translation = $modification->product->translations->first();

      $mod_translation = new Modification;
      $mod_translation->original_id = $modification->id;
      $mod_translation->product_id = $product_translation->id;
      $mod_translation->language_abbr = $product_translation->language_abbr;
      $mod_translation->code = $modification->code;
      $mod_translation->name = $modification->trans_name? $modification->trans_name : str_replace('-комнатная', '-к', $modification->name);
      $mod_translation->slug = $this->makeUniqueSlug($mod_translation->name);
      $mod_translation->price = $modification->price;
      $mod_translation->old_price = $modification->old_price;
      $mod_translation->images = $modification->images;
      $mod_translation->is_default = $modification->is_default? 1 : 0;
      $mod_translation->is_active = $modification->is_active;
      $mod_translation->in_stock = $modification->in_stock;
      $mod_translation->extras = $modification->extras;
      $mod_translation->layouts = $modification->layouts; // remove
      $mod_translation->save();

      foreach($modification->attrs as $attr) {
        $newAttr = new AttributeModification;
        $newAttr->attribute_id = $attr->id;
        $newAttr->modification_id = $mod_translation->id;
        $newAttr->value = $attr->pivot->value;
        $newAttr->save();
      }
    }

    if($modification->original) {

      $attrArray = [];

      foreach($modification->original->attrs as $attr) {
        $val = $attr->pivotValue;
        if(($attr->id == 7 || $attr->id == 6 || $attr->id == 8) && !$val)
          $val = 0;

        $attrArray[$attr->id] = ['value' => $val];
      }
      
      $modification->attrs()->sync($attrArray);
    }
  }

  
  public function saving(Modification $modification) {
    if($modification->product)
      $modification->language_abbr = $modification->product->language_abbr;

    if($modification->original) {
      $modification->images = $modification->original->images;
      // $modification->layouts = $modification->original->layouts;
      $modification->price = $modification->original->price;
      $modification->old_price = $modification->original->old_price;
      $modification->is_active = $modification->original->is_active;
      // $modification->is_pricehidden = $modification->original->is_pricehidden;
      // $modification->in_stock = $modification->original->in_stock;
      $modification->code = $modification->original->code;
    }
  }
  
  public function deleting(Modification $modification) {
    if($modification->images) {
      foreach($modification->images as $img) {
        \Storage::disk('common')->delete($img);
      }
    }

    if($modification->layouts) {
      foreach($modification->layouts as $layout) {
        \Storage::disk('common')->delete($layout['image']);
      }
    }
    
    $modification->translations()->delete();
  }

  private function makeUniqueSlug($name)
  {
    $slug = \Str::slug($name) . now()->timestamp;

    if(Modification::withoutGlobalScopes()->where('slug', $slug)->first())
      return $this->makeUniqueSlug($name . rand(0, 99));
    else
      return $slug;
  }
}
