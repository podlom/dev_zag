<?php

namespace App\Http\Controllers;

use DB as BaseDb; // added by @ts 2024-07-04 20:47
use Illuminate\Support\Facades\Log; // added by @ts 2024-07-04 20:47
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Aimix\Shop\app\Models\Product;
use Aimix\Shop\app\Models\Attribute;
use Aimix\Shop\app\Models\AttributeGroup;
use Aimix\Shop\app\Models\Category;
use Aimix\Shop\app\Models\Modification;
use Aimix\Shop\app\Models\Brand;
use App\Models\Country;
use Aimix\Promotion\app\Models\Promotion;
use Aimix\Review\app\Models\Review;
use App\Models\Faq;
use Backpack\NewsCRUD\app\Models\Article;
use Backpack\NewsCRUD\app\Models\Category as ArticleCategory;
use Backpack\PageManager\app\Models\Page;
use App\Models\Meta;
use App\Region;
use App\Area;
use App\City;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Kyivdistrict;
use App\Models\Statistics;
use App\Models\PollQuestion;
use App\Models\PollIp;
use App\Models\PollAnswer;
use Illuminate\Support\Facades\Redis;

class CatalogController extends \App\Http\Controllers\Controller
{
    const DEFAULT_LANG_FOR_PRODUCTS = 'uk';

    //
    // PROPERTIES
    //

    // filters etc.
    private $search_value = '';
    private $attributes = [];
    private $product_attributes = [];
    private $country = null;

    private $filters = [
        'search_value' => '',
        'attributes' => [],
        'country' => [],
    ];

    private $isJson = false;
    private $page = 1;
    private $per_page = null;
    private $sort = [
        'value' => 'created_at',
        'dirr' => 'desc'
    ];
    private $sort_string = 'created_at_desc';

    private $category = null;

    // data arrays
    private $all_filters = [];
    private $products;

    private $selected_filters = [];

    private $price_filter = null;

    private $is_new = false;
    private $is_sale = false;

    private $address = [
        'region' => null,
        'area' => null,
        'city' => null,
        'kyivdistrict' => null
    ];
    private $latlng = [
        'lat' => null,
        'lng' => null
    ];
    private $radius = 25;

    //
    // METHODS
    //
    public function __construct()
    {
        // $this->per_page = config('aimix.shop.per_page');
        $this->per_page = config('aimix.shop.per_page') - 1; // added by @ts 2024-08-19 21:03
    }

    // get overall catalog data
    public function index(Request $request, $category_slug)
    {
        // die(var_export($category_slug, true));
        Log::info(__METHOD__ . ' +' . __LINE__ . ' $category_slug: ' . var_export($category_slug, true)); // added by @ts 2024-07-04 20:48

        // Listen for database queries
        /* BaseDb::listen(function ($query) {
            Log::info(__METHOD__ . ' SQL: ' . $query->sql);
            Log::info(__METHOD__ . ' Bindings: ' . implode(', ', $query->bindings));
            Log::info(__METHOD__ . ' Time: ' . $query->time . 'ms');
        }); */

        // set properties
        $this->setPropertiesFromRequest($request, $category_slug);

        // select category
        $category = Category::where('slug', $category_slug)->firstOrTranslation('/catalog');

        // die(var_export($category,true)); // @ts
        Log::info(__METHOD__ . ' +' . __LINE__ . ' $category: ' . var_export($category, true)); // added by @ts 2024-07-04 20:48

        // if(!$category && Category::withoutGlobalScopes()->where('slug', $category_slug)->first())
        //   return redirect(Category::withoutGlobalScopes()->where('slug', $category_slug)->first()->link . '/catalog', 301);

        // if there is no category with this slug
        if ($category_slug && !$category) {
            Log::info(__METHOD__ . ' +' . __LINE__ . ' before 404 error.'); // added by @ts 2024-07-08 10:22
            abort(404);
        }

        $translation_link = $category->translation_link . '/catalog';
        $category_id = $category->id;
        // die(var_export($category_id,true)); // 2024-07-04 @ts

        // Range Price in Cache
        if (!Cache::has('price_range_options_' . $category_id)) {
            Log::info(__METHOD__ . ' +' . __LINE__ . ' !Cache::has("price_range_options_' . $category_id . '")'); // added by @ts 2024-07-08 10:24
            $expiresAt = Carbon::now()->addDay();

            $range_min = Modification::notBase()->where('price', '>', 0)->whereHas('product', function ($q) use ($category_id) {
                $q->where('products.is_active', 1)->where('products.category_id', $category_id);
            })->min('price');
            $range_max = Modification::notBase()->where('price', '>', 0)->whereHas('product', function ($q) use ($category_id) {
                $q->where('products.is_active', 1)->where('products.category_id', $category_id);
            })->max('price');

            $range_options = [
                'min' => $range_min,
                'max' => $range_max,
                'step' => 100
            ];

            Cache::put('price_range_options_' . $category_id, $range_options, $expiresAt);
        } else {
            $range_options = Cache::get('price_range_options_' . $category_id);
            Log::info(__METHOD__ . ' +' . __LINE__ . ' $range_options: ' . var_export($range_options, true)); // added by @ts 2024-07-08 10:24
        }

        if (!$this->selected_filters['price'])
            $this->selected_filters['price'] = [$range_options['min'], $range_options['max']];

        if ($this->selected_filters['price'][0] < $range_options['min'])
            $this->selected_filters['price'][0] = $range_options['min'];

        if ($this->selected_filters['price'][1] > $range_options['max'])
            $this->selected_filters['price'][1] = $range_options['max'];

        if ($this->selected_filters['price'][0] > $this->selected_filters['price'][1])
            $this->selected_filters['price'][0] = $this->selected_filters['price'][1];

        // die(var_export($this->selected_filters,true)); // @ts 2024-07-04 20:39

        if (!$this->isJson) {
            Log::info(__METHOD__ . ' +' . __LINE__ . ' $this->isJson: ' . var_export($this->isJson, true)); // added by @ts 2024-07-08 10:25
            return view('catalog.index')
                ->with('filters', $this->all_filters)
                ->with('selected_filters', (object)$this->selected_filters)
                ->with('category', $category)
                ->with('categories', Category::noEmpty()->get())
                ->with('range_options', $range_options)
                ->with('currentCategoryName', $category ? $category->name : trans('main.all_products'))
                ->with('currentCategorySlug', $category_slug)
                ->with('regions', Region::pluck('name', 'region_id'))
                ->with('page', Page::where('template', 'catalog')->first()->withFakes())
                ->with('translation_link', $translation_link);
        }


        // SELECT PRODUCTS
        $products = Product::with(['modifications', 'category'])
            ->active()
            ->distinct('products.id')
            ->select('products.*')
            ->join('modifications', 'modifications.product_id', '=', 'products.id')
            ->where('modifications.is_default', 0);

        if ($category_slug && $category) {
            $products = $products->where('category_id', $category->id);
            Log::info(__METHOD__ . ' +' . __LINE__); // added by @ts 2024-07-08 10:41
        }

        // Make request to DB
        if ($this->search_value) {
            Log::info(__METHOD__ . ' +' . __LINE__ . ' $this->search_value: ' . var_export($this->search_value, true)); // added by @ts 2024-07-08 10:26
            //$products .= 'AND pr.name LIKE "%' . $this->search_value . '%" ';
            $filterSwitched = switchTextToEnglish($this->search_value) != $this->search_value ? switchTextToEnglish($this->search_value) : switchTextToRussian($this->search_value);


            $products = $products->where(function ($query) use ($filterSwitched) {
                $regions = Region::where('name', 'like', '%' . $this->search_value . '%')->orWhere('name', 'like', '%' . $filterSwitched . '%')->pluck('region_id');
                $areas = Area::where('name', 'like', '%' . $this->search_value . '%')->orWhere('name', 'like', '%' . $filterSwitched . '%')->pluck('area_id');
                $cities = City::where('name', 'like', '%' . $this->search_value . '%')->orWhere('name', 'like', '%' . $filterSwitched . '%')->pluck('city_id');

                $query->where('products.name', 'like', '%' . $this->search_value . '%')->orWhere('products.name', 'like', '%' . $filterSwitched . '%')->orWhereIn('address->region', $regions)->orWhereIn('address->area', $areas)->orWhereIn('address->city', $cities);
                // ->orWhereHas('brand', function(Builder $brand_query){
                //   $brand_query->where('brands.name', 'like', '%'.$this->search_value.'%');
                // });
            });
        }

        foreach ($this->product_attributes as $attr => $values) {
            Log::info(__METHOD__ . ' +' . __LINE__ . ' $attr: ' . var_export($attr, true)); // added by @ts 2024-07-08 10:27
            Log::info(__METHOD__ . ' +' . __LINE__ . ' $values: ' . var_export($values, true)); // added by @ts 2024-07-08 10:27

            if (!count($values))
                continue;

            if ($attr == 'status') {
                $products = $products->where(function ($q) use ($values) {
                    $q->whereIn('products.extras->status', $values);
                    if (in_array('frozen', $values))
                        $q->orWhere('products.extras->is_frozen', 1);

                    if (in_array('sold', $values))
                        $q->orWhere('products.is_sold', 1);
                });
                continue;
            }

            $products = $products->whereIn('products.extras->' . $attr, $values);
        }

        foreach ($this->attributes as $attr_id => $attr_value) {
            Log::info(__METHOD__ . ' +' . __LINE__ . ' $attr_id: ' . var_export($attr_id, true)); // added by @ts 2024-07-08 10:27
            Log::info(__METHOD__ . ' +' . __LINE__ . ' $attr_value: ' . var_export($attr_value, true)); // added by @ts 2024-07-08 10:27

            if (!count($attr_value))
                continue;

            $attr_name = 'attr_' . $attr_id;

            if ($this->all_filters['attributes'][$attr_id]->type === 'number') {
                // $products = $products->where(function($query) use ($attr_value, $attr_id) {
                //     $query->where('attribute_modification.attribute_id', $attr_id)->where('attribute_modification.value', '>=', $attr_value[0])->where('attribute_modification.value', '<=', $attr_value[1]);
                // });
                $products = $products->join('attribute_modification as ' . $attr_name, function ($join) use ($attr_name, $attr_value, $attr_id) {
                    $join->on($attr_name . '.modification_id', '=', 'modifications.id')->where($attr_name . '.attribute_id', $attr_id)->where($attr_name . '.value', '>=', (int)$attr_value[0])->where($attr_name . '.value', '<=', (int)$attr_value[1]);
                });
            } else {
                $products = $products->join('attribute_modification as ' . $attr_name, function ($join) use ($attr_value, $attr_id, $attr_name) {
                    $join->on($attr_name . '.modification_id', '=', 'modifications.id')->where($attr_name . '.attribute_id', $attr_id);

                    $join->where(function ($query) use ($attr_value, $attr_name) {
                        foreach ($attr_value as $key => $val) {
                            $whereJsonContainsFunction = $key == 0 ? 'whereJsonContains' : 'orWhereJsonContains';

                            $query->{$whereJsonContainsFunction}($attr_name . '.value', $val);
                        }
                    });
                });
            }
        }

        // if(!$this->radius) {
        if ($this->address['region'])
            $products = $products->where('address->region', $this->address['region']);

        if ($this->address['kyivdistrict'])
            $products = $products->where('address->kyivdistrict', $this->address['kyivdistrict']);

        if ($this->address['area'])
            $products = $products->where('address->area', $this->address['area']);

        if ($this->address['city'])
            $products = $products->where('address->city', $this->address['city']);
        // $products.= 'AND json_unquote(json_extract(pr.address, "$.region")) = ' . $this->address['region'] . ' ';
        // }


        if (!$this->price_filter) {
            $this->price_filter = [$range_options['min'], $range_options['max']];
            Log::info(__METHOD__ . ' +' . __LINE__ . ' $this->price_filter: ' . var_export($this->price_filter, true)); // added by @ts 2024-07-08 10:28
        }

        if ($this->price_filter[0] != $range_options['min'] || $this->price_filter[1] != $range_options['max']) {
            $products = $products->where('modifications.price', '>=', $this->price_filter[0])->where('modifications.price', '<=', $this->price_filter[1]);
            Log::info(__METHOD__ . ' +' . __LINE__ . ' $products: ' . var_export($products, true)); // added by @ts 2024-07-08 10:28
        }

        //$products .= 'AND md.price BETWEEN ' . $this->price_filter[0] . ' AND ' . $this->price_filter[1] . ' ';

        // $offset = $request->page? $this->per_page * ($request->page - 1) : 0;

        $products = $products->orderBy($this->sort['value'], $this->sort['dirr'])->paginate($this->per_page);
        //$products .= 'GROUP BY pr.id ORDER BY ' . $this->sort['value'] . ' ' . $this->sort['dirr'] . ' LIMIT ' . $this->per_page . ' OFFSET ' . $offset;

        // if($this->radius) {
        //   $products_ids = clone ($this->products->pluck('id', 'id'));
        //   $this->products->pluck('address', 'id')->each(function($item, $key) use ($products_ids) {
        //     $distance = calculateTheDistance($item['latlng']['lat'], $item['latlng']['lng'], $this->latlng['lat'], $this->latlng['lng']);

        //     if($distance > $this->radius * 1000)
        //       $products_ids->forget($key);
        //   });

        //   $this->products = $this->products->whereIn('products.id', $products_ids);
        // } elseif($this->radius == 0) {
        //   if($this->address['region'])
        //     $this->products = $this->products->where('address->region', $this->address['region']);

        //   if($this->address['area'])
        //     $this->products = $this->products->where('address->area', $this->address['area']);


        //   if($this->address['city'])
        //     $this->products = $this->products->where('address->city', $this->address['city']);
        //

        $this->products = new \App\Http\Resources\Products($products);
        Log::info(__METHOD__ . ' +' . __LINE__ . ' $this->products: ' . var_export($this->products, true)); // added by @ts 2024-07-08 10:29

        // die(var_export($products, true)); // @ts 2024-07-04 20:38

        return response()->json(['products' => $this->products, 'aatr' => $this->attributes, 'range_options' => $range_options]);
    }




