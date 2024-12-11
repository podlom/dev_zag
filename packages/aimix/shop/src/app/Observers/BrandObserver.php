<?php

namespace Aimix\Shop\app\Observers;

use Aimix\Shop\app\Models\Brand;
use App\Region;
use App\Area;
use App\City;

class BrandObserver
{
  public function saving(Brand $brand){
    if($brand->original) {
      $brand->created_at = $brand->original->created_at;
      $brand->images = $brand->original->images;
      $brand->contacts = $brand->original->contacts;
      $brand->is_popular = $brand->original->is_popular;
      $brand->is_active = $brand->original->is_active;
      $brand->is_parsed = $brand->original->is_parsed;
      $brand->address = $brand->original->address;
    } else {
      if($brand->address != $brand->getOriginal('address') || (!isset($brand->getOriginal('extras_translatable')['address_string']) && isset($brand->extras_translatable['address_string']) && $brand->extras_translatable['address_string']) || (isset($brand->getOriginal('extras_translatable')['address_string']) && $brand->extras_translatable['address_string'] != $brand->getOriginal('extras_translatable')['address_string'])) {
          $ad = $brand->address;
          $city = \App\City::withoutGlobalScopes()->where('city_id', $brand->address['city'])->where('language_abbr', 'uk')->first();
          $region = \App\Region::withoutGlobalScopes()->where('language_abbr', 'uk')->where('region_id', $brand->address['region'])->first();
              if(!$city || !$region)
                  return;

          $address = isset($brand->extras_translatable['address_string']) && $brand->extras_translatable['address_string']? $city->name . ', ' . $brand->extras_translatable['address_string'] . ', ' . $region->name . ' область' : $city->name . ', ' . $region->name . ' область';
          
          $geo = \Geocoder::getCoordinatesForAddress($address . ' Украина');
          
          $ad['latlng'] = [
              'lat' => $geo['lat'],
              'lng' => $geo['lng']
          ];
  
          $brand->address = $ad;
      }
    }

    $brand->translations()->update([
      'created_at' => $brand->created_at,
      'images' => $brand->images,
      'contacts' => $brand->contacts,
      'is_popular' => $brand->is_popular,
      'is_active' => $brand->is_active,
      'is_parsed' => $brand->is_parsed,
      'address' => $brand->address
    ]);
  }

    public function updated(Brand $brand){
        foreach($brand->translations as $item) {
          if($brand->category->translations->where('language_abbr', $item->language_abbr)->first())
            $item->category()->associate($brand->category->translations->where('language_abbr', $item->language_abbr)->first())->save();
        }
    }

    public function deleted(Brand $brand)
    {
        $brand->translations()->delete();
    }
}
