<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Backpack\PageManager\app\Models\Page;
use Aimix\Promotion\app\Models\Promotion;
use Aimix\Shop\app\Models\Product;
use Aimix\Shop\app\Models\Category;
use Aimix\Shop\app\Models\Brand;

use App\Region;
use App\City;

class PromotionController extends Controller
{
    public function index(Request $request, $type_slug = null)
    {
        $lang = session()->has('lang')? session('lang') : 'ru';
        $slug = explode('/', $request->path())[0] == 'uk' || explode('/', $request->path())[0] == 'ru'? explode('/', $request->path())[1] : explode('/', $request->path())[0];

        if($lang == 'uk' && $slug == 'akcii')
            return redirect(url('uk/akcij'), 301);

        if($lang == 'ru' && $slug == 'akcij')
            return redirect(url('ru/akcii'), 301);

        $type = $request->type;
        // dd(Product::active()->has('promotions')->where('address->region', 16)->get());
        $page = Page::where('template', 'promotions')->first()->withFakes();

        $page_type = '';
        $cottages_slug = $page->cottage_slug;
        $newbuilds_slug = $page->newbuild_slug;

        $types_slugs = [
            1 => $cottages_slug,
            2 => $newbuilds_slug,
            6 => $cottages_slug,
            7 => $newbuilds_slug
        ];

        if($type_slug === $cottages_slug){
            $page_type = 'cottage_';
            $type = $lang === 'ru'? 1 : 6;
        }
        elseif($type_slug === $newbuilds_slug){
            $page_type = 'newbuild_';
            $type = $lang === 'ru'? 2 : 7;
        }
        
        $title = $page->{$page_type . 'main_title'};
        $meta_title = $page->{$page_type . 'meta_title'};
        $meta_desc = $page->{$page_type . 'meta_desc'};
        $seo_title = $page->{$page_type . 'seo_title'};
        $seo_text = $page->{$page_type . 'seo_text'};

        $promotions = Promotion::where('promotions.id', '!=', null);
        $regions = Product::has('promotions')->get()->pluck('region', 'address.region');
        $cities = [];
        $companies = Brand::has('promotions');
        $types = Category::pluck('name', 'id');
        
        if($request->region) {
            $region = $request->region;
            $cities = Product::active()->has('promotions')->where('address->region', $region);
            $companies = $companies->whereHas('products', function($query) use ($region) {
                $query->where('address->region', $region);
            });

            $promotions = $promotions->whereHas('product', function($query) use ($region) {
                $query->where('address->region', $region);
            });
        }

        if($request->city) {
            $city = $request->city;
            $companies = $companies->whereHas('products', function($query) use ($city) {
                $query->where('address->city', $city);
            });

            $promotions = $promotions->whereHas('product', function($query) use ($city) {
                $query->where('address->city', $city);
            });
        }
        
        if($type) {
            $promotions = $promotions->whereHas('product', function($query) use ($type) {
                $query->where('category_id', $type);
            });

            if($request->region)
                $cities = $cities->where('category_id', $type);
        }

        if($request->company) {
            $company = $request->company;
            $promotions = $promotions->whereHas('product', function($query) use ($company) {
                $query->where('brand_id', $company);
            });
        }
        
        $companies = $companies->pluck('name', 'id');
        $promotions = $promotions->orderBy('created_at', 'desc')->paginate(12);

        if($request->region)
            $cities = $cities->get()->pluck('city', 'address.city');

        if($request->isJson)
            return response()->json(['promotions' => $promotions, 'cities' => $cities, 'companies' => $companies, 'title' => $title, 'meta_title' => $meta_title, 'seo_title' => $seo_title, 'seo_text' => $seo_text]);
        else
            return view('promotions.index', compact('page', 'promotions', 'regions', 'cities', 'companies', 'types', 'title', 'meta_title', 'meta_desc', 'seo_title', 'seo_text', 'types_slugs', 'slug', 'lang', 'type'));
    }
}
