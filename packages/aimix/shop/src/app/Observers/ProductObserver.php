<?php

namespace Aimix\Shop\app\Observers;

use Aimix\Shop\app\Models\Product;
use Aimix\Shop\app\Models\Modification;
use Aimix\Shop\app\Models\AttributeModification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Notification;
use App\Region;
use App\Area;
use App\City;

class ProductObserver
{
    private $product;
    
    public function created(Product $product)
    {
        if($product->original) {
            foreach($product->original->modifications as $mod) {
                $newMod = new Modification;
                $newMod->product_id = $product->id;
                $newMod->original_id = $mod->id;
                $newMod->language_abbr = $product->language_abbr;
                $newMod->code = $mod->code;
                $newMod->name = $mod->name;
                $newMod->price = $mod->price;
                $newMod->old_price = $mod->old_price;
                $newMod->images = $mod->images;
                $newMod->is_default = $mod->is_default;
                $newMod->is_active = $mod->is_active;
                $newMod->in_stock = $mod->in_stock;
                $newMod->extras = $mod->extras;
                $newMod->layouts = $mod->layouts;
                $newMod->save();
        
                foreach($mod->attrs as $attr) {
                    $newAttr = new AttributeModification;
                    $newAttr->attribute_id = $attr->id;
                    $newAttr->modification_id = $newMod->id;
                    $newAttr->value = $attr->pivot->value;
                    $newAttr->save();
                }
            }
        }
    }

    public function saved(Product $product){
      $this->product = $product;
      $new_price = null;
      $noty = null;
      
      // remove from package
      if(!$product->isModificationRelation && $product->getOriginal('extras')){
        foreach($product->modifications_array as $item) {
            
          if(($product->category_id == 2 && !isset($item['is_default'])) || ($product->category_id == 1 && !isset($item['is_default']) && $item['attrs'][1]['value'] == $product->type)) {
            $new_price = !$new_price || $new_price > $item['price']? $item['price'] : $new_price;
          }
        }
        
        // if(!count($product->modifications_array))
        //     $new_price = $product->price;

        if(!$product->original && count($product->modifications->where('is_default', 0)) && ($product->getOriginal('extras')['status'] != $product->extras['status'] || ($new_price && $new_price != $product->price) || $product->getOriginal('is_sold') != $product->is_sold)) {
          $noty = new Notification;
          $noty->type = 'old';
          $noty->product_id = $product->id;
          $noty->old_status = $product->getOriginal('is_sold')? 'sold' : $product->getOriginal('extras')['status'];
          $noty->status = $product->is_sold? 'sold' : $product->extras['status'];
          $noty->price = $new_price? $new_price : $product->price;
          $noty->old_price = $product->price;
          if(!Notification::where('product_id', $noty->product_id)->where('old_status', $noty->old_status)->where('status', $noty->status)->where('price', $noty->price)->where('old_price', $noty->old_price)->first())
            $noty->save();
        else
            $noty = null;
        }
          $product->isModificationRelation = false;
      } elseif($product->getOriginal('extras')) {
          foreach($product->modifications_array as $item) {
            if(($product->category_id == 2 && !isset($item['is_default'])) || ($product->category_id == 1 && !isset($item['is_default']) && $item['attrs'][1]['value'] == $product->type)) {
              $new_price = !$new_price || $new_price > $item['price']? $item['price'] : $new_price;
            }
          }
        if(!$product->original && count($product->modifications->where('is_default', 0)) && ($product->getOriginal('extras')['status'] != $product->extras['status'] || ($new_price && $new_price != $product->price) || $product->getOriginal('is_sold') != $product->is_sold)) {
          $noty = new Notification;
          $noty->type = 'old';
          $noty->product_id = $product->id;
          $noty->old_status = $product->getOriginal('is_sold')? 'sold' : $product->getOriginal('extras')['status'];
          $noty->status = $product->is_sold? 'sold' : $product->extras['status'];
          $noty->price = $new_price? $new_price : $product->price;;
          $noty->old_price = $product->price;
          if(!Notification::where('product_id', $noty->product_id)->where('old_status', $noty->old_status)->where('status', $noty->status)->where('price', $noty->price)->where('old_price', $noty->old_price)->first())
            $noty->save();
        else
            $noty = null;
        }
      }

      // end remove from package

      if(!$product->isModificationRelation){
        if($product->modifications_array)
          $product->modifications_array[0]['images'] = $product->images_array;
        
          $this->updateOrCreateModification($product->modifications_array);
      }

      // remove from package
      
      if(!$product->original && count($product->modifications->where('is_default', 0)) && $product->is_active && !Notification::where('product_id', $product->id)->where('type', 'new')->first()) {
        $noty = new Notification;
        $noty->type = 'new';
        $noty->product_id = $product->id;
        $noty->old_status = $product->extras['status'];
        $noty->status = $product->extras['status'];
        $noty->price = $product->price;
        $noty->old_price = $product->price;
        // if(!Notification::where('product_id', $noty->product_id)->where('old_status', $noty->old_status)->where('status', $noty->status)->where('price', $noty->price)->where('old_price', $noty->old_price)->first())
            $noty->save();
        // else
        //     $noty = null;
      }

      // end remove from package

      foreach($product->translations as $item) {
        $item->update([
          'created_at' => $product->created_at,
          'image' => $product->image,
          'is_hit' => $product->is_hit,
          'is_sold' => $product->is_sold, // remove
          'is_new' => $product->is_new,
          'is_parsed' => $product->is_parsed,
          'is_recommended' => $product->is_recommended,
          'is_active' => $product->is_active,
          'extras' => $product->extras,
          'address' => $product->address, // remove
          'category_id' => $product->category->translations->where('language_abbr', $item->language_abbr)->first()->id,
          'brand_id' => $product->brand? $product->brand->translations->where('language_abbr', $item->language_abbr)->first()->id : 0,
          'top_rating' => $product->top_rating
        ]);
        // $item->baseModification->update(['images' => $product->baseModification->images]);
      }

      if($product->category_id == 1)
        $this->calcCottageRating($product);
      elseif($product->category_id == 2)
        $this->calcNewbuildRating($product);
    }
    
