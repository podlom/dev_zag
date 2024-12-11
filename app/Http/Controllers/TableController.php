<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aimix\Shop\app\Models\Product;
use Aimix\Shop\app\Models\Modification;
use Aimix\Shop\app\Models\AttributeModification;
use App\Region;
use App\Area;
use App\City;

class TableController extends Controller
{
    public function index()
    {
        return view('tables.index');
    }

    public function complex(Request $request)
    {
        $files = \Storage::disk('common')->files('tables/statistics-complex');
        $last = count($files)? $files[array_key_last($files)] : null;
        $selectedTable = $request->table? $request->table : $last;
        $table = $selectedTable? json_decode(\Storage::disk('common')->get($selectedTable)) : null;
        $category_id = 2;
        $title = 'Статистика по новостройкам со ссылками на';
        $tables = [];
        foreach($files as $file) {
            $tables[$file] = explode('.', explode('/', $file)[2])[0];
        }
        
        if($request->isJson)
            return response()->json(['tables' => $tables, 'table' => $table]);
        else
            return view('tables.statistics', ['table' => $table, 'tables' => $tables, 'selectedTable' => $selectedTable, 'category_id' => $category_id, 'title' => $title, 'show_links' => true, 'area' => false]);
    }

    public function complex_no_links(Request $request)
    {
        $files = \Storage::disk('common')->files('tables/statistics-complex');
        $last = count($files)? $files[array_key_last($files)] : null;
        $selectedTable = $request->table? $request->table : $last;
        $table = $selectedTable? json_decode(\Storage::disk('common')->get($selectedTable)) : null;
        $category_id = 2;
        $title = 'Статистика по новостройкам без ссылок на';
        $tables = [];
        foreach($files as $file) {
            $tables[$file] = explode('.', explode('/', $file)[2])[0];
        }
        
        if($request->isJson)
            return response()->json(['tables' => $tables, 'table' => $table]);
        else
            return view('tables.statistics', ['table' => $table, 'tables' => $tables, 'selectedTable' => $selectedTable, 'category_id' => $category_id, 'title' => $title, 'show_links' => false, 'area' => false]);
    }

    public function cottages(Request $request)
    {
        $files = \Storage::disk('common')->files('tables/statistics-cottages');
        $last = count($files)? $files[array_key_last($files)] : null;
        $selectedTable = $request->table? $request->table : $last;
        $table = $selectedTable? json_decode(\Storage::disk('common')->get($selectedTable)) : null;
        $category_id = 1;
        $title = 'Статистика по коттеджным городкам со ссылками на';
        $tables = [];
        foreach($files as $file) {
            $tables[$file] = explode('.', explode('/', $file)[2])[0];
        }
        
        if($request->isJson)
            return response()->json(['tables' => $tables, 'table' => $table]);
        else
            return view('tables.statistics', ['table' => $table, 'tables' => $tables, 'selectedTable' => $selectedTable, 'category_id' => $category_id, 'title' => $title, 'show_links' => true, 'area' => false]);
    }

    public function cottages_no_links(Request $request)
    {
        $files = \Storage::disk('common')->files('tables/statistics-cottages');
        $last = count($files)? $files[array_key_last($files)] : null;
        $selectedTable = $request->table? $request->table : $last;
        $table = $selectedTable? json_decode(\Storage::disk('common')->get($selectedTable)) : null;
        $category_id = 1;
        $title = 'Статистика по коттеджным городкам без ссылок на';
        $tables = [];
        foreach($files as $file) {
            $tables[$file] = explode('.', explode('/', $file)[2])[0];
        }
        
        if($request->isJson)
            return response()->json(['tables' => $tables, 'table' => $table]);
        else
            return view('tables.statistics', ['table' => $table, 'tables' => $tables, 'selectedTable' => $selectedTable, 'category_id' => $category_id, 'title' => $title, 'show_links' => false, 'area' => false]);
    }

    public function cottages_area(Request $request)
    {
        $files = \Storage::disk('common')->files('tables/statistics-cottages-area');
        $last = count($files)? $files[array_key_last($files)] : null;
        $selectedTable = $request->table? $request->table : $last;
        $table = $selectedTable? json_decode(\Storage::disk('common')->get($selectedTable)) : null;
        $category_id = 1;
        $title = 'Статистика по коттеджным городкам (земля) со ссылками на';
        $tables = [];
        foreach($files as $file) {
            $tables[$file] = explode('.', explode('/', $file)[2])[0];
        }
        
        if($request->isJson)
            return response()->json(['tables' => $tables, 'table' => $table]);
        else
            return view('tables.statistics', ['table' => $table, 'tables' => $tables, 'selectedTable' => $selectedTable, 'category_id' => $category_id, 'title' => $title, 'show_links' => true, 'area' => true]);
    }

