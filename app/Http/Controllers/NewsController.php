<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Backpack\NewsCRUD\app\Models\Article;
use Backpack\NewsCRUD\app\Models\Category;
use Backpack\NewsCRUD\app\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Aimix\Review\app\Models\Review;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\ArticleRegion;
use Backpack\PageManager\app\Models\Page;
use App\Models\PollQuestion;
use App\Models\PollAnswer;
use Aimix\Shop\app\Models\Product;

class NewsController extends Controller
{
      public function index(Request $request, $theme_slug = null) {
        // $start = microtime(true);
        // dd(microtime(true) - $start);
        $slug = explode('/', $request->path())[0] == 'uk' || explode('/', $request->path())[0] == 'ru'? explode('/', $request->path())[1] : explode('/', $request->path())[0];
        $category = null;
        $theme = null;
        $articles = Article::where('category_id', '!=', null)->where('status', 'PUBLISHED')->where('title', '!=', '');
        $parent_categories = Category::doesnthave('parent')->has('children')->whereIn('id', [1,14,5,26,12,27,13,28])->pluck('name', 'slug');
        $categories = [];
        
        $regions = ArticleRegion::pluck('name', 'region_id');
        $years = [];
        $meta_title = null;
        $meta_desc = null;
        $seo_text = null;
        $seo_title = null;
        $translation_link = null;
        
        if($theme_slug) {
          $theme = Category::where('slug', $theme_slug)->firstOrTranslation();

          if(!$theme || !$theme->is_active)
            abort(404);
        }

        if($slug) {
          $category = $slug? Category::where('slug', $slug)->firstOrTranslation() : null;

          if(!$category || !$category->is_active)
            abort(404);

          $meta_title = $category->meta_title? $category->meta_title : $category->name;
          $meta_desc = $category->meta_desc;
          $seo_text = $category->seo_text;
          $translation_link = $category->translation_link;

          $categories = Category::doesnthave('children')->where('is_active', 1)->whereHas('parent', function(Builder $query) use($slug) {
            $query->where('slug', $slug);
          })->pluck('name', 'slug');

          $articles = $articles->whereHas('category', function(Builder $query) use($category) {
            $query->where('parent_id', $category->id);
          });
        }

        if($theme_slug) {
          $articles = $articles->where('category_id', $theme->id);
          $meta_title = $theme->meta_title? $theme->meta_title : $category->name . ' | ' . __('main.Тема') . ' ' . $theme->name;
          $meta_desc = $theme->meta_desc? $theme->meta_desc : $meta_desc;
          $seo_text = $theme->seo_text? $theme->seo_text : $seo_text;
          $translation_link = $theme->translation_link;
        }

        if($request->region != null) {
          $region = $request->region;
          
          $articles = $articles->where('region', $region);
        }

        if (!Cache::has('article_years')) {
          $expiresAt = Carbon::now()->addWeek();

          foreach(Article::pluck('id', 'date') as $date => $id) {
            $year = explode('-', $date)[0];
            $years[$year] = $year;
          }
          
          unset($years[null]);
          Cache::put('article_years', $years, $expiresAt);
        } else {
            $years = Cache::get('article_years');
        }
        if($request->year != null) {
          $year = $request->year;
          
          $articles = $articles->where('date', '<', $year . '-12-31 23:59:59')->where('date', '>', $year . '-01-01 00:00:00');
        }

        if($request->sort) {
          preg_match_all("/([\w]+)_([\w]+)/", $request->sort, $value);
      
          $sort = ['value' => $value[1][0], 'dirr' => $value[2][0]];

          if($sort['value'] == 'reviews_count')
            $articles = $articles->withCount('reviews');

          $articles = $articles->orderBy($sort['value'], $sort['dirr']);
        } else {
          $articles = $articles->orderBy('date', 'desc');
        }
        
        $articles = $articles->paginate(10);
        
        if($request->isJson)
          return response()->json(['articles' => $articles->withPath($request->url().'&page='.$request->page), 
          'categories' => $categories, 'regions' => $regions, 'years' => $years, 'seo_text' => $seo_text, 'meta_title' => $meta_title]);
        else
          return view('news.index')->with('articles', $articles)->with('parent_categories', $parent_categories)->with('categories', $categories)->with('regions', $regions)->with('years', $years)->with('category', $category)->with('currentThemeSlug', $theme_slug)->with('meta_title', $meta_title)->with('meta_desc', $meta_desc)->with('seo_text', $seo_text)->with('seo_title', $seo_title)->with('year', $request->year)->with('region', $request->region)->with('sort', $request->sort)->with('translation_link', $translation_link);
      }

      public function show(Request $request, $slug) {
        $lang = session()->has('lang')? session('lang') : 'ru';
        $parent_category = explode('/', $request->path())[0] == 'uk' || explode('/', $request->path())[0] == 'ru'? explode('/', $request->path())[1] : explode('/', $request->path())[0];
        $slug = str_replace('.html', '', $slug);
        $article = Article::published()->where('slug', $slug)->firstOrTranslation();

        if(!$article)
          return redirect($lang . '/' . $parent_category, 301);

        $translation_link = $article->translation_link;
        $reviews = $article->reviews()->orderBy('created_at', 'desc')->paginate(10);
        
        $prev = Article::where('status', 'PUBLISHED')->where('category_id', $article->category_id)->where('title', '!=', '')->where('date', '<=', date('Y-m-d H:i:s'))->where('date', '<=', $article->date)->where('id', '!=', $article->id)->orderBy('date', 'DESC')->first();
        
        $next = Article::where('status', 'PUBLISHED')->where('category_id', $article->category_id)->where('title', '!=', '')->where('date', '<=', date('Y-m-d H:i:s'))->where('date', '>=', $article->date)->where('id', '!=', $article->id)->orderBy('date', 'ASC')->first();
        
        $otherArticles = Article::published()->where('slug', '!=', $slug)->where('category_id', $article->category_id);

        if((clone $otherArticles)->where('region', $article->region)->count() >= 3)
          $otherArticles = $otherArticles->where('region', $article->region);

        $otherArticles = $otherArticles->take(3)->get();

        $sameCategoryArticle = Article::where('status', 'PUBLISHED')->where('title', '!=', '')->where('slug', '!=', $slug)->where('category_id', $article->category_id)->latest()->take(10)->get()->shuffle()->first();
        
        if($request->isJson)
          return response()->json(['reviews' => $reviews->withPath($request->url().'&page='.$request->page)]);
        else {
          return view('news.show')->with('article', $article)->with('otherArticles', $otherArticles)->with('prev', $prev)->with('next', $next)->with('reviews', $reviews)->with('sameCategoryArticle', $sameCategoryArticle)->with('translation_link', $translation_link);
        }
      }

