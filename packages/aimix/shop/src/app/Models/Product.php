<?php

namespace Aimix\Shop\app\Models;

use Illuminate\Database\Eloquent\Builder;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

use Session;

class Product extends Model
{
    use CrudTrait;
    use Sluggable;
    use SluggableScopeHelpers;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'products';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    protected $casts = [
      'extras' => 'array',
      'extras_translatable' => 'array',
      'address' => 'array', // remove
    ];
    protected $fakeColumns = [
      'sales', 'extras','extras_translatable'
    ];

	  protected $hidden = ['price'];

    public $modifications_array = [];

    public $isModificationRelation = false;
    public $test = [];

    public $images_array = [];
    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

/*
	public function register() {

    }
*/


    protected static function boot()
    {

        parent::boot();
        if(config('aimix.aimix.enable_languages')) {
          static::addGlobalScope('language', function (Builder $builder) {
          	  $language = session()->has('lang')? session()->get('lang'): 'ru';
              $builder->where('products.language_abbr', $language);
          });
        }


        $ids = \App\Region::withoutGlobalScope('active')->withoutGlobalScope('noEmpty')->where('is_active', 0)->pluck('region_id');
        static::addGlobalScope('exclude_regions', function (Builder $builder) use ($ids) {
          $builder->whereNotIn('products.address->region', $ids);
        });
    }

    public function clearGlobalScopes()
    {
        static::$globalScopes = [];
    }

