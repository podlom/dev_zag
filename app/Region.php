<?php

namespace App;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class Region extends Model
{
    use CrudTrait;
    use Sluggable;
    use SluggableScopeHelpers;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'regions';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    protected $casts = [
        'extras' => 'array'
    ];

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

        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('is_active', 1);
        });

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
        return $this->hasMany('\App\Region', 'original_id');
    }

    public function original()
    {
        return $this->belongsTo('\App\Region', 'original_id');
    }

    public function areas()
    {
        return $this->hasMany('\App\Area', 'region_id', 'region_id');
    }

    public function products()
    {
        return $this->hasMany('\Aimix\Shop\app\Models\Product', 'address->region', 'region_id');
    }

    public function cottages()
    {
        return $this->hasMany('\Aimix\Shop\app\Models\Product', 'address->region', 'region_id')->whereIn('products.category_id', [1,6]);
    }

    public function newbuilds()
    {
        return $this->hasMany('\Aimix\Shop\app\Models\Product', 'address->region', 'region_id')->whereIn('products.category_id', [2,7]);
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