    /************/
    /* setPath */

    /************/
    // some setters
    private function setPath($request)
    {
        $path = $request->url() . '?per_page=' . $this->selected_filters['per_page'] . '&page=' . $this->selected_filters['page'] . '&sort=' . $this->selected_filters['sort'];

        if ($this->selected_filters['filters']['search_value'])
            $path .= '&filters[search_value]=' . $this->selected_filters['filters']['search_value'];

        if ($this->selected_filters['filters']['country'])
            $path .= '&filters[country]=' . $this->selected_filters['filters']['country'];

        foreach ($this->selected_filters['filters']['attributes'] as $key => $attribute) {
            $path .= '&filters[attributes][' . $key . ']=' . $attribute;
        }

        return $path;
    }



    /************/
    /* setPropertiesFromRequest */

    /************/
    private function setPropertiesFromRequest($request, $category_slug)
    {

        if ($category_slug) {
            $this->category = Category::where('slug', $category_slug)->first();
        }

        if ($request->page)
            $this->page = $request->page;
        else
            $this->page = 1;

        if ($request->price) {
            $this->price_filter = $request->price;
        } else {
            $this->price_filter = null;
        }

        if ($this->category) {
            $this->all_filters = [
                'attributes' => $this->category->attributes()->where('in_filters', 1)->get()->keyBy('id'),
                'wall_materials' => __('attributes.wall_materials'),
                'roof_materials' => __('attributes.roof_materials'),
                'statuses' => array_merge(__('main.product_statuses'), ['frozen' => __('main.Заморожено')]),
                'newbuild_types' => __('attributes.newbuild_types'),
                // 'countries' => Country::noEmpty()->get()
            ];
        } else {
            $this->all_filters = [
                'attributes' => Category::first()->attributes()->where('in_filters', 1)->get()->keyBy('id'),
                // 'countries' => Country::noEmpty()->get()
            ];
        }

        if ($request->is_new)
            $this->is_new = true;

        if ($request->is_sale)
            $this->is_sale = true;

        // Set properties form request
        if ($request->has('filters')) {
            if (isset($request->input('filters')['search_value']))
                $this->search_value = $request->input('filters')['search_value'];
            else
                $this->search_value = '';

            if (isset($request->input('filters')['attributes']))
                $this->attributes = $request->input('filters')['attributes'];
            else
                $this->attributes = [];

            if (isset($request->input('filters')['product_attributes']))
                $this->product_attributes = $request->input('filters')['product_attributes'];
            else
                $this->product_attributes = [];

            if (isset($request->input('filters')['country']))
                $this->country = $request->input('filters')['country'];

            $this->filters = $request->input('filters');
        }

        if ($request->has('per_page'))
            $this->per_page = $request->input('per_page');

        if ($request->has('sort')) {
            $this->sort_string = $request->input('sort');
            $this->sort = $this->getSortArray($request->input('sort'));
        }

        if ($request->has('address')) {
            foreach ($request->input('address') as $key => $value) {
                $this->address[$key] = $value;
            }
        }

        if ($request->has('latlng'))
            $this->latlng = $request->input('latlng');

        if ($request->has('radius'))
            $this->radius = $request->input('radius');

        $this->attributes = array_filter($this->attributes, function ($item) {
            return $item == 'Не выбрано' || (gettype($item) == 'array' && !count($item)) ? false : true;
        });

        $this->product_attributes = array_filter($this->product_attributes, function ($item) {
            return $item == 'Не выбрано' || (gettype($item) == 'array' && !count($item)) ? false : true;
        });

        if ($request->isJson)
            $this->isJson = $request->isJson;

        foreach ($this->all_filters['attributes'] as $key => $attr) {
            if (isset($this->attributes[$key]))
                continue;

            $this->attributes[$key] = [];

            if ($attr->type === 'number')
                $this->attributes[$key] = [+$attr->values->min, +$attr->values->max];
        }
        if (!isset($this->product_attributes['wall_material']))
            $this->product_attributes['wall_material'] = [];

        if (!isset($this->product_attributes['roof_material']))
            $this->product_attributes['roof_material'] = [];

        if (!isset($this->product_attributes['status']))
            $this->product_attributes['status'] = [];

        if (!isset($this->product_attributes['newbuild_type']))
            $this->product_attributes['newbuild_type'] = [];

        $this->selected_filters = [
            'isJson' => true,
            'price' => $this->price_filter,
            'address' => $this->address,
            'latlng' => $this->latlng,
            'radius' => $this->radius,
            'filters' => [
                'search_value' => $this->search_value,
                'attributes' => $this->attributes,
                'product_attributes' => $this->product_attributes,
                // 'country' => $this->country,
            ],
            'sort' => $this->sort_string,
            'per_page' => $this->per_page,
            'page' => $this->page
        ];
    }

    private function getSortArray($sort_string)
    {
        preg_match_all("/([\w]+)_([\w]+)/", $sort_string, $value);

        return ['value' => $value[1][0], 'dirr' => $value[2][0]];
    }

    public function requestSearchList(Request $request, $type, $value)
    {
        $values = [];

        if ($type == 'brand' && $value) {
            $values = Brand::noEmpty()->where('name', 'like', '%' . $value . '%')->get();
        } elseif ($type == 'name' && $value) {
            $values = Product::where('is_active', 1)->where('name', 'like', '%' . $value . '%')->get();
        }

        return response()->json($values);
    }

