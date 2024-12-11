<?php

namespace Backpack\MenuCRUD\app\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class MenuItem extends Model
{
    use CrudTrait;

    protected $table = 'menu_items';
    protected $fillable = ['name', 'type', 'link', 'page_id', 'parent_id', 'language_abbr', 'original_id'];

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

    public function parent()
    {
        return $this->belongsTo('Backpack\MenuCRUD\app\Models\MenuItem', 'parent_id');
    }

    public function children()
    {
        return $this->hasMany('Backpack\MenuCRUD\app\Models\MenuItem', 'parent_id');
    }

    public function page()
    {
        return $this->belongsTo('Backpack\PageManager\app\Models\Page', 'page_id');
    }

    public function translations()
    {
        return $this->hasMany('Backpack\MenuCRUD\app\Models\MenuItem', 'original_id');
    }

    public function original()
    {
        return $this->belongsTo('Backpack\MenuCRUD\app\Models\MenuItem', 'original_id');
    }

    /**
     * Get all menu items, in a hierarchical collection.
     * Only supports 2 levels of indentation.
     */
    public static function getTree()
    {
        $menu = self::orderBy('lft')->get();

        if ($menu->count()) {
            foreach ($menu as $k => $menu_item) {
                $menu_item->children = collect([]);

                foreach ($menu as $i => $menu_subitem) {
                    if ($menu_subitem->parent_id == $menu_item->id) {
                        $menu_item->children->push($menu_subitem);

                        // remove the subitem for the first level
                        $menu = $menu->reject(function ($item) use ($menu_subitem) {
                            return $item->id == $menu_subitem->id;
                        });
                    }
                }
            }
        }

        return $menu;
    }

    public function url()
    {
        $lang = session()->has('lang')? session('lang') : 'ru';
        switch ($this->type) {
            case 'external_link':
                return $this->link;
                break;

            case 'internal_link':
                $link = $this->link[0] == '/'? $this->link : '/' . $this->link;
                return is_null($this->link) ? '#' : url($lang . $link);
                break;

            default: //page_link
                if ($this->page) {
                    return url($lang . '/' . $this->page->slug);
                }
                break;
        }
    }
}
