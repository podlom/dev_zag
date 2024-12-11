<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Backpack\PageManager\app\Models\Page;
use Backpack\NewsCRUD\app\Models\Article;
use App\Models\Term;

class DictionaryController extends Controller
{
    public function index(Request $request)
    {
        $letters = Term::orderBy('name')->get()->groupBy(function ($item, $key) {
            return mb_substr($item['name'], 0, 1);
        });
        
        $articles = Article::published()->whereHas('category', function($query) {
            $query->whereIn('parent_id', [1,14,5,26,12,27,13,28]);
        })->take(3)->get();
        $page = Page::where('template', 'dictionary')->first()->withFakes();
    
        return view('pages.dictionary', ['letters' => $letters, 'articles' => $articles, 'page' => $page]);
    }
}