    public function toArray()
    {
      $lang = session()->has('lang')? session()->get('lang') : 'ru';

      return [
        'id' => $this->id,
        'language_abbr' => $this->language_abbr,
        'original_id' => $this->original_id,
        'name' => $this->name,
        'slug' => $this->slug,
        'category_id' => $this->category_id,
        'category_name' => $this->category->name,

        'brand_id' => $this->brand? $this->brand_id : null,
        'brand_name' => $this->brand? $this->brand->name : null,
        'brand_link' => $this->brand? $this->brand->link : null,
        'price' => $this->price,
        'old_price' => isset($this->baseModification, $this->baseModification->old_price) ? $this->baseModification->old_price : null,
        'sale_percent' => $this->salePercent,
        'is_active' => $this->is_active,
        'is_hit' => $this->is_hit,
        'is_new' => $this->is_new,
        'is_recommended' => $this->is_recommended,
        'rating' => $this->true_rating, // $this->rating
        'extras' => $this->extras,
        'extras_translatable' => $this->extras_translatable,
        'image' => url($this->image),
        'images' => isset($this->baseModification, $this->baseModification->images) ? $this->baseModification->images : null,
        'link' => $this->link,
        'amount' => isset($this->amount)? $this->amount : 1,
        'code' => isset($this->code) ? $this->code : null,
        'in_stock' => isset($this->baseModification, $this->baseModification->in_stock) ? $this->baseModification->in_stock : null,
        'description' => nl2br($this->description),
        'discount_amount' => $this->discountAmount,
        // 'attrs' => $this->baseModification->getPluckedAttributesArray(), //WAS COMMENTED

        // remove from package
        'status_string' => $this->status_string,
        'type' => $this->type,
        'address' => $this->address,
        'brand_site' => $this->brand? $this->brand->contacts['site'] : null,
        'brand_phone' => $this->brand? $this->brand->contacts['phone'] : null,
        'region' => $this->region,
        'area' => $this->area,
        'city' => $this->city,
        'communications' => $this->communications_string,
        'infrastructure' => isset($this->extras_translatable['infrastructure'])? $this->extras_translatable['infrastructure'] : '-',
        'wall_material' => isset($this->extras['wall_material'])? __('attributes.wall_materials.' . $this->extras['wall_material']) : '-',
        // 'totalItems' => $this->totalItems, //WAS COMMENTED
        // 'prices' => $this->prices, //WAS COMMENTED
        'top_rating' => $this->top_rating,
        'old_rating_count' => $this->old_rating_count,
        'reviews_rating' => $this->old_rating_count? round($this->old_rating / $this->old_rating_count, 1) : null
      ];
    }


    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'slug_or_name',
            ],
        ];
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function modifications()
    {
      $this->isModificationRelation = true;
      return $this->hasMany('Aimix\Shop\app\Models\Modification');
    }

    public function category()
    {
      return $this->belongsTo('\Aimix\Shop\app\Models\Category', 'category_id');
    }

    public function brand()
    {
      return $this->belongsTo('\Aimix\Shop\app\Models\Brand');
    }

    public function reviews()
    {
        return $this->morphMany('Aimix\Review\app\Models\Review', 'reviewable')->published();
    }

    public function orders()
    {
      return $this->belongsToMany('\Aimix\Shop\app\Models\Order');
    }

    public function translations()
    {
        return $this->hasMany('\Aimix\Shop\app\Models\Product', 'original_id');
    }

    public function original()
    {
        return $this->belongsTo('\Aimix\Shop\app\Models\Product', 'original_id');
    }

    public function promotions()
    {
        return $this->hasMany('\Aimix\Promotion\app\Models\Promotion');
    }

    // remove from package
    public function notifications()
    {
        return $this->hasMany('\App\Notification');
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopeActive($query)
    {
      return $query->where('products.is_active', 1);
    }
    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */
    public function getSlugOrNameAttribute()
    {
        if ($this->slug != '') {
            return $this->slug;
        }
        return $this->name;
    }

    public function getSalesAttribute()
    {
      return json_decode($this->extras['sales']);
    }

    public function getComplectationsAttribute()
    {
      return $this->modifications()->base()->extras['complectations'];
    }

    public function getNotBaseModificationsAttribute()
    {
      return $this->modifications()->notBase();
    }

    public function getBaseModificationAttribute()
    {
      return $this->modifications()->base();
    }

    public function getBaseAttributesAttribute()
    {
      return $this->baseModification->attrs()->important()->get();
    }
    public function getFullnameAttribute()
    {
      return $this->brand->name . ' ' . $this->name;
    }

    // public function getPriceAttribute()
    // {

    //   $price = $this->baseModification->price;
    //   $old_price = $this->baseModification->old_price;

    //   // foreach($this->notBaseModifications->get() as $mod) {
    //   //   if($mod->price && (!$price || $mod->price < $price)) {
    //   //     $price = $mod->price;
    //   //     $old_price = $mod->old_price;
    //   //   }
    //   // }

    //   return $price;
    // }

    public function getOldPriceAttribute()
    {
      // $price = $this->baseModification->price;
      // $old_price = $this->baseModification->old_price;

      // foreach($this->notBaseModifications->get() as $mod) {
      //   if($mod->price && (!$price || $mod->price < $price)) {
      //     $price = $mod->price;
      //     $old_price = $mod->old_price;
      //   }
      // }

        if (isset($this->baseModification, $this->baseModification->old_price)) {
            $old_price = $this->baseModification->old_price;
            return $old_price;
        }

        return null;
    }

    public function getLinkAttribute()
    {
      $category_slug = $this->category->slug;

      return url($this->language_abbr . '/' . $category_slug . '/' . $this->slug);
    }

    public function getImagesAttribute()
    {
      if($this->modifications->where('is_default', 1)->first())
        return $this->modifications->where('is_default', 1)->first()->images;
    }

    public function getSalePercentAttribute()
    {
      return $this->baseModification->first() && $this->baseModification->old_price ? number_format(($this->baseModification->old_price - $this->baseModification->price) * 100 / $this->baseModification->old_price, 0) : null;
    }

    public function getDiscountAmountAttribute()
    {
      return $this->old_price? $this->old_price - $this->price : null;
    }

    public function getCodeAttribute()
    {
        if (isset($this->baseModification, $this->baseModification->code)) {
            return $this->baseModification->code;
        }
        return null;
    }

    // remove from package
    public function getTypeAttribute()
    {
      $lang = session()->has('lang')? session()->get('lang') : 'ru';
      \App::setLocale($lang);
      if($this->category_id === 2 || $this->category_id === 7)
        return __('attributes.newbuild_types.' . $this->extras['newbuild_type']);

      $type = $this->notBaseModifications->get()->groupBy('type_key')->map(function($item) {
        return $item->sum('total');
      })->sort()->keys()->last();

      return $type? __('attributes.cottage_types.' . $type) : null;
    }

    public function getPriceAttribute()
    {
      if(!count($this->modifications->where('is_default', 0)))
        return 0;

      if($this->category_id == 2 || $this->category_id == 7)
        return $this->modifications->where('is_default', 0)->where('price', '>', 0)->min('price');

      // return $this->modifications->where('is_default', 0)->where('price', '>', 0)->min('price');

      if($this->type !== 'Участок' && $this->type !== 'Ділянка') {
        return $this->modifications->where('is_default', 0)->where('type', str_replace('_', ' ', array_flip(__('attributes.cottage_types'))[$this->type]))->where('price', '>', 0)->min('price');
      } elseif($this->type === 'Участок' || $this->type === 'Ділянка') {
        return $this->modifications->where('is_default', 0)->where('type_key', 'Земельный участок')->where('price', '>', 0)->min('price');
      } else {
        return 0;
      }
    }

    public function getMaxPriceAttribute()
    {
      if(!count($this->modifications->where('is_default', 0)))
        return 0;

      if($this->category_id == 2 || $this->category_id == 7)
        return $this->modifications->where('is_default', 0)->where('old_price', '>', 0)->max('old_price');

      // return $this->modifications->where('is_default', 0)->where('old_price', '>', 0)->max('old_price');

      if($this->type !== 'Участок' && $this->type !== 'Ділянка') {
        return $this->modifications->where('is_default', 0)->where('type', str_replace('_', ' ', array_flip(__('attributes.cottage_types'))[$this->type]))->where('old_price', '>', 0)->max('old_price');
      } elseif($this->type === 'Участок' || $this->type === 'Ділянка') {
        return $this->modifications->where('is_default', 0)->where('type_key', 'Земельный участок')->where('old_price', '>', 0)->max('old_price');
      } else {
        return 0;
      }
    }

    public function getStatisticsPriceAttribute()
    {
      if(!count($this->modifications->where('is_default', 0)->where('type_key', '!=', 'Земельный участок')))
        return 0;

      if($this->category_id == 2 || $this->category_id == 7)
        return $this->modifications->where('is_default', 0)->where('price', '>', 0)->min('price');

      return $this->modifications->where('is_default', 0)->where('price', '>', 0)->where('type_key', '!=', 'Земельный участок')->min('price');
    }

    public function getStatisticsPricePlotAttribute()
    {
      if(!count($this->modifications->where('is_default', 0)->where('type_key', 'Земельный участок')))
        return 0;

      return $this->modifications->where('is_default', 0)->where('type_key', 'Земельный участок')->where('price', '>', 0)->min('price');
    }

    public function getStatisticsPricePlotMaxAttribute()
    {
      if(!count($this->modifications->where('is_default', 0)->where('type_key', 'Земельный участок')))
        return 0;

      return $this->modifications->where('is_default', 0)->where('type_key', 'Земельный участок')->where('old_price', '>', 0)->max('old_price');
    }

    public function getTotalItemsAttribute()
    {
      $total = 0;

      foreach($this->modifications->where('is_default', 0)->groupBy('type') as $item) {
        $total += $item->sum('total');
      }

      return $total;
    }

    public function getPricesAttribute()
    {
      return $this->notBaseModifications->get()->groupBy('rooms')->map(function($item) {
        return $item->min('price') * $this->notBaseModifications->where('price', $item->min('price'))->get()->min('area');
      });
    }

    public function getRegionAttribute()
    {
      if(!\App\Region::where('region_id', $this->address['region'])->first())
        return null;

      return \App\Region::where('region_id', $this->address['region'])->first()->name;
    }

    public function getAreaAttribute()
    {
      if(!\App\Area::where('area_id', $this->address['area'])->first())
        return null;

      return \App\Area::where('area_id', $this->address['area'])->first()->name;
    }

    public function getCityAttribute()
    {
	    $city = \App\City::where('city_id', $this->address['city'])->first();

      if(!$city)
        return null;

      return $city->name;
    }


    public function getKyivdistrictAttribute()
    {
      if(!isset($this->address['kyivdistrict']) || !\App\Kyivdistrict::where('kyivdistrict_id', $this->address['kyivdistrict'])->first())
        return null;

      return \App\Kyivdistrict::where('kyivdistrict_id', $this->address['kyivdistrict'])->first()->name;
    }

    public function getAreaM2Attribute()
    {
      return $this->extras['area'];
    }

    public function getAreaMinAttribute()
    {
      if(!count($this->modifications->where('is_default', 0)))
      return 0;

      if($this->category_id == 2 || $this->category_id == 7)
        return $this->modifications->where('is_default', 0)->where('area', '>', 0)->min('area');

      // return $this->modifications->where('is_default', 0)->where('area', '>', 0)->min('area');

      if($this->type !== 'Участок' && $this->type !== 'Ділянка') {
        return $this->modifications->where('is_default', 0)->where('type', str_replace('_', ' ', array_flip(__('attributes.cottage_types'))[$this->type]))->where('area', '>', 0)->min('area');
      } elseif($this->type === 'Участок' || $this->type === 'Ділянка') {
        return $this->modifications->where('is_default', 0)->where('type_key', 'Земельный участок')->where('area', '>', 0)->min('area');
      } else {
        return 0;
      }
    }

    public function getAreaMaxAttribute()
    {
      if(!count($this->modifications->where('is_default', 0)))
      return 0;

      if($this->category_id == 2 || $this->category_id == 7)
        return $this->modifications->where('is_default', 0)->where('area', '>', 0)->max('area');

      // return $this->modifications->where('is_default', 0)->where('area', '>', 0)->max('area');

      if($this->type !== 'Участок' && $this->type !== 'Ділянка') {
        return $this->modifications->where('is_default', 0)->where('type', str_replace('_', ' ', array_flip(__('attributes.cottage_types'))[$this->type]))->where('area', '>', 0)->max('area');
      } elseif($this->type === 'Участок' || $this->type === 'Ділянка') {
        return $this->modifications->where('is_default', 0)->where('type_key', 'Земельный участок')->where('area', '>', 0)->max('area');
      } else {
        return 0;
      }
    }

    public function getAreaMinPlotAttribute()
    {
      if(!count($this->modifications->where('is_default', 0)))
        return 0;

      if($this->category_id == 1 || $this->category_id == 6)
        return $this->modifications->where('is_default', 0)->where('area', '>', 0)->where('type_key', 'Земельный участок')->min('area');

      return 0;
    }

    public function getAreaMaxPlotAttribute()
    {
      if(!count($this->modifications->where('is_default', 0)))
        return 0;

      if($this->category_id == 1 || $this->category_id == 6)
        return $this->modifications->where('is_default', 0)->where('area', '>', 0)->where('type_key', 'Земельный участок')->max('area');

      return 0;
    }

    public function getCommunicationsStringAttribute()
    {
      if(!isset($this->extras['communications']))
        return '';

      $communications = '';
      foreach($this->extras['communications'] as $key => $item) {
        if($key === 0)
          $communications = __('attributes.communications.' . $item);
        else
          $communications .= ', ' . __('attributes.communications.' . $item);
      }

      return $communications;
    }

    public function getLatAttribute()
    {
      if(!isset($this->address['latlng']))
        return null;

      if(getType($this->address['latlng']) == 'string')
        return isset($this->address['latlng'])? json_decode($this->address['latlng'])->lat : null;
      else
        return isset($this->address['latlng'])? $this->address['latlng']['lat'] : null;
    }

    public function getLngAttribute()
    {
      if(!isset($this->address['latlng']))
        return null;

      if(getType($this->address['latlng']) == 'string')
        return isset($this->address['latlng'])? json_decode($this->address['latlng'])->lng : null;
      else
        return isset($this->address['latlng'])? $this->address['latlng']['lng'] : null;
    }

    public function getTrueRatingAttribute()
    {
      if(!$this->old_rating_count)
        return null;

      return round($this->old_rating / $this->old_rating_count, 1);
    }

    public function getShowPriceAttribute()
    {
      if($this->is_sold || $this->extras['is_frozen'])
        return false;

      return true;
    }

    public function getAreaUnitAttribute()
    {
      if($this->type == __('attributes.cottage_types.Земельный участок'))
        return 'сот';

      return 'кв.м';
    }

    public function getStatusStringAttribute()
    {
      if($this->extras['status'])
        return __('main.product_statuses.' . $this->extras['status']);

      return '';
    }

    public function getSecondStatusAttribute()
    {
      if(isset($this->extras['is_frozen']) && $this->extras['is_frozen'])
        return __('main.Заморожено');

      if($this->is_sold)
        return __('main.product_statuses.sold');

      return '';
    }

    public function getTelegramImgAttribute() {
        return $this->image? url(str_replace('uploads', 'common/uploads', $this->image) . '?w=510&fm=pjpg&q=85') : url('glide/NOVOSTI/news.jpg?w=510&fm=pjpg&q=85');
    }

    public function getTranslationLinkAttribute() {
        $lang = $this->language_abbr === 'ru'? 'uk' : 'ru';
        $scopes = static::$globalScopes;
        $this->clearGlobalScopes();

        if($lang === 'ru' && $this->original) {
            $link = $this->original()->first()->link;
        } elseif($lang !== 'ru' && $this->translations->where('language_abbr', $lang)->first()){
            $link = $this->translations->where('language_abbr', $lang)->first()->link;
        } else {
            $link = url($lang);
        }

        static::$globalScopes = $scopes;
        return $link;
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
    public function setModAttribute($value)
    {
      $this->modifications_array = $value;
    }

    public function setImagesAttribute($value)
    {
      $this->images_array = [];

      foreach($value as $key => $img) {
        if(gettype($img) == 'string') {
          $this->images_array[$key] = str_replace(url(''), '', $img);
          continue;
        }

        $extension = $img->getClientOriginalExtension();
        Storage::disk('common')->put('uploads/' . $img->getFilename().'.'.$extension,  File::get($img));
        $this->images_array[$key] = 'uploads/' . $img->getFilename().'.'.$extension;
      }
    }

    public function setImageAttribute($value)
    {
        $attribute_name = "image";
        $disk = config('backpack.base.root_disk_name'); // or use your own disk, defined in config/filesystems.php
        $destination_path = "public/uploads/product_images"; // path relative to the disk above
      // dd(request()->input());
        // if the image was erased
        if ($value==null) {
            // delete the image from disk
            \Storage::disk($disk)->delete($this->{$attribute_name});

            // set null in the database column
            $this->attributes[$attribute_name] = null;
        }

        // if a base64 was sent, store it in the db
        if (starts_with($value, 'data:image'))
        {
            // 0. Make the image
            $image = \Image::make($value)->encode('jpg', 90);

        // 1. Generate a filename.
            $filename = md5($value.time()).'.jpg';

        // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path.'/'.$filename, $image->stream());

        // 3. Delete the previous image, if there was one.
            \Storage::disk($disk)->delete($this->{$attribute_name});

            // 4. Save the public path to the database
        // but first, remove "public/" from the path, since we're pointing to it from the root folder
        // that way, what gets saved in the database is the user-accesible URL
            $public_destination_path = Str::replaceFirst('public/', '', $destination_path);
            $this->attributes[$attribute_name] = $public_destination_path.'/'.$filename;

        } else {
          $this->attributes[$attribute_name] = str_replace(url(''), '', $value);
        }
    }

    public function baseModification()
    {
        return $this->hasOne(BaseModification::class);
    }
}