    public function cottages_area_no_links(Request $request)
    {
        $files = \Storage::disk('common')->files('tables/statistics-cottages-area');
        $last = count($files)? $files[array_key_last($files)] : null;
        $selectedTable = $request->table? $request->table : $last;
        $table = $selectedTable? json_decode(\Storage::disk('common')->get($selectedTable)) : null;
        $category_id = 1;
        $title = 'Статистика по коттеджным городкам (земля) без ссылок на';
        $tables = [];
        foreach($files as $file) {
            $tables[$file] = explode('.', explode('/', $file)[2])[0];
        }
        
        if($request->isJson)
            return response()->json(['tables' => $tables, 'table' => $table]);
        else
            return view('tables.statistics', ['table' => $table, 'tables' => $tables, 'selectedTable' => $selectedTable, 'category_id' => $category_id, 'title' => $title, 'show_links' => false, 'area' => true]);
    }

    public function generate_table(Request $request)
    {
        // $mods = Modification::distinct('modifications.id')->join('products', 'products.id', '=', 'modifications.product_id')->where('modifications.is_default', 0)->where('category_id', 2)->where('products.is_active', 1)->get();
        // $prods = Product::withoutGlobalScopes()->where('category_id', 2)->get();

        $category_id = $request->category_id;
        $is_area = $request->area;

        $table = [
            'regions' => [],
            'total' => []
        ];

        $regions = Region::withoutGlobalScope('language')->where('language_abbr', 'ru')->orderBy('name', 'asc')->get();

        (new Product)->clearGlobalScopes();

        foreach($regions as $region) {
            $region_row = [];

            // $region_row['total'] = $prods->where('address.region', $region->region_id)->count();
            $region_row['total'] = Product::withoutGlobalScopes()->where('category_id', $category_id)->where('address->region', $region->region_id);

            $region_row['total'] = $is_area? $region_row['total']->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', 'Земельный участок')->select('products.*') : ($category_id == 1? $region_row['total']->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonDoesntContain('attribute_modification.value', 'Земельный участок')->select('products.*') : $region_row['total']);

            $region_row['total'] = $region_row['total']->count();

            if(!$region_row['total'])
                continue;

            // $min = $mods->where('price', '!=', 0)->where('address.region', $region->region_id)->sortBy('price')->first();
            $min = Modification::withoutGlobalScopes()->where('modifications.is_default', 0)->where('modifications.price', '!=', 0)->join('products', 'products.id', '=', 'modifications.product_id')->where('products.category_id', $category_id)->where('products.is_active', 1)->where('products.address->region', $region->region_id)->orderBy('price', 'asc');

            $min = $is_area? $min->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', 'Земельный участок') : ($category_id == 1?$min->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonDoesntContain('attribute_modification.value', 'Земельный участок') : $min);

            $min = $min->first();
            
            $region_row['min'] = [
                'price' => $min? $min->price : 0,
                'link' => $min? $min->product->link : null
            ];
            // $max = $mods->where('old_price', '!=', 0)->where('address.region', $region->region_id)->sortByDesc('old_price')->first();
            $max = Modification::withoutGlobalScopes()->where('modifications.is_default', 0)->where('modifications.old_price', '!=', 0)->join('products', 'products.id', '=', 'modifications.product_id')->where('products.category_id', $category_id)->where('products.is_active', 1)->where('products.address->region', $region->region_id)->orderBy('old_price', 'desc');

            $max = $is_area? $max->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', 'Земельный участок') : ($category_id == 1? $max->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonDoesntContain('attribute_modification.value', 'Земельный участок') : $max);

            $max = $max->first();

            $region_row['max'] = [
                'price' => $max? $max->old_price : 0,
                'link' => $max? $max->product->link : null
            ];

            // $region_row['avg_min'] = round($mods->where('price', '!=', 0)->where('address.region', $region->region_id)->avg('price'));
            $region_row['avg_min'] = Modification::withoutGlobalScopes()->where('modifications.is_default', 0)->where('modifications.price', '!=', 0)->join('products', 'products.id', '=', 'modifications.product_id')->where('products.category_id', $category_id)->where('products.address->region', $region->region_id)->where('products.extras->is_frozen', 0);

            $region_row['avg_min'] = $is_area? $region_row['avg_min']->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', 'Земельный участок') : ($category_id == 1? $region_row['avg_min']->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonDoesntContain('attribute_modification.value', 'Земельный участок') : $region_row['avg_min']);

            $region_row['avg_min'] = round($region_row['avg_min']->avg('price'));

            // $region_row['avg_max'] = round($mods->where('old_price', '!=', 0)->where('address.region', $region->region_id)->avg('old_price'));
            $region_row['avg_max'] = Modification::withoutGlobalScopes()->where('modifications.is_default', 0)->where('modifications.old_price', '!=', 0)->join('products', 'products.id', '=', 'modifications.product_id')->where('products.category_id', $category_id)->where('products.address->region', $region->region_id)->where('products.extras->is_frozen', 0);

            $region_row['avg_max'] = $is_area? $region_row['avg_max']->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', 'Земельный участок') : ($category_id == 1? $region_row['avg_max']->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonDoesntContain('attribute_modification.value', 'Земельный участок') : $region_row['avg_max']);

            $region_row['avg_max'] = round($region_row['avg_max']->avg('old_price'));

            // $region_row['total_active'] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->where('address->region', $region->region_id);

            // $region_row['total_active'] = $is_area? $region_row['total_active']->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', 'Земельный участок')->select('products.*') : $region_row['total_active'];
            
            // $region_row['total_active'] = $region_row['total_active']->count();

            if($category_id == 2) {
                $region_row['types'] = [];
                foreach(__('attributes.newbuild_types') as $key => $item) {
                    $region_row['types'][$key] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->where('address->region', $region->region_id)->where('products.extras->newbuild_type', $key)->count();
                }
            }

            $region_row['areas'] = [];

            foreach($region->areas->where('language_abbr', 'ru') as $area) {
                $area_row = [];

                // $area_row['total'] = $prods->where('address.area', $area->area_id)->count();
                $area_row['total'] = Product::withoutGlobalScopes()->where('category_id', $category_id)->where('address->area', $area->area_id);

                $area_row['total'] = $is_area? $area_row['total']->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', 'Земельный участок')->select('products.*') : ($category_id == 1? $area_row['total']->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonDoesntContain('attribute_modification.value', 'Земельный участок')->select('products.*') : $area_row['total']);
    
                $area_row['total'] = $area_row['total']->count();

                if(!$area_row['total'])
                    continue;

                // $min = $mods->where('price', '!=', 0)->where('address.area', $area->area_id)->sortBy('price')->first();
                $min = Modification::withoutGlobalScopes()->where('modifications.is_default', 0)->where('modifications.price', '!=', 0)->join('products', 'products.id', '=', 'modifications.product_id')->where('products.category_id', $category_id)->where('products.is_active', 1)->where('products.address->area', $area->area_id)->orderBy('price', 'asc');

                $min = $is_area? $min->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', 'Земельный участок') : ($category_id == 1? $min->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonDoesntContain('attribute_modification.value', 'Земельный участок') : $min);

                $min = $min->first();
    
                $area_row['min'] = [
                    'price' => $min? $min->price : 0,
                    'link' => $min? $min->product->link : null
                ];
    
                // $max = $mods->where('old_price', '!=', 0)->where('address.area', $area->area_id)->sortByDesc('old_price')->first();
                $max = Modification::withoutGlobalScopes()->where('modifications.is_default', 0)->where('modifications.old_price', '!=', 0)->join('products', 'products.id', '=', 'modifications.product_id')->where('products.category_id', $category_id)->where('products.is_active', 1)->where('products.address->area', $area->area_id)->orderBy('old_price', 'desc');

                $max = $is_area? $max->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', 'Земельный участок') : ($category_id == 1? $max->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonDoesntContain('attribute_modification.value', 'Земельный участок') : $max);
    
                $max = $max->first();
    
                $area_row['max'] = [
                    'price' => $max? $max->old_price : 0,
                    'link' => $max? $max->product->link : null
                ];

                // $area_row['avg_min'] = round($mods->where('price', '!=', 0)->where('address.area', $area->area_id)->avg('price'));
                $area_row['avg_min'] = Modification::withoutGlobalScopes()->where('modifications.is_default', 0)->where('modifications.price', '!=', 0)->join('products', 'products.id', '=', 'modifications.product_id')->where('products.category_id', $category_id)->where('products.address->area', $area->area_id)->where('products.extras->is_frozen', 0);

                $area_row['avg_min'] = $is_area? $area_row['avg_min']->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', 'Земельный участок') : ($category_id == 1? $area_row['avg_min']->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonDoesntContain('attribute_modification.value', 'Земельный участок') : $area_row['avg_min']);
    
                $area_row['avg_min'] = round($area_row['avg_min']->avg('price'));

                // $area_row['avg_max'] = round($mods->where('old_price', '!=', 0)->where('address.area', $area->area_id)->avg('old_price'));
                $area_row['avg_max'] = Modification::withoutGlobalScopes()->where('modifications.is_default', 0)->where('modifications.old_price', '!=', 0)->join('products', 'products.id', '=', 'modifications.product_id')->where('products.category_id', $category_id)->where('products.address->area', $area->area_id)->where('products.extras->is_frozen', 0);

                $area_row['avg_max'] = $is_area? $area_row['avg_max']->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', 'Земельный участок') : ($category_id == 1? $area_row['avg_max']->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonDoesntContain('attribute_modification.value', 'Земельный участок') : $area_row['avg_max']);
    
                $area_row['avg_max'] = round($area_row['avg_max']->avg('old_price'));
    
                // $area_row['total_active'] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->where('address->area', $area->area_id);

                // $area_row['total_active'] = $is_area? $area_row['total_active']->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', 'Земельный участок')->select('products.*') : $area_row['total_active'];
                
                // $area_row['total_active'] = $area_row['total_active']->count();

                if($category_id == 2) {
                    $area_row['types'] = [];
                    foreach(__('attributes.newbuild_types') as $key => $item) {
                        $area_row['types'][$key] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->where('address->area', $area->area_id)->where('products.extras->newbuild_type', $key)->count();
                    }
                }

                $area_row['cities'] = [];

                foreach($area->cities->where('language_abbr', 'ru') as $city) {
                    $city_row = [];

                    $city_row['total'] = Product::withoutGlobalScopes()->where('category_id', $category_id)->where('address->city', $city->city_id);

                    $city_row['total'] = $is_area? $city_row['total']->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', 'Земельный участок')->select('products.*') : ($category_id == 1? $city_row['total']->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonDoesntContain('attribute_modification.value', 'Земельный участок')->select('products.*') : $city_row['total']);
        
                    $city_row['total'] = $city_row['total']->count();

                    if(!$city_row['total'])
                        continue;

                    $min = Modification::withoutGlobalScopes()->where('modifications.is_default', 0)->where('modifications.price', '!=', 0)->join('products', 'products.id', '=', 'modifications.product_id')->where('products.category_id', $category_id)->where('products.is_active', 1)->where('products.address->city', $city->city_id)->orderBy('price', 'asc');

                    $min = $is_area? $min->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', 'Земельный участок') : ($category_id == 1? $min->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonDoesntContain('attribute_modification.value', 'Земельный участок') : $min);
    
                    $min = $min->first();
        
                    $city_row['min'] = [
                        'price' => $min? $min->price : 0,
                        'link' => $min? $min->product->link : null
                    ];

                    $max = Modification::withoutGlobalScopes()->where('modifications.is_default', 0)->where('modifications.old_price', '!=', 0)->join('products', 'products.id', '=', 'modifications.product_id')->where('products.category_id', $category_id)->where('products.is_active', 1)->where('products.address->city', $city->city_id)->orderBy('old_price', 'desc');

                    $max = $is_area? $max->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', 'Земельный участок') : ($category_id == 1? $max->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonDoesntContain('attribute_modification.value', 'Земельный участок') : $max);
        
                    $max = $max->first();
        
                    $city_row['max'] = [
                        'price' => $max? $max->old_price : 0,
                        'link' => $max? $max->product->link : null
                    ];

                    $city_row['avg_min'] = Modification::withoutGlobalScopes()->where('modifications.is_default', 0)->where('modifications.price', '!=', 0)->join('products', 'products.id', '=', 'modifications.product_id')->where('products.category_id', $category_id)->where('products.address->city', $city->city_id)->where('products.extras->is_frozen', 0);

                    $city_row['avg_min'] = $is_area? $city_row['avg_min']->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', 'Земельный участок') : ($category_id == 1? $city_row['avg_min']->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonDoesntContain('attribute_modification.value', 'Земельный участок') : $city_row['avg_min']);
        
                    $city_row['avg_min'] = round($city_row['avg_min']->avg('price'));

                    $city_row['avg_max'] = Modification::withoutGlobalScopes()->where('modifications.is_default', 0)->where('modifications.old_price', '!=', 0)->join('products', 'products.id', '=', 'modifications.product_id')->where('products.category_id', $category_id)->where('products.address->city', $city->city_id)->where('products.extras->is_frozen', 0);

                    $city_row['avg_max'] = $is_area? $city_row['avg_max']->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', 'Земельный участок') : ($category_id == 1? $city_row['avg_max']->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonDoesntContain('attribute_modification.value', 'Земельный участок') : $city_row['avg_max']);
        
                    $city_row['avg_max'] = round($city_row['avg_max']->avg('old_price'));
        
                    // $city_row['total_active'] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->where('address->city', $city->city_id);

                    // $city_row['total_active'] = $is_area? $city_row['total_active']->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', 'Земельный участок')->select('products.*') : $city_row['total_active'];
                    
                    // $city_row['total_active'] = $city_row['total_active']->count();

                    if($category_id == 2) {
                        $city_row['types'] = [];
                        foreach(__('attributes.newbuild_types') as $key => $item) {
                            $city_row['types'][$key] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->where('address->city', $city->city_id)->where('products.extras->newbuild_type', $key)->count();
                        }
                    }

                    $area_row['cities'][$city->name] = $city_row;
                }

                $region_row['areas'][$area->name] = $area_row;
            }

            $table['regions'][$region->name] = $region_row;
        }

        $excluded = \App\Region::withoutGlobalScope('active')->withoutGlobalScope('noEmpty')->where('is_active', 0)->pluck('region_id');

        $total = [];

        // $total['min'] = $mods->where('price', '!=', 0)->whereNotIn('address.region', $excluded)->min('price');
        $total['min'] = Modification::withoutGlobalScopes()->where('is_default', 0)->where('price', '!=', 0)->whereHas('product', function($q) use ($excluded, $category_id) {
            $q->where('category_id', $category_id)->where('products.is_active', 1)->whereNotIn('address->region', $excluded);
        });

        $total['min'] = $is_area? $total['min']->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', 'Земельный участок') : ($category_id == 1? $total['min']->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonDoesntContain('attribute_modification.value', 'Земельный участок') : $total['min']);

        $total['min'] = $total['min']->min('price');

        // $total['max'] = $mods->where('old_price', '!=', 0)->whereNotIn('address.region', $excluded)->max('old_price');
        $total['max'] = Modification::withoutGlobalScopes()->where('is_default', 0)->where('old_price', '!=', 0)->whereHas('product', function($q) use ($excluded, $category_id) {
            $q->where('category_id', $category_id)->where('products.is_active', 1)->whereNotIn('address->region', $excluded);
        });

        $total['max'] = $is_area? $total['max']->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', 'Земельный участок') : ($category_id == 1? $total['max']->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonDoesntContain('attribute_modification.value', 'Земельный участок') : $total['max']);

        $total['max'] = $total['max']->max('old_price');

        // $total['avg_min'] = round($mods->where('price', '!=', 0)->whereNotIn('address.region', $excluded)->avg('price'));
        $total['avg_min'] = Modification::withoutGlobalScopes()->where('is_default', 0)->where('price', '!=', 0)->whereHas('product', function($q) use ($excluded, $category_id) {
            $q->where('category_id', $category_id)->where('products.is_active', 1)->whereNotIn('address->region', $excluded)->where('products.extras->is_frozen', 0);
        });

        $total['avg_min'] = $is_area? $total['avg_min']->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', 'Земельный участок') : ($category_id == 1? $total['avg_min']->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonDoesntContain('attribute_modification.value', 'Земельный участок') : $total['avg_min']);

        $total['avg_min'] = round($total['avg_min']->avg('price'));

        // $total['avg_max'] = round($mods->where('old_price', '!=', 0)->whereNotIn('address.region', $excluded)->avg('old_price'));
        $total['avg_max'] = Modification::withoutGlobalScopes()->where('is_default', 0)->where('old_price', '!=', 0)->whereHas('product', function($q) use ($excluded, $category_id) {
            $q->where('category_id', $category_id)->where('products.is_active', 1)->whereNotIn('address->region', $excluded)->where('products.extras->is_frozen', 0);
        });

        $total['avg_max'] = $is_area? $total['avg_max']->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', 'Земельный участок') : ($category_id == 1? $total['avg_max']->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonDoesntContain('attribute_modification.value', 'Земельный участок') : $total['avg_max']);

        $total['avg_max'] = round($total['avg_max']->avg('old_price'));

        // $total['total_active'] = $prods->where('is_active', 1)->whereNotIn('address.region', $excluded)->count();
        $total['total_active'] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->whereNotIn('address->region', $excluded);

        $total['total_active'] = $is_area? $total['total_active']->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', 'Земельный участок')->select('products.*') : ($category_id == 1? $total['total_active']->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonDoesntContain('attribute_modification.value', 'Земельный участок')->select('products.*') : $total['total_active']);
                    
        $total['total_active'] = $total['total_active']->count();

        // $total['total'] = $prods->whereNotIn('address.region', $excluded)->count();
        $total['total'] = Product::withoutGlobalScopes()->where('category_id', $category_id)->whereNotIn('address->region', $excluded);

        $total['total'] = $is_area? $total['total']->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', 'Земельный участок')->select('products.*') : ($category_id == 1? $total['total']->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonDoesntContain('attribute_modification.value', 'Земельный участок')->select('products.*') : $total['total']);
                    
        $total['total'] = $total['total']->count();

        if($category_id == 2) {
            $total['types'] = [];
            foreach(__('attributes.newbuild_types') as $key => $item) {
                $total['types'][$key] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->whereNotIn('address->region', $excluded)->where('products.extras->newbuild_type', $key)->count();
            }
        }

        $table['total'] = $total;
        $date = now()->format('Y-m-d H:i:s');
        $table['date'] = $date;

        $folder = $category_id == 2? 'statistics-complex' : 'statistics-cottages';
        $folder = $is_area? 'statistics-cottages-area' : $folder;

        \Storage::disk('common')->put('tables/' . $folder . '/' . $date . '.json', json_encode($table));
    }