    public function show(Request $request, $category_slug, $slug, $tab = null, $project_slug = null)
    {
        // die(__FILE__ . ' +' . __LINE__);
        Log::info(__METHOD__ . ' +' . __LINE__ . ' $category_slug: ' . var_export($category_slug, true)); // added by @ts 2024-08-16 14:43
        Log::info(__METHOD__ . ' +' . __LINE__ . ' $slug: ' . var_export($slug, true)); // added by @ts 2024-08-16 14:43

        if (session()->has('lang')) {
            $lang = session('lang');
            Log::info(__METHOD__ . ' +' . __LINE__ . ' @ts session $lang: ' . var_export($lang, true)); // added by @ts 2024-08-16 15:01
        } else {
            Log::info(__METHOD__ . ' +' . __LINE__ . ' @ts no $lang session is defined.'); // added by @ts 2024-08-16 15:01
        }

        $data = [];
        $data['start'] = microtime(true);
        $data['product'] = Product::where('slug', $slug)->active()->firstOrTranslation();
        $data['project'] = null;
        $data['tab_name'] = '';

        if (!$data['product'])
            abort(404);

        $data['translation_link'] = $tab ? $data['product']->translation_link . "/$tab" : $data['product']->translation_link;
        $data['product'] = $data['product']->withFakes();

        $data['tab'] = $tab;
        $type = $data['product']->category_id == 1 || $data['product']->category->original_id == 1 ? 'cottage' : 'newbuild';
        $data['poll'] = PollQuestion::where('is_active', 1)->whereIn('type', [$type, 'all'])->get()->shuffle()->first();
        $data['poll_voted'] = $data['poll'] ? PollIp::whereIn('product_id', [$data['product']->id, $data['product']->original_id])->whereIn('question_id', [$data['poll']->id, $data['poll']->original_id])->where('ip', $request->ip())->exists() : false;
        $data['poll_answers'] = $data['poll'] ? PollAnswer::whereIn('product_id', [$data['product']->id, $data['product']->original_id])->whereIn('question_id', [$data['poll']->id, $data['poll']->original_id])->pluck('votes', 'option_id') : [];

        $category = Category::where('slug', $category_slug)->firstOrTranslation('/' . $data['product']->slug);
        $category_id = $category->id;
        // dd($data['product']->notBaseModifications->first()->attrs);

        $data['h1'] = $data['product']->name;
        $data['meta_title'] = $data['product']->meta_title;
        $data['meta_desc'] = $data['product']->meta_desc;

        $region = $data['product']->region;
        $area = $data['product']->area;
        $city = $data['product']->city;

        if (!$data['meta_title']) {
            $data['meta_title'] = $data['product']->name;
            $data['meta_title'] = $data['meta_title'] . ' - ' . $data['product']->type;
            $data['meta_title'] = $region ? $data['meta_title'] . ' | ' . $region : $data['meta_title'];
            $data['meta_title'] = $area && $area != $region ? $data['meta_title'] . ', ' . $area : $data['meta_title'];
            $data['meta_title'] = $city && $city != $area ? $data['meta_title'] . ', ' . $city : $data['meta_title'];
        }

        if (!$data['meta_desc']) {
            $data['meta_desc'] = '► ' . __('main.type_' . $type) . ' ► ' . $data['product']->name . ' ► ' . $data['product']->type;
            $data['meta_desc'] = $region ? $data['meta_desc'] . ' ➨ ' . $region : $data['meta_desc'];
            $data['meta_desc'] = $area && $area != $region ? $data['meta_desc'] . ' ➨ ' . $area : $data['meta_desc'];
            $data['meta_desc'] = $city && $city != $area ? $data['meta_desc'] . ' ➨ ' . $city : $data['meta_desc'];
            $data['meta_desc'] = $data['meta_desc'] . ': ' . __('main.цена, фото, местоположение') . ' ☛ ' . __('main.Заходите') . '! ✔️ ';
        }

        if (!$tab) {
            $data['types'] = $category->id === 1 || $category->original_id === 1 ? $data['product']->modifications->where('is_default', 0)->groupBy('type_key') : $data['product']->modifications->where('is_default', 0)->groupBy('rooms');

            $data['statuses_array'] = [];
            $data['houses_amount'] = 0;
            $data['houses_area'] = ['min' => 0, 'max' => 0];
            $data['plot_amount'] = 0;
            $data['plot_area'] = null;
            $data['companies'] = Brand::whereIn('category_id', [1, 18])->where('is_popular', 1)->orderBy('created_at', 'desc')->take(4)->get();
            $data['companies_count'] = Brand::whereIn('category_id', [1, 18])->count();
            $data['promotions'] = Promotion::orderBy('created_at', 'desc')->take(12)->get();
            $data['promotions_count'] = Promotion::count();
            // start rating position
            $data['rating_position'] = $this->getRatingPosition($data['product']);
            // end rating position

            foreach ($data['types'] as $type => $projects) {
                if ($type == 'Земельный участок') {
                    $data['plot_amount'] = count($projects);
                    $data['plot_area'] = [
                        'min' => $projects->min('area'),
                        'max' => $projects->max('area')
                    ];
                } else {
                    if ($projects->where('area', '!=', 0)->count()) {
                        $data['houses_area']['min'] = ($projects->where('area', '!=', 0)->min('area') && $projects->where('area', '!=', 0)->min('area') < $data['houses_area']['min']) || !$data['houses_area'] || !$data['houses_area']['min'] ? $projects->where('area', '!=', 0)->min('area') : $data['houses_area']['min'];
                        $data['houses_area']['max'] = $projects->where('area', '!=', 0)->max('area') > $data['houses_area']['max'] ? $projects->where('area', '!=', 0)->max('area') : $data['houses_area']['max'];
                    }
                }

                $data['houses_amount'] += $projects->sum('total');

                if (!isset($data['statuses_array'][$type]))
                    $data['statuses_array'][$type] = ['project' => null, 'building' => null, 'done' => null];

                $numNotEmptyAmount = 0;

                foreach ($projects as $project) {
                    foreach ($project->amount as $key => $amount) {
                        if ($amount !== null) {
                            $data['statuses_array'][$type][$key] += $amount;
                            $numNotEmptyAmount++;
                        }
                    }
                }

                if ($numNotEmptyAmount === 0)
                    unset($data['statuses_array'][$type]);
            }
        } elseif ($tab == 'reviews') {
            $data['tab_name'] = __('main.Отзывы');
            $data['reviews'] = $data['product']->reviews()->orderBy('created_at', 'desc')->paginate(10);
            Log::info(__METHOD__ . ' +' . __LINE__ . ' product reviews: ' . var_export($data['reviews'], true)); // added by @ts 2024-08-16 14:45

            if ($category_id == 1 || $category_id == 6) {
                $data['h1'] = $data['product']->name . ': ' . __('main.отзывы о поселке');
                $data['meta_title'] = __('main.Отзывы о') . ' ' . $data['product']->name . ', ' . $data['product']->city . '. ' . __('main.Мнения о коттеджных городках');
                $data['meta_desc'] = __('main.Отзывы о') . ' ' . $data['product']->name . ', ' . $data['product']->city . ' ' . __('main.от застройщика') . ' ► ' . __('main.Оставить отзыв о') . ' ' . $data['product']->name . ' ► ' . __('main.Все отзывы, мнения и обсуждения о поселке');
            } else {
                $data['h1'] = $data['product']->name . ': ' . __('main.отзывы о новостройке');
                $data['meta_title'] = __('main.Отзывы о') . ' ' . $data['product']->name . ', ' . $data['product']->city . ' | ' . __('main.Отзывы о новостройках в пригороде');
                $data['meta_desc'] = __('main.Отзывы о') . ' ' . $data['product']->name . ', ' . $data['product']->city . ' ' . __('main.от застройщика') . ' ► ' . __('main.Оставить отзыв о') . ' ' . $data['product']->name . ' ► ' . __('main.Все отзывы, мнения и обсуждения о пригородных новостройках');
            }

            if ($request->isJson)
                return response()->json(['reviews' => $data['reviews']]);
        } elseif ($tab == 'projects') {
            $data['tab_name'] = __('main.Типовые проекты');
            $data['projects'] = $data['product']->notBaseModifications->get();

            if ($category_id == 1 || $category_id == 6) {
                $data['h1'] = $data['product']->name . ': ' . mb_strtolower(__('main.Типовые проекты'));
                $data['meta_title'] = $data['product']->name . ': ' . mb_strtolower(__('main.Типовые проекты в коттеджном городке'));
                $data['meta_desc'] = __('main.Типовые проекты') . ' ' . __('main.в') . ' ' . $data['product']->name . ': ' . mb_strtolower(__('attributes.cottage_types_plural.Дуплекс')) . ' ➨ ' . mb_strtolower(__('attributes.cottage_types_plural.Коттедж')) . ' ➨ ' . mb_strtolower(__('attributes.cottage_types_plural.Таунхаус')) . ' ➨ ' . mb_strtolower(__('attributes.cottage_types_plural.Земельный участок')) . ' ➨ ' . mb_strtolower(__('main.Фото')) . ' ➨ ' . __('main.цена') . ' ➨ ' . __('main.планировка');
            } else {
                $data['h1'] = __('main.Планы квартир') . ' ' . __('main.в') . ' ' . $data['product']->name;
                $data['meta_title'] = __('main.Планировки') . ' ' . $data['product']->name . ', ' . $data['product']->city . ' | ' . mb_strtolower(__('main.Планы квартир')) . ' ' . __('main.от застройщика');
                $data['meta_desc'] = __('main.Все планировки квартир в жилом комплексе') . ' ' . $data['product']->name;
                $data['meta_desc'] = $data['product']->address_string ? $data['meta_desc'] . '(' . $data['product']->address_string . ')' : $data['meta_desc'];
                $data['meta_desc'] .= ' ◆ ' . __('main.Варианты планировок') . ': ' . __('main.однокомнатные') . ' ➨ ' . __('main.двухкомнатные') . ' ➨ ' . __('main.трехкомнатные') . ' ' . __('main.квартиры в новостройках');
            }
        } elseif ($tab == 'promotions') {
            $data['tab_name'] = __('main.Акции');
            $data['promotions'] = $data['product']->promotions;

            if ($category_id == 1 || $category_id == 6) {
                $data['h1'] = __('main.Акции') . ' ' . __('main.в') . ' ' . $data['product']->name;
                $data['meta_title'] = __('main.Акции и скидки') . ' ' . __('main.в') . ' ' . $data['product']->name;
                $data['meta_desc'] = __('main.Акции и скидки') . ' ' . __('main.для покупателей') . mb_strtolower(__('attributes.cottage_types_plural_genitive.Коттедж')) . ' ► ' . mb_strtolower(__('attributes.cottage_types_plural_genitive.Таунхаус')) . ' ► ' . mb_strtolower(__('attributes.cottage_types_plural_genitive.Дуплекс')) . ' ' . __('main.в') . ' ' . $data['product']->name . ' ' . __('main.от застройщика') . '. ' . __('main.Подпишитесь на получение скидок') . '!';
            } else {
                $data['h1'] = __('main.Акции') . ' ' . __('main.в') . ' ' . $data['product']->name . ', ' . $data['product']->city;
                $data['meta_title'] = __('main.Акции и скидки') . ' ' . __('main.в') . ' ' . $data['product']->name . ', ' . $data['product']->city . ' ' . __('main.от застройщика');
                $data['meta_desc'] = __('main.Акции и скидки') . ' ' . __('main.для покупателей') . ' ' . mb_strtolower(__('attributes.newbuild_types_plural_genitive.Квартира')) . ' ' . __('main.и') . ' ' . mb_strtolower(__('main.newbuild_types_plural_genitive.Апартаменты')) . ' ' . __('main.в') . ' ' . $data['product']->name . ' ' . __('main.от застройщика') . '. ' . __('main.Подпишитесь на получение скидок') . '!';
            }
        } elseif ($tab == 'map') {
            $data['tab_name'] = __('main.Карта');
            if ($category_id == 1 || $category_id == 6) {
                $data['h1'] = $data['product']->name . ': ' . mb_strtolower(__('main.Расположение')) . ' ' . __('main.на карте');
                $data['meta_title'] = __('main.type_cottage') . ' ' . $data['product']->name . ': ' . mb_strtolower(__('main.Расположение')) . ' ' . __('main.на карте');
                $data['meta_desc'] = __('main.Расположение') . ' ' . mb_strtolower(__('main.type_cottage_genitive')) . ' ' . __('main.на карте') . ' ' . __('main.от застройщика') . ': ' . $data['product']->region;
                $data['meta_desc'] = $data['product']->region != $data['product']->area ? $data['meta_desc'] . ', ' . $data['product']->area : $data['meta_desc'];
                $data['meta_desc'] = $data['product']->area != $data['product']->city ? $data['meta_desc'] . ', ' . $data['product']->city : $data['meta_desc'];
                $data['meta_desc'] .= ' ➨ ' . mb_strtolower(__('attributes.cottage_types_plural.Дуплекс')) . ' ➨ ' . mb_strtolower(__('attributes.cottage_types_plural.Коттедж')) . ' ➨ ' . mb_strtolower(__('attributes.cottage_types_plural.Таунхаус')) . ' ➨ ' . mb_strtolower(__('attributes.cottage_types_plural.Земельный участок')) . ' ➨ ' . mb_strtolower(__('main.Фото')) . ' ➨ ' . __('main.цена') . ' ➨ ' . mb_strtolower(__('main.Отзывы'));
            } else {
                $data['h1'] = $data['product']->name . ': ' . mb_strtolower(__('main.Расположение')) . ' ' . __('main.на карте');
                $data['meta_title'] = $data['product']->name . ': ' . mb_strtolower(__('main.Расположение')) . ' ' . __('main.на карте');
                $data['meta_desc'] = __('main.Расположение') . ' ' . $data['product']->name . ' ' . __('main.на карте') . ' ' . __('main.от застройщика') . ': ' . $data['product']->region;
                $data['meta_desc'] = $data['product']->region != $data['product']->area ? $data['meta_desc'] . ', ' . $data['product']->area : $data['meta_desc'];
                $data['meta_desc'] = $data['product']->area != $data['product']->city ? $data['meta_desc'] . ', ' . $data['product']->city : $data['meta_desc'];
                $data['meta_desc'] .= ' ➨ ' . mb_strtolower(__('attributes.newbuild_types_plural_genitive.Квартира')) . ' ➨ ' . mb_strtolower(__('main.Фото')) . ' ➨ ' . __('main.цена') . ' ➨ ' . mb_strtolower(__('main.Отзывы'));
            }
        } elseif ($tab == 'video') {
            $data['tab_name'] = __('main.Видео');
            if ($category_id == 1 || $category_id == 6) {
                // $data['h1'] = __('main.type_cottage') . ' ' . $data['product']->name . ': ' . mb_strtolower(__('main.Видео о поселке'));
                $data['h1'] = $data['product']->name . ': ' . mb_strtolower(__('main.Видео о поселке'));
                $data['meta_title'] = __('main.Видео о поселке') . ' ' . $data['product']->name . ': ' . $data['product']->region;
                $data['meta_title'] = $data['product']->region != $data['product']->area ? $data['meta_title'] . ', ' . $data['product']->area : $data['meta_title'];
                $data['meta_title'] = $data['product']->area != $data['product']->city ? $data['meta_title'] . ', ' . $data['product']->city : $data['meta_title'];
                $data['meta_desc'] = __('main.Смотрите видео о') . ' ' . $data['product']->name . ' ' . __('main.от застройщика') . ': ' . $data['product']->region;
                $data['meta_desc'] = $data['product']->region != $data['product']->area ? $data['meta_desc'] . ', ' . $data['product']->area : $data['meta_desc'];
                $data['meta_desc'] = $data['product']->area != $data['product']->city ? $data['meta_desc'] . ', ' . $data['product']->city : $data['meta_desc'];
                $data['meta_desc'] .= ' ➨ ' . mb_strtolower(__('attributes.cottage_types_plural.Дуплекс')) . ' ➨ ' . mb_strtolower(__('attributes.cottage_types_plural.Коттедж')) . ' ➨ ' . mb_strtolower(__('attributes.cottage_types_plural.Таунхаус')) . ' ➨ ' . mb_strtolower(__('attributes.cottage_types_plural.Земельный участок')) . ' ➨ ' . mb_strtolower(__('main.Фото')) . ' ➨ ' . __('main.цена') . ' ➨ ' . mb_strtolower(__('main.Отзывы'));
            } else {
                $data['h1'] = $data['product']->name . ': ' . mb_strtolower(__('main.Видео о новостройке'));
                $data['meta_title'] = __('main.Видео о новостройке') . ' ' . $data['product']->name . ': ' . $data['product']->region;
                $data['meta_title'] = $data['product']->region != $data['product']->area ? $data['meta_title'] . ', ' . $data['product']->area : $data['meta_title'];
                $data['meta_title'] = $data['product']->area != $data['product']->city ? $data['meta_title'] . ', ' . $data['product']->city : $data['meta_title'];
                $data['meta_desc'] = __('main.Смотрите видео о') . ' ' . $data['product']->name . ' ' . __('main.от застройщика') . ': ' . $data['product']->region . ', ' . $data['product']->area;
                $data['meta_desc'] = $data['product']->region != $data['product']->area ? $data['meta_desc'] . ', ' . $data['product']->area : $data['meta_desc'];
                $data['meta_desc'] = $data['product']->area != $data['product']->city ? $data['meta_desc'] . ', ' . $data['product']->city : $data['meta_desc'];
                $data['meta_desc'] .= ' ➨ ' . __('main.квартиры в пригородных новостройках') . ' ➨ ' . mb_strtolower(__('main.Фото')) . ' ➨ ' . __('main.цена') . ' ➨ ' . mb_strtolower(__('main.Отзывы'));
            }
        }

        if ($project_slug) {
            $data['project'] = $data['product']->modifications->where('slug', $project_slug)->first();

            if (!$data['project'])
                abort(404);

            $data['translation_link'] = $data['project']->translation_link;
            $data['types'] = $data['product']->modifications->where('slug', '!=', $project_slug)->where('is_default', 0)->groupBy('type_key');
            $area_string = $data['project']->area ? ' (' . $data['project']->area . ' ' . $data['project']->area_unit . ')' : ''; // @ts 2024-08-02

            if ($category_id == 1 || $category_id == 6) {
                $data['h1'] = $data['product']->name . ' - ' . $data['project']->name . $area_string;
                $data['meta_title'] = $data['product']->name . ': ' . $data['project']->name . $area_string . ' ' . __('main.в') . ' ' . __('main.коттеджном городке');
                $data['meta_desc'] = __('main.Типовый проект') . ' ' . $data['project']->name . $area_string . ' ' . __('main.в') . ' ' . __('main.коттеджном городке') . ' ' . $data['product']->name . ': ' . mb_strtolower(__('attributes.cottage_types_plural.Дуплекс')) . ' ➨ ' . mb_strtolower(__('attributes.cottage_types_plural.Коттедж')) . ' ➨ ' . mb_strtolower(__('attributes.cottage_types_plural.Таунхаус')) . ' ➨ ' . mb_strtolower(__('attributes.cottage_types_plural.Земельный участок')) . ' ➨ ' . mb_strtolower(__('main.Фото')) . ' ➨ ' . __('main.цена') . ' ➨ ' . __('main.планировка');
            } else {
                $data['h1'] = $data['product']->name . ' - ' . $data['project']->name . $area_string;
                $data['meta_title'] = $data['product']->name . ': ' . $data['project']->name . $area_string . ' ' . __('main.в') . ' ' . __('main.жилом комплексе');
                $data['meta_desc'] = __('main.Типовый проект') . ' ' . $data['project']->name . $area_string . ' ' . __('main.в') . ' ' . __('main.жилом комплексе') . ' ' . $data['product']->name . ': ' . mb_strtolower(__('attributes.newbuild_types_plural_genitive.Квартира')) . ' ➨ ' . mb_strtolower(__('main.Фото')) . ' ➨ ' . __('main.цена') . ' ➨ ' . __('main.планировка');
            }

            return view('product.project', $data);
        }

        $data['same_category_products'] = Product::active()->where('category_id', $category_id)->where('slug', '!=', $slug)->get();

        $view = $tab ? 'product.' . $tab : 'product.show';

        return view($view, $data);
    }

