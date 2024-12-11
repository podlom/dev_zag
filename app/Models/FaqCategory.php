<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class FaqCategory extends Model
{
    use CrudTrait;
    use Sluggable;
    use SluggableScopeHelpers;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'faq_categories';
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
    public function questions()
    {
        return $this->hasMany('App\Models\Faq', 'category_id');
    }
    public function translations()
    {
        return $this->hasMany('\App\Models\FaqCategory', 'original_id');
    }

    public function original()
    {
        return $this->belongsTo('\App\Models\FaqCategory', 'original_id');
    }

    public function article_categories()
    {
        return $this->hasMany('\Backpack\NewsCRUD\app\Models\Category', 'faq_category_id');
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopeNoEmpty($query){
      
        return $query->has('questions');
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
        return url($this->language_abbr . '/faq/' . $this->slug);
    }

    public function getCategoriesAttribute()
    {
        return $this->article_categories->pluck('id')->toArray();
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

    public function setCategoriesAttribute($value)
    {
        $this->article_categories->each(function($item) {
            $item->update(['faq_category_id' => null]);
        });
        
        foreach($value as $id) {
            if($id)
                \Backpack\NewsCRUD\app\Models\Category::find($id)->update(['faq_category_id' => $this->id]);
        }
    }
}