    public function complex_number(Request $request)
    {
        $files = \Storage::disk('common')->files('tables/statistics-complex-number');
        $last = count($files)? $files[array_key_last($files)] : null;
        $selectedTable = $request->table? $request->table : $last;
        $table = $selectedTable? json_decode(\Storage::disk('common')->get($selectedTable)) : null;
        $category_id = 2;
        $title = 'Статистика по количеству новостроек на';
        $tables = [];
        foreach($files as $file) {
            $tables[$file] = explode('.', explode('/', $file)[2])[0];
        }
        
        if($request->isJson)
            return response()->json(['tables' => $tables, 'table' => $table]);
        else
            return view('tables.number_complex', ['table' => $table, 'tables' => $tables, 'selectedTable' => $selectedTable, 'category_id' => $category_id, 'title' => $title]);
    }

    public function cottages_number(Request $request)
    {
        $files = \Storage::disk('common')->files('tables/statistics-cottages-number');
        $last = count($files)? $files[array_key_last($files)] : null;
        $selectedTable = $request->table? $request->table : $last;
        $table = $selectedTable? json_decode(\Storage::disk('common')->get($selectedTable)) : null;
        $category_id = 1;
        $title = 'Статистика по количеству коттеджных городков на';
        $tables = [];
        foreach($files as $file) {
            $tables[$file] = explode('.', explode('/', $file)[2])[0];
        }
        
        //dd($title);
        
        if($request->isJson)
            return response()->json(['tables' => $tables, 'table' => $table]);
        else
            return view('tables.number_cottages', ['table' => $table, 'tables' => $tables, 'selectedTable' => $selectedTable, 'category_id' => $category_id, 'title' => $title]);
    }