    public function getNearestProducts(Request $request)
    {
        $key = 'nearest.product:' . $request->product_id;

        if ($products = Redis::get($key)) {
            return response()->json(['products' => json_decode($products)]);
        }

        $product = Product::find($request->product_id);

        $other_products = Product::where('category_id', $product->category_id)->where('id', '!=', $product->id)->where('address->city', $product->address['city'])->paginate(12);
        $other_products = $other_products->total() < 12 ? Product::where('category_id', $product->category_id)->where('id', '!=', $product->id)->where('address->area', $product->address['area'])->paginate(12) : $other_products;
        $other_products = $other_products->total() < 12 ? Product::where('category_id', $product->category_id)->where('id', '!=', $product->id)->where('address->region', $product->address['region'])->paginate(12) : $other_products;

        $other_products = new \App\Http\Resources\Products($other_products);

        Redis::set($key, json_encode($other_products), 'EX', 108000);

        return response()->json(['products' => $other_products]);
    }


    /*
    ***
    ***	PRODUCT RATING PAGE
    ***
    /*****/
    public function product_rating(Request $request, $category, $slug)
    {
        $data = [];
        $category = Category::where('slug', $category)->firstOrTranslation('/' . $slug . '/rating');
        $product = Product::where('slug', $slug)->firstOrTranslation('/rating');
        $data['product'] = $product;
        $data['translation_link'] = $product->translation_link . '/rating';

        if (!$data['product'])
            abort(404);

        $data['type'] = ($product->category_id === 1 || $product->category_id === 6) ? 'cottage' : 'newbuild';

        // start rating table
        $table = [];

        // type
        $type = $product->type;

        $rating = 0;

        switch ($type) {
            case 'Коттедж':
            case 'Котедж':
                $rating = 4;
                break;
            case 'Таунхаус':
                $rating = 3;
                break;
            case 'Дуплекс':
                $rating = 3;
                break;
            case 'Квадрекс':
                $rating = 3;
                break;
            case 'Эллинг':
            case 'Елінг':
                $rating = 2;
                break;
            case 'Вилла':
            case 'Вілла':
                $rating = 5;
                break;
            case 'Земельный участок':
            case 'Ділянка':
                $rating = 2;
                break;
            case 'Квартира':
                $rating = 2;
                break;
            case 'Апартаменты':
                $rating = 3;
                break;
        }

        if ($type) {
            $table[__('main.Тип недвижимости')] = [
                'value' => $type,
                'rating' => $rating
            ];
        }

        // status
        $status = $product->extras['status'];
        $rating = 0;

        switch ($status) {
            case 'sold':
            case 'done':
                $rating = 5;
                break;
            case 'building':
                $rating = 3;
                break;
            case 'project':
                $rating = 2;
                break;
        }

        $table[__('main.Готовность')] = [
            'value' => __('main.product_statuses.' . $status),
            'rating' => $rating
        ];

        // region
        if ($product->address['region'] == 29) // Киев
            $rating = 5;
        else
            $rating = 4;

        $table[__('main.Область')] = [
            'value' => $product->region,
            'rating' => $rating
        ];

        // distance
        $distance = $product->extras['distance'];

        if ($distance <= 20)
            $rating = 7;
        elseif ($distance <= 30)
            $rating = 6;
        elseif ($distance <= 40)
            $rating = 5;
        elseif ($distance <= 50)
            $rating = 4;
        elseif ($distance <= 70)
            $rating = 3;
        elseif ($distance <= 100)
            $rating = 2;
        else
            $rating = 1;

        $table[__('main.Расстояние от черты города')] = [
            'value' => $distance,
            'rating' => $rating
        ];

        if ($product->notBaseModifications->count()) {
            // total objects
            $total = 0;
            foreach ($product->notBaseModifications->get() as $mod) {
                $total += $mod->attrs->find(6)->pivotValue + $mod->attrs->find(7)->pivotValue + $mod->attrs->find(8)->pivotValue;
            }
            $rating = 0;

            if ($total < 50)
                $rating = 1;
            elseif ($total < 100)
                $rating = 2;
            elseif ($total < 200)
                $rating = 3;
            elseif ($total >= 200)
                $rating = 4;

            $table[__('main.Количество домовладений')] = [
                'value' => $total,
                'rating' => $rating
            ];

            // area
            $area = $product->notBaseModifications->first()->area;
            $rating = 0;

            if ($area < 120)
                $rating = 2;
            elseif ($area < 200)
                $rating = 3;
            elseif ($area < 300)
                $rating = 4;
            elseif ($area >= 300)
                $rating = 5;

            $table[__('main.Площадь домовладения')] = [
                'value' => $area,
                'rating' => $rating
            ];

            // floors
            if ($product->category_id == 1 || $product->category_id == 6) {
                $floors = $product->notBaseModifications->first()->floors;
                $rating = 0;

                switch ($floors) {
                    case 1:
                        $rating = 1;
                        break;
                    case 2:
                        $rating = 2;
                        break;
                    case 6:
                    case 5:
                    case 4:
                    case 3:
                        $rating = 3;
                        break;
                }

                $table[__('main.Этажность')] = [
                    'value' => $floors,
                    'rating' => $rating
                ];
            } else {
                $floors = $product->extras['floors'];
                $rating = 0;

                if ($floors <= 5)
                    $rating = 1;
                elseif ($floors <= 12)
                    $rating = 3;
                elseif ($floors > 12)
                    $rating = 2;

                $table[__('main.Этажность')] = [
                    'value' => $floors,
                    'rating' => $rating
                ];
            }

            // price
            $price = $product->notBaseModifications->where('price', '!=', 0)->min('price');
            $rating = 0;

            if ($price == 0)
                $rating = 2;
            elseif ($price <= 13500)
                $rating = 4;
            elseif ($price <= 54000)
                $rating = 3;
            else
                $rating = 2;

            $table[__('main.Цена')] = [
                'value' => $price,
                'rating' => $rating
            ];
        }

        // infrastructure
        $prod = $product->original_id ? Product::withoutGlobalScopes()->find($product->original_id) : $product;
        $infrastructure = isset($prod->extras_translatable['infrastructure']) ? $prod->extras_translatable['infrastructure'] : '';
        $rating = 0;

        $keywords = ['автомойка', 'парковка', 'аптека', 'скважина', 'банк', 'бассейн', 'бильярд', 'боулинг', 'выставочный центр', 'гараж', 'гольф', 'детсад', 'детский сад', 'детские площадки', 'детская площадка', 'казино', 'кино', 'баня', 'сауна', 'ледовый стадион', 'каток', 'медпункт', 'медицинский центр', 'мини-отель', 'минимаркет', 'супермаркет', 'магазин', 'ночной клуб', 'офисы', 'паркинг', 'парикмахерская', 'салон красоты', 'пляж', 'пожарная', 'прачечная', 'ресторан', 'бар', 'кафе', 'салон красоты', 'парная', 'спортивные площадки', 'спортивная площадка', 'СТО', 'теннис', 'фитнес', 'спортивный центр', 'спортивный зал', 'химчистка', 'храм', 'церковь', 'школа', 'школу', 'лицей', 'яхт-клуб', 'ТРЦ', 'индивидуальное отопление', 'металлопластиковые окна', 'бронированные двери', 'автономное энергообеспечение', 'частная клиника', 'охрана', 'видеонаблюдение'];

        foreach ($keywords as $word) {
            if (strpos($infrastructure, $word) != false)
                $rating += 1;
        }

        $table[__('main.Инфраструктура')] = [
            'value' => isset($product->extras_translatable['infrastructure']) ? $product->extras_translatable['infrastructure'] : '',
            'rating' => $rating
        ];

        // wall material
        $wall = $product->extras['wall_material'];

        switch ($wall) {
            case 14:
            case 21:
                $rating = 4;
            case 11:
            case null:
                $rating = 2;
                break;
            case 1:
            case 5:
            case 3:
                $rating = 5;
                break;
            default:
                $rating = 3;
        }

        $table[__('main.Материал стен')] = [
            'value' => $wall ? __('attributes.wall_materials.' . $wall) : '-',
            'rating' => $rating
        ];

        if ($product->category_id == 1 || $product->category_id == 6) {
            // roof material
            $roof = $product->extras['roof_material'];

            switch ($roof) {
                case null:
                case 1:
                    $rating = 1;
                    break;
                case 6:
                    $rating = 2;
                    break;
                case 5:
                case 4:
                    $rating = 4;
                    break;
                case 2:
                case 3:
                    $rating = 3;
                    break;
            }

            $table[__('main.Материал крыши')] = [
                'value' => $roof ? __('attributes.roof_materials.' . $roof) : '-',
                'rating' => $rating
            ];
        }

        // communications
        if (isset($product->extras['communications'])) {
            $communications = $product->extras['communications'];
            $rating = (2 * count($communications));

            $table[__('main.Коммуникации')] = [
                'value' => $product->communications_string,
                'rating' => $rating
            ];
        }

        $data['table'] = $table;
        // end rating table

        // start rating position
        $data['rating_position'] = $this->getRatingPosition($product);
        // end rating position

        if ($request->isJson) {
            $products = Product::active()->where('top_rating', '!=', 0)->where('category_id', $product->category_id)->where('id', '!=', $product->id)->where('extras->is_frozen', 0)->orderByRaw('(ABS (top_rating - ' . $product->top_rating . ')) asc')->orderBy('id', 'desc')->paginate(12);
            $products = new \App\Http\Resources\Products($products);
            return response()->json($products);
        }

        return view('product.rating', $data);
    }


