<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Faq extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'faqs';
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
                $builder->where('language_abbr', $language);
            });
        }
    }
    
    public function clearGlobalScopes()
    {
        static::$globalScopes = [];
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function category()
    {
      return $this->belongsTo('App\Models\FaqCategory', 'category_id');
    }
    
    public function translations()
    {
        return $this->hasMany('\App\Models\Faq', 'original_id');
    }

    public function original()
    {
        return $this->belongsTo('\App\Models\Faq', 'original_id');
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

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}