    public function updateOrCreateModification($modifications) {
      foreach($modifications as $key => $modification){
        if(isset($modification['images'])) 
          $modification['images'] = $this->setImages($modification['images']);
        else
            $modification['images'] = [];

        if($key != 0 && isset($modification['layouts'])) 
            $modification['layouts'] = $this->setLayouts($modification['layouts']);
        else
            $modification['layouts'] = [];

        $this->product->modifications()->updateOrCreate([
          'id' => $modification['id'],
        ], $modification);
      }
    }

    private function setImages($value)
    {
      $images_array = [];
      foreach($value as $key => $img) {
        if(!$img)
            continue;
        if(gettype($img) == 'string') {
          $images_array[$key] = $img;
        } else {
          $extension = $img->getClientOriginalExtension();
          Storage::disk('common')->put('uploads/' . $img->getFilename().'.'.$extension,  File::get($img));
          $images_array[$key] = 'uploads/' . $img->getFilename().'.'.$extension;
        }
      }

      return $images_array;
    }

    private function setLayouts($value)
    {
      $layouts_array = [];
      foreach($value as $key => $item) {
        if(!$item || !$item['image'])
            continue;
        $layouts_array[$key] = [];
        $layouts_array[$key]['name'] = $item['name'];
        if(gettype($item['image']) == 'string') {
          $layouts_array[$key]['image'] = $item['image'];
        } else {
          $extension = $item['image']->getClientOriginalExtension();
          Storage::disk('common')->put('uploads/' . $item['image']->getFilename().'.'.$extension,  File::get($item['image']));
          $layouts_array[$key]['image'] = 'uploads/' . $item['image']->getFilename().'.'.$extension;
        }
      }

      return $layouts_array;
    }
    
    public function deleting(Product $product) {
      $product->notifications()->delete(); // remove

      foreach($product->modifications as $modification){
        $modification->attrs()->detach();
        $modification->translations()->delete();
      }
      
      $product->translations()->delete();
      $product->modifications()->delete();
    }

