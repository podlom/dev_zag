<?php

namespace Aimix\Review\app\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Aimix\Review\app\Http\Requests\ReviewRequest;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use Aimix\Review\app\Models\Review;
use Aimix\Shop\app\Models\Product;
use Backpack\PageManager\app\Models\Page;

class ReviewController extends BaseController
{
    const DEFAULT_LANG = 'uk';

    private $sort = [
        'value' => 'created_at',
        'dirr' => 'desc'
    ];

    private $types_ru = [
        'otzyvy-o-kompanii-realekspo' => 'realexpo',
        'otzyvy-o-kottedzhnyh-gorodkah' => 'cottage',
        'otzyvy-o-portale-zagorodnacom' => 'zagorodna',
        'otzyvy-o-prigorodnyh-novostroykah' => 'newbuild',
        'otzyvy-o-zastroishikah' => 'brand',
    ];

    private $types_uk = [
        'vidguki-pro-kompaniiu-realekspo' => 'realexpo',
        'vidguki-pro-kotedzhni-mistechka' => 'cottage',
        'vidguki-pro-portal-zagorodnacom' => 'zagorodna',
        'vidguki-pro-primiski-novobudovi' => 'newbuild',
        'vidguki-pro-zabudovnikiv' => 'brand',
    ];

    private function getSortArray($sort_string)
    {
        preg_match_all("/([\w]+)_([\w]+)/", $sort_string, $value);

        return ['value' => $value[1][0], 'dirr' => $value[2][0]];
    }

    public function index(Request $request, $type = null)
    {
        $lang = explode('/', $request->path())[0];

        Log::info(__METHOD__ . '+' . __LINE__ . '@ts $lang: '. var_export($lang, true));

        $slug = $lang == 'uk' || $lang == 'ru' ? explode('/', $request->path())[1] : $lang;

        $types = $this->{"types_$lang"};

        if ($type && !isset($types[$type]))
            return redirect($lang . '/' . $slug);

        $reviews = Review::where('is_moderated', '1');
        $page = Page::where('template', 'reviews')->first()->withFakes();

        if ($type) {
            $type = $types[$type];
            Log::info(__METHOD__ . '+' . __LINE__ . '@ts $type: '. var_export($type, true));
            $reviews = $reviews->where('type', $type);
        } else
            $reviews = $reviews->where('type', 'zagorodna');

        if ($request->forProducts == true)
            $reviews = $reviews->where('product_id', '!=', null);
        elseif ($request->forProducts == false)
            $reviews = $reviews->where('product_id', null);

        if ($request->has('sort')) {
            $this->sort = $this->getSortArray($request->input('sort'));
        }

        $reviews = $reviews->orderBy($this->sort['value'], $this->sort['dirr'])->paginate(config('aimix.review.per_page'));

        $h1 = $type ? $page[$type . '_h1'] : $page->h1;
        $meta_title = $type && $page[$type . '_meta_title'] ? $page[$type . '_meta_title'] : $page->meta_title;
        $meta_desc = $type && $page[$type . '_meta_desc'] ? $page[$type . '_meta_desc'] : $page->meta_desc;
        $seo_title = $type ? $page[$type . '_seo_title'] : $page->seo_title;
        $seo_text = $type ? $page[$type . '_seo_text'] : $page->seo_text;

        if ($request->isJson)
            return response()->json(['reviews' => $reviews, 'h1' => $h1, 'meta_title' => $meta_title, 'seo_title' => $seo_title, 'seo_text' => $seo_text]);
        else
            return view('reviews.index')->with('reviews', $reviews)->with('type', $type)->with('page', $page)->with('h1', $h1)->with('meta_title', $meta_title)->with('meta_desc', $meta_desc)->with('seo_title', $seo_title)->with('seo_text', $seo_text)->with('slug', $slug)->with('types', array_flip($types));
    }

    public function requestSearchList(Request $request, $value)
    {
        $values = [];

        if ($value) {
            $values = Product::where('is_active', 1)->where('name', 'like', '%' . $value . '%')->get();
        }

        return response()->json($values);
    }

    public function create(ReviewRequest $request, $type = 'text')
    {
        $review = new Review;
        $review->type = $type;
        $review->name = $request->input($type . '_review_name');
        $review->email = $request->input($type . '_review_email');
        $review->text = $request->input($type . '_review_text');
        $review->reviewable_type = $request->input('reviewable_type');
        $review->reviewable_id = $request->input('reviewable_id');
        // added by @ts 2024-08-12
        $review->language_abbr = $request->input('lang');
        if (empty($review->language_abbr)) {
            $review->language_abbr = $request->input('language_abbr') ?? self::DEFAULT_LANG;
        }
        Log::info(__METHOD__ . '+' . __LINE__ . '@ts $review->language_abbr: '. var_export($review->language_abbr, true));

        if (config('aimix.review.enable_review_for_product'))
            $review->product_id = $request->input($type . '_review_product_id');

        if (config('aimix.review.enable_rating'))
            $review->rating = $request->input($type . '_review_rating');


        if ($request->file($type . '_review_file')) {
            $path = $request->file($type . '_review_file')->store('reviews', 'reviews');

            $review->file = '/uploads/' . $path;
        }
        if ($request->file($type . '_review_images')) {
            $images = [];
            foreach ($request->file($type . '_review_images') as $image) {
                $path = $image->store('reviews', 'reviews');

                $images[] = '/uploads/' . $path;
            }
            $review->images = $images;
        }

        $review->save();

        return back()->with('message', __('forms.success.review'))->with('type', 'review');
    }
}