    public function generate_table_number(Request $request)
    {
        $category_id = $request->category_id;
        $category_id = 1;
        $type = $category_id == 1? 'cottage' : 'newbuild';

        $table = [
            'regions' => [],
            'total' => []
        ];

        $regions = Region::withoutGlobalScope('language')->where('language_abbr', 'ru')->orderBy('name', 'asc')->get();

        foreach($regions as $region) {
            
            $region_row = [];

            $region_row['total'] = Product::withoutGlobalScopes()->where('category_id', $category_id)->where('address->region', $region->region_id)->count();

            if(!$region_row['total'])
                continue;

            // $region_row['total_active'] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->where('address->region', $region->region_id)->count();

            $region_row['types'] = [];

            foreach(__('attributes.' . $type . '_types') as $key => $item) {
                if($key == 'Эллинг')
                    continue;

                $region_row['types'][$key] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->where('address->region', $region->region_id);

                if($category_id == 1) {
                    $region_row['types'][$key] = $region_row['types'][$key]->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', str_replace('_', ' ', $key))->select('products.*');
                } elseif($category_id == 2) {
                    $region_row['types'][$key] = $region_row['types'][$key]->where('products.extras->newbuild_type', $key);
                }

                $region_row['types'][$key] = $region_row['types'][$key]->count();
            }

            $region_row['status_done'] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->where('address->region', $region->region_id)->where('products.extras->status', 'done')->count();

            $region_row['status_project'] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->where('address->region', $region->region_id)->where('products.extras->status', 'project')->count();

            $region_row['status_building'] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->where('address->region', $region->region_id)->where('products.extras->status', 'building')->count();

            $region_row['frozen'] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->where('address->region', $region->region_id)->where('products.extras->is_frozen', 1)->count();

            if($category_id == 2) {
                $region_row['status_done_flats'] = AttributeModification::where('attribute_modification.attribute_id', 8)->join('modifications', 'modifications.id', '=', 'attribute_modification.modification_id')->where('modifications.is_default', 0)->join('products', 'products.id', '=', 'modifications.product_id')->where('products.category_id', $category_id)->where('products.address->region', $region->region_id)->sum('value');
                
                $region_row['status_project_flats'] = AttributeModification::where('attribute_modification.attribute_id', 6)->join('modifications', 'modifications.id', '=', 'attribute_modification.modification_id')->where('modifications.is_default', 0)->join('products', 'products.id', '=', 'modifications.product_id')->where('products.category_id', $category_id)->where('products.address->region', $region->region_id)->sum('value');
                
                $region_row['status_building_flats'] = AttributeModification::where('attribute_modification.attribute_id', 7)->join('modifications', 'modifications.id', '=', 'attribute_modification.modification_id')->where('modifications.is_default', 0)->join('products', 'products.id', '=', 'modifications.product_id')->where('products.category_id', $category_id)->where('products.address->region', $region->region_id)->sum('value');

                $region_row['total_flats'] = $region_row['status_done_flats'] + $region_row['status_project_flats'] + $region_row['status_building_flats'];
            }

            $region_row['areas'] = [];

            foreach($region->areas->where('language_abbr', 'ru') as $area) {
                $area_row = [];

                $area_row['total'] = Product::withoutGlobalScopes()->where('category_id', $category_id)->where('address->area', $area->area_id)->count();

                if(!$area_row['total'])
                    continue;
    
                // $area_row['total_active'] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->where('address->area', $area->area_id)->count();

                $area_row['types'] = [];

                foreach(__('attributes.' . $type . '_types') as $key => $item) {
                    if($key == 'Эллинг')
                        continue;

                    $area_row['types'][$key] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->where('address->area', $area->area_id);
    
                    if($category_id == 1) {
                        $area_row['types'][$key] = $area_row['types'][$key]->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', str_replace('_', ' ', $key))->select('products.*');
                    } elseif($category_id == 2) {
                        $area_row['types'][$key] = $area_row['types'][$key]->where('products.extras->newbuild_type', $key);
                    }
    
                    $area_row['types'][$key] = $area_row['types'][$key]->count();
                }

                $area_row['status_done'] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->where('address->area', $area->area_id)->where('products.extras->status', 'done')->count();

                $area_row['status_project'] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->where('address->area', $area->area_id)->where('products.extras->status', 'project')->count();
    
                $area_row['status_building'] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->where('address->area', $area->area_id)->where('products.extras->status', 'building')->count();
    
                $area_row['frozen'] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->where('address->area', $area->area_id)->where('products.extras->is_frozen', 1)->count();

                if($category_id == 2) {
                    $area_row['status_done_flats'] = AttributeModification::where('attribute_modification.attribute_id', 8)->join('modifications', 'modifications.id', '=', 'attribute_modification.modification_id')->where('modifications.is_default', 0)->join('products', 'products.id', '=', 'modifications.product_id')->where('products.category_id', $category_id)->where('products.address->area', $area->area_id)->sum('value');
                    
                    $area_row['status_project_flats'] = AttributeModification::where('attribute_modification.attribute_id', 6)->join('modifications', 'modifications.id', '=', 'attribute_modification.modification_id')->where('modifications.is_default', 0)->join('products', 'products.id', '=', 'modifications.product_id')->where('products.category_id', $category_id)->where('products.address->area', $area->area_id)->sum('value');
                    
                    $area_row['status_building_flats'] = AttributeModification::where('attribute_modification.attribute_id', 7)->join('modifications', 'modifications.id', '=', 'attribute_modification.modification_id')->where('modifications.is_default', 0)->join('products', 'products.id', '=', 'modifications.product_id')->where('products.category_id', $category_id)->where('products.address->area', $area->area_id)->sum('value');
    
                    $area_row['total_flats'] = $area_row['status_done_flats'] + $area_row['status_project_flats'] + $area_row['status_building_flats'];
                }

                $area_row['cities'] = [];

                foreach($area->cities->where('language_abbr', 'ru') as $city) {
                    $city_row = [];

                    $city_row['total'] = Product::withoutGlobalScopes()->where('category_id', $category_id)->where('address->city', $city->city_id)->count();

                    if(!$city_row['total'])
                        continue;
        
                    // $city_row['total_active'] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->where('address->city', $city->city_id)->count();

                    $city_row['types'] = [];

                    foreach(__('attributes.' . $type . '_types') as $key => $item) {
                        if($key == 'Эллинг')
                            continue;

                        $city_row['types'][$key] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->where('address->city', $city->city_id);
        
                        if($category_id == 1) {
                            $city_row['types'][$key] = $city_row['types'][$key]->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', str_replace('_', ' ', $key))->select('products.*');
                        } elseif($category_id == 2) {
                            $city_row['types'][$key] = $city_row['types'][$key]->where('products.extras->newbuild_type', $key);
                        }
        
                        $city_row['types'][$key] = $city_row['types'][$key]->count();
                    }

                    $city_row['status_done'] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->where('address->city', $city->city_id)->where('products.extras->status', 'done')->count();

                    $city_row['status_project'] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->where('address->city', $city->city_id)->where('products.extras->status', 'project')->count();
        
                    $city_row['status_building'] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->where('address->city', $city->city_id)->where('products.extras->status', 'building')->count();
        
                    $city_row['frozen'] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->where('address->city', $city->city_id)->where('products.extras->is_frozen', 1)->count();

                    if($category_id == 2) {
                        $city_row['status_done_flats'] = AttributeModification::where('attribute_modification.attribute_id', 8)->join('modifications', 'modifications.id', '=', 'attribute_modification.modification_id')->where('modifications.is_default', 0)->join('products', 'products.id', '=', 'modifications.product_id')->where('products.category_id', $category_id)->where('products.address->city', $city->city_id)->sum('value');
                        
                        $city_row['status_project_flats'] = AttributeModification::where('attribute_modification.attribute_id', 6)->join('modifications', 'modifications.id', '=', 'attribute_modification.modification_id')->where('modifications.is_default', 0)->join('products', 'products.id', '=', 'modifications.product_id')->where('products.category_id', $category_id)->where('products.address->city', $city->city_id)->sum('value');
                        
                        $city_row['status_building_flats'] = AttributeModification::where('attribute_modification.attribute_id', 7)->join('modifications', 'modifications.id', '=', 'attribute_modification.modification_id')->where('modifications.is_default', 0)->join('products', 'products.id', '=', 'modifications.product_id')->where('products.category_id', $category_id)->where('products.address->city', $city->city_id)->sum('value');
        
                        $city_row['total_flats'] = $city_row['status_done_flats'] + $city_row['status_project_flats'] + $city_row['status_building_flats'];
                    }

                    $area_row['cities'][$city->name] = $city_row;
                }

                $region_row['areas'][$area->name] = $area_row;
            }

            $table['regions'][$region->name] = $region_row;
            
        }

        $excluded = \App\Region::withoutGlobalScope('active')->withoutGlobalScope('noEmpty')->where('is_active', 0)->pluck('region_id');

        $total = [];

        $total['total'] = Product::withoutGlobalScopes()->where('category_id', $category_id)->whereNotIn('address->region', $excluded)->count();

        // $total['total_active'] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->whereNotIn('address->region', $excluded)->count();

        $total['types'] = [];

        foreach(__('attributes.' . $type . '_types') as $key => $item) {
            if($key == 'Эллинг')
                continue;

            $total['types'][$key] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->whereNotIn('address->region', $excluded);

            if($category_id == 1) {
                $total['types'][$key] = $total['types'][$key]->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', str_replace('_', ' ', $key))->select('products.*');
            } elseif($category_id == 2) {
                $total['types'][$key] = $total['types'][$key]->where('products.extras->newbuild_type', $key);
            }

            $total['types'][$key] = $total['types'][$key]->count();
        }

        $total['status_done'] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->whereNotIn('address->region', $excluded)->where('products.extras->status', 'done')->count();

        $total['status_project'] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->whereNotIn('address->region', $excluded)->where('products.extras->status', 'project')->count();

        $total['status_building'] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->whereNotIn('address->region', $excluded)->where('products.extras->status', 'building')->count();

        $total['frozen'] = Product::withoutGlobalScopes()->where('products.is_active', 1)->where('category_id', $category_id)->whereNotIn('address->region', $excluded)->where('products.extras->is_frozen', 1)->count();

        if($category_id == 2) {
            $total['status_done_flats'] = AttributeModification::where('attribute_modification.attribute_id', 8)->join('modifications', 'modifications.id', '=', 'attribute_modification.modification_id')->where('modifications.is_default', 0)->join('products', 'products.id', '=', 'modifications.product_id')->where('products.category_id', $category_id)->whereNotIn('address->region', $excluded)->sum('value');
            
            $total['status_project_flats'] = AttributeModification::where('attribute_modification.attribute_id', 6)->join('modifications', 'modifications.id', '=', 'attribute_modification.modification_id')->where('modifications.is_default', 0)->join('products', 'products.id', '=', 'modifications.product_id')->where('products.category_id', $category_id)->whereNotIn('address->region', $excluded)->sum('value');
            
            $total['status_building_flats'] = AttributeModification::where('attribute_modification.attribute_id', 7)->join('modifications', 'modifications.id', '=', 'attribute_modification.modification_id')->where('modifications.is_default', 0)->join('products', 'products.id', '=', 'modifications.product_id')->where('products.category_id', $category_id)->whereNotIn('address->region', $excluded)->sum('value');

            $total['total_flats'] = $total['status_done_flats'] + $total['status_project_flats'] + $total['status_building_flats'];
        }

            //dd($table);
        $table['total'] = $total;
        $date = now()->format('Y-m-d H:i:s');
        $table['date'] = $date;

        $folder = $category_id == 2? 'statistics-complex-number' : 'statistics-cottages-number';

        \Storage::disk('common')->put('tables/' . $folder . '/' . $date . '.json', json_encode($table));
    }
}