    public function saving(Product $product){
      if($product->original) {
        $product->clearGlobalScopes(); // to find other language category
        
        $product->image = $product->original->image;
        $product->is_hit = $product->original->is_hit;
        $product->created_at = $product->original->created_at;
        $product->is_sold = $product->original->is_sold; // remove
        $product->is_new = $product->original->is_new;
        $product->is_parsed = $product->original->is_parsed;
        $product->is_recommended = $product->original->is_recommended;
        $product->is_active = $product->original->is_active;
        $product->address = $product->original->address; // remove
        $product->category_id = $product->original->category->translations->where('language_abbr', $product->language_abbr)->first()->id;
        $product->brand_id = $product->original->brand && $product->original->brand->translations->where('language_abbr', $product->language_abbr)->first()? $product->original->brand->translations->where('language_abbr', $product->language_abbr)->first()->id : 0;
        $product->extras = $product->original->extras;
        $product->top_rating = $product->original->top_rating;
      } else {
        if($product->address != $product->getOriginal('address') || (!isset($product->getOriginal('extras_translatable')['address_string']) && isset($product->extras_translatable['address_string']) && $product->extras_translatable['address_string']) || (isset($product->getOriginal('extras_translatable')['address_string']) && $product->extras_translatable['address_string'] != $product->getOriginal('extras_translatable')['address_string'])) {
            $ad = $product->address;
            $city = \App\City::withoutGlobalScopes()->where('city_id', $product->address['city'])->where('language_abbr', 'uk')->first();
            $region = \App\Region::withoutGlobalScopes()->where('language_abbr', 'uk')->where('region_id', $product->address['region'])->first();
                if(!$city || !$region)
                    return;

            $address = isset($product->extras_translatable['address_string']) && $product->extras_translatable['address_string']? $city->name . ', ' . $product->extras_translatable['address_string'] . ', ' . $region->name . ' область' : $city->name . ', ' . $region->name . ' область';
            
            $geo = \Geocoder::getCoordinatesForAddress($address . ' Украина');

            $ad['latlng'] = [
                'lat' => $geo['lat'],
                'lng' => $geo['lng']
            ];
    
            $product->address = $ad;
        }
      }
    }

