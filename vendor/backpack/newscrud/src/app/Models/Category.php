<?php

namespace Backpack\NewsCRUD\app\Models;

use Illuminate\Database\Eloquent\Builder;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use CrudTrait;
    use Sluggable, SluggableScopeHelpers;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'categories';
    protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['name', 'slug', 'parent_id', 'language_abbr', 'original_id', 'meta_title', 'meta_desc', 'seo_text', 'is_active', 'image', 'content', 'faq_category_id'];
    // protected $hidden = [];
    // protected $dates = [];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
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
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    protected static function boot()
    {
        parent::boot();
        if(config('aimix.aimix.enable_languages')) {
            static::addGlobalScope('language', function (Builder $builder) {
            	$language = session()->has('lang')? session()->get('lang'): 'ru';
                $builder->where('categories.language_abbr', $language);
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

    public function parent()
    {
        return $this->belongsTo('Backpack\NewsCRUD\app\Models\Category', 'parent_id');
    }

    public function children()
    {
        return $this->hasMany('Backpack\NewsCRUD\app\Models\Category', 'parent_id');
    }

    public function articles()
    {
        return $this->hasMany('Backpack\NewsCRUD\app\Models\Article');
    }

    public function translations()
    {
        return $this->hasMany('\Backpack\NewsCRUD\app\Models\Category', 'original_id');
    }

    public function original()
    {
        return $this->belongsTo('\Backpack\NewsCRUD\app\Models\Category', 'original_id');
    }

    public function activeArticles()
    {
        return $this->hasMany('Backpack\NewsCRUD\app\Models\Article')->where('status', 'PUBLISHED');
    }

    public function faq_category()
    {
        return $this->belongsTo('App\Models\FaqCategory', 'faq_category_id');
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeFirstLevelItems($query)
    {
        return $query->where('depth', '1')
                    ->orWhere('depth', null)
                    ->orderBy('lft', 'ASC');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    // The slug is created automatically from the "name" field if no slug exists.
    public function getSlugOrNameAttribute()
    {
        if ($this->slug != '') {
            return $this->slug;
        }

        return $this->name;
    }

    public function getLinkAttribute()
    {
        $services = [369,370,371,372,373,374];
        if(in_array($this->id, $services))
            return url($this->language_abbr . '/servisy/' . $this->slug);

        $analitics = [194,195,375,376];
        if(in_array($this->id, $analitics))
            return url($this->language_abbr . '/analitics/' . $this->slug);

        if(!$this->parent_id)
            return url($this->language_abbr . '/' . $this->slug);

        $news = [1,14,5,26,12,27,13,28];
        if(in_array($this->parent->id, $news))
            return url($this->language_abbr . '/' . $this->parent->slug . '/bookmarks/' . $this->slug);

        if(in_array($this->parent_id, $services))
            return url($this->language_abbr . '/servisy/' . $this->parent->slug . '/' . $this->slug);

            
        if(in_array($this->parent_id, $analitics))
            return url($this->language_abbr . '/analitics/' . $this->parent->slug . '/' . $this->slug);
        
        
        return url($this->language_abbr . '/' . $this->parent->slug . '/' . $this->slug);

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
