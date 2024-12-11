<?php

namespace Aimix\Shop\app\Models;

use Illuminate\Database\Eloquent\Builder;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class Brand extends Model
{
    use CrudTrait;
    use Sluggable;
    use SluggableScopeHelpers;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'brands';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    protected $casts = [
      'images' => 'array',
      'contacts' => 'array',
      'extras' => 'array', 
      'extras_translatable' => 'array', 
      'achievements' => 'array',
      'address' => 'array',
    ];

    protected $fakeColumns = ['images','contacts','extras', 'extras_translatable'];
    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    protected static function boot()
    {
        parent::boot();
        if(config('aimix.aimix.enable_languages')) {
          static::addGlobalScope('language', function (Builder $builder) {
              $language = session()->has('lang')? session()->get('lang'): 'ru';
              $builder->where('language_abbr', $language);
          });
      }

      $ids = \App\Region::withoutGlobalScope('active')->withoutGlobalScope('noEmpty')->where('is_active', 0)->pluck('region_id');
      static::addGlobalScope('exclude_regions', function (Builder $builder) use ($ids) {
        $builder->whereNotIn('brands.address->region', $ids);
      });


      static::addGlobalScope('active_categories', function (Builder $builder) {
        $builder->whereHas('category', function($q) {
          $q->where('is_active', 1);
        });
      });

      static::addGlobalScope('active', function (Builder $builder) {
          $builder->where('is_active', 1);
      });
    }
    
    public function clearGlobalScopes()
    {
        static::$globalScopes = [];
    }
    
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'slug_or_name',
            ],
        ];
    }

    public function toArray()
    {
      return [
        'id' => $this->id,
        'original_id' => $this->original_id,
        'name' => $this->name,
        'slug' => $this->slug,
        'category_id' => $this->category_id,
        'category_name' => $this->category->name,
        'description' => $this->description,
        'images' => $this->images,
        'contacts' => $this->contacts,
        'extras' => $this->extras,
        'achievements' => $this->achievements,
        'link' => $this->link,
        'is_popular' => $this->is_popular,
        'address' => $this->address,

        // remove
        'city' => $this->city,
        'area' => $this->area,
        'region' => $this->region,
        'business_card' => $this->business_card
      ];
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function category()
    {
      return $this->belongsTo('Aimix\Shop\app\Models\BrandCategory');
    }

    public function products()
    {
      return $this->hasMany('Aimix\Shop\app\Models\Product');
    }
    
    public function activeProducts()
    {
      return $this->hasMany('Aimix\Shop\app\Models\Product')->where('is_active', 1);
    }
    
    public function country()
    {
      return $this->belongsTo('App\Models\Country');
    }

    public function reviews()
    {
        return $this->morphMany('Aimix\Review\app\Models\Review', 'reviewable')->published();
    }

    public function translations()
    {
        return $this->hasMany('\Aimix\Shop\app\Models\Brand', 'original_id');
    }

    public function original()
    {
        return $this->belongsTo('\Aimix\Shop\app\Models\Brand', 'original_id');
    }

    public function promotions()
    {
        return $this->hasManyThrough('\Aimix\Promotion\app\Models\Promotion', 'Aimix\Shop\app\Models\Product');
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopeNoEmpty($query){
      return $query->has('activeProducts');
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

    public function getLinkAttribute()
    {
      return url($this->language_abbr . '/firms/' . $this->category->slug . '/' . $this->slug);
    }
    
    // remove from package
    public function getRegionAttribute()
    {
      if($this->address['region'] && \App\Region::withoutGlobalScope('noEmpty')->where('region_id', $this->address['region'])->first())
        return \App\Region::withoutGlobalScope('noEmpty')->where('region_id', $this->address['region'])->first()->name;

      return '';
    }

    public function getAreaAttribute()
    {
      if($this->address['region'] && \App\Area::withoutGlobalScope('noEmpty')->where('area_id', $this->address['area'])->first())
        return \App\Area::withoutGlobalScope('noEmpty')->where('area_id', $this->address['area'])->first()->name;

      return '';
    }

    public function getCityAttribute()
    {
      if($this->address['region'] && \App\City::withoutGlobalScope('noEmpty')->where('city_id', $this->address['city'])->first())
        return \App\City::withoutGlobalScope('noEmpty')->where('city_id', $this->address['city'])->first()->name;

      return '';
    }

    public function getKyivdistrictAttribute()
    {
      if($this->address['region'] && isset($this->address['kyivdistrict']) && \App\Region::withoutGlobalScope('noEmpty')->where('region_id', $this->address['region'])->first())
        return \App\Kyivdistrict::withoutGlobalScope('noEmpty')->where('kyivdistrict_id', $this->address['kyivdistrict'])->first()->name;

      return '';
    }

    // public function getBusinessCardAttribute()
    // {
    //   return $this->images['business_card']? url($this->images['business_card']) : url('/img/fireplace-rect.svg');
    // }

    // public function getImageAttribute()
    // {
    //   return $this->images['image']? url($this->images['image']) : url('/img/company-cover.jpg');
    // }

    // public function getLogoAttribute()
    // {
    //   return $this->images['logo']? url($this->images['logo']) : url('/img/fireplace-circle.svg');
    // }

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

    public function setAchievementsAttribute()
    {
      $value = request()->achievements;

      $images_array = [];
      foreach($value as $key => $item) {
        $img = $item['image'];
        $images_array[$key]['name'] = $item['name'];
        if(gettype($item['image']) == 'string') {
        $images_array[$key]['image'] = $img;
        continue;
        }
        $extension = $img->getClientOriginalExtension();
        \Storage::disk('common')->put('uploads/' . $img->getFilename().'.'.$extension,  \File::get($img));
        $images_array[$key]['image'] = 'uploads/' . $img->getFilename().'.'.$extension;
      }
      
      $this->attributes['achievements'] = json_encode($images_array);
    }
}
