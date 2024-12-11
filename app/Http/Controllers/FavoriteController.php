<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aimix\Promotion\app\Models\Promotion;
use Backpack\NewsCRUD\app\Models\Article;
use Backpack\NewsCRUD\app\Models\Category;
use Aimix\Shop\app\Models\Product;
use Aimix\Shop\app\Models\Modification;
use Aimix\Shop\app\Models\Attribute;
use Aimix\Shop\app\Models\Category as ProductCategory;
use Aimix\Shop\app\Models\Brand;
use Aimix\Shop\app\Models\BrandCategory;
use Illuminate\Database\Eloquent\Builder;
use App\Region;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use Backpack\PageManager\app\Models\Page;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $page = Page::where('template', 'favorite')->first()->withFakes();

        return view('favorite.index')->with('page', $page);
    }

    public function response(Request $request)
    {
        $lang = $request->segment(1);
        \App::setLocale($lang);
        $type = $request->tab;
        $ids = $request->ids;
        $items = null;
        $sorts = [];
        $filters_1 = [];
        $filters_2 = [];
        $filters_3 = [];
        $filter_1 = $request->filter_1;
        $filter_2 = $request->filter_2;
        $filter_3 = $request->filter_3;
        $filter_name_1 = null;
        $filter_name_2 = null;
        $filter_name_3 = null;
        
        if($type == 'cottages') {
            $items = $filter_3 === 0? Product::active()->whereIn('category_id', [1,6]) : Modification::active()->whereHas('product', function($q) {
                $q->whereIn('category_id', [1,6]);
            });
            $sorts = __('sorts.products');
            $filter_name_1 = __('main.Область');
            $filter_name_2 = __('main.Тип');
            $filters_1 = Region::pluck('name', 'region_id');
            $filters_2 = Attribute::find(1)->values;
            $filters_3 = [__('main.Коттеджные городки'), __('main.Типовые проекты')];
            
            if($filter_1 !== null) {
                $items = $filter_3 === 0? $items->where('address->region', $filter_1) : $items->whereHas('product', function($query) use ($filter_1) {
                    $query->where('address->region', $filter_1);
                });
            }

            if($filter_2 !== null) {
                $items = $items->whereHas('modifications', function(Builder $query) use($filters_2, $filter_2) {
                    $query->active()->notBase()->whereHas('attrs', function(Builder $attr_query) use($filters_2, $filter_2) {
                      $attr_query->where('attribute_id', 1)->whereJsonContains('value', $filters_2[$filter_2]);
                    });
                });
            }
        }
        elseif($type == 'newbuilds') {
            $items = $filter_3 === 0? Product::active()->whereIn('category_id', [2,7]) : Modification::active()->whereHas('product', function($q) {
                $q->whereIn('category_id', [2,7]);
            });
            $sorts = __('sorts.products');
            $filter_name_1 = __('main.Область');
            $filter_name_2 = __('main.Тип');

            $filters_1 = Region::pluck('name', 'region_id');
            $filters_2 = __('attributes.newbuild_types');
            $filters_3 = [__('main.Новостройки'), __('main.Типовые проекты')];

            if($filter_1 != null) {
                $items = $filter_3 === 0? $items->where('address->region', $filter_1) : $items->whereHas('product', function($query) use ($filter_1) {
                    $query->where('address->region', $filter_1);
                });
            }

            if($filter_2 != null) {
                $items = $filter_3 === 0? $items->where('extras->newbuild_type', $filter_2) : $items->whereHas('product', function($query) use ($filter_2) {
                    $query->where('extras->newbuild_type', $filter_2);
                });
            }
        }
        elseif($type == 'articles') {
            $items = Article::where('status', 'PUBLISHED');
            $sorts = __('sorts.articles');
            $filter_name_1 = __('main.Категория');
            $filter_name_2 = __('main.Тема');
            $filters_1 = Category::doesnthave('parent')->has('children')->whereIn('id', [1,14,5,26,12,27,13,28])->pluck('name', 'slug');

            if($filter_1 != null) {
                $filters_2 = Category::doesnthave('children')->has('articles')->whereHas('parent', function(Builder $query) use($filter_1) {
                  $query->where('slug', $filter_1);
                })->pluck('name', 'slug');
      
                $items = $items->whereHas('category', function(Builder $query) use($filter_1) {
                  $query->whereHas('parent', function(Builder $cat_query) use($filter_1) {
                    $cat_query->where('slug', $filter_1);
                  });
                });
            }

            if($filter_2 != null) {
                $items = $items->whereHas('category', function(Builder $query) use($filter_2) {
                  $query->where('slug', $filter_2);
                });
            }
        }
        elseif($type == 'companies') {
            $items = Brand::where('id', '!=', null);
            $filter_name_1 = __('main.Область');
            $filter_name_2 = __('main.Специализация');
            $filters_1 = Region::pluck('name', 'region_id');
            $filters_2 = BrandCategory::noEmpty()->pluck('name', 'slug');

            if($filter_1 != null) {
                $items = $items->where('address->region', $filter_1);
            }

            if($filter_2 != null) {
                $items = $items->whereHas('category', function(Builder $query) use($filter_2) {
                  $query->where('slug', $filter_2);
                });
            }
        }
        elseif($type == 'promotions') {
            $items = Promotion::where('id', '!=', null);
            $filter_name_1 = __('main.Тип недвижимости');
            $filter_name_2 = __('main.Застройщик');
            $filters_1 = ProductCategory::pluck('name', 'id');
            $filters_2 = Brand::has('promotions')->pluck('name', 'id');

            if($filter_1 != null) {
                $items = $items->whereHas('product', function(Builder $query) use($filter_1) {
                  $query->where('category_id', $filter_1);
                });
            }

            if($filter_2 != null) {
                $items = $items->whereHas('product', function(Builder $query) use($filter_2) {
                  $query->where('brand_id', $filter_2);
                });
            }
        }

        
        $items = $items->where(function($query) use ($ids) {
            $query->whereIn('id', $ids)->orWhereIn('original_id', $ids);
        });

        if($request->sort) {
            preg_match_all("/([\w]+)_([\w]+)/", $request->sort, $value);
          
            $sort = ['value' => $value[1][0], 'dirr' => $value[2][0]];
  
            if($sort['value'] == 'reviews_count')
              $items = $items->withCount('reviews');
  
            $items = $items->orderBy($sort['value'], $sort['dirr']);
        } else {
            $items = $items->orderBy('created_at', 'desc');
        }
        
        $items = $items->paginate(6);
        
        return response()->json(['items' => $items, 'sorts' => $sorts, 'filters_1' => $filters_1, 'filters_2' => $filters_2, 'filter_name_1' => $filter_name_1, 'filter_name_2' => $filter_name_2, 'filters_3' => $filters_3, 'filter_name_3' => $filter_name_3]);
    }
}