      public function servisy(Request $request, $slug = null, $theme_slug = null) {
        $lang = session()->has('lang')? session('lang') : 'ru';
        // $slug = explode('/', $request->path())[0] == 'uk' || explode('/', $request->path())[0] == 'ru'? explode('/', $request->path())[2] : explode('/', $request->path())[1];
        $category = null;
        $articles = Article::where('category_id', '!=', null)->where('status', 'PUBLISHED');
        $parent_categories = Category::doesnthave('parent')->where('is_active', 1)->whereIn('id', [369,370,371,372,373,374])->pluck('name', 'slug');
        $categories = [];
        $meta_title = null;
        $meta_desc = null;
        $seo_title = null;
        $seo_text = null;
        $content = null;
        $translation_link = null;
        
        if(!$slug)
          return redirect(url($lang . '/servisy/' . $parent_categories->keys()->first()), 301);

        if($theme_slug) {
          $theme = Category::where('slug', $theme_slug)->firstOrTranslation();

          if(!$theme || !$theme->is_active)
            abort(404);
        }

        if($slug) {
          $category = $slug? Category::where('slug', $slug)->firstOrTranslation() : null;

          if(!$category || !$category->is_active)
           abort(404);

          $meta_title = $category? $category->meta_title : null;
          $meta_desc = $category? $category->meta_desc : null;
          $seo_text = $category? $category->seo_text : null;
          $content = $category? $category->content : null;
          $translation_link = $category->translation_link;
          $categories = Category::doesnthave('children')->where('is_active', 1)->where('parent_id', $category->id)->pluck('name', 'slug');

          $articles = $articles->whereHas('category', function(Builder $query) use($category) {
            $query->where(function($q) use ($category) {
              $q->where('parent_id', $category->id)->orWhere('id', $category->id);
            });
          });
        }

        if($theme_slug) {
          $articles = $articles->where('category_id', $theme->id);
          $meta_title = $theme->meta_title? $theme->meta_title : $theme->name;
          $meta_desc = $theme->meta_desc? $theme->meta_desc : $meta_desc;
          $seo_text = $theme->seo_text? $theme->seo_text : $seo_text;
          $content = $theme->content;
          $translation_link = $theme->translation_link;
        }

        if($request->sort) {
          preg_match_all("/([\w]+)_([\w]+)/", $request->sort, $value);
      
          $sort = ['value' => $value[1][0], 'dirr' => $value[2][0]];

          if($sort['value'] == 'reviews_count')
            $articles = $articles->withCount('reviews');

          $articles = $articles->orderBy($sort['value'], $sort['dirr']);
        } else {
          $articles = $articles->orderBy('date', 'desc');
        }
        
        $articles = $articles->paginate(10);
        
        if($request->isJson)
          return response()->json(['articles' => $articles->withPath($request->url().'&page='.$request->page), 
          'categories' => $categories, 'seo_text' => $seo_text, 'meta_title' => $meta_title, 'content' => $content]);
        else
          return view('servisy.index')->with('articles', $articles)->with('parent_categories', $parent_categories)->with('categories', $categories)->with('category', $category)->with('currentThemeSlug', $theme_slug)->with('meta_title', $meta_title)->with('meta_desc', $meta_desc)->with('seo_text', $seo_text)->with('seo_title', $seo_title)->with('content', $content)->with('translation_link', $translation_link);
      }

      public function servisy_show(Request $request, $parent_cat, $theme = null, $slug) {
        $lang = session()->has('lang')? session('lang') : 'ru';
        $parent_category = explode('/', $request->path())[0] == 'uk' || explode('/', $request->path())[0] == 'ru'? explode('/', $request->path())[1] : explode('/', $request->path())[0];
        $slug = str_replace('.html', '', $slug);
        $article = Article::published()->where('slug', $slug)->firstOrTranslation();
                
        if(!$article)
          return redirect($lang . '/' . $parent_category, 301);

        $translation_link = $article->translation_link;
        $reviews = $article->reviews()->orderBy('created_at', 'desc')->paginate(10);
        
        $otherArticles = Article::whereHas('category', function($query) use ($parent_category) {
          $query->whereHas('parent', function($q) use ($parent_category) {
            $q->where('slug', $parent_category);
          });
        })->published()->where('slug', '!=', $slug)->orderBy('created_at', 'desc')->take(3)->get();
        
        if($request->isJson)
          return response()->json(['reviews' => $reviews->withPath($request->url().'&page='.$request->page)]);
        else {
          return view('servisy.show')->with('article', $article)->with('otherArticles', $otherArticles)->with('reviews', $reviews)->with('translation_link', $translation_link);
        }
      }

