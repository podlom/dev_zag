<?php

namespace Aimix\Shop\app\Models;

use Illuminate\Database\Eloquent\Builder;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class BrandCategory extends Model
{
    use CrudTrait;
    use Sluggable;
    use SluggableScopeHelpers;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'brand_categories';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    protected $casts = [
        'extras' => 'array',
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
            'name' => $this->name,
            'slug' => $this->slug,
            'image' => $this->image? url($this->image) : url('/img/company-cover.jpg'),
            'brands_count' => $this->brands->count(),
            'link' => $this->link
        ];
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function brands()
    {
        return $this->hasMany('Aimix\Shop\app\Models\Brand', 'category_id');
    }

    public function translations()
    {
        return $this->hasMany('\Aimix\Shop\app\Models\BrandCategory', 'original_id');
    }

    public function original()
    {
        return $this->belongsTo('\Aimix\Shop\app\Models\BrandCategory', 'original_id');
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopeNoEmpty($query){
      
        return $query->has('brands');
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
        return url($this->language_abbr . '/firms/' . $this->slug);
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
}
