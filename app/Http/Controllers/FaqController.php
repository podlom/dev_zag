<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faq;
use App\Models\FaqCategory;
use Backpack\NewsCRUD\app\Models\Article;
use Backpack\PageManager\app\Models\Page;

class FaqController extends Controller
{
    
    public function index(Request $request, $slug = null) {
        if(!$slug)
            return redirect(FaqCategory::where('is_active', 1)->noEmpty()->orderBy('created_at')->first()->link, 301);

        $page = Page::where('template', 'faq')->first()->withFakes();
        $category = FaqCategory::where('slug', $slug)->firstOrTranslation();
        $questions = Faq::where('category_id', $category->id)->get();
        $categories = FaqCategory::where('is_active', 1)->noEmpty()->orderBy('created_at')->pluck('name', 'slug');
        $articles = Article::published()->whereIn('category_id', $category->categories)->take(3)->get();

        if(!$category->is_active)
            return redirect('/faq', 301);

        $translation_link = $category->translation_link;
        $meta_title = $category->meta_title? $category->meta_title : $page->meta_title;
        $meta_desc = $category->meta_desc? $category->meta_desc : $page->meta_desc;
        $seo_text = $category->seo_text? $category->seo_text : $page->seo_text;
        
        if($request->isJson)
            return response()->json(['questions' => $questions, 'meta_title' => $meta_title, 'seo_text' => $seo_text, 'articles' => $articles]);
        else
            return view('pages.faq', compact('category', 'questions', 'categories', 'articles', 'page', 'meta_title', 'meta_desc', 'seo_text', 'translation_link'));
    }
}
