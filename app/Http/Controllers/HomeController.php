<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aimix\Shop\app\Models\Product;
use Aimix\Shop\app\Models\Modification;
use Aimix\Shop\app\Models\BrandCategory;
use Aimix\Shop\app\Models\Brand;
use Aimix\Review\app\Models\Review;
use Aimix\Banner\app\Models\Banner;
use Backpack\PageManager\app\Models\Page;
use Backpack\NewsCRUD\app\Models\Article;
use Backpack\NewsCRUD\app\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Aimix\Promotion\app\Models\Promotion;
use App\Region;
use App\Area;
use App\City;
use App\Kyivdistrict;
use App\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // session()->remove('lang');
        // dd(session()->get('lang'));
        // (new Page)->clearGlobalScopes();
        // dd(Page::get());
        $data['banners'] = Banner::orderBy('lft')->orderBy('created_at', 'DESC')->get();
        $data['page'] = Page::where('template', 'main')->first()->withFakes();
        // dd($data['page']);
        $data['company_categories'] = BrandCategory::where('is_popular', 1)->where('is_active', 1)->take(6)->get();
        $data['brands_count'] = Brand::count();
        // $data['cottages'] = Product::active()->whereIn('category_id', [1,6])->take(12)->get();
        $data['cottages_count'] = Product::active()->whereIn('category_id', [1,6])->count();
        // $data['newbuilds'] = Product::active()->whereIn('category_id', [2,7])->take(12)->get();
        $data['newbuilds_count'] = Product::active()->whereIn('category_id', [2,7])->count();
        // $data['articles'] = Article::published()->whereHas('category', function(Builder $query) {
            // $query->whereIn('parent_id', [1, 14]);
        // })->take(12)->get();
        $data['reviews'] = Review::where('type', 'zagorodna')->where('is_moderated', 1)->orderBy('created_at', 'DESC')->take(12)->get();
        $data['reviews_count'] = Review::where('type', 'zagorodna')->where('is_moderated', 1)->count();
        // $data['promotions'] = Promotion::orderBy('created_at', 'desc')->take(12)->get();
        $data['promotions_count'] = Promotion::count();
      
        // $data['hits'] = Product::active()->where('is_hit', 1)->take(12)->get();
        $data['hits_count'] = Product::active()->where('is_hit', 1)->count();
        
        $data['cottages_slug'] = \Aimix\Shop\app\Models\Category::find([1,6])->first()->slug;
        $data['newbuilds_slug'] = \Aimix\Shop\app\Models\Category::find([2,7])->first()->slug;
        $data['regions'] = Region::pluck('name', 'region_id');

        $exhibition_count = Article::whereHas('category', function($q) {
            $q->whereIn('parent_id', [215,219]);
        })->count();
        $contest_count = Article::whereHas('category', function($q) {
            $q->whereIn('parent_id', [218,222]);
        })->count();
        
        $data['numbers'] = [
            $data['cottages_count'],
            $contest_count,
            $exhibition_count,
            $data['newbuilds_count']
        ];

        if (!Cache::has('price_range_options')) {
            $expiresAt = Carbon::now()->addDay();
      
            $range_min_1 = Modification::notBase()->where('price', '>', 0)->whereHas('product', function($q) {
                $q->where('products.is_active', 1)->whereIn('category_id', [2,7]);
            })->min('price');
            $range_max_1 = Modification::notBase()->where('price', '>', 0)->whereHas('product', function($q) {
                $q->where('products.is_active', 1)->whereIn('category_id', [2,7]);
            })->max('price');
            $range_min_2 = Modification::notBase()->where('price', '>', 0)->whereHas('product', function($q) {
                $q->where('products.is_active', 1)->whereIn('category_id', [1,6]);
            })->min('price');
            $range_max_2 = Modification::notBase()->where('price', '>', 0)->whereHas('product', function($q) {
                $q->where('products.is_active', 1)->whereIn('category_id', [1,6]);
            })->max('price');
      
            $range_options = [
              'min_1' => $range_min_1,
              'max_1' => $range_max_1,
              'min_2' => $range_min_2,
              'max_2' => $range_max_2,
            ];
            
            Cache::put('price_range_options', $range_options, $expiresAt);
          } else {
            $range_options = Cache::get('price_range_options');
          }
          
          $data['range_options'] = $range_options;
        
        return view('index', $data);
    }

    public function getArticles(Request $request, $category)
    {
	    $lang = $request->segment(1);
        if($lang === 'uk' || $lang === 'ru')
            session()->put('lang', $lang);

        $categories = [
            0 => [1,14],
            1 => [12, 27],
            2 => [5,26],
            3 => [13,28]
        ];

        $selected = $categories[$category];
        $category_slug = Category::whereIn('id', $selected)->first()->slug;
        
        $newsCategoryLink = url($lang . '/' . $category_slug);

        $articles = Article::published()->whereHas('category', function(Builder $query) use ($selected) {
            $query->whereIn('parent_id', $selected);
        })->orderBy('date', 'desc')->take(12)->get();

        return response()->json(['articles' => $articles, 'newsCategoryLink' => $newsCategoryLink]);
    }

    public function getPromotions(Request $request)
    {
        $promotions = Promotion::orderBy('created_at', 'desc')->take(12)->get();
        
        return response()->json(['promotions' => $promotions]);
    }

    public function getReviews(Request $request)
    {
        $reviews = Review::orderBy('created_at', 'desc');

        if($request->type)
            $reviews = $reviews->where('type', $request->type);

        if($request->region) {
            $reviews = $reviews->whereHasMorph(
                'reviewable',
                'Aimix\Shop\app\Models\Product',
                function($query) use ($request) {
                    $query->where('products.address->region', $request->region);
                });
        }

        if($request->area) {
            $reviews = $reviews->whereHasMorph(
                'reviewable',
                'Aimix\Shop\app\Models\Product',
                function($query) use ($request) {
                    $query->where('products.address->area', $request->area);
                });
        }

        if($request->city) {
            $reviews = $reviews->whereHasMorph(
                'reviewable',
                'Aimix\Shop\app\Models\Product',
                function($query) use ($request) {
                    $query->where('products.address->city', $request->city);
                });
        }

        if($request->kyivdistrict) {
            $reviews = $reviews->whereHasMorph(
                'reviewable',
                'Aimix\Shop\app\Models\Product',
                function($query) use ($request) {
                    $query->where('products.address->kyivdistrict', $request->kyivdistrict);
                });
        }

        
        $reviews = $reviews->take(12)->get();

        return response()->json(['reviews' => $reviews]);
    }

    public function search(Request $request)
    {
        $type = $request->type;
        $filter = $request->filter;
        $filterSwitched = switchTextToEnglish($filter) != $filter? switchTextToEnglish($filter) : switchTextToRussian($filter);
        $items = [];

        switch ($type) {
            case 'cottages':
                $regions = Region::where('name', 'like', '%'.$filter.'%')->orWhere('name', 'like', '%'.$filterSwitched.'%')->pluck('region_id');
                $areas = Area::where('name', 'like', '%'.$filter.'%')->orWhere('name', 'like', '%'.$filterSwitched.'%')->pluck('area_id');
                $cities = City::where('name', 'like', '%'.$filter.'%')->orWhere('name', 'like', '%'.$filterSwitched.'%')->pluck('city_id');
                $items = Product::where('is_active', 1)->whereIn('category_id', [1,6])->where(function($q) use($regions, $areas, $cities, $filter, $filterSwitched) {
                    $q->where('name', 'like', '%'.$filter.'%')->orWhere('name', 'like', '%'.$filterSwitched.'%')->orWhere('extras_translatable->address_string', 'like', '%'.$filter.'%')->orWhere('extras_translatable->address_string', 'like', '%'.$filterSwitched.'%')->orWhereIn('address->region', $regions)->orWhereIn('address->area', $areas)->orWhereIn('address->city', $cities);
                })->paginate(20);
                
                  $items = new \App\Http\Resources\Products($items);
                break;
            case 'newbuilds':
                $regions = Region::where('name', 'like', '%'.$filter.'%')->orWhere('name', 'like', '%'.$filterSwitched.'%')->pluck('region_id');
                $areas = Area::where('name', 'like', '%'.$filter.'%')->orWhere('name', 'like', '%'.$filterSwitched.'%')->pluck('area_id');
                $cities = City::where('name', 'like', '%'.$filter.'%')->orWhere('name', 'like', '%'.$filterSwitched.'%')->pluck('city_id');
                $items = Product::where('is_active', 1)->whereIn('category_id', [2,7])->where(function($q) use($regions, $areas, $cities, $filter, $filterSwitched) {
                    $q->where('name', 'like', '%'.$filter.'%')->orWhere('name', 'like', '%'.$filterSwitched.'%')->orWhere('extras_translatable->address_string', 'like', '%'.$filter.'%')->orWhere('extras_translatable->address_string', 'like', '%'.$filterSwitched.'%')->orWhereIn('address->region', $regions)->orWhereIn('address->area', $areas)->orWhereIn('address->city', $cities);
                })->paginate(20);
                
	  	        $items = new \App\Http\Resources\Products($items);
                break;
            case 'promotions':
                $items = Promotion::select('promotions.*')->distinct('promotions.id')->join('products', 'products.id', '=', 'promotions.product_id')->where('promotions.title', 'like', '%'.$filter.'%')->orWhere('promotions.desc', 'like', '%'.$filter.'%')->orWhere('products.name', 'like', '%'.$filter.'%')->paginate(20);
                break;
            case 'companies':
                // $regions = Region::where('name', 'like', '%'.$filter.'%')->orWhere('name', 'like', '%'.$filterSwitched.'%')->pluck('region_id');
                $areas = Area::where('name', 'like', '%'.$filter.'%')->orWhere('name', 'like', '%'.$filterSwitched.'%')->pluck('area_id');
                $cities = City::where('name', 'like', '%'.$filter.'%')->orWhere('name', 'like', '%'.$filterSwitched.'%')->pluck('city_id');
                $items = Brand::where('name', 'like', '%'.$filter.'%')->orWhere('name', 'like', '%'.$filterSwitched.'%')->orWhereIn('address->area', $areas)->orWhereIn('address->city', $cities)->paginate(20);
                $items = new \App\Http\Resources\Companies($items);
                break;
            case 'articles':
                $items = Article::published()->where(function($q) use ($filter, $filterSwitched) {
                    $q->where('title', 'like', '%'.$filter.'%')->orWhere('title', 'like', '%'.$filterSwitched.'%');
                })->whereHas('category', function($query) {
                    $query->whereIn('parent_id', [1,14,5,26,12,27,13,28]);
                })->paginate(20);
                break;
        }

        return response()->json($items);
    }

    public function getAreas(Request $request)
    {
        $areas = $request->region == 29? Kyivdistrict::where('id', '!=', null) : Area::where('region_id', $request->region);

        if($request->empty)
            $areas->withoutGlobalScopes()->where('language_abbr', 'ru');

        $areas = $request->region == 29? $areas->pluck('name', 'kyivdistrict_id') : $areas->pluck('name', 'area_id');

        return response()->json(['areas' => $areas]);
    }

    public function getCities(Request $request)
    {
        $cities = City::where('area_id', $request->area);

        if($request->empty)
            $cities->withoutGlobalScopes()->where('language_abbr', 'ru');

        $cities = $cities->pluck('name', 'city_id');

        return response()->json(['cities' => $cities]);
    }

    public function setRegion(Request $request)
    {
        session()->put('region', $request->input('selectedRegion'));
        return back();
    }

    public function getNotifications(Request $request)
    {
        $notifications = $request->notifications;
        $products = $notifications['products'];
        $companies = $notifications['companies'];
        $items = Notification::whereIn('product_id', $products)->where('type', 'old')->orWhereHas('product', function($query) use ($companies) {
          $query->whereIn('brand_id', $companies)->where('type', 'new');
        })->orderBy('created_at', 'desc')->take(10)->get();
      
        return response()->json(['notifications' => $items]);
    }

    public function getSelection(Request $request)
    {
        $categories = [
            'cottage' => [1,6],
            'newbuild' => [2,7]
        ];

        $products = Product::select('products.*')->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)
        ->whereIn('category_id', $categories[$request->type])->where('is_sold', 0)->where('products.is_active', 1)->where('products.extras->is_frozen', 0);

        if($request->city) {
            $products = $products->where('address->city', $request->city);
        } elseif($request->area) {
            $products = $products->where('address->area', $request->area);
        } elseif($request->region) {
            $products = $products->where('address->region', $request->region);
        }

        if($request->status && count($request->status)) {
            $products = $products->whereIn('products.extras->status', $request->status);
        }
        
        if($request->size) {
            $products = $products->join('attribute_modification as size', function($join) use ($request) {
                $join->on('size.modification_id', '=', 'modifications.id')->where('size.attribute_id', 4)->where('size.value', '!=', 0)->where('size.value', '>', $request->size - 20)->where('size.value', '<', $request->size + 20);
            });
        }

        if($request->type === 'cottage' && count($request->cottage_type)) {
            $products = $products->join('attribute_modification as type', function($join) use ($request) {
                $join->on('type.modification_id', '=', 'modifications.id')->where('type.attribute_id', 1);

                $join->where(function($query) use ($request) {
                    foreach($request->cottage_type as $key => $val) {
                      $whereJsonContainsFunction = $key == 0? 'whereJsonContains' : 'orWhereJsonContains';
        
                      $query->{$whereJsonContainsFunction}('type.value', $val);
                    }
                  });
            });
          }

        $products = $products->paginate(5);
        
	  	$products = new \App\Http\Resources\Products($products);

        return response()->json($products);

    }
}