    public function calcCottageRating(Product $item){
      if(!$item->notBaseModifications->first())
          return;

      $rating = 0;
      
      // type
      $type = $item->type;
      
      switch ($type) {
          case 'Коттедж':
              $rating += 4;
          break;
          case 'Таунхаус':
              $rating += 3;
          break;
          case 'Дуплекс':
              $rating += 3;
          break;
          case 'Квадрекс':
              $rating += 3;
          break;
          case 'Эллинг':
              $rating += 2;
          break;
          case 'Вилла':
              $rating += 5;
          break;
          case 'Земельный участок':
              $rating += 2;
          break;
      }
      
      // status
      $status = $item->extras['status'];
      switch ($status) {
        case 'sold':
        case 'done':
            $rating += 5;
        break;
        case 'building':
            $rating += 3;
        break;
        case 'project':
            $rating += 2;
        break;
      }
      
      // region
      if($item->address['region'] == 29) // Киев
          $rating += 5;
      else
          $rating += 4;

      // distance
      $distance = $item->extras['distance'];
      if($distance <= 20)
          $rating += 7;
      elseif($distance <= 30)
          $rating += 6;
      elseif($distance <= 40)
          $rating += 5;
      elseif($distance <= 50)
          $rating += 4;
      elseif($distance <= 70)
          $rating += 3;
      elseif($distance <= 100)
          $rating += 2;
      else
          $rating += 1;

      // total objects
      $total = 0;
      foreach($item->notBaseModifications->get() as $mod) {
        if(!$mod->attrs->find(6) || !$mod->attrs->find(6) || !$mod->attrs->find(6))
            continue;

        $total += $mod->attrs->find(6)->pivotValue + $mod->attrs->find(7)->pivotValue + $mod->attrs->find(8)->pivotValue;
      }
      
      if($total < 50)
          $rating += 1;
      elseif($total < 100)
          $rating += 2;
      elseif($total < 200)
          $rating += 3;
      elseif($total >= 200)
          $rating += 4;

      // area
      $area = $item->notBaseModifications->first()->area;
      
      if($area < 120)
          $rating += 2;
      elseif($area < 200)
          $rating += 3;
      elseif($area < 300)
          $rating += 4;
      elseif($area >= 300)
          $rating += 5;

      // floors
      $floors = $item->notBaseModifications->first()->floors;
      
      switch ($floors) {
          case 1:
              $rating += 1;
          break;
          case 2:
              $rating += 2;
          break;
          case 6:
          case 5:
          case 4:
          case 3:
              $rating += 3;
          break;
      }

      
      // price
      $price = $item->notBaseModifications->where('price', '!=', 0)->min('price');
      
      if($price == 0)
          $rating += 2;
      elseif($price <= 13500)
          $rating += 4;
      elseif($price <= 54000)
          $rating += 3;
      else
          $rating += 2;
          
      // infrastructure
      $infrastructure = isset($item->extras_translatable['infrastructure'])? $item->extras_translatable['infrastructure'] : '';
      
      $keywords = ['автомойка', 'парковка', 'аптека', 'скважина', 'банк', 'бассейн', 'бильярд', 'боулинг', 'выставочный центр', 'гараж', 'гольф', 'детсад', 'детский сад', 'детские площадки', 'детская площадка', 'казино', 'кино', 'баня', 'сауна', 'ледовый стадион', 'каток', 'медпункт', 'медицинский центр', 'мини-отель', 'минимаркет', 'супермаркет', 'магазин', 'ночной клуб', 'офисы', 'паркинг', 'парикмахерская', 'салон красоты', 'пляж', 'пожарная', 'прачечная', 'ресторан', 'бар', 'кафе', 'салон красоты', 'парная', 'спортивные площадки', 'спортивная площадка', 'СТО', 'теннис', 'фитнес', 'спортивный центр', 'спортивный зал', 'химчистка', 'храм', 'церковь', 'школа', 'школу', 'лицей', 'яхт-клуб', 'ТРЦ', 'индивидуальное отопление', 'металлопластиковые окна', 'бронированные двери', 'автономное энергообеспечение', 'частная клиника', 'охрана', 'видеонаблюдение'];

      foreach($keywords as $word) {
          if(strpos($infrastructure, $word) != false)
              $rating += 1;
      }

      // wall material
      $wall = $item->extras['wall_material'];
      
      switch($wall) {
          case 11:
          case null:
              $rating += 2;
          break;
          case 1:
          case 5:
          case 3:
              $rating += 5;
          break;
          default:
              $rating += 3;
      }

      // roof material
      $roof = $item->extras['roof_material'];

      switch($roof) {
          case null:
          case 1:
              $rating += 1;
          break;
          case 6:
              $rating += 2;
          break;
          case 5:
          case 4:
              $rating += 4;
          break;
          case 2:
          case 3:
              $rating += 3;
          break;
      }


      // communications
      if(isset($item->extras['communications'])) {
          $communications = $item->extras['communications'];
          $rating += (2 * count($communications));
      }
      if($item->top_rating != $rating) {
        $item->update(['top_rating' => $rating]);
        $item->translations()->update(['top_rating' => $rating]);
      }
    }

