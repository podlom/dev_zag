<?php

namespace App\Http\Controllers;

use Backpack\PageManager\app\Models\Page;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Backpack\NewsCRUD\app\Models\Article;

class PageController extends Controller
{
    private $research_slugs = [
        'marketingovye-issledovaniya-vsekh-rynkov-nedvizhimosti.html' => 'all',
        'marketingovi-doslidzhennia-vsih-rinkiv-neruhomosti.html' => 'all',
        'marketingovoe-issledovanie-rynka-kottedzhnyh-gorodkov.html' => 'cottage',
        'marketingove-doslidzhennia-rinku-kottedzhnih-mistechok.html' => 'cottage',
        'marketingovye-issledovaniia-kompanii-realekspo.html' => 'realexpo',
        'marketingovi-doslidzhennia-kompanii-realekspo.html' => 'realexpo'
    ];

    public function index(Request $request)
    {
        $slug = explode('/', $request->path())[0] == 'uk' || explode('/', $request->path())[0] == 'ru'? explode('/', $request->path())[1] : explode('/', $request->path())[0];
        
        $page = Page::where('slug', $slug)->firstOrTranslation();
        
        if (!$page)
        {
            abort(404, 'Please go back to our <a href="'.url('').'">homepage</a>.');
        }

        $this->data['title'] = $page->title;
        $this->data['page'] = $page->withFakes();

        return view('pages.'.$page->template, $this->data);
    }

    public function research(Request $request, $slug = null)
    {
        $page_slug = $request->segment(1) . '/' . $request->segment(2);
        
        if(isset($this->research_slugs[$slug]))
            return redirect($page_slug . '/' . $this->research_slugs[$slug], 301);

        if(!in_array($slug, ['', 'all', 'cottage', 'realexpo']))
            abort(404);

        $page = Page::where('template', 'researches')->first()->withFakes();
        $articles = Article::published()->whereIn('category_id', [52,131])->take(3)->get();

        return view('pages.researches')->with('page', $page)->with('slug', $slug)->with('articles', $articles)->with('page_slug', $page_slug);
    }

    public function about(Request $request)
    {
        $lang = session()->has('lang')? session('lang') : 'ru';
        $slug = explode('/', $request->path())[0] == 'uk' || explode('/', $request->path())[0] == 'ru'? explode('/', $request->path())[1] : explode('/', $request->path())[0];

        if($lang == 'uk' && $slug == 'o-kompanii')
            return redirect(url('uk/pro-kompaniyu'), 301);

        if($lang == 'ru' && $slug == 'pro-kompaniyu')
            return redirect(url('ru/o-kompanii'), 301);

        $page = Page::where('template', 'about')->first()->withFakes();

        return view('pages.about')->with('page', $page);
    }

    public function policy(Request $request)
    {
        $lang = session()->has('lang')? session('lang') : 'ru';
        $slug = explode('/', $request->path())[0] == 'uk' || explode('/', $request->path())[0] == 'ru'? explode('/', $request->path())[2] : explode('/', $request->path())[1];

        if($lang == 'uk' && $slug == 'politika-konfidencialnosti.html')
            return redirect(url('uk/pro-kompaniyu/politika-konfidenciynosti.html'), 301);

        if($lang == 'ru' && $slug == 'politika-konfidenciynosti.html')
            return redirect(url('ru/o-kompanii/politika-konfidencialnosti.html'), 301);

        $page = Page::where('template', 'policy')->first()->withFakes();

        return view('pages.policy')->with('page', $page);
    }

    public function cookies(Request $request)
    {
        $page = Page::where('template', 'cookies')->first()->withFakes();

        return view('pages.cookies')->with('page', $page);
    }

    public function allow_cookies(Request $request)
    {
        $response = new \Illuminate\Http\Response();

        return $response->withCookie(cookie()->forever('allow_cookies', true));
    }

    public function contacts(Request $request)
    {
        $lang = session()->has('lang')? session('lang') : 'ru';
        $slug = explode('/', $request->path())[0] == 'uk' || explode('/', $request->path())[0] == 'ru'? explode('/', $request->path())[1] : explode('/', $request->path())[0];

        if($lang == 'uk' && $slug == 'kontakty')
            return redirect(url('uk/kontakti'), 301);

        if($lang == 'ru' && $slug == 'kontakti')
            return redirect(url('ru/kontakty'), 301);

        $page = Page::where('template', 'contacts')->first()->withFakes();

        return view('pages.contacts')->with('page', $page);
    }
}