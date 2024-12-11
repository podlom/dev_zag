<?php

namespace App;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class City extends Model
{
    use CrudTrait;
    use Sluggable;
    use SluggableScopeHelpers;
    
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'cities';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

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
                $builder->where('cities.language_abbr', $language);
            });
        }

        static::addGlobalScope('noEmpty', function (Builder $builder) {
            $builder->has('products');
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
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function translations()
    {
        return $this->hasMany('\App\City', 'original_id');
    }

    public function original()
    {
        return $this->belongsTo('\App\City', 'original_id');
    }

    public function area()
    {
        return $this->belongsTo('\App\Area', 'area_id', 'area_id');
    }

    public function area_admin()
    {
        return $this->belongsTo('\App\Area', 'area_id', 'area_id')->where('language_abbr', 'ru');
    }

    public function region()
    {
        if($this->area)
            return $this->area->belongsTo('\App\Region', 'region_id', 'region_id');

        return null;
    }

    public function products()
    {
        return $this->hasMany('\Aimix\Shop\app\Models\Product', 'address->city', 'city_id');
    }

    public function cottages()
    {
        return $this->hasMany('\Aimix\Shop\app\Models\Product', 'address->city', 'city_id')->whereIn('products.category_id', [1,6]);
    }

    public function newbuilds()
    {
        return $this->hasMany('\Aimix\Shop\app\Models\Product', 'address->city', 'city_id')->whereIn('products.category_id', [2,7]);
    }
    
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

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
    
    public function getTranslationSlugAttribute() {
        $lang = $this->language_abbr === 'ru'? 'uk' : 'ru';
        $scopes = static::$globalScopes;
        $this->clearGlobalScopes();
        
        if($lang === 'ru' && $this->original) {
            $slug = $this->original()->first()->slug;
        } elseif($lang !== 'ru' && $this->translations->where('language_abbr', $lang)->first()){
            $slug = $this->translations->where('language_abbr', $lang)->first()->slug;
        } else {
            $slug = url($lang);
        }

        static::$globalScopes = $scopes;
        return $slug;
    }
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