    public function calcNewbuildRating(Product $item){
      if(!$item->notBaseModifications->first())
          return;

      $rating = 0;
      
      // type
      $type = $item->type;
      
      switch ($type) {
          case 'Квартира':
              $rating += 2;
          break;
          case 'Апартаменты':
              $rating += 3;
          break;
      }
      
      // status
      $status = $item->extras['status'];
      switch ($status) {
          case 'sold':
          case 'done':
              $rating += 5;
          break;
          case 'building':
              $rating += 3;
          break;
          case 'project':
              $rating += 2;
          break;
      }
      
      // region
      if($item->address['region'] == 29) // Киев
          $rating += 5;
      else
          $rating += 4;

      // distance
      $distance = $item->extras['distance'];
      if($distance <= 10)
          $rating += 8;
      elseif($distance <= 20)
          $rating += 7;
      elseif($distance <= 30)
          $rating += 6;
      elseif($distance <= 40)
          $rating += 5;
      elseif($distance <= 50)
          $rating += 4;
      elseif($distance <= 70)
          $rating += 3;
      elseif($distance <= 100)
          $rating += 2;
      else
          $rating += 1;

      // total objects
      $total = 0;
      foreach($item->notBaseModifications->get() as $mod) {
          if(!$mod->attrs->find(6) || !$mod->attrs->find(6) || !$mod->attrs->find(6))
            continue;

          $total += $mod->attrs->find(6)->pivotValue + $mod->attrs->find(7)->pivotValue + $mod->attrs->find(8)->pivotValue;
      }
      
      if($total < 50)
          $rating += 1;
      elseif($total < 200)
          $rating += 2;
      elseif($total < 400)
          $rating += 3;
      elseif($total >= 400)
          $rating += 4;

      // area
      $area = $item->notBaseModifications->first()->area;
      
      if($area < 50)
          $rating += 2;
      elseif($area < 100)
          $rating += 3;
      elseif($area < 200)
          $rating += 4;
      elseif($area >= 200)
          $rating += 5;

      // floors
      $floors = $item->extras['floors'];
      
      if($floors <= 5)
          $rating += 1;
      elseif($floors <= 12)
          $rating += 3;
      elseif($floors > 12)
          $rating += 2;
      
      // price
      $price = $item->notBaseModifications->where('price', '!=', 0)->min('price');
      
      if($price == 0)
          $rating += 3;
      elseif($price <= 13500)
          $rating += 6;
      elseif($price <= 27000)
          $rating += 5;
      elseif($price <= 54000)
          $rating += 4;
      else
          $rating += 3;
          
      // infrastructure
      $infrastructure = isset($item->extras_translatable['infrastructure'])? $item->extras_translatable['infrastructure'] : '';
      
      $keywords = ['автомойка', 'парковка', 'аптека', 'скважина', 'банк', 'бассейн', 'бильярд', 'боулинг', 'выставочный центр', 'гараж', 'гольф', 'детсад', 'детский сад', 'детские площадки', 'детская площадка', 'казино', 'кино', 'баня', 'сауна', 'ледовый стадион', 'каток', 'медпункт', 'медицинский центр', 'мини-отель', 'минимаркет', 'супермаркет', 'магазин', 'ночной клуб', 'офисы', 'паркинг', 'парикмахерская', 'салон красоты', 'пляж', 'пожарная', 'прачечная', 'ресторан', 'бар', 'кафе', 'салон красоты', 'парная', 'спортивные площадки', 'спортивная площадка', 'СТО', 'теннис', 'фитнес', 'спортивный центр', 'спортивный зал', 'химчистка', 'храм', 'церковь', 'школа', 'школу', 'лицей', 'яхт-клуб', 'ТРЦ', 'индивидуальное отопление', 'металлопластиковые окна', 'бронированные двери', 'автономное энергообеспечение', 'частная клиника', 'охрана', 'видеонаблюдение'];

      foreach($keywords as $word) {
          if(strpos($infrastructure, $word) != false)
              $rating += 1;
      }

      // wall material
      $wall = $item->extras['wall_material'];
      
      switch($wall) {
          case 14:
          case 21:
              $rating += 4;
          break;
          case 3:
              $rating += 5;
          break;
          case 6:
          case 20:
              $rating += 3;
          break;
          default:
              $rating += 2;
      }


      // communications
      if(isset($item->extras['communications'])) {
          $communications = $item->extras['communications'];
          $rating += (2 * count($communications));
      }

      if($item->top_rating != $rating) {
        $item->update(['top_rating' => $rating]);
        $item->translations()->update(['top_rating' => $rating]);
      }
    }
}
