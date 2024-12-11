<?php

namespace Backpack\NewsCRUD\app\Models;

use Illuminate\Database\Eloquent\Builder;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

use Backpack\NewsCRUD\app\Models\Category;

use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

class Article extends Model implements Feedable
{
    const DEFAULT_LANG = 'uk';

    use CrudTrait;
    use Sluggable, SluggableScopeHelpers;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'articles';
    protected $primaryKey = 'id';
    public $timestamps = true;
    // protected $guarded = ['id'];
    protected $fillable = ['slug', 'title', 'content', 'image', 'status', 'category_id', 'featured', 'date', 'short_desc', 'language_abbr', 'region', 'original_id', 'meta_title', 'meta_desc', 'hide_from_index', 'nofollow_links', 'images', 'in_telegram', 'show_form'];
    // protected $hidden = [];
    // protected $dates = [];
    protected $casts = [
        'featured'  => 'boolean',
        'date'      => 'datetime',
        'hide_from_index' => 'boolean',
        'nofollow_links' => 'boolean',
        'images' => 'array',
        'in_telegram' => 'boolean'
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'slug_or_title',
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
                // @ts 2024-06-27 default lang to Ukrainian - uk
            	$language = session()->has('lang')? session()->get('lang'): self::DEFAULT_LANG;
                $builder->where('articles.language_abbr', $language);
            });
        }

        static::addGlobalScope('notEmpty', function (Builder $builder) {
            $builder->where('articles.title', '!=', '');
        });

        static::addGlobalScope('beforeNow', function (Builder $builder) {
            $builder->where(function($query) {
                $query->where('articles.date', '<=', date('Y-m-d H-i'))->orWhere('articles.date', null);
            });
        });

        static::addGlobalScope('hasCategory', function (Builder $builder) {
            $builder->where('articles.category_id', '!=', null);
        });
    }

    public function clearGlobalScopes()
    {
      static::$globalScopes = [];
    }

    public function toArray()
    {
      return [
        'id' => $this->id,
        'original_id' => $this->original_id,
        'title' => $this->trueTitle,
        'link' => $this->link,
        'short_desc' => $this->short_desc,
        'content' => $this->content,
        'image' => $this->img,
        'featured' => $this->featured,
        // 'date' => $this->humanDate,
        'date' => $this->date? $this->date->format('d.m.Y') : null,
        'category' => $this->category_id? Category::find($this->category_id)->name : null,
        'category_link' => $this->category_id? Category::find($this->category_id)->link : null,
        'region' => $this->region_name,
        'reviews_count' => $this->reviews->count(),
        'views' => $this->views,
        'hide_from_index' => $this->hide_from_index,
        'parent_category_id' => $this->category? $this->category->parent_id : null,
        'showImage' => $this->showImage,
        'showFavoriteButton' => $this->showFavoriteButton,
        'showDate' => $this->showDate,
        'robots_date' => $this->date? $this->date->format('Y-m-d') : null,
      ];
    }

    public function toFeedItem()
    {
        (new Article)->clearGlobalScopes();
        return FeedItem::create([
            'id' => $this->id,
            'title' => $this->title,
            'updated' => $this->date,
            'summary' => strip_tags($this->short_desc),
            'fullText' => strip_tags($this->content),
            'theContent' => $this->content,
            'author' => 'Zagorodna.com',
            'link' => $this->link,
            'image' => $this->bigImg
        ]);
    }

    public static function getFeedItems()
    {
        $lang = request()->lang? request()->lang : 'ru';

        return Article::withoutGlobalScopes()->where('articles.date', '<=', date('Y-m-d H-i'))->where('language_abbr', $lang)->where('title', '!=', '')->where('status', 'PUBLISHED')->whereHas('category', function($q) {
            $q->withoutGlobalScopes()->whereIn('parent_id', [1,14,5,26,12,27,13,28]);
        })->orderBy('date', 'desc')->take(30)->get();
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function category()
    {
        return $this->belongsTo('Backpack\NewsCRUD\app\Models\Category', 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany('Backpack\NewsCRUD\app\Models\Tag', 'article_tag');
    }

    public function reviews()
    {
        return $this->morphMany('Aimix\Review\app\Models\Review', 'reviewable')->published();
    }

    public function translations()
    {
        return $this->hasMany('\Backpack\NewsCRUD\app\Models\Article', 'original_id');
    }

    public function original()
    {
        return $this->belongsTo('\Backpack\NewsCRUD\app\Models\Article', 'original_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopePublished($query)
    {
        return $query->where('articles.status', 'PUBLISHED')
                    ->orderBy('articles.date', 'DESC')
                    ->orderBy('articles.created_at', 'DESC');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    // The slug is created automatically from the "title" field if no slug exists.
    public function getSlugOrTitleAttribute()
    {
        if ($this->slug != '') {
            return $this->slug;
        }

        return $this->title;
    }

    public function getLinkAttribute() {
        if(!$this->category)
            return '';

        if(($this->category_id == 371 || $this->category_id == 372) && \App\Models\PollQuestion::find($this->poll_id))
            return url($this->language_abbr . '/servisy/' . $this->category->slug . '/' . \App\Models\PollQuestion::find($this->poll_id)->id . '.html');

        if(!$this->category->parent)
            return url($this->language_abbr . '/' . $this->category->slug . '/' . $this->slug  . '.html');

        $analitics = [194,195,375,376];
        if(in_array($this->category->parent_id, $analitics))
            return url($this->language_abbr . '/analitics/' . $this->category->parent->slug . '/' . $this->category->slug . '/' . $this->slug  . '.html');

        $regions = [204,208];

        if(in_array($this->category_id, $regions))
            return url($this->language_abbr . '/' . $this->category->parent->slug . '/' . $this->slug);

        $categories = [1,14,5,26,12,27,13,28];

        if(in_array($this->category->parent_id, $categories))
            return url($this->language_abbr . '/' . $this->category->parent->slug . '/' . $this->slug  . '.html');

        return url($this->language_abbr . '/' . $this->category->parent->slug . '/' . $this->category->slug . '/' . $this->slug  . '.html');
    }

    public function getHumanDateAttribute() {
        return \Carbon\Carbon::createFromTimeStamp(strtotime($this->date))->diffForHumans();
    }

    public function getRegionNameAttribute() {
        return $this->region? \App\ArticleRegion::where('region_id', $this->region)->first()->name : '';
    }

    public function getShowImageAttribute() {
        if(!$this->category)
            return true;

        $excluded = [194,195,215,216,218,219,220,222,217,221,281,282,293,294,297,324,302,329,349,350,300,327,347,348,351,352,369,370,371,372,373,374];
        if(in_array($this->category->parent_id, $excluded) || in_array($this->category->id, $excluded))
            return false;

        return true;
    }

    public function getShowFavoriteButtonAttribute() {
        if(!$this->category)
            return false;

        $included = [1,14,5,26,12,27,13,28];
        if(in_array($this->category->parent_id, $included))
            return true;

        return false;
    }

    public function getShowDateAttribute() {
        if(!$this->category)
            return true;

        $excluded = [202,203,281,282,293,294,297,324,302,329,349,350,300,327,347,348,351,352,369,370,371,372,373,374];
        if(in_array($this->category->parent_id, $excluded) || in_array($this->category->id, $excluded))
            return false;

        return true;
    }

    public function getImgAttribute() {
        if($this->image)
            return str_replace(' ', '%20', url(str_replace('files', 'glide', $this->image) . '?w=170&fm=pjpg&q=85'));

        if($this->category->image)
            return str_replace(' ', '%20', url(str_replace('files', 'glide', $this->category->image) . '?w=170&fm=pjpg&q=85'));

        return url('files/NOVOSTI/news.jpg');
    }

    public function getBigImgAttribute() {
        if($this->image)
            return str_replace(' ', '%20', url(str_replace('files', 'glide', $this->image) . '?w=720&fm=pjpg&q=85'));

        if($this->category->image)
            return str_replace(' ', '%20', url(str_replace('files', 'glide', $this->category->image) . '?w=720&fm=pjpg&q=85'));

        return url('files/NOVOSTI/news.jpg');
    }

    public function getTelegramImgAttribute() {
        if($this->image)
            return url(str_replace('files', 'glide', $this->image) . '?w=510&fm=pjpg&q=85');

        if($this->category->image)
            return url(str_replace('files', 'glide', $this->category->image) . '?w=510&fm=pjpg&q=85');

        return url('glide/NOVOSTI/news.jpg?w=510&fm=pjpg&q=85');
    }

    public function getTrueTitleAttribute() {
        return $this->poll_id? __('main.Результаты опроса') . ' "' . \App\Models\PollQuestion::find($this->poll_id)->title . '"' : $this->title;
    }

    public function getFilteredContentAttribute() {
        if($this->nofollow_links) {
            $content = str_replace('<a ', '<a rel="nofollow" ', $this->content);
            $content = str_replace('<a rel="nofollow" href="https://www.zagorodna.com', '<a href="https://www.zagorodna.com', $content);
            $content = str_replace('<a rel="nofollow" href="http://www.zagorodna.com', '<a href="http://www.zagorodna.com', $content);

            return $content;
        }

        return $this->content;
    }

    public function getTranslationLinkAttribute() {
        $lang = $this->language_abbr === 'ru'? 'uk' : 'ru';
        $scopes = static::$globalScopes;
        $this->clearGlobalScopes();

        if($lang === 'ru' && $this->original) {
            $link = $this->original()->first()->link;
        } elseif($lang !== 'ru' && $this->translations->where('language_abbr', $lang)->where('title', '!=', '')->first()){
            $link = $this->translations->where('language_abbr', $lang)->where('title', '!=', '')->first()->link;
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
    public function setDateAttribute($value) {
        $this->attributes['date'] = Carbon::parse($value);
    }
}
