<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aimix\Shop\app\Models\Brand;
use Aimix\Shop\app\Models\BrandCategory;
use Aimix\Promotion\app\Models\Promotion;
use Backpack\PageManager\app\Models\Page;
use App\Region;
use App\Area;
use App\City;

class CompanyController extends Controller
{
    public function index(Request $request, $slug = null)
    {
        $page = Page::where('template', 'companies')->first()->withFakes();
        if($slug)
            $category = BrandCategory::where('slug', $slug)->firstOrTranslation();
        else
            $category = null;
        $category = $category? $category->withFakes() : $category;
        $promotions = Promotion::take(12)->get();
        $promotions_count = Promotion::count();
        if($slug && !$category)
            return redirect('companies', 301);
        
        $categories = BrandCategory::where('is_active', 1)->pluck('name', 'slug');
        $popular_categories = BrandCategory::where('is_popular', 1)->where('is_active', 1)->get();
        $companies = $category? Brand::where('category_id', $category->id) : Brand::whereHas('category', function($q) {
            $q->where('is_active', 1);
        });
        $regions = Region::pluck('name', 'region_id');
        
        if($request->region) {
            $companies = $companies->where('address->region', $request->region);
        }

        if($request->area) {
            $companies = $companies->where('address->area', $request->area);
        }

        if($request->city) {
            $companies = $companies->where('address->city', $request->city);
        }

        if($request->search) {
            $filterSwitched = switchTextToEnglish($request->search) != $request->search? switchTextToEnglish($request->search) : switchTextToRussian($request->search);

            $companies = $companies->where(function($q) use ($request, $filterSwitched) {
                $q->where('name', 'like', '%' . $request->search . '%')->orWhere('name', 'like', '%' . $filterSwitched . '%');
            });
        }

        $meta_title = $category ? ($category->meta_title ?: $category->name) : $page->meta_title;
        $meta_desc = $category ? ($category->meta_desc ?: $category->name) : $page->meta_desc;
        $seo_title = $slug? $category->seo_title : $page->seo_title;
        $seo_desc = $slug? $category->seo_desc : $page->seo_desc;
        $translation_link = $slug? $category->translation_link : null;
        
        $companies = $companies->latest()->paginate(10);
        $companies = new \App\Http\Resources\Companies($companies);
        
        if($request->isJson)
            return response()->json(['companies' => $companies, 'seo_title' => $seo_title, 'seo_text' => $seo_desc, 'meta_title' => $meta_title, 'meta_desc' => $meta_desc]);
        else
            return view('companies.index', compact('categories', 'companies', 'category', 'popular_categories', 'seo_title', 'seo_desc', 'page', 'promotions', 'promotions_count', 'regions', 'translation_link'));
    }

    public function show(Request $request, $category_slug, $slug, $tab = null)
    {
        $page = Page::where('template', 'company')->first()->withFakes();
        $data['tab'] = $tab;
        $data['reviews'] = null;
        $data['promotions'] = null;
        $data['category'] = BrandCategory::where('slug', $category_slug)->firstOrWithoutScopes();
        $data['meta_title'] = $tab? $page[$tab . '_meta_title'] : $page['main_meta_title'];
        $data['meta_desc'] = $tab? $page[$tab . '_meta_desc'] : $page['main_meta_desc'];
        $data['h1'] = $tab? $page[$tab . '_main_title'] : $page['main_main_title'];
        
        if(!$data['category'])
            return redirect('companies', 301);

        $data['company'] = Brand::where('category_id', $data['category']->id)->where('slug', $slug)->firstOrTranslation()->withFakes();
        
        $companies = Brand::where('category_id', $data['category']->id)->where('slug', '!=', $slug)->paginate(6);
        $data['companies'] = new \App\Http\Resources\Companies($companies);
        $data['companies_count'] = Brand::where('category_id', $data['category']->id)->count();
        
        if(!$data['company'])
            return redirect('companies/' . $data['category']->slug, 301);

        $data['translation_link'] = $tab? $data['company']->translation_link . "/$tab" : $data['company']->translation_link;
        $data['products_count'] = $data['company']->products->count();
        $data['products'] = new \App\Http\Resources\Products($data['company']->products()->paginate(12));

        if($tab == 'reviews') {
            $data['reviews'] = $data['company']->reviews()->orderBy('created_at', 'desc')->paginate(6);

            if($request->isJson)
                return response()->json(['reviews' => $data['reviews']]);
        } elseif($tab == 'promotions') {
            $data['promotions'] = $data['company']->promotions;
        }
        
        $view = $tab? 'companies.' . $tab : 'companies.show';
        
        $data['meta_title'] = str_replace(['{name}', '{category}'], [$data['company']->name, $data['company']->category->name], $data['meta_title']);
        $data['meta_desc'] = str_replace(['{name}', '{category}'], [$data['company']->name, $data['company']->category->name], $data['meta_desc']);
        $data['h1'] = str_replace(['{name}', '{category}'], [$data['company']->name, $data['company']->category->name], $data['h1']);
    
        return view($view, $data);
    }
}
