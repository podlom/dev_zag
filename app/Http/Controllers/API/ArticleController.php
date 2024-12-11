<?php

namespace App\Http\Controllers\API;

use Illuminate\Database\Eloquent\Builder;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use \Backpack\NewsCRUD\app\Models\Article;
use \Backpack\NewsCRUD\app\Models\Category;
use App\Region;
use Illuminate\Support\Facades\Redis;

class ArticleController extends Controller
{
	public function byArea(Request $request, $articleTab, $region){
		$articleCategories = [
			0 => [1,14],
			1 => [12, 27],
			2 => [5,26],
			3 => [13,28]
		];
		$articleTab = $articleTab? (int)$articleTab : 0;
    
    $language = session()->has('lang')? session()->get('lang'): 'ru';
        
		$newsCategoryLink = url('/' . $language . '/' . Category::whereIn('id', $articleCategories[$articleTab])->first()->slug);

        $key = "articles.lang:$language.tab:$articleTab";

		if($region != 'null')
            $key .= "region:$region";

        if($articles = Redis::get($key)) {
            return response()->json(['articles' => json_decode($articles), 'newsCategoryLink' => $newsCategoryLink]);
        }
      
		$parent_id = $articleCategories[$articleTab];
		
		$articles = Article::published()->whereHas('category', function(Builder $query) use ($parent_id) {
	        $query->whereIn('parent_id', $parent_id);
	    });
		
		if($region != 'null') {
            $region_id = Region::where('region_id', $region)->first()->article_region_id;
            $articles = $articles->where('region', $region_id);
        }
		
		$articles = $articles->take(12)->get()->toArray();

        Redis::set($key, json_encode($articles), 'EX', 108000);
		
        return response()->json(['articles' => $articles, 'newsCategoryLink' => $newsCategoryLink]);
	}
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
    
}