      public function poll_results(Request $request, $poll_id = null) {
        $lang = session()->has('lang')? session('lang') : 'ru';
        $parent_category = explode('/', $request->path())[0] == 'uk' || explode('/', $request->path())[0] == 'ru'? explode('/', $request->path())[1] : explode('/', $request->path())[0];
        $article = Article::where('poll_id', $poll_id)->firstOrTranslation();
        $poll = PollQuestion::find($poll_id);
        
        if(!$article || !$poll)
          return redirect($lang . '/' . $parent_category, 301);

        $translation_link = $article->translation_link;
        $poll_answers = $poll->original_id? PollAnswer::where('question_id', $poll->original_id)->get()->groupBy('product_id') : PollAnswer::where('question_id', $poll->id)->get()->groupBy('product_id');
        $poll_answers = $poll_answers->sortByDesc(function($answer) {
          return $answer->sum('votes');
        });
        
        if($request->isJson)
          return response()->json(['reviews' => $reviews->withPath($request->url().'&page='.$request->page)]);
        else {
          return view('servisy.poll_results')->with('article', $article)->with('poll_answers', $poll_answers)->with('poll', $poll)->with('translation_link', $translation_link);
        }
      }

      public function analitics(Request $request, $slug = null, $theme_slug = null) {
        $lang = session()->has('lang')? session('lang') : 'ru';
        
        $category = null;
        $articles = Article::where('category_id', '!=', null)->where('status', 'PUBLISHED')->where('title', '!=', '');
        $parent_categories = Category::doesnthave('parent')->where('is_active', 1)->whereIn('id', [194,195,375,376])->pluck('name', 'slug');
        $categories = [];
        $meta_title = null;
        $meta_desc = null;
        $seo_title = null;
        $seo_text = null;
        $content = null;
        $top_rating = null;
        $reviews_rating = null;
        $translation_link = null;
        
        if(!$slug)
          return redirect(url($lang . '/analitics/' . $parent_categories->keys()->first()), 301);

        if($theme_slug) {
          $theme = Category::where('slug', $theme_slug)->firstOrTranslation();
          
          switch ($theme->id) {
            case 377:
            case 381:
              $top_rating = Product::active()->where('is_sold', 0)->where('extras->is_frozen', 0)->where('extras->status', 'building')->whereIn('category_id', [1,6])->orderBy('top_rating', 'desc')->orderBy('id', 'desc')->take(10)->get();
              break;

            case 378:
            case 382:
              $top_rating = Product::active()->where('is_sold', 0)->where('extras->is_frozen', 0)->where('extras->status', 'building')->whereIn('category_id', [2,7])->orderBy('top_rating', 'desc')->orderBy('id', 'desc')->take(10)->get();
              break;

            case 379:
            case 383:
              $reviews_rating = Product::active()->where('is_sold', 0)->where('extras->is_frozen', 0)->where('extras->status', 'building')->whereIn('category_id', [1,6])->where('old_rating_count', '>=', 3)->orderByRaw('old_rating / old_rating_count desc')->take(10)->get();
              break;

            case 380:
            case 384:
              $reviews_rating = Product::active()->where('is_sold', 0)->where('extras->is_frozen', 0)->where('extras->status', 'building')->whereIn('category_id', [2,7])->where('old_rating_count', '>=', 3)->orderByRaw('old_rating / old_rating_count desc')->take(10)->get();
              break;
          }
            
          if(!$theme || !$theme->is_active)
            abort(404);
        }

        if($slug) {
          $category = $slug? Category::where('slug', $slug)->firstOrTranslation() : null;

          if(!$category || !$category->is_active)
           abort(404);

          $meta_title = $category? $category->meta_title : null;
          $meta_desc = $category? $category->meta_desc : null;
          $seo_text = $category? $category->seo_text : null;
          $content = $category? $category->content : null;
          $translation_link = $category->translation_link;
          $categories = Category::doesnthave('children')->where('is_active', 1)->where('parent_id', $category->id)->pluck('name', 'slug');

          $articles = $articles->whereHas('category', function(Builder $query) use($category) {
            $query->where(function($q) use ($category) {
              $q->where('parent_id', $category->id)->orWhere('id', $category->id);
            });
          });
        }

        if($theme_slug) {
          $articles = $articles->where('category_id', $theme->id);
          $meta_title = $theme->meta_title? $theme->meta_title : $theme->name;
          $meta_desc = $theme->meta_desc? $theme->meta_desc : $meta_desc;
          $seo_text = $theme->seo_text? $theme->seo_text : $seo_text;
          $content = $theme->content;
          $translation_link = $theme->translation_link;
        }

        if($request->sort) {
          preg_match_all("/([\w]+)_([\w]+)/", $request->sort, $value);
      
          $sort = ['value' => $value[1][0], 'dirr' => $value[2][0]];

          if($sort['value'] == 'reviews_count')
            $articles = $articles->withCount('reviews');

          $articles = $articles->orderBy($sort['value'], $sort['dirr']);
        } else {
          $articles = $articles->orderBy('date', 'desc');
        }
        
        $articles = $articles->paginate(10);
        
        if($request->isJson)
          return response()->json(['articles' => $articles->withPath($request->url().'&page='.$request->page), 
          'categories' => $categories, 'seo_text' => $seo_text, 'meta_title' => $meta_title, 'content' => $content, 'top_rating' => $top_rating, 'reviews_rating' => $reviews_rating]);
        else
          return view('analitics.index')->with('articles', $articles)->with('parent_categories', $parent_categories)->with('categories', $categories)->with('category', $category)->with('currentThemeSlug', $theme_slug)->with('meta_title', $meta_title)->with('meta_desc', $meta_desc)->with('seo_text', $seo_text)->with('seo_title', $seo_title)->with('content', $content)->with('top_rating', $top_rating)->with('reviews_rating', $reviews_rating)->with('translation_link', $translation_link);
      }

      public function analitics_show(Request $request, $parent_cat, $theme = null, $slug) {
        $lang = session()->has('lang')? session('lang') : 'ru';
        $parent_category = explode('/', $request->path())[0] == 'uk' || explode('/', $request->path())[0] == 'ru'? explode('/', $request->path())[1] : explode('/', $request->path())[0];
        $slug = str_replace('.html', '', $slug);
        $article = Article::published()->where('slug', $slug)->firstOrTranslation();

        if(!$article)
          return redirect($lang . '/' . $parent_category, 301);

        $translation_link = $article->translation_link;
        $reviews = $article->reviews()->orderBy('created_at', 'desc')->paginate(10);
        
        $otherArticles = Article::whereHas('category', function($query) use ($parent_category) {
          $query->whereHas('parent', function($q) use ($parent_category) {
            $q->where('slug', $parent_category);
          });
        })->published()->where('slug', '!=', $slug)->orderBy('created_at', 'desc')->take(3)->get();
        
        if($request->isJson)
          return response()->json(['reviews' => $reviews->withPath($request->url().'&page='.$request->page)]);
        else {
          return view('analitics.show')->with('article', $article)->with('otherArticles', $otherArticles)->with('reviews', $reviews)->with('translation_link', $translation_link);
        }
      }

      public function regions(Request $request, $theme_slug = null) {
        if($theme_slug && Article::withoutGlobalScopes()->where('slug', str_replace('.html', '', $theme_slug))->first()) {
          return $this->regions_show($request, null, $theme_slug);
        }
        
        if(!$theme_slug) {
          return redirect(Category::doesnthave('children')->where('is_active', 1)->whereIn('parent_id', [202,203])->first()->link, 301);
        }

        $lang = session()->has('lang')? session('lang') : 'ru';
        $articles = Article::where('status', 'PUBLISHED')->where('title', '!=', '')->whereHas('category', function(Builder $query) {
          $query->whereIn('parent_id', [202,203]);
        });
        $parent_category = Category::whereIn('id', [202,203])->first();
        $parent_category_slug = $parent_category->slug;
        $categories = Category::doesnthave('children')->where('is_active', 1)->whereIn('parent_id', [202,203]);
        $meta_title = $parent_category->meta_title? $parent_category->meta_title : $parent_category->name;
        $meta_desc = $parent_category->meta_desc;
        $seo_title = null;
        $seo_text = $parent_category->seo_text;
        $translation_link = $parent_category->translation_link;
        // dd(Category::withoutGlobalScopes()->where('slug', $theme_slug)->first()->translations->where('language_abbr', 'uk')->first());
        if($theme_slug) {
          $theme = Category::where('slug', $theme_slug)->firstOrTranslation();

          if(!$theme || !$theme->is_active || !$parent_category->is_active)
            abort(404);
        }
        
        if(!$theme_slug)
          return redirect(url($parent_category_slug . '/' . $categories->first()->slug), 301);

        if (!Cache::has('regions_categories_' . $lang)) {
          $expiresAt = Carbon::now()->addWeek();

          $categories = $categories->pluck('name', 'slug');

          Cache::put('regions_categories_' . $lang, $categories, $expiresAt);
        } else {
          $categories = Cache::get('regions_categories_' . $lang);
        }
        
        if($theme_slug) {
          $articles = $articles->where('category_id', $theme->id);
          $meta_title = $theme->meta_title? $theme->meta_title : $theme->name;
          $meta_desc = $theme->meta_desc? $theme->meta_desc : $meta_desc;
          $seo_text = $theme->seo_text? $theme->seo_text : $seo_text;
          $translation_link = $theme->translation_link;
        }
        
        $articles = $articles->orderBy('title')->paginate(10);
        
        if($request->isJson)
          return response()->json(['articles' => $articles->withPath($request->url().'&page='.$request->page), 
          'categories' => $categories, 'seo_text' => $seo_text, 'meta_title' => $meta_title]);
        else
          return view('regions.index')->with('articles', $articles)->with('categories', $categories)->with('currentThemeSlug', $theme_slug)->with('parent_category_slug', $parent_category_slug)->with('meta_title', $meta_title)->with('meta_desc', $meta_desc)->with('seo_title', $seo_title)->with('seo_text', $seo_text)->with('translation_link', $translation_link);
      }

      public function regions_show(Request $request, $theme = null, $slug) {
        $lang = session()->has('lang')? session('lang') : 'ru';
        $parent_category = explode('/', $request->path())[0] == 'uk' || explode('/', $request->path())[0] == 'ru'? explode('/', $request->path())[1] : explode('/', $request->path())[0];
        $slug = str_replace('.html', '', $slug);
        $article = Article::where('status', 'PUBLISHED')->orderBy('date', 'DESC')->orderBy('created_at', 'DESC')->where('slug', $slug)->firstOrTranslation();
        
        if(!$article)
          return redirect($lang . '/' . $parent_category, 301);

        $translation_link = $article->translation_link;
        $reviews = $article->reviews()->orderBy('created_at', 'desc')->paginate(10);
        
        $otherArticles = Article::whereHas('category', function($query) use ($parent_category) {
          $query->whereHas('parent', function($q) use ($parent_category) {
            $q->where('slug', $parent_category);
          });
        })->published()->where('slug', '!=', $slug)->orderBy('created_at', 'desc')->take(3)->get();
        
        if($request->isJson)
          return response()->json(['reviews' => $reviews->withPath($request->url().'&page='.$request->page)]);
        else {
          return view('regions.show')->with('article', $article)->with('otherArticles', $otherArticles)->with('reviews', $reviews)->with('translation_link', $translation_link);
        }
      }