    /*****
     * COUNT ON WHAT POSITION OF ALL OBJECTS
     * THIS IS
     * /******/
    public function getRatingPosition(Product $product)
    {
        $position = 0;

        // start rating position
        if ($product->top_rating) {
            $position = DB::select("SELECT COUNT(*) AS position FROM products
          WHERE category_id = " . $product->category_id . "
          AND is_active = 1
          AND is_sold = 0
          AND json_unquote(json_extract(`extras`, '$.\"is_frozen\"')) = 0
          AND json_unquote(json_extract(`extras`, '$.\"status\"')) = 'building'
          AND top_rating >= " . $product->top_rating);

            $position = $position[0]->position;

            // if several products with same rating
            if (Product::where('top_rating', $product->top_rating)->count() > 1)
                $position = $position - (DB::select("SELECT COUNT(*) AS position FROM products
          WHERE category_id = " . $product->category_id . "
          AND is_active = 1
          AND is_sold = 0
          AND json_unquote(json_extract(`extras`, '$.\"is_frozen\"')) = 0
          AND json_unquote(json_extract(`extras`, '$.\"status\"')) = 'building'
          AND top_rating = " . $product->top_rating . "
          AND id < " . $product->id))[0]->position;
        }

        return $position;
        // end rating position
    }

    public function precatalog(Request $request, $slug)
    {
        Log::info(__METHOD__ . ' +' . __LINE__ . ' $slug: ' . var_export($slug, true)); // added by @ts 2024-08-16 14:01

        $category = Category::where('slug', $slug)->firstOrTranslation(explode($slug, urldecode(request()->path()))[1]);
        $data['page'] = Page::where('template', 'precatalog')->first()->withFakes()->toArray();

        if (!$category)
            abort(404);

        $products = Product::active()->where('category_id', $category->id);

        // START DEFAULT VALUES
        $arg1 = 0;
        $arg2 = 0;
        $frozen = 0;
        $data['areas'] = [];
        $data['cities'] = [];
        $data['region_name'] = __('main.Украина');
        $data['region_name_genitive'] = __('main.Украины');
        $data['article_region_name_genitive'] = __('main.Украины');
        $data['region_article'] = null;
        $data['classification_article'] = null;
        $data['translation_link'] = $category->translation_link;
        $region_model = null;
        $area_model = null;
        $city_model = null;
        $kyivdistrict_model = null;
        $data['region'] = null;
        $data['area'] = null;
        $data['city'] = null;
        $data['kyivdistrict'] = null;
        $redirect = request()->path();
        // END DEFAULT VALUES

        $segment1 = request()->segment(3);
        $segment2 = request()->segment(4);
        $segment3 = request()->segment(5);
        $segment4 = request()->segment(6);

        if ($segment4) {
            $arg1 = $segment3;
            $arg2 = $segment4;
        } elseif ($segment3) {
            $arg1 = $segment3;
        } elseif ($segment2) {
            $arg1 = $segment1;
            $arg2 = $segment2;
        } elseif ($segment1) {
            $arg1 = $segment1;
        }

        // GET LOCATION
        if ($segment1 == 'region') {
            $region_model = Region::where('slug', $segment2)->first();

            if ($region_model) {
                $data['areas'] = Area::where('region_id', $region_model->region_id)->get();
                $data['region'] = $region_model;
                $data['region_name'] = $region_model->region_id == 29 ? $region_model->name : $region_model->name . ' ' . __('main.область');
                $data['region_name_genitive'] = $region_model->region_id == 29 ? $region_model->name_genitive : $region_model->name_genitive . ' ' . __('main.области');
                $data['article_region_name_genitive'] = $data['region_name_genitive'];
                $data['translation_link'] .= "/region/{$region_model->translation_slug}";
                $products = $products->where('address->region', $region_model->region_id);
                // $data['region_article'] = $data['region']->region_id == 29? Article::find([55341,55353])->first() : Article::where('status', 'PUBLISHED')->whereIn('category_id', [204,208])->where('title', 'like', $data['region']->name . '%')->first();
            } elseif (Region::withoutGlobalScopes()->where('slug', $segment2)->first()) {
                // if region with this slug exists in other language
                $translation = Region::withoutGlobalScopes()->where('slug', $segment2)->first();
                if (!$translation->original && !count($translation->translations))
                    abort(404);

                $translation = $translation->original ? $translation->original->slug : $translation->translations->first()->slug;
                $redirect = str_replace($segment2, $translation, $redirect);
            } else {
                abort(404);
            }
        } elseif ($segment1 == 'area') {
            $area_model = Area::where('slug', $segment2)->first();

            if ($area_model) {
                $data['areas'] = Area::where('region_id', $area_model->region_id)->get();
                $data['cities'] = City::where('area_id', $area_model->area_id)->get();
                $data['area'] = $area_model;
                $data['region_name'] = $area_model->is_center || strpos($area_model->name, 'горсовет') !== false || strpos($area_model->name, 'міськрада') !== false ? $area_model->name : $area_model->name . ' ' . __('main.район');
                $data['region_name_genitive'] = $area_model->is_center || strpos($area_model->name, 'горсовет') !== false || strpos($area_model->name, 'міськрада') ? $area_model->name_genitive : $area_model->name_genitive . ' ' . __('main.района');
                $region_model = $area_model->region;
                $data['translation_link'] .= "/area/{$area_model->translation_slug}";
                $products = $products->where('address->area', $area_model->area_id);
            } elseif (Area::withoutGlobalScopes()->where('slug', $segment2)->first()) {
                // if area with this slug exists in other language
                $translation = Area::withoutGlobalScopes()->where('slug', $segment2)->first();
                if (!$translation->original && !count($translation->translations))
                    abort(404);

                $translation = $translation->original ? $translation->original->slug : $translation->translations->first()->slug;
                $redirect = str_replace($segment2, $translation, $redirect);
            } else {
                abort(404);
            }
        } elseif ($segment1 == 'city') {
            $city_model = City::where('slug', $segment2)->first();

            if ($city_model) {

                $same_name_area = Area::where('slug', $segment2)->where('area_id', $city_model->area_id)->first();

                if ($same_name_area && $same_name_area->name === $city_model->name)
                    abort(404);

                $data['cities'] = City::where('area_id', $city_model->area_id)->get();
                $data['city'] = $city_model;
                $data['region_name'] = $city_model->name;
                $data['region_name_genitive'] = $city_model->name_genitive ? $city_model->name_genitive : $city_model->name;
                $area_model = $city_model->area;
                $region_model = $area_model->region;
                $data['translation_link'] .= "/city/{$city_model->translation_slug}";
                $products = $products->where('address->city', $city_model->city_id);
            } elseif (City::withoutGlobalScopes()->where('slug', $segment2)->first()) {
                // if city with this slug exists in other language
                $translation = City::withoutGlobalScopes()->where('slug', $segment2)->first();
                if (!$translation->original && !count($translation->translations))
                    abort(404);

                $translation = $translation->original ? $translation->original->slug : $translation->translations->first()->slug;
                $redirect = str_replace($segment2, $translation, $redirect);
            } else {
                abort(404);
            }
        } elseif ($segment1 == 'kyivdistrict') {
            $kyivdistrict_model = Kyivdistrict::where('slug', $segment2)->first();

            if ($kyivdistrict_model) {
                $data['kyivdistrict'] = $kyivdistrict_model;
                $data['region_name'] = $kyivdistrict_model->name;
                $data['region_name_genitive'] = $kyivdistrict_model->name_genitive ? $kyivdistrict_model->name_genitive . ' ' . __('main.района') : $kyivdistrict_model->name . ' ' . __('main.район');
                $region_model = Region::where('region_id', 29)->first();
                $data['translation_link'] .= "/kyivdistrict/{$kyivdistrict_model->translation_slug}";
                // $data['region_article'] = Article::where('status', 'PUBLISHED')->whereIn('category_id', [205,209])->where('title', 'like', $data['kyivdistrict']->name . '%')->first();
                $products = $products->where('address->kyivdistrict', $kyivdistrict_model->kyivdistrict_id);
            } elseif (Kyivdistrict::withoutGlobalScopes()->where('slug', $segment2)->first()) {
                // if kyivdistrict with this slug exists in other language
                $translation = Kyivdistrict::withoutGlobalScopes()->where('slug', $segment2)->first();
                if (!$translation->original && !count($translation->translations))
                    abort(404);

                $translation = $translation->original ? $translation->original->slug : $translation->translations->first()->slug;
                $redirect = str_replace($segment2, $translation, $redirect);
            } else {
                abort(404);
            }
        }

        if ($redirect != request()->path())
            return redirect($redirect, 301);

        if ($region_model) {
            $data['article_region_name_genitive'] = $region_model->region_id == 29 ? $region_model->name_genitive : $region_model->name_genitive . ' ' . __('main.области');
        }

        $data['category'] = $category;
        $data['other_category'] = Category::where('slug', '!=', $slug)->first();
        $type = ($category->id === 1 || $category->original_id === 1) ? 'cottage' : 'newbuild';

        $allTypes = $category->id === 1 || $category->original_id === 1 ? __('attributes.cottage_types') : __('attributes.newbuild_types');
        $allTypesArray = [];
        $allTypesGenitive = $category->id === 1 || $category->original_id === 1 ? __('attributes.cottage_types_genitive') : __('attributes.newbuild_types_genitive');
        $allTypesPlural = $category->id === 1 || $category->original_id === 1 ? __('attributes.cottage_types_plural') : __('attributes.newbuild_types_plural');
        $allTypesPluralGenitive = $category->id === 1 || $category->original_id === 1 ? __('attributes.cottage_types_plural_genitive') : __('attributes.newbuild_types_plural_genitive');

        foreach ($allTypes as $key => $item) {
            $allTypesArray[\Str::slug($key)] = $key;
        }

        $status = isset(__('main.product_statuses')[$arg1]) || $arg1 === 'frozen' || $arg1 === 'sold' ? $arg1 : null;
        $data['status'] = $status;
        $statusPlural = $status ? __('main.product_statuses_plural')[$arg1] : null;
        $objectType = !$arg2 && (isset($allTypesArray[$arg1]) || isset($allTypesArray[$arg1])) ? $arg1 : $arg2;
        $objectType = isset($allTypesArray[$objectType]) ? $allTypesArray[$objectType] : null;
        $objectTypeGenitive = isset($allTypesGenitive[$objectType]) ? $allTypesGenitive[$objectType] : __('main.type_' . $type . '_genitive');
        $objectTypePlural = isset($allTypesPlural[$objectType]) ? $allTypesPlural[$objectType] : __('main.type_' . $type . '_plural');
        $objectTypePluralGenitive = isset($allTypesPluralGenitive[$objectType]) ? $allTypesPluralGenitive[$objectType] : __('main.type_' . $type . '_plural_genitive');

        // dd($redirect);

        if (($segment3 && !$status && !$objectType) || ($segment4 && !$objectType))
            abort(404);

        if ($status) {
            $data['translation_link'] .= "/$status";
            switch ($status) {
                case 'frozen':
                    $products = $products->where('products.extras->is_frozen', 1);
                    break;

                case 'sold':
                    // $products = $products->where('products.is_sold', 1)->where('products.extras->is_frozen', 0);
                    $products = $products->where('products.is_sold', 1);
                    break;

                default:
                    // $products = $products->where('products.extras->status', 'project')->where('products.extras->is_frozen', 0);
                    $products = $products->where('products.extras->status', $status);
                    break;
            }
        }

        if ($objectType) {
            $data['translation_link'] .= "/" . array_flip($allTypesArray)[$objectType];
            $products = $type == 'cottage' ? $products->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', str_replace('_', ' ', $objectType))->select('products.*') : $products->whereJsonContains('extras->newbuild_type', $objectType);
        }

        if (!$products->count())
            abort(404);

        $data['objectType'] = $objectType;
        $data['objectTypePlural'] = $objectTypePlural;

        $data['regions'] = Region::get();
        $data['kyivdistricts'] = Kyivdistrict::orderBy('name')->get();

        Log::info(__METHOD__ . ' +' . __LINE__ . ' $type: ' . var_export($type, true)); // added by @ts 2024-08-16 14:02
        $reviews = Review::where('type', $type);

        $promotions = Promotion::select('promotions.*')->distinct('promotions.id')->join('products', 'products.id', '=', 'promotions.product_id')->where('products.category_id', $category->id);
        $faqCategory = ($category->id === 1 || $category->original_id === 1) ? 47 : 48;
        $tagId = ($category->id === 1 || $category->original_id === 1) ? 81 : 33;


        // ->whereHas('tags', function(Builder $query_tags) use ($tagId) {
        //   $query_tags->where('tags.id', $tagId)->orWhere('tags.original_id', $tagId);
        // });


        if ($region_model) {
            $promotions = $promotions->whereHas('product', function ($query) use ($region_model) {
                $query->where('address->region', $region_model->region_id);
            });
            $reviews = $reviews->whereHasMorph(
                'reviewable',
                'Aimix\Shop\app\Models\Product',
                function ($query) use ($region_model) {
                    $query->where('products.address->region', $region_model->region_id);
                });
        }

        if ($area_model) {
            $promotions = $promotions->whereHas('product', function ($query) use ($area_model) {
                $query->where('address->area', $area_model->area_id);
            });
            $reviews = $reviews->whereHasMorph(
                'reviewable',
                'Aimix\Shop\app\Models\Product',
                function ($query) use ($area_model) {
                    $query->where('products.address->area', $area_model->area_id);
                });
        }

        if ($city_model) {
            $promotions = $promotions->whereHas('product', function ($query) use ($city_model) {
                $query->where('address->city', $city_model->city_id);
            });
            $reviews = $reviews->whereHasMorph(
                'reviewable',
                'Aimix\Shop\app\Models\Product',
                function ($query) use ($city_model) {
                    $query->where('products.address->city', $city_model->city_id);
                });
        }

        if ($kyivdistrict_model) {
            $promotions = $promotions->whereHas('product', function ($query) use ($kyivdistrict_model) {
                $query->where('address->kyivdistrict', $kyivdistrict_model->kyivdistrict_id);
            });
            $reviews = $reviews->whereHasMorph(
                'reviewable',
                'Aimix\Shop\app\Models\Product',
                function ($query) use ($kyivdistrict_model) {
                    $query->where('products.address->kyivdistrict', $kyivdistrict_model->kyivdistrict_id);
                });
        }

        $data['category_slug'] = $slug;
        $data['type'] = $type;

        $data['promotions'] = $promotions->take(12)->get();
        $data['promotions_total'] = $promotions->count();
        // $data['articles'] = $articles;
        if (!$objectType && !$status && !$region_model && !$area_model && !$city_model && !$kyivdistrict_model) {
            $data['questions'] = Faq::whereHas('category', function ($query) use ($faqCategory) {
                $query->where('id', $faqCategory)->orWhere('original_id', $faqCategory);
            })->orderBy('created_at', 'desc')->get();
        } else {
            $data['questions'] = null;
        }
        // $data['reviews'] = $reviews->orderBy('created_at')->take(12)->get();
        $data['reviews_total'] = $reviews->count();

        // Start exactly matching meta
        if ($region_model)
            $meta = Meta::where('is_map', 0)
                ->where('address->region', $region_model->region_id);
        else
            $meta = Meta::where('is_map', 0)
                ->where('address->region', 0);

        if ($data['area'])
            $meta = $meta->where('address->area', $data['area']->area_id);
        else
            $meta = $meta->where('address->area', 0);

        if ($data['city'])
            $meta = $meta->where('address->city', $data['city']->city_id);
        else
            $meta = $meta->where('address->city', 0);

        if ($status)
            $meta = $meta->where('status', $status);
        else
            $meta = $meta->where('status', null);

        if ($objectType)
            $meta = $meta->where('type', $objectType);
        else
            $meta = $meta->where('type', null);

        $meta = $meta->orderBy('created_at', 'desc')->first();
        // End exactly matching meta

        $exactly_matching_meta = false;

        if ($meta)
            $exactly_matching_meta = true;

        // Start 2/3 matching meta
        if (!$meta) {
            $meta = Meta::where('is_map', 0)
                ->where(function ($query) use ($region_model, $status, $objectType) {
                    $query->where(function ($q) use ($region_model, $status, $objectType) {
                        $q->where('status', $status)
                            ->where('type', null);

                        if ($region_model)
                            $q->where('address->region', $region_model->region_id);
                        else
                            $q->where('address->region', 0);

                    })->orWhere(function ($q) use ($region_model, $status, $objectType) {
                        $q->where('status', null)
                            ->where('type', $objectType);

                        if ($region_model)
                            $q->where('address->region', $region_model->region_id);
                        else
                            $q->where('address->region', 0);

                    })->orWhere(function ($q) use ($status, $objectType) {
                        $q->where('address->region', 0)
                            ->where('status', $status)
                            ->where('type', $objectType);
                    });
                });

            if ($area_model)
                $meta = $meta->whereIn('address->area', [$area_model->area_id, 0]);
            else
                $meta = $meta->where('address->area', 0);

            if ($city_model)
                $meta = $meta->whereIn('address->city', [$city_model->city_id, 0]);
            else
                $meta = $meta->where('address->city', 0);

            $meta = $meta->orderBy('created_at', 'desc')->first();
        }
        // End 2/3 matching meta

        // Start 1/3 matching meta
        if (!$meta) {
            if ($region_model) {
                $meta = Meta::where('is_map', 0)
                    ->where('address->region', $region_model->region_id)
                    ->where('status', null)
                    ->where('type', null);

                if ($data['area'])
                    $meta = $meta->whereIn('address->area', [$data['area']->area_id, 0]);
                else
                    $meta = $meta->where('address->area', 0);

                if ($data['city'])
                    $meta = $meta->whereIn('address->city', [$data['city']->city_id, 0]);
                else
                    $meta = $meta->where('address->city', 0);
            } elseif ($objectType) {
                $meta = Meta::where('is_map', 0)
                    ->where('address->region', 0)
                    ->where('status', null)
                    ->where('type', $objectType);
            } elseif ($status) {
                $meta = Meta::where('is_map', 0)
                    ->where('address->region', 0)
                    ->where('status', $status)
                    ->where('type', null);
            }

            $meta = $meta ? $meta->orderBy('created_at', 'desc')->first() : null;
        }
        // End 1/3 matching meta

        $data['seo_title'] = null;
        $data['seo_text'] = null;
        $data['meta_title'] = $data['page'][$type . '_meta_title'];
        $data['meta_desc'] = $data['page'][$type . '_meta_desc'];
        $data['h1'] = $data['page'][$type . '_h1'];

        if (!$region_model && !$area_model && !$city_model && !$kyivdistrict_model && !$arg1 && !$arg2) {
            $data['seo_title'] = $data['page'][$type . '_seo_title'];
            $data['seo_text'] = $data['page'][$type . '_seo_text'];
            // uncomment for classification article
            // $data['classification_article'] = $type == 'cottage'? ArticleCategory::find([387,398])->first() : Article::find([696,29668])->first();
        }

        if ($data['region'] && !$data['area']) {
            $data['meta_title'] = $data['region']->extras[$type . '_meta_title'] ? $data['region']->extras[$type . '_meta_title'] : $data['meta_title'];
            $data['meta_desc'] = $data['region']->extras[$type . '_meta_desc'] ? $data['region']->extras[$type . '_meta_desc'] : $data['meta_desc'];
            $data['seo_title'] = !$objectType && !$status && $data['region']->extras[$type . '_h1'] ? $data['region']->extras[$type . '_h1'] : $data['seo_title'];
            $data['h1'] = $data['region']->extras[$type . '_h1'] ? $data['region']->extras[$type . '_h1'] : $data['h1'];
            $data['seo_text'] = !$objectType && !$status && $data['region']->extras[$type . '_seo_text'] ? $data['region']->extras[$type . '_seo_text'] : $data['seo_text'];
        }

        if ($data['area'] && !$data['city']) {
            $data['meta_title'] = $data['area']->extras[$type . '_meta_title'] ? $data['area']->extras[$type . '_meta_title'] : $data['meta_title'];
            $data['meta_desc'] = $data['area']->extras[$type . '_meta_desc'] ? $data['area']->extras[$type . '_meta_desc'] : $data['meta_desc'];
            $data['seo_title'] = !$objectType && !$status && $data['area']->extras[$type . '_h1'] ? $data['area']->extras[$type . '_h1'] : $data['seo_title'];
            $data['h1'] = $data['area']->extras[$type . '_h1'] ? $data['area']->extras[$type . '_h1'] : $data['h1'];
            $data['seo_text'] = !$objectType && !$status && $data['area']->extras[$type . '_seo_text'] ? $data['area']->extras[$type . '_seo_text'] : $data['seo_text'];
        }

        if ($data['kyivdistrict']) {
            $data['meta_title'] = $data['kyivdistrict']->extras[$type . '_meta_title'] ? $data['kyivdistrict']->extras[$type . '_meta_title'] : $data['meta_title'];
            $data['meta_desc'] = $data['kyivdistrict']->extras[$type . '_meta_desc'] ? $data['kyivdistrict']->extras[$type . '_meta_desc'] : $data['meta_desc'];
            $data['seo_title'] = !$objectType && !$status && $data['kyivdistrict']->extras[$type . '_h1'] ? $data['kyivdistrict']->extras[$type . '_h1'] : $data['seo_title'];
            $data['h1'] = $data['kyivdistrict']->extras[$type . '_h1'] ? $data['kyivdistrict']->extras[$type . '_h1'] : $data['h1'];
            $data['seo_text'] = !$objectType && !$status && $data['kyivdistrict']->extras[$type . '_seo_text'] ? $data['kyivdistrict']->extras[$type . '_seo_text'] : $data['seo_text'];
        }

        if ($meta) {
            $data['meta_title'] = $meta->extras[$type . '_meta_title'] ? $meta->extras[$type . '_meta_title'] : $data['meta_title'];
            $data['meta_desc'] = $meta->extras[$type . '_meta_desc'] ? $meta->extras[$type . '_meta_desc'] : $data['meta_desc'];
            $data['seo_title'] = $exactly_matching_meta && $meta->extras[$type . '_seo_title'] ? $meta->extras[$type . '_seo_title'] : $data['seo_title'];
            $data['h1'] = $meta->extras[$type . '_h1'] ? $meta->extras[$type . '_h1'] : $data['h1'];
            $data['seo_text'] = $exactly_matching_meta && $meta->extras[$type . '_seo_text'] ? $meta->extras[$type . '_seo_text'] : $data['seo_text'];
        }

        // Start replacing meta variables
        $objectTypeString = $objectType ? $objectType : __('main.type_' . $type);
        $replace = ['{region}', '{region_genitive}', '{type}', '{type_genitive}', '{type_plural}', '{type_plural_genitive}', '{status_plural}'];
        $replaceFor = [$data['region_name'], $data['region_name_genitive'], $objectTypeString, $objectTypeGenitive, $objectTypePlural, $objectTypePluralGenitive, $statusPlural];

        $data['seo_title'] = trim(str_replace($replace, $replaceFor, $data['seo_title']));
        $data['seo_text'] = trim(str_replace($replace, $replaceFor, $data['seo_text']));
        $data['meta_title'] = trim(str_replace($replace, $replaceFor, $data['meta_title']));
        $data['meta_desc'] = trim(str_replace($replace, $replaceFor, $data['meta_desc']));
        $data['h1'] = trim(str_replace($replace, $replaceFor, $data['h1']));
        // End replacing meta variables
        $data['land_articles'] = Article::where('status', 'PUBLISHED')->where('title', '!=', '')->whereIn('category_id', [347, 348])->get();
        $data['statistics'] = Statistics::where('type', $type)->latest()->take(2)->get();
        $data['top_rating'] = Product::active()->where('extras->is_frozen', 0)->where('extras->status', 'building')->where('category_id', $category->id)->orderBy('top_rating', 'desc')->orderBy('id', 'desc')->take(10)->get();
        $data['reviews_rating'] = Product::active()->where('extras->is_frozen', 0)->where('category_id', $category->id)->where('old_rating_count', '>=', 3)->orderByRaw('old_rating / old_rating_count desc')->take(10)->get();

        $data['regions_collection'] = Region::get();

        return view('catalog.precatalog', $data);
    }

    public function getProducts(Request $request)
    {
        $key = 'products';

        if ($request->category)
            $key .= ".category:" . $request->category;

        if ($request->is_hit) {
            $language = session()->has('lang') ? session()->get('lang') : self::DEFAULT_LANG_FOR_PRODUCTS; // 'ru'

            // [ added by @ts 2024-08-23 19:03
            $requestLang = $request->input('lang');
            if (isset($requestLang) && !empty($requestLang)) {
                $language = $requestLang ?? self::DEFAULT_LANG_FOR_PRODUCTS;
            }
            // ]

            $key .= ".is_hit:1.lang=$language";
        }

        if ($request->type)
            $key .= ".type:" . $request->type;

        if ($request->status)
            $key .= ".status:" . $request->status;

        if ($request->kyivdistrict)
            $key .= ".kyivdistrict:" . $request->kyivdistrict;
        elseif ($request->city)
            $key .= ".city:" . $request->city;
        elseif ($request->area)
            $key .= ".area:" . $request->area;
        elseif ($request->region)
            $key .= ".region:" . $request->region;

        if ($request->number)
            $key .= ".number:" . $request->number;

        if (!$request->caching && $products = Redis::get($key)) {
            return response()->json(json_decode($products));
        }

        $products = Product::with('modifications')->active();

        if ($request->category) {
            $category = Category::where('slug', $request->category)->first();

            if ($category)
                $products = $products->where('category_id', $category->id);
        }

        if ($request->is_hit) {
            $products = $products->where('products.is_hit', 1);
        }

        if ($request->type) {
            if ($category->id == 1 || $category->original_id == 1) {
                $products = $products->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', str_replace('_', ' ', $request->type))->select('products.*');
            } elseif ($category->id == 2 || $category->original_id == 2) {
                $products = $products->where('products.extras->newbuild_type', $request->type);
            }
        } elseif ($request->category && ($category->id == 1 || $category->original_id == 1)) {
            // $products = $products->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonDoesntContain('attribute_modification.value', 'Земельный участок')->select('products.*');
        }


        if ($request->status) {
            if ($request->status === 'frozen')
                $products = $products->where('products.extras->is_frozen', 1);
            elseif ($request->status === 'sold')
                $products = $products->where('products.is_sold', 1);
            else
                $products = $products->where('products.extras->status', $request->status);
        } else {
            $products = $products->where('products.extras->status', '!=', 'null');
        }

        if ($request->region) {
            $products = $products->where('address->region', $request->region);
        }

        if ($request->area) {
            $products = $products->where('address->area', $request->area);
        }

        if ($request->city) {
            $products = $products->where('address->city', $request->city);
        }

        if ($request->kyivdistrict) {
            $products = $products->where('address->kyivdistrict', $request->kyivdistrict);
        }

        $products = $products->orderBy('created_at', 'desc');

        if ($request->number)
            $products = $products->paginate($request->number);
        else
            $products = $products->paginate(12);

        $products = new \App\Http\Resources\Products($products);

        Redis::set($key, json_encode($products), 'EX', 108000);

        return response()->json($products);
    }

    public function getPrices(Request $request)
    {
        $key = 'prices';

        if ($request->category)
            $key .= ".category:" . $request->category;

        if ($request->type)
            $key .= ".type:" . $request->type;

        if ($request->status)
            $key .= ".status:" . $request->status;

        if ($request->kyivdistrict)
            $key .= ".kyivdistrict:" . $request->kyivdistrict;
        elseif ($request->city)
            $key .= ".city:" . $request->city;
        elseif ($request->area)
            $key .= ".area:" . $request->area;
        elseif ($request->region)
            $key .= ".region:" . $request->region;

        if ($products = Redis::get($key)) {
            return response()->json(json_decode($products));
        }

        $products = Product::with('modifications')->select('products.*')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->active()->where('price', '!=', 0)->where('products.extras->is_frozen', 0);

        if ($request->category) {
            $category = Category::where('slug', $request->category)->first();
            $products = $products->where('category_id', $category->id);
        }

        if ($request->type) {
            if ($category->id == 1 || $category->original_id == 1) {
                $products = $products->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', str_replace('_', ' ', $request->type));
            } elseif ($category->id == 2 || $category->original_id == 2) {
                $products = $products->where('products.extras->newbuild_type', $request->type);
            }
        } elseif ($category->id == 1 || $category->original_id == 1) {
            $products = $products->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonDoesntContain('attribute_modification.value', 'Земельный участок');
        }

        if ($request->status) {
            if ($request->status === 'frozen')
                $products = $products->where('products.extras->is_frozen', 1);
            elseif ($request->status === 'sold')
                $products = $products->where('products.is_sold', 1)->where('products.extras->is_frozen', 0);
            else
                $products = $products->where('products.extras->status', $request->status)->where('products.extras->is_frozen', 0);
        }

        if ($request->region) {
            $products = $products->where('address->region', $request->region);
        }

        if ($request->area) {
            $products = $products->where('address->area', $request->area);
        }

        if ($request->city) {
            $products = $products->where('address->city', $request->city);
        }

        if ($request->kyivdistrict) {
            $products = $products->where('address->kyivdistrict', $request->kyivdistrict);
        }

        $data = [
            'min' => $products->min('price'),
            'max' => $products->max('old_price'),
            'avg' => round($products->avg('price')),
            'products' => new \App\Http\Resources\Products($products->orderBy('price', 'asc')->paginate(30))
        ];

        Redis::set($key, json_encode($data), 'EX', 108000);

        return response()->json($data);
    }

    public function statistics(Request $request, $slug)
    {
        $data['page'] = Page::where('template', 'precatalog_statistics')->first()->withFakes();
        $category = Category::where('slug', $slug)->firstOrTranslation('/statistics');
        $type = $category->id == 1 || $category->id == 6 ? 'cottage' : 'newbuild';

        $data['category'] = $category;
        $data['type'] = $type;
        $data['statistics'] = Statistics::where('type', $type)->latest()->take(2)->get();

        return view('catalog.statistics', $data);
    }

    public function map(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->firstOrTranslation(explode($slug, request()->path())[1]);
        $data['page'] = Page::where('template', 'map')->first()->withFakes()->toArray();

        if (!$category)
            abort(404);

        $products = Product::active()->where('category_id', $category->id);

        // START DEFAULT VALUES
        $arg1 = 0;
        $arg2 = 0;
        $frozen = 0;
        $data['areas'] = [];
        $data['cities'] = [];
        $data['region_name'] = __('main.Украина');
        $data['region_name_genitive'] = __('main.Украины');
        $data['article_region_name_genitive'] = __('main.Украины');
        $region_model = null;
        $area_model = null;
        $city_model = null;
        $kyivdistrict_model = null;
        $data['region'] = null;
        $data['area'] = null;
        $data['city'] = null;
        $data['kyivdistrict'] = null;
        $data['translation_link'] = $category->translation_link . '/map';
        $redirect = request()->path();
        // END DEFAULT VALUES

        $segment1 = request()->segment(4);
        $segment2 = request()->segment(5);
        $segment3 = request()->segment(6);
        $segment4 = request()->segment(7);

        if ($segment4) {
            $arg1 = $segment3;
            $arg2 = $segment4;
        } elseif ($segment3) {
            $arg1 = $segment3;
        } elseif ($segment2) {
            $arg1 = $segment1;
            $arg2 = $segment2;
        } elseif ($segment1) {
            $arg1 = $segment1;
        }

        // GET LOCATION
        if ($segment1 == 'region') {
            $region_model = Region::where('slug', $segment2)->first();

            if ($region_model) {
                $data['areas'] = Area::where('region_id', $region_model->region_id)->get();
                $data['region'] = $region_model;
                $data['region_name'] = $region_model->region_id == 29 ? $region_model->name : $region_model->name . ' ' . __('main.область');
                $data['region_name_genitive'] = $region_model->region_id == 29 ? $region_model->name_genitive : $region_model->name_genitive . ' ' . __('main.области');
                $data['article_region_name_genitive'] = $data['region_name_genitive'];
                $data['translation_link'] .= "/region/{$region_model->translation_slug}";
                $products = $products->where('address->region', $region_model->region_id);
            } elseif (Region::withoutGlobalScopes()->where('slug', $segment2)->first()) {
                // if region with this slug exists in other language
                $translation = Region::withoutGlobalScopes()->where('slug', $segment2)->first();
                $translation = $translation->original ? $translation->original->slug : $translation->translations->first()->slug;
                $redirect = str_replace($segment2, $translation, $redirect);
            } else {
                abort(404);
            }
        } elseif ($segment1 == 'area') {
            $area_model = Area::where('slug', $segment2)->first();

            if ($area_model) {
                $data['areas'] = Area::where('region_id', $area_model->region_id)->get();
                $data['cities'] = City::where('area_id', $area_model->area_id)->get();
                $data['area'] = $area_model;
                $data['region_name'] = $area_model->is_center ? $area_model->name : $area_model->name . ' ' . __('main.район');
                $data['region_name_genitive'] = $area_model->is_center ? $area_model->name_genitive : $area_model->name_genitive . ' ' . __('main.района');
                $region_model = $area_model->region;
                $data['translation_link'] .= "/area/{$area_model->translation_slug}";
                $products = $products->where('address->area', $area_model->area_id);
            } elseif (Area::withoutGlobalScopes()->where('slug', $segment2)->first()) {
                // if area with this slug exists in other language
                $translation = Area::withoutGlobalScopes()->where('slug', $segment2)->first();
                $translation = $translation->original ? $translation->original->slug : $translation->translations->first()->slug;
                $redirect = str_replace($segment2, $translation, $redirect);
            } else {
                abort(404);
            }
        } elseif ($segment1 == 'city') {
            $city_model = City::where('slug', $segment2)->first();

            if ($city_model) {
                $data['cities'] = City::where('area_id', $city_model->area_id)->get();
                $data['city'] = $city_model;
                $data['region_name'] = $city_model->name;
                $data['region_name_genitive'] = $city_model->name_genitive ? $city_model->name_genitive : $city_model->name;
                $area_model = $city_model->area;
                $region_model = $area_model->region;
                $data['translation_link'] .= "/city/{$city_model->translation_slug}";
                $products = $products->where('address->city', $city_model->city_id);
            } elseif (City::withoutGlobalScopes()->where('slug', $segment2)->first()) {
                // if city with this slug exists in other language
                $translation = City::withoutGlobalScopes()->where('slug', $segment2)->first();
                $translation = $translation->original ? $translation->original->slug : $translation->translations->first()->slug;
                $redirect = str_replace($segment2, $translation, $redirect);
            } else {
                abort(404);
            }
        } elseif ($segment1 == 'kyivdistrict') {
            $kyivdistrict_model = Kyivdistrict::where('slug', $segment2)->first();

            if ($kyivdistrict_model) {
                $data['kyivdistrict'] = $kyivdistrict_model;
                $data['region_name'] = $kyivdistrict_model->name;
                $data['region_name_genitive'] = $kyivdistrict_model->name_genitive ? $kyivdistrict_model->name_genitive . ' ' . __('main.района') : $kyivdistrict_model->name . ' ' . __('main.район');
                $region_model = Region::where('region_id', 29)->first();
                $data['translation_link'] .= "/kyivdistrict/{$kyivdistrict_model->translation_slug}";
                $products = $products->where('address->kyivdistrict', $kyivdistrict_model->kyivdistrict_id);
            } elseif (Kyivdistrict::withoutGlobalScopes()->where('slug', $segment2)->first()) {
                // if kyivdistrict with this slug exists in other language
                $translation = Kyivdistrict::withoutGlobalScopes()->where('slug', $segment2)->first();

                $translation = $translation->original ? $translation->original->slug : $translation->translations->first()->slug;
                $redirect = str_replace($segment2, $translation, $redirect);
            } else {
                abort(404);
            }
        }

        if ($redirect != request()->path())
            return redirect($redirect, 301);

        $data['category'] = $category;
        $data['other_category'] = Category::where('slug', '!=', $slug)->first();
        $type = ($category->id === 1 || $category->original_id === 1) ? 'cottage' : 'newbuild';

        $allTypes = $category->id === 1 || $category->original_id === 1 ? __('attributes.cottage_types') : __('attributes.newbuild_types');
        $allTypesArray = [];
        $allTypesGenitive = $category->id === 1 || $category->original_id === 1 ? __('attributes.cottage_types_genitive') : __('attributes.newbuild_types_genitive');
        $allTypesPlural = $category->id === 1 || $category->original_id === 1 ? __('attributes.cottage_types_plural') : __('attributes.newbuild_types_plural');
        $allTypesPluralGenitive = $category->id === 1 || $category->original_id === 1 ? __('attributes.cottage_types_plural_genitive') : __('attributes.newbuild_types_plural_genitive');

        foreach ($allTypes as $key => $item) {
            $allTypesArray[\Str::slug($key)] = $key;
        }

        $status = isset(__('main.product_statuses')[$arg1]) || $arg1 === 'frozen' ? $arg1 : null;
        $data['status'] = $status;
        $statusPlural = $status ? __('main.product_statuses_plural')[$arg1] : null;
        $objectType = !$arg2 && (isset($allTypesArray[$arg1]) || isset($allTypesArray[$arg1])) ? $arg1 : $arg2;
        $objectType = isset($allTypesArray[$objectType]) ? $allTypesArray[$objectType] : null;
        $objectTypeGenitive = isset($allTypesGenitive[$objectType]) ? $allTypesGenitive[$objectType] : __('main.type_' . $type . '_genitive');
        $objectTypePlural = isset($allTypesPlural[$objectType]) ? $allTypesPlural[$objectType] : __('main.type_' . $type . '_plural');
        $objectTypePluralGenitive = isset($allTypesPluralGenitive[$objectType]) ? $allTypesPluralGenitive[$objectType] : __('main.type_' . $type . '_plural_genitive');

        if ($status) {
            $data['translation_link'] .= "/{$status}";
            if ($status === 'frozen')
                $products->where('products.extras->is_frozen', 1);
            elseif ($status === 'sold')
                $products->where('products.is_sold', 1);
            else
                $products->where('products.extras->status', $status);
        }

        if ($objectType) {
            $data['translation_link'] .= "/" . array_flip($allTypesArray)[$objectType];
            $products = $type == 'cottage' ? $products->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', str_replace('_', ' ', $objectType))->select('products.*') : $products->whereJsonContains('extras->newbuild_type', $objectType);
        }

        if (!$products->count())
            abort(404);

        $data['objectType'] = $objectType;
        $data['objectTypePlural'] = $objectTypePlural;

        $data['regions'] = Region::get();
        $data['kyivdistricts'] = Kyivdistrict::orderBy('name')->get();

        $data['category_slug'] = $slug;
        $data['type'] = $type;

        // dd($region_model->region_id);
        // Start exactly matching meta
        if ($region_model)
            $meta = Meta::where('is_map', 1)
                ->where('address->region', $region_model->region_id);
        else
            $meta = Meta::where('is_map', 1)
                ->where('address->region', 0);

        if ($data['area'])
            $meta = $meta->where('address->area', $data['area']->area_id);
        else
            $meta = $meta->where('address->area', 0);

        if ($data['city'])
            $meta = $meta->where('address->city', $data['city']->city_id);
        else
            $meta = $meta->where('address->city', 0);

        if ($status)
            $meta = $meta->where('status', $status);
        else
            $meta = $meta->where('status', null);

        if ($objectType)
            $meta = $meta->where('type', $objectType);
        else
            $meta = $meta->where('type', null);

        $meta = $meta->orderBy('created_at', 'desc')->first();
        // End exactly matching meta

        $exactly_matching_meta = false;

        if ($meta)
            $exactly_matching_meta = true;

        // Start 2/3 matching meta
        if (!$meta) {
            $meta = Meta::where('is_map', 1)
                ->where(function ($query) use ($region_model, $status, $objectType) {
                    $query->where(function ($q) use ($region_model, $status, $objectType) {
                        $q->where('status', $status)
                            ->where('type', null);

                        if ($region_model)
                            $q->where('address->region', $region_model->region_id);
                        else
                            $q->where('address->region', 0);

                    })->orWhere(function ($q) use ($region_model, $status, $objectType) {
                        $q->where('status', null)
                            ->where('type', $objectType);

                        if ($region_model)
                            $q->where('address->region', $region_model->region_id);
                        else
                            $q->where('address->region', 0);

                    })->orWhere(function ($q) use ($status, $objectType) {
                        $q->where('address->region', 0)
                            ->where('status', $status)
                            ->where('type', $objectType);
                    });
                });

            if ($area_model)
                $meta = $meta->whereIn('address->area', [$area_model->area_id, 0]);
            else
                $meta = $meta->where('address->area', 0);

            if ($city_model)
                $meta = $meta->whereIn('address->city', [$city_model->city_id, 0]);
            else
                $meta = $meta->where('address->city', 0);

            $meta = $meta->orderBy('created_at', 'desc')->first();
        }
        // End 2/3 matching meta

        // Start 1/3 matching meta
        if (!$meta) {
            if ($region_model) {
                $meta = Meta::where('is_map', 1)
                    ->where('address->region', $region_model->region_id)
                    ->where('status', null)
                    ->where('type', null);

                if ($data['area'])
                    $meta = $meta->whereIn('address->area', [$data['area']->area_id, 0]);
                else
                    $meta = $meta->where('address->area', 0);

                if ($data['city'])
                    $meta = $meta->whereIn('address->city', [$data['city']->city_id, 0]);
                else
                    $meta = $meta->where('address->city', 0);
            } elseif ($objectType) {
                $meta = Meta::where('is_map', 1)
                    ->where('address->region', 0)
                    ->where('status', null)
                    ->where('type', $objectType);
            } elseif ($status) {
                $meta = Meta::where('is_map', 1)
                    ->where('address->region', 0)
                    ->where('status', $status)
                    ->where('type', null);
            }

            $meta = $meta ? $meta->orderBy('created_at', 'desc')->first() : null;
        }
        // End 1/3 matching meta

        $data['seo_title'] = null;
        $data['seo_text'] = null;
        $data['meta_title'] = $data['page'][$type . '_meta_title'];
        $data['meta_desc'] = $data['page'][$type . '_meta_desc'];
        $data['h1'] = $data['page'][$type . '_h1'];

        if (!$region_model && !$area_model && !$city_model && !$kyivdistrict_model && !$arg1 && !$arg2) {
            $data['seo_title'] = $data['page'][$type . '_seo_title'];
            $data['seo_text'] = $data['page'][$type . '_seo_text'];
        }

        if ($meta) {
            $data['meta_title'] = $meta->extras[$type . '_meta_title'] ? $meta->extras[$type . '_meta_title'] : $data['meta_title'];
            $data['meta_desc'] = $meta->extras[$type . '_meta_desc'] ? $meta->extras[$type . '_meta_desc'] : $data['meta_desc'];
            $data['seo_title'] = $exactly_matching_meta && $meta->extras[$type . '_seo_title'] ? $meta->extras[$type . '_seo_title'] : $data['seo_title'];
            $data['h1'] = $meta->extras[$type . '_h1'] ? $meta->extras[$type . '_h1'] : $data['h1'];
            $data['seo_text'] = $exactly_matching_meta && $meta->extras[$type . '_seo_text'] ? $meta->extras[$type . '_seo_text'] : $data['seo_text'];
        }

        // Start replacing meta variables
        $objectTypeString = $objectType ? $objectType : __('main.type_' . $type);
        $replace = ['{region}', '{region_genitive}', '{type}', '{type_genitive}', '{type_plural}', '{type_plural_genitive}', '{status_plural}'];
        $replaceFor = [$data['region_name'], $data['region_name_genitive'], $objectTypeString, $objectTypeGenitive, $objectTypePlural, $objectTypePluralGenitive, $statusPlural];

        $data['seo_title'] = trim(str_replace($replace, $replaceFor, $data['seo_title']));
        $data['seo_text'] = trim(str_replace($replace, $replaceFor, $data['seo_text']));
        $data['meta_title'] = trim(str_replace($replace, $replaceFor, $data['meta_title']));
        $data['meta_desc'] = trim(str_replace($replace, $replaceFor, $data['meta_desc']));
        $data['h1'] = trim(str_replace($replace, $replaceFor, $data['h1']));
        // End replacing meta variables

        $data['regions_collection'] = Region::get();

        return view('catalog.map', $data);
    }

    public function generateDocx(Request $request, $id)
    {
        $area = Area::where('area_id', $id)->firstOrFail();
        $products = $area->products()->where('category_id', 1)->get();

        $headers = array(
            "Content-type" => "text/html",
            "Content-Disposition" => "attachment;Filename=" . $area->slug . ".docx"
        );

        $content = view('docx.products', compact('products', 'area'))->render();
        return \Response::make($content, 200, $headers);
    }
}
