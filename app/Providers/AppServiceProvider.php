<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Research;
use Aimix\Review\app\Models\Review;
use App\Models\Faq;
use App\Observers\FaqObserver;
use App\Models\FaqCategory;
use App\Observers\FaqCategoryObserver;
use App\Models\Term;
use App\Observers\TermObserver;
use Backpack\NewsCRUD\app\Models\Category;
use App\Observers\CategoryObserver;
use Backpack\NewsCRUD\app\Models\Tag;
use App\Observers\TagObserver;
use Backpack\NewsCRUD\app\Models\Article;
use App\Observers\ArticleObserver;
use App\Models\Meta;
use App\Observers\MetaObserver;
use App\Region;
use App\Observers\RegionObserver;
use App\Area;
use App\Observers\AreaObserver;
use App\City;
use App\Observers\CityObserver;
use Backpack\MenuCRUD\app\Models\MenuItem;
use App\Observers\MenuItemObserver;
use Aimix\Feedback\app\Models\Feedback;
use App\Observers\PageObserver;
use Backpack\PageManager\app\Models\Page;
use App\Models\PollQuestion;
use App\Observers\PollQuestionObserver;
use App\Models\PollOption;
use App\Observers\PollOptionObserver;
use Aimix\Shop\app\Models\Attribute;

use Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //dd();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        FaqCategory::observe(FaqCategoryObserver::class);
        Faq::observe(FaqObserver::class);
        Term::observe(TermObserver::class);
        Category::observe(CategoryObserver::class);
        Tag::observe(TagObserver::class);
        Article::observe(ArticleObserver::class);
        Meta::observe(MetaObserver::class);
        Region::observe(RegionObserver::class);
        Area::observe(AreaObserver::class);
        City::observe(CityObserver::class);
        MenuItem::observe(MenuItemObserver::class);
        Page::observe(PageObserver::class);
        PollQuestion::observe(PollQuestionObserver::class);
        PollOption::observe(PollOptionObserver::class);
		
		View::composer('backpack::inc.topbar_left_content', function ($view) {
            $new_reviews = Review::withoutGlobalScopes()->where('type', '!=', 'article')->where('is_moderated', 0)->count();
            $new_feedbacks = Feedback::where('processed', 0)->count();
			$view->with('new_reviews', $new_reviews)->with('new_feedbacks', $new_feedbacks);	
		});
		
        View::composer('backpack::inc.sidebar_content', function ($view) {
            $new_researches = Research::where('status', 'new')->count();
            $new_comments = Review::withoutGlobalScopes()->where('type', 'article')->where('is_moderated', 0)->count();
            
            $view->with('new_researches', $new_researches)->with('new_comments', $new_comments);
        });

        View::composer('*', function($view) {
            $selectedRegion = session()->get('region')? Region::where('slug', session()->get('region'))->first() : null;
            $lang = \App::getLocale();
            $allRegions = Region::pluck('name', 'slug');
            $headerMenu = MenuItem::whereIn('id', [11,12])->first();
            $footerMenu = MenuItem::whereIn('id', [19,20])->first();
            $footerSubMenu = MenuItem::whereIn('id', [50,52,53,62,64,65,51,63])->get();//51,63,
            $version = env('CACHE_VERSION') . '.3';
            $policyLink = $lang == 'ru'? url('ru/o-kompanii/politika-konfidencialnosti.html') : url('uk/pro-kompaniyu/politika-konfidenciynosti.html');
            $cottage_slug = \Aimix\Shop\app\Models\Category::find([1,6])->first()->slug;
            $newbuild_slug = \Aimix\Shop\app\Models\Category::find([2,7])->first()->slug;
            $allow_cookies = request()->cookie('allow_cookies');
            $regions = Region::pluck('name', 'region_id');
            $max_area = Attribute::find(4)->values->max;
            $google_news_link = $lang == 'ru'? 'https://news.google.com/publications/CAAqBwgKMJCXoQswqKG5Aw/sections/CAQqEAgAKgcICjCQl6ELMKihuQMw6Z_4Bg?hl=ru&gl=UA&ceid=UA%3Aru' : 'https://news.google.com/publications/CAAqBwgKMJCXoQswqKG5Aw?hl=ru&gl=UA&ceid=UA%3Aru';

            $view->with('allRegions', $allRegions)->with('selectedRegion', $selectedRegion)->with('headerMenu', $headerMenu)->with('footerMenu', $footerMenu)->with('footerSubMenu', $footerSubMenu)->with('lang', $lang)->with('version', $version)->with('policyLink', $policyLink)->with('cottage_slug', $cottage_slug)->with('newbuild_slug', $newbuild_slug)->with('allow_cookies', $allow_cookies)->with('regions', $regions)->with('max_area', $max_area)->with('google_news_link', $google_news_link);//
        });
    }
}