      public function events(Request $request, $theme_slug = null) {
        $slug = explode('/', $request->path())[0] == 'uk' || explode('/', $request->path())[0] == 'ru'? explode('/', $request->path())[1] : explode('/', $request->path())[0];
        $category = null;
        $articles = Article::where('category_id', '!=', null)->where('status', 'PUBLISHED')->where('title', '!=', '');
        $parent_categories = Category::doesnthave('parent')->where('is_active', 1)->whereIn('id', [215,216,218,219,220,222,217,221])->pluck('name', 'slug');
        $categories = [];
        $meta_title = null;
        $meta_desc = null;
        $seo_title = null;
        $seo_text = null;
        $translation_link = null;
        
        if($theme_slug) {
          if(Article::withoutGlobalScopes()->where('slug', str_replace('.html', '', $theme_slug))->first())
            return $this->seminars_show($request);

          $theme = Category::where('slug', $theme_slug)->firstOrTranslation();

          if(!$theme || !$theme->is_active)
            abort(404);
        }

        if($slug) {
          $category = $slug? Category::where('slug', $slug)->firstOrTranslation() : null;

          if(!$category || !$category->is_active)
            abort(404);

          $meta_title = $category? $category->meta_title : null;
          $meta_desc = $category? $category->meta_desc : null;
          $seo_text = $category? $category->seo_text : null;
          $translation_link = $category->translation_link;
          $categories = Category::doesnthave('children')->where('is_active', 1)->where('parent_id', $category->id)->pluck('name', 'slug');

          $articles = $articles->whereHas('category', function(Builder $query) use($category) {
            $query->where(function($q) use ($category) {
              $q->where('parent_id', $category->id)->orWhere('id', $category->id);
            });
          });
        }

        if($theme_slug) {
          $articles = $articles->where('category_id', $theme->id);
          $meta_title = $theme->meta_title? $theme->meta_title : $theme->name;
          $meta_desc = $theme->meta_desc? $theme->meta_desc : $meta_desc;
          $seo_text = $theme->seo_text? $theme->seo_text : $seo_text;
          $translation_link = $theme->translation_link;
        }

        if($request->sort) {
          preg_match_all("/([\w]+)_([\w]+)/", $request->sort, $value);
      
          $sort = ['value' => $value[1][0], 'dirr' => $value[2][0]];

          if($sort['value'] == 'reviews_count')
            $articles = $articles->withCount('reviews');

          $articles = $articles->orderBy($sort['value'], $sort['dirr']);
        } else {
          $articles = $articles->orderBy('date', 'desc');
        }
        
        $articles = $articles->paginate(10);
        
        if($request->isJson)
          return response()->json(['articles' => $articles->withPath($request->url().'&page='.$request->page), 
          'categories' => $categories, 'seo_text' => $seo_text, 'meta_title' => $meta_title]);
        else
          return view('events.index')->with('articles', $articles)->with('parent_categories', $parent_categories)->with('categories', $categories)->with('category', $category)->with('currentThemeSlug', $theme_slug)->with('meta_title', $meta_title)->with('meta_desc', $meta_desc)->with('seo_text', $seo_text)->with('seo_title', $seo_title)->with('translation_link', $translation_link);
      }

      public function events_show(Request $request, $theme = null, $slug) {
        $lang = session()->has('lang')? session('lang') : 'ru';
        $parent_category = explode('/', $request->path())[0] == 'uk' || explode('/', $request->path())[0] == 'ru'? explode('/', $request->path())[1] : explode('/', $request->path())[0];
        $slug = str_replace('.html', '', $slug);
        $article = Article::where('status', 'PUBLISHED')->where('slug', $slug)->firstOrTranslation();
        
        if(!$article)
          return redirect($lang . '/' . $parent_category, 301);

        $translation_link = $article->translation_link;
        $reviews = $article->reviews()->orderBy('created_at', 'desc')->paginate(10);
        
        // $prev = Article::where('status', 'PUBLISHED')->where('title', '!=', '')->where('date', '<=', date('Y-m-d'))->where('date', '<=', $article->date)->where('id', '!=', $article->id)->orderBy('date', 'DESC')->first();
        
        // $next = Article::where('status', 'PUBLISHED')->where('title', '!=', '')->where('date', '<=', date('Y-m-d'))->where('date', '>=', $article->date)->where('id', '!=', $article->id)->orderBy('date', 'ASC')->first();
        
        $otherArticles = Article::where('status', 'PUBLISHED')->where('slug', '!=', $slug)->where('category_id', $article->category_id);

        if((clone $otherArticles)->where('region', $article->region)->count())
          $otherArticles = $otherArticles->where('region', $article->region);

        $otherArticles = $otherArticles->orderBy('created_at', 'desc')->take(3)->get();
        
        if($request->isJson)
          return response()->json(['reviews' => $reviews->withPath($request->url().'&page='.$request->page)]);
        else {
          return view('events.show')->with('article', $article)->with('otherArticles', $otherArticles)->with('reviews', $reviews)->with('translation_link', $translation_link);
        }
      }

      public function seminars_show(Request $request) {
        $lang = session()->has('lang')? session('lang') : 'ru';
        $parent_category = explode('/', $request->path())[0] == 'uk' || explode('/', $request->path())[0] == 'ru'? explode('/', $request->path())[1] : explode('/', $request->path())[0];
        $slug = explode('/', $request->path())[0] == 'uk' || explode('/', $request->path())[0] == 'ru'? explode('/', $request->path())[2] : explode('/', $request->path())[1];
        $slug = str_replace('.html', '', $slug);
        $article = Article::published()->where('slug', $slug)->firstOrTranslation();

        if(!$article)
          return redirect($lang . '/' . $parent_category, 301);

        $translation_link = $article->translation_link;
        $reviews = $article->reviews()->orderBy('created_at', 'desc')->paginate(10);
        
        $prev = Article::where('status', 'PUBLISHED')->where('category_id', $article->category_id)->where('title', '!=', '')->where('date', '<=', date('Y-m-d H:i:s'))->where('date', '<=', $article->date)->where('id', '!=', $article->id)->orderBy('date', 'DESC')->first();
        
        $next = Article::where('status', 'PUBLISHED')->where('category_id', $article->category_id)->where('title', '!=', '')->where('date', '<=', date('Y-m-d H:i:s'))->where('date', '>=', $article->date)->where('id', '!=', $article->id)->orderBy('date', 'ASC')->first();
        
        $otherArticles = Article::published()->where('slug', '!=', $slug)->where('category_id', $article->category_id);

        if((clone $otherArticles)->where('region', $article->region)->count())
          $otherArticles = $otherArticles->where('region', $article->region);

        $otherArticles = $otherArticles->orderBy('created_at', 'desc')->take(3)->get();
        
        if($request->isJson)
          return response()->json(['reviews' => $reviews->withPath($request->url().'&page='.$request->page)]);
        else {
          return view('events.show')->with('article', $article)->with('otherArticles', $otherArticles)->with('prev', $prev)->with('next', $next)->with('reviews', $reviews)->with('translation_link', $translation_link);
        }
      }

