<?php

namespace App;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Kyivdistrict extends Model
{
    use CrudTrait;
    
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'kyivdistricts';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    protected $casts = [
        'extras' => 'array'
    ];


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

    public function translations()
    {
        return $this->hasMany('\App\Kyivdistrict', 'original_id');
    }

    public function original()
    {
        return $this->belongsTo('\App\Kyivdistrict', 'original_id');
    }

    public function cottages()
    {
        return $this->hasMany('\Aimix\Shop\app\Models\Product', 'address->kyivdistrict', 'kyivdistrict_id')->whereIn('products.category_id', [1,6]);
    }

    public function newbuilds()
    {
        return $this->hasMany('\Aimix\Shop\app\Models\Product', 'address->kyivdistrict', 'kyivdistrict_id')->whereIn('products.category_id', [2,7]);
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
}