      public function ecology(Request $request, $theme_slug = null) {
        $parent_category_slug = explode('/', $request->path())[0] == 'uk' || explode('/', $request->path())[0] == 'ru'? explode('/', $request->path())[1] : explode('/', $request->path())[0];
        $lang = session()->has('lang')? session('lang') : 'ru';
        $articles = Article::where('status', 'PUBLISHED')->where('title', '!=', '')->whereHas('category', function(Builder $query) {
          $query->whereIn('parent_id', [281,282]);
        });
        $categories = Category::doesnthave('children')->where('is_active', 1)->whereIn('parent_id', [281,282]);

        if($theme_slug) {
          $theme = Category::where('slug', $theme_slug)->firstOrTranslation();

          if(!$theme || !$theme->is_active)
            abort(404);
        }

        $parent_category = Category::where('slug', $parent_category_slug)->firstOrTranslation();
        $meta_title = $parent_category->meta_title? $parent_category->meta_title : $parent_category->name;
        $meta_desc = $parent_category->meta_desc;
        $seo_title = null;
        $seo_text = $parent_category->seo_text;
        $content = $parent_category->content;
        $translation_link = $parent_category->translation_link;

        if(!$parent_category->is_active)
          abort(404);
        
        if (!Cache::has('ecology_categories_' . $lang)) {
          $expiresAt = Carbon::now()->addWeek();

          $categories = $categories->pluck('name', 'slug');

          Cache::put('ecology_categories_' . $lang, $categories, $expiresAt);
        } else {
          $categories = Cache::get('ecology_categories_' . $lang);
        }

        if($theme_slug) {
          $articles = $articles->where('category_id', $theme->id);
          $meta_title = $theme->meta_title? $theme->meta_title : $theme->name;
          $meta_desc = $theme->meta_desc? $theme->meta_desc : $meta_desc;
          $seo_text = $theme->seo_text? $theme->seo_text : $seo_text;
          $content = $theme->content;
          $translation_link = $theme->translation_link;
        }
        
        $articles = $articles->orderBy('date', 'desc')->paginate(10);
        
        if($request->isJson)
          return response()->json(['articles' => $articles->withPath($request->url().'&page='.$request->page), 
          'categories' => $categories, 'seo_text' => $seo_text, 'meta_title' => $meta_title, 'content' => $content]);
        else
          return view('ecology.index')->with('articles', $articles)->with('categories', $categories)->with('currentThemeSlug', $theme_slug)->with('parent_category_slug', $parent_category_slug)->with('meta_title', $meta_title)->with('meta_desc', $meta_desc)->with('seo_text', $seo_text)->with('seo_title', $seo_title)->with('content', $content)->with('translation_link', $translation_link);
      }

      public function ecology_show(Request $request, $theme = null, $slug) {
        $lang = session()->has('lang')? session('lang') : 'ru';
        $parent_category = explode('/', $request->path())[0] == 'uk' || explode('/', $request->path())[0] == 'ru'? explode('/', $request->path())[1] : explode('/', $request->path())[0];
        $slug = str_replace('.html', '', $slug);
        $article = Article::published()->where('slug', $slug)->firstOrTranslation();
        
        if(!$article)
          return redirect($lang . '/' . $parent_category, 301);

        $translation_link = $article->translation_link;
        $reviews = $article->reviews()->orderBy('created_at', 'desc')->paginate(10);
        
        $otherArticles = Article::whereHas('category', function($query) use ($parent_category) {
          $query->whereHas('parent', function($q) use ($parent_category) {
            $q->where('slug', $parent_category);
          });
        })->published()->where('slug', '!=', $slug)->orderBy('created_at', 'desc')->take(3)->get();
        
        if($request->isJson)
          return response()->json(['reviews' => $reviews->withPath($request->url().'&page='.$request->page)]);
        else {
          return view('ecology.show')->with('article', $article)->with('otherArticles', $otherArticles)->with('reviews', $reviews)->with('translation_link', $translation_link);
        }
      }

      public function information(Request $request, $theme_slug = null) {
        $parent_category_slug = explode('/', $request->path())[0] == 'uk' || explode('/', $request->path())[0] == 'ru'? explode('/', $request->path())[1] : explode('/', $request->path())[0];
        $lang = session()->has('lang')? session('lang') : 'ru';
        $articles = Article::where('status', 'PUBLISHED')->where('title', '!=', '')->whereHas('category', function(Builder $query) {
          $query->whereIn('parent_id', [293,294]);
        });
        $categories = Category::doesnthave('children')->where('is_active', 1)->whereIn('parent_id', [293,294]);

        if($theme_slug) {
          $theme = Category::where('slug', $theme_slug)->firstOrTranslation();

          if(!$theme || !$theme->is_active)
            abort(404);
        }

        $parent_category = Category::where('slug', $parent_category_slug)->firstOrTranslation();
        $meta_title = $parent_category->meta_title? $parent_category->meta_title : $parent_category->name;
        $meta_desc = $parent_category->meta_desc;
        $seo_title = null;
        $seo_text = $parent_category->seo_text;
        $content = $parent_category->content;
        $translation_link = $parent_category->translation_link;

        if(!$parent_category->is_active)
          abort(404);
        
        if (!Cache::has('information_categories_' . $lang)) {
          $expiresAt = Carbon::now()->addWeek();

          $categories = $categories->pluck('name', 'slug');

          Cache::put('information_categories_' . $lang, $categories, $expiresAt);
        } else {
          $categories = Cache::get('information_categories_' . $lang);
        }

        if($theme_slug) {
          $articles = $articles->where('category_id', $theme->id);
          $meta_title = $theme->meta_title? $theme->meta_title : $theme->name;
          $meta_desc = $theme->meta_desc? $theme->meta_desc : $meta_desc;
          $seo_text = $theme->seo_text? $theme->seo_text : $seo_text;
          $content = $theme->content;
          $translation_link = $theme->translation_link;
        }
        
        $articles = $articles->orderBy('date', 'desc')->paginate(10);
        
        if($request->isJson)
          return response()->json(['articles' => $articles->withPath($request->url().'&page='.$request->page), 
          'categories' => $categories, 'seo_text' => $seo_text, 'meta_title' => $meta_title, 'content' => $content]);
        else
          return view('information.index')->with('articles', $articles)->with('categories', $categories)->with('currentThemeSlug', $theme_slug)->with('parent_category_slug', $parent_category_slug)->with('meta_title', $meta_title)->with('meta_desc', $meta_desc)->with('seo_text', $seo_text)->with('seo_title', $seo_title)->with('content', $content)->with('translation_link', $translation_link);
      }

      public function information_show(Request $request, $theme = null, $slug) {
        $lang = session()->has('lang')? session('lang') : 'ru';
        $parent_category = explode('/', $request->path())[0] == 'uk' || explode('/', $request->path())[0] == 'ru'? explode('/', $request->path())[1] : explode('/', $request->path())[0];
        $slug = str_replace('.html', '', $slug);
        $article = Article::published()->where('slug', $slug)->firstOrTranslation();
        
        if(!$article)
          return redirect($lang . '/' . $parent_category, 301);

        $translation_link = $article->translation_link;
        $reviews = $article->reviews()->orderBy('created_at', 'desc')->paginate(10);
        
        $otherArticles = Article::whereHas('category', function($query) use ($parent_category) {
          $query->whereHas('parent', function($q) use ($parent_category) {
            $q->where('slug', $parent_category);
          });
        })->published()->where('slug', '!=', $slug)->orderBy('created_at', 'desc')->take(3)->get();
        
        if($request->isJson)
          return response()->json(['reviews' => $reviews->withPath($request->url().'&page='.$request->page)]);
        else {
          return view('information.show')->with('article', $article)->with('otherArticles', $otherArticles)->with('reviews', $reviews)->with('translation_link', $translation_link);
        }
      }

      public function business(Request $request, $theme_slug = null) {
        $slug = explode('/', $request->path())[0] == 'uk' || explode('/', $request->path())[0] == 'ru'? explode('/', $request->path())[1] : explode('/', $request->path())[0];
        $category = null;
        $articles = Article::where('status', 'PUBLISHED')->where('title', '!=', '');
        $parent_categories = Category::doesnthave('parent')->where('is_active', 1)->whereIn('id', [297,324,302,329,349,350,300,327,347,348])->pluck('name', 'slug');
        $categories = [];
        $meta_title = null;
        $meta_desc = null;
        $seo_title = null;
        $seo_text = null;
        $content = null;
        $translation_link = null;
        
        if($theme_slug) {
          $theme = Category::where('slug', $theme_slug)->firstOrTranslation();

          if(!$theme || !$theme->is_active)
            abort(404);
        }

        if($slug) {
          $category = Category::where('slug', $slug)->firstOrTranslation();

          if(!$category || !$category->is_active)
            abort(404);

          $categories = Category::doesnthave('children')->where('is_active', 1)->where('parent_id', $category->id)->pluck('name', 'slug');
          $meta_title = $category? $category->meta_title : null;
          $meta_desc = $category? $category->meta_desc : null;
          $seo_title = null;
          $seo_text = $category? $category->seo_text : null;
          $content = $category->content;
          $translation_link = $category->translation_link;
          
          $articles = $articles->whereHas('category', function(Builder $query) use($category) {
            $query->where(function($q) use ($category) {
              $q->where('parent_id', $category->id)->orWhere('id', $category->id);
            });
          });
        }

        if($theme_slug) {
          $articles = $articles->where('category_id', $theme->id);
          $meta_title = $theme->meta_title? $theme->meta_title : $theme->name;
          $meta_desc = $theme->meta_desc? $theme->meta_desc : $meta_desc;
          $seo_text = $theme->seo_text? $theme->seo_text : $seo_text;
          $content = $theme->content;
          $translation_link = $theme->translation_link;
        }

        if($request->sort) {
          preg_match_all("/([\w]+)_([\w]+)/", $request->sort, $value);
      
          $sort = ['value' => $value[1][0], 'dirr' => $value[2][0]];

          if($sort['value'] == 'reviews_count')
            $articles = $articles->withCount('reviews');

          $articles = $articles->orderBy($sort['value'], $sort['dirr']);
        } else {
          $articles = $articles->orderBy('date', 'desc');
        }
        
        $articles = $articles->paginate(10);
        
        if($request->isJson)
          return response()->json(['articles' => $articles->withPath($request->url().'&page='.$request->page), 'categories' => $categories, 'seo_text' => $seo_text, 'meta_title' => $meta_title, 'content' => $content]);
        else
          return view('business.index')->with('articles', $articles)->with('parent_categories', $parent_categories)->with('categories', $categories)->with('category', $category)->with('currentThemeSlug', $theme_slug)->with('meta_title', $meta_title)->with('meta_desc', $meta_desc)->with('seo_title', $seo_title)->with('seo_text', $seo_text)->with('content', $content)->with('translation_link', $translation_link);
      }

      public function business_show(Request $request, $theme, $slug = null) {
        $lang = session()->has('lang')? session('lang') : 'ru';
        $parent_category = explode('/', $request->path())[0] == 'uk' || explode('/', $request->path())[0] == 'ru'? explode('/', $request->path())[1] : explode('/', $request->path())[0];

        if(!$slug) {
          $slug = $theme;
        }

        $slug = str_replace('.html', '', $slug);

        $article = Article::published()->where('slug', $slug)->firstOrTranslation();
        
        if(!$article)
          return redirect($lang . '/' . $parent_category, 301);

        $translation_link = $article->translation_link;
        $reviews = $article->reviews()->orderBy('created_at', 'desc')->paginate(10);
        
        $prev = Article::where('status', 'PUBLISHED')->where('category_id', $article->category_id)->where('date', '<=', date('Y-m-d'))->where('date', '<=', $article->date)->where('id', '!=', $article->id)->orderBy('date', 'DESC')->first();
        
        $next = Article::where('status', 'PUBLISHED')->where('category_id', $article->category_id)->where('date', '<=', date('Y-m-d'))->where('date', '>=', $article->date)->where('id', '!=', $article->id)->orderBy('date', 'ASC')->first();
        
        $otherArticles = Article::published()->where('slug', '!=', $slug)->where('category_id', $article->category_id);

        if((clone $otherArticles)->where('region', $article->region)->count())
          $otherArticles = $otherArticles->where('region', $article->region);

        $otherArticles = $otherArticles->orderBy('created_at', 'desc')->take(3)->get();
        
        if($request->isJson)
          return response()->json(['reviews' => $reviews->withPath($request->url().'&page='.$request->page)]);
        else {
          return view('business.show')->with('article', $article)->with('otherArticles', $otherArticles)->with('prev', $prev)->with('next', $next)->with('reviews', $reviews)->with('translation_link', $translation_link);
        }
      }

      public function services(Request $request, $theme_slug = null) {
        $lang = session()->has('lang')? session('lang') : 'ru';
        $articles = Article::where('status', 'PUBLISHED')->where('title', '!=', '')->whereHas('category', function(Builder $query) {
          $query->whereIn('parent_id', [351,352]);
        });
        $parent_category = Category::whereIn('id', [351,352])->first();
        $parent_category_slug = $parent_category->slug;
        $categories = Category::doesnthave('children')->where('is_active', 1)->whereIn('parent_id', [351,352]);
        $meta_title = $parent_category->meta_title? $parent_category->meta_title : $parent_category->name;
        $meta_desc = $parent_category->meta_desc;
        $seo_title = null;
        $seo_text = $parent_category->seo_text;
        $content = $parent_category->content;
        $translation_link = $parent_category->translation_link;
        
        if($theme_slug) {
          $theme = Category::where('slug', $theme_slug)->firstOrTranslation();

          if(!$theme || !$theme->is_active || !$parent_category->is_active)
            abort(404);
        }

        if(!$theme_slug)
          return redirect(url($lang . '/' . $parent_category_slug . '/' . $categories->first()->slug), 301);

        if (!Cache::has('services_categories_' . $lang)) {
          $expiresAt = Carbon::now()->addWeek();

          $categories = $categories->pluck('name', 'slug');

          Cache::put('services_categories_' . $lang, $categories, $expiresAt);
        } else {
          $categories = Cache::get('services_categories_' . $lang);
        }
        
        if($theme_slug) {
          $articles = $articles->where('category_id', $theme->id);
          $meta_title = $theme->meta_title? $theme->meta_title : $theme->name;
          $meta_desc = $theme->meta_desc? $theme->meta_desc : $meta_desc;
          $seo_text = $theme->seo_text? $theme->seo_text : $seo_text;
          $content = $theme->content;
          $translation_link = $theme->translation_link;
        }
        
        $articles = $articles->orderBy('title')->paginate(10);
        
        if($request->isJson)
          return response()->json(['articles' => $articles->withPath($request->url().'&page='.$request->page), 
          'categories' => $categories, 'seo_text' => $seo_text, 'meta_title' => $meta_title, 'content' => $content]);
        else
          return view('services.index')->with('articles', $articles)->with('categories', $categories)->with('currentThemeSlug', $theme_slug)->with('parent_category_slug', $parent_category_slug)->with('meta_title', $meta_title)->with('meta_desc', $meta_desc)->with('seo_title', $seo_title)->with('seo_text', $seo_text)->with('content', $content)->with('translation_link', $translation_link);
      }

      public function services_show(Request $request, $theme = null, $slug) {
        $lang = session()->has('lang')? session('lang') : 'ru';
        $parent_category = explode('/', $request->path())[0] == 'uk' || explode('/', $request->path())[0] == 'ru'? explode('/', $request->path())[1] : explode('/', $request->path())[0];
        $slug = str_replace('.html', '', $slug);
        $article = Article::where('status', 'PUBLISHED')->orderBy('date', 'DESC')->orderBy('created_at', 'DESC')->where('slug', $slug)->firstOrTranslation();
        
        if(!$article)
          return redirect($lang . '/' . $parent_category, 301);

        $translation_link = $article->translation_link;
        $reviews = $article->reviews()->orderBy('created_at', 'desc')->paginate(10);
        
        $otherArticles = Article::whereHas('category', function($query) use ($parent_category) {
          $query->whereHas('parent', function($q) use ($parent_category) {
            $q->where('slug', $parent_category);
          });
        })->published()->where('slug', '!=', $slug)->orderBy('created_at', 'desc')->take(3)->get();
        
        if($request->isJson)
          return response()->json(['reviews' => $reviews->withPath($request->url().'&page='.$request->page)]);
        else {
          return view('services.show')->with('article', $article)->with('otherArticles', $otherArticles)->with('reviews', $reviews)->with('translation_link', $translation_link);
        }
      }

      public function tags(Request $request) 
      {
        $page = Page::where('template', 'tags')->first()->withFakes();
        $tag_id = $request->id;
        $tag = Tag::where('id', $tag_id)->firstOrTranslation();
        
        $articles = Article::where('articles.status', 'PUBLISHED')->where('articles.category_id', '!=', null)->orderBy('articles.date', 'DESC')->orderBy('articles.created_at', 'DESC')->join('article_tag', 'article_tag.article_id', '=', 'articles.id')->where('article_tag.tag_id', $tag_id)->select('articles.*')->distinct('articles.id')->paginate(10);

        if($request->isJson)
          return response()->json(['articles' => $articles]);
        else
          return view('news.tags', ['tag' => $tag, 'articles' => $articles, 'page' => $page]);
      }

      public function addArticleView(Request $request)
      {
        $article = Article::find($request->id);

        if($article)
          $article->update(['views' => $article->views++]);
      }
}
