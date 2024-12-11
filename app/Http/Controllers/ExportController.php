<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aimix\Promotion\app\Models\Promotion;
use Aimix\Shop\app\Models\Brand;
use Aimix\Shop\app\Models\BrandCategory;
use Aimix\Shop\app\Models\Product;
use Aimix\Shop\app\Models\Modification;
use Aimix\Shop\app\Models\AttributeModification;
use Aimix\Shop\app\Models\Attribute;
use App\Region;
use App\Area;
use App\City;
use App\Kyivdistrict;
use Backpack\NewsCRUD\app\Models\Tag;
use Backpack\NewsCRUD\app\Models\Article;
use Backpack\NewsCRUD\app\Models\Category;
use App\ArticleRegion;
use Aimix\Review\app\Models\Review;
use Goutte\Client;
use App\Models\Meta;
use App\Models\Communication;
use App\Models\Infrastructure;
use App\Models\Term;
use Backpack\PageManager\app\Models\Page;
use App\Models\PollQuestion;

use Illuminate\Support\Facades\DB;
use App\Imports\ContactsImport;
use Maatwebsite\Excel\Facades\Excel;
use Telegram;
use Spatie\Sitemap\SitemapGenerator;
use App\ParsingLog;
use Symfony\Component\DomCrawler\Crawler;
use UploadImage;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ParseNewbuildPage;
use App\Jobs\ParseProductPage;
use App\Notification;
use Illuminate\Support\Facades\Redis;

class ExportController extends Controller
{

    private $product = [
        'address' => []
    ];

    private $promotions = [];
    private $company = [
        'extras' => [
            'statistics' => []
        ],
        'translation' => [
            'extras_translatable' => [
                'statistics' => []
            ]
        ]
    ];

    private $array = [];
	
	public function products(){
        // \Artisan::call('statistics:generate');
	}

    private function uploadSvgImage($link)
    {
        // Get html remote text.
        $html = file_get_contents($link);

        // Create new instance for parser.
        $crawler = new Crawler(null, $link);
        $crawler->addHtmlContent($html, 'UTF-8');

        $svg_html = $crawler->html();
        $svg_name = now()->timestamp . '.svg';
        $path = 'uploads/new_images/' . $svg_name;

        Storage::disk('common')->put($path, $svg_html);
        return $path;
    }

            // /**
    //  * Get list of catalogs by region/city
    //  *
    //  * @param  string  $link
    //  * @return array
    // */
    private function getCatalogsLinks($link)
    {
        // Get html remote text.
        $html = file_get_contents($link);

        // Create new instance for parser.
        $crawler = new Crawler(null, $link);
        $crawler->addHtmlContent($html, 'UTF-8');

        // List of catalogs by region/city
        $catalogs = $crawler->filter('.GeoControlGlobal-suggestion div:not(.GeoControlGlobal-cities) a')->extract(['href']);

        return $catalogs;
    }

    private function get_http_response_code($url) {
        $headers = get_headers($url);
        return substr($headers[0], 9, 3);
    }

    // /**
    //  * Get all products from catalog page
    //  *
    //  * @param  string  $path
    //  * @param  string  $site
    //  * @return array
    // */
    private function getCatalogProducts($path, $site)
    {
        $link = $site . $path;

        if($this->get_http_response_code($link) != "200"){
            return [];
        }else{
            $html = file_get_contents($link);
        }

        // Create new instance for parser.
        $crawler = new Crawler(null, $link);
        $crawler->addHtmlContent($html, 'UTF-8');

        $subtitle = $crawler->filter('.UISubtitle-content')->text();
        preg_match('!\d+!', $subtitle, $matches);
        $pages = ceil($matches[0] / 24);

        // get first page products
        $products = $crawler->filter('.card .card-media');

        if(!count($products))
            return [];

        $products = $products->extract(['href']);

        // if more than 1 page -> get other pages products
        if($pages) {
            for($i = 2; $i <= $pages; $i++) {
                $paged_link = $link . '?page=' . $i;
                // Get html remote text.
                $html = file_get_contents($paged_link);

                // Create new instance for parser.
                $crawler = new Crawler(null, $paged_link);
                $crawler->addHtmlContent($html, 'UTF-8');

                $products = array_merge($products, $crawler->filter('.card .card-media')->extract(['href']));
            }
        }

        $link = $link . '?radius=0';
        $html = file_get_contents($link);
        // Create new instance for parser.
        $crawler = new Crawler(null, $link);
        $crawler->addHtmlContent($html, 'UTF-8');

        $subtitle = $crawler->filter('.UISubtitle-content')->text();
        preg_match('!\d+!', $subtitle, $matches);
        $pages = ceil($matches[0] / 24);
        
        // get first page products
        $city_products = $crawler->filter('.card .card-media');

        if(!count($city_products))
            return $products;

        $city_products = $city_products->extract(['href']);
        
        // if more than 1 page -> get other pages products
        if($pages) {
            for($i = 2; $i <= $pages; $i++) {
                $paged_link = $link . '&page=' . $i;
                // Get html remote text.
                $html = file_get_contents($paged_link);

                // Create new instance for parser.
                $crawler = new Crawler(null, $paged_link);
                $crawler->addHtmlContent($html, 'UTF-8');

                $city_products = array_merge($city_products, $crawler->filter('.card .card-media')->extract(['href']));
            }
        }

        return array_diff($products, $city_products);
    }

    private function parsePromotion($promotion_link, $node)
    {
        $promotion['link'] = str_replace(['%3A', '%2F'], [':', '/'], explode('&to=', $promotion_link)[1]);
        $promotion['title'] = str_replace('Акция ・ ', '', $node->filter('.UICardLink-title')->text());
        $promotion['desc'] = $node->filter('.UICardLink-description')->text();
        $schema = json_decode($node->filter('script[type="application/ld+json"]')->text());
        $promotion['start'] = $schema->startDate;
        $promotion['end'] = $schema->endDate;

        return $promotion;
    }


    public function common()
    {
        (new Article)->clearGlobalScopes();


        // dd(\DB::table('failed_jobs')->orderBy('failed_at', 'desc')->first());

        // $site = 'https://lun.ua';
        // $path = '/ru/%D0%B6%D0%BA-%D1%94%D0%B2%D1%80%D0%BE%D0%BC%D1%96%D1%81%D1%82%D0%BE-2-%D0%BA%D1%80%D1%8E%D0%BA%D0%BE%D0%B2%D1%89%D0%B8%D0%BD%D0%B0';

        // ParseNewbuildPage::dispatch($site, $path)->onQueue('parsing');
        // ParseProductPage::dispatch($site, $path)->onQueue('parsing');

        // \Artisan::call("optimize");

        // Product::where('is_parsed', 1)->where('is_active', 0)->delete();
        
        // $controller = new \App\Http\Controllers\CatalogController;

        // $request = new Request;
        // $request->setMethod('POST');
        // $request->request->add([
        //     'caching' => true
        // ]);

        // $req = clone $request;
        // $req->request->add(['a' => 1]);

        // dd($req->all());

        // dd($controller->getProducts($request, true));


        // $controller = new \App\Http\Controllers\CatalogController;
        // $request = new Request;
        // $request->setMethod('POST');
    
        // $request->request->add([
        //     'is_hit' => '1'
        // ]);

        // dd($controller->getProducts($request, true));


        // $start = microtime(true);
        
        // if(Redis::get('test')) {
        //     $products = Redis::get('test');
        // } else {
        //     $products = Product::join('categories', 'categories.id', '=', 'products.category_id')->where('products.language_abbr', 'ru')->where('categories.id', 1)->where('products.address->region', '!=', '29')->orderBy('products.name', 'desc')->paginate(100);
    
        //     $products = new \App\Http\Resources\Products($products);
        //     $products = json_encode($products);
        //     Redis::set('test', $products, 'EX', '300');
        // }

        // $end = microtime(true) - $start;

        
        // dd(json_decode($products), $end);


        // Redis::flushall();
        // dd(Redis::get('test'));

        // dd(count(Redis::keys('*')));
        // dd($products, $end);

        // dd(now()->addSeconds(15)->timestamp);
        // Redis::set('text', 'aaa', 'EX', '15');

        // $site = 'https://lun.ua';
        // $path = '/ru/%D0%B6%D0%BA-%D1%8D%D0%BB%D0%B8%D1%82%D0%BD%D1%8B%D0%B9-%D0%BA%D0%BE%D0%BB%D0%BE%D0%BC%D1%8B%D1%8F';
        
        // ParseNewbuildPage::dispatch($site, $path)->onQueue('parsing');
        
        // $tg = Telegram::getUpdates();
        // dd($tg);

        // $folder = 'tables/statistics-cottages-number';

        // $files = \Storage::disk('common')->files($folder);
        // foreach($files as $file) {
            
        //     $filename = explode('.', explode('/', $file)[2])[0];
        //     $day = substr($filename, 0, 2);
        //     $year = substr($filename, 6, 4);
            
        //     $filename_new = substr_replace($filename, $day, 6, 4);
        //     $filename_new = substr_replace($filename_new, $year, 0, 2);
            
        //     \Storage::disk('common')->move($folder . '/' . $filename . '.json', $folder . '/' . $filename_new . '.json');
        // }


        // \Storage::disk('common')->put('tables/' . $folder . '/' . $date . '.json', json_encode($table));


        // dd(Modification::withoutGlobalScopes()->where('slug', 'proekt-11610965670')->first());

        // Brand::first()->save();
        // $brands = Brand::where('language_abbr', 'ru')->where('address->latlng', null);
        // 1268
        // $brands->each(function($item) {
        //     // $item->update(['extras' => $item->extras]);
        //     $item->save();
        // });

        // dd($brands->count());

        // $dupls = DB::select('SELECT original_id, COUNT(original_id)
        // FROM modifications
        // GROUP BY original_id
        // HAVING COUNT(original_id)>1');

        // foreach($dupls as $dupl) {
        //     $mods = Modification::where('original_id', $dupl->original_id)->skip(1)->take(999)->get();

        //     $mods->each(function($item) {
        //         $item->delete();
        //     });
        // }
        
        
        // foreach(DB::table('failed_jobs')->where('queue', 'parsing')->get() as $key => $item) {
        //     if($key > 1)
        //     dd(json_decode($item->payload)->data->command);
            // DB::table('jobs')->insert([
            //     'payload' => $item->payload,
            //     'queue' => $item->queue,
            //     'attempts' => 0,
            //     'available_at' => now()->timestamp,
            //     'created_at' => now()->timestamp,
            //     'reserved_at' => now()->timestamp,
            // ]);
        // }

        // dd(Promotion::where('language_abbr', 'ru')->get());

        // Product::find(8376)->modifications->each(function($item) {
        //     var_dump($item->attrs->find(7)->pivot->value);
        // });
        // die();

        // dd(Product::find(8376)->modifications);
    

        // $site = 'https://lun.ua';
        // $path = '/ru/%D0%B6%D0%BA-%D1%87%D0%B0%D0%B1%D0%B0%D0%BD%D1%8B-2-%D1%87%D0%B0%D0%B1%D0%B0%D0%BD%D1%8B';
        // ParseNewbuildPage::dispatch($site, $path)->onQueue('parsing');

        
// foreach($q as $item) {
//     if(Article::find($item->article_id)->tags()->where('tags.id', $item->tag_id)->count() > 1)
//         Article::find($item->article_id)->tags()->detach($item->tag_id);
// }

        // Article::where('language_abbr', 'ru')->where('images', '!=', null)->each(function($i) {
        //     $i->save();
        // });
        // dd(Article::where('language_abbr', 'uk')->where('images', '!=', null)->count());

        // Modification::where('language_abbr', 'ru')->whereDoesntHave('translations')->each(function($item) {
        //     $item->save();
        // });

        // dd(Modification::where('language_abbr', 'ru')->whereDoesntHave('translations')->count());

        // $mod = Modification::whereDoesntHave('attrs');
        // // $mod->each(function($item) {
        // //     $item->save();
        // // })
        // dd($mod->count());

        // foreach(DB::table('failed_jobs')->pluck('payload') as $item) {
        //     dd(json_decode($item)->data->command);
        // }



        // $file = json_decode(\Storage::disk('common')->get('parsing_test.json'));
        // // $file[] = 'sd';
        // if(in_array('ss', $file))
        //     unset($file[array_search('ss', $file)]);
        // \Storage::disk('common')->put('parsing_test.json', json_encode(array_values($file), JSON_PRETTY_PRINT));

        // dd($file);



        // Modification::where('language_abbr', 'uk')->where('layouts', 'like', "%\\\u044d\\\u0442\\\u0430\\\u0436%")->each(function($item) {
        //     $layouts = $item->layouts;
        //     foreach($layouts as $key => $layout) {
        //         $layouts[$key]['name'] = str_replace('этаж', 'поверх', $layout['name']);
        //     }
        //     $item->layouts = $layouts;
        //     $item->save();
        // });
        
        // Modification::where('language_abbr', 'uk')->where('name', 'like', '%двухуровневые%')->each(function($item) {
        //     $item->name = str_replace('Двухуровневые', 'Дворівневі', $item->name);
        //     $item->save();
        // });
        
        // $props = ['insulation', 'closed_area', 'class', 'technology', 'ceilings', 'condition', 'parking', ];
        
        // $products = Product::where('id', '!=', null);

        // foreach($props as $key => $prop) {
        //     $where = $key === 0? 'where' : 'orWhere';
        //     $products = $products->{$where}("extras->$prop", '!=', null);
        // }

        // $products->each(function($item) use ($props) {
        //     $extras = $item->extras;
        //     $extras_trans = $item->extras_translatable;

        //     foreach($props as $prop) {
        //         if(array_key_exists($prop, $extras)) {
        //             $extras_trans[$prop] = $extras[$prop];
        //             unset($extras[$prop]);
        //         }
        //     }
            
        //     $item->update(['extras' => $extras, 'extras_translatable' => $extras_trans]);
        // });

        // $ids = [6606, 6658, 6718, 6722, 6724, 6736, 6770, 6820, 6838, 6892, 6960, 6964, 6966, 6968, 6970, 6972, 6974, 6976, 6978, 6982, 6984, 7064, 7086, 7742, 7877, 7922];
        // $links = Product::find($ids)->pluck('parsing_link');
        // $array = [];
        // dd();

        // foreach($links as $link) {
        //     // Get html remote text.
        //     $html = file_get_contents($link);
    
        //     // Create new instance for parser.
        //     $crawler = new Crawler(null, $link);
        //     $crawler->addHtmlContent($html, 'UTF-8');


        // }

        // Черкассы (Волынская) city_id 31093 area_id 306 region_id 3
        // Черкассы city_id 253237 area_id 2745 region_id 25

        // Львов (Днепропетровская) city_id 40699 area_id 405 region_id 4
        // Львов city_id 142550 area_id 2735 region_id 14

        // Полтава (Луганская) city_id 132443 area_id 1313 region_id 13
        // Полтава city_id 174013 area_id 2738 region_id 17

        // Городок (Винницкая) city_id 21009 area_id 206 region_id 2
        // Городок (Ровненская) city_id 182620 area_id 1813 region_id 18

        // Старт очистки спарсеных объектов
        // $log = ParsingLog::latest()->first();

        // $items = array_filter($log->details, function($item) {
        //     return $item['type'] === 'brand';
        // });

        // foreach($items as $item) {
        //     if(!$item['is_new'])
        //         continue;

        //     $id = explode('/', explode('/brand/', $item['admin_link'])[1])[0];
        //     Brand::find($id)->delete();
        // }

        // $items = array_filter($log->details, function($item) {
        //     return $item['type'] === 'newbuild';
        // });

        // foreach($items as $item) {
        //     if(!$item['is_new'])
        //         continue;

        //     $id = explode('/', explode('/product/', $item['admin_link'])[1])[0];
        //     Product::find($id)->delete();
        // }

        // foreach($items as $item) {
        //     if($item['is_new'])
        //         continue;

        //     $id = explode('/', explode('/product/', $item['admin_link'])[1])[0];
        //     if(Product::find($id) && Product::find($id)->baseModification->images && strpos(implode(Product::find($id)->baseModification->images), 'new_images_2') !== false)
        //         Product::find($id)->baseModification->update(['images' => []]);
        // }
        

        // $items = array_filter($log->details, function($item) {
        //     return $item['type'] === 'promotion';
        // });

        // foreach($items as $item) {
        //     if(!$item['is_new'])
        //         continue;

        //     $id = explode('/', explode('/promotion/', $item['admin_link'])[1])[0];
        //     Promotion::find($id)->delete();
        // }
        // Конец очистки спарсеных объектов

        // Product::where('address->city', 40699)->each(function($prod) {
        //     $name = $prod->name;

        //     $names = [$name];
        
        //     if(mb_strpos($name, 'КГ') !== false) {
        //         // КГ Шевченковский / КГ «Шевченковский» => Шевченковский КГ / «Шевченковский» КГ
        //         $name_alt = str_replace('КГ ', '', $name);
        //         $names[] = $name_alt . ' КГ';
        //         if(mb_strpos($name_alt, '«') === false)
        //             $names[] = '«' . $name_alt . '»' . ' КГ';
        //         else
        //             $names[] = str_replace(['«', '»'], '', $name_alt) . ' КГ';
        //     } elseif(mb_strpos($name, 'Дуплексы') !== false) {
        //         // Дуплексы Шевченковский / Дуплексы «Шевченковский» => Шевченковский дуплексы / «Шевченковский» дуплексы
        //         $name_alt = str_replace('Дуплексы ', '', $name);
        //         $names[] = $name_alt . ' дуплексы';
        //         if(mb_strpos($name_alt, '«') === false)
        //             $names[] = '«' . $name_alt . '»' . ' дуплексы';
        //         else
        //             $names[] = str_replace(['«', '»'], '', $name_alt) . ' дуплексы';
        //     } elseif(mb_strpos($name, 'Таунхаусы') !== false) {
        //         // Таунхаусы Шевченковский / Таунхаусы «Шевченковский» => Шевченковский таунхаусы / «Шевченковский» таунхаусы
        //         $name_alt = str_replace('Таунхаусы ', '', $name);
        //         $names[] = $name_alt . ' таунхаусы';
        //         if(mb_strpos($name_alt, '«') === false)
        //             $names[] = '«' . $name_alt . '»' . ' таунхаусы';
        //         else
        //             $names[] = str_replace(['«', '»'], '', $name_alt) . ' таунхаусы';
        //     } elseif(mb_strpos($name, 'Квадрексы') !== false) {
        //         // Квадрексы Шевченковский / Квадрексы «Шевченковский» => Шевченковский квадрексы / «Шевченковский» квадрексы
        //         $name_alt = str_replace('Квадрексы ', '', $name);
        //         $names[] = $name_alt . ' квадрексы';
        //         if(mb_strpos($name_alt, '«') === false)
        //             $names[] = '«' . $name_alt . '»' . ' квадрексы';
        //         else
        //             $names[] = str_replace(['«', '»'], '', $name_alt) . ' квадрексы';
        //     } elseif(mb_strpos($name, 'Коттеджный квартал') !== false) {
        //         // Коттеджный квартал Шевченковский / Коттеджный квартал «Шевченковский» => Шевченковский КГ / Шевченковский коттеджный квартал / «Шевченковский» КГ / «Шевченковский» коттеджный квартал
        //         $name_alt = str_replace('Коттеджный квартал ', '', $name);
        //         $names[] = $name_alt . ' КГ';
        //         $names[] = $name_alt . ' коттеджный квартал';
        //         if(mb_strpos($name_alt, '«') === false) {
        //             $names[] = '«' . $name_alt . '»' . ' КГ';
        //             $names[] = '«' . $name_alt . '»' . ' коттеджный квартал';
        //         }
        //         else
        //             $names[] = str_replace(['«', '»'], '', $name_alt) . ' КГ';
        //             $names[] = str_replace(['«', '»'], '', $name_alt) . ' коттеджный квартал';
        //     } else {
        //         // Шевченковский / «Шевченковский» => «Шевченковский» КГ
        //         $names[] = mb_strpos($name, '«') !== false? $name . ' КГ' : '«' . $name . '»' . ' КГ';
        //     }
    
        //     foreach($names as $item) {
        //         // «Шевченковский 2» КГ => «Шевченковский-2» КГ
        //         $number = preg_replace('/[^0-9]/', '', $item);
        //         if(mb_strpos($item, ' ' . $number) !== false) {
        //             $names[] = str_replace(' ' . $number, '-' . $number, $item);
        //         }
        //     }
    
        //     foreach($names as $item) {
        //         // «Шевченковский» КГ => "Шевченковский" КГ
        //         if(mb_strpos($item, '«') !== false)
        //             $names[] = str_replace(['«', '»'], '"', $item);
        //     }

        //     $product = Product::whereIn('name', $names)->where('address->city', 142550)->first();

        //     $product->modifications()->delete();
        //     $prod->modifications()->update(['product_id' => $product->id]);
        //     $prod->delete();
        // });

        
        // dd(Product::where('address->city', 40699)->update(['address' => Product::find(6998)->address]));
            
        // $products = DB::table('products')->select('products.*')->distinct('products.id')->where('products.language_abbr', 'ru')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.layouts', 'like', '%parsed%')->pluck('name');
        //     dd($products);
            

        // $jobs = DB::table('failed_jobs')->where('queue', 'parsing')->get();

        // foreach($jobs as $job) {
        //     DB::table('jobs')->insert([
        //         'queue' => 'parsing',
        //         'payload' => $job->payload,
        //         'attempts' => 0,
        //         'available_at' => now()->timestamp,
        //         'created_at' => now()->timestamp
        //     ]);
        // }

        // $brands = Brand::get();

        // Brand::each(function($item) {
        //     $images = $item->images;
        //     if($images['logo'] && !$images['business_card']) {
        //         $images['business_card'] = $images['logo'];
        //         $item->update(['images' => $images]);
        //     }
        // });

        // dd(Product::where('extras->site', 'like', '%zk.com.ua%')->get());

        // $products = Product::with('modifications')->select(['products.*', 'modifications.price'])->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->active()->where('price', '!=', 0)->where('products.extras->is_frozen', 0);

        // $data = [
        //     'min' => $products->min('price'),
        //     'max' => $products->max('price'),
        //     'avg' => round($products->avg('price')),
        //     'products' => new \App\Http\Resources\Products($products->distinct('products.id')->orderBy('modifications.price', 'asc')->paginate(10))
        //   ];

        // dd($data);

        // dd(Product::with('modifications')->distinct()->select('products.*')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->active()->where('price', '!=', 0)->where('products.extras->is_frozen', 0)->paginate(10));

        // dd(Product::with('modifications')->select('products.*')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->active()->where('price', '!=', 0)->where('products.extras->is_frozen', 0)->where('price', 4850)->get());
        

        // PollQuestion::where('language_abbr', 'ru')->each(function($item) {
        //     $lang = $item->language_abbr;
        //     $title = $item->title;
        //     $article = new Article;
        //     $article->language_abbr = $lang;
        //     $article->poll_id = $item->id;
        //     $article->category_id = $lang == 'ru'? 371 : 372;
        //     $article->save();
        // });

        // PollQuestion::where('language_abbr', 'uk')->each(function($item) {
        //     $lang = $item->language_abbr;
        //     $title = $item->title;
        //     $article = new Article;
        //     $article->language_abbr = $lang;
        //     $article->poll_id = $item->id;
        //     $article->category_id = $lang == 'ru'? 371 : 372;
        //     $article->original_id = $lang == 'ru'? null : Article::where('category_id', 371)->where('poll_id', $item->original->id)->first()->id;
        //     $article->save();
        // });
        
        // dd(Product::where('address->latlng->lng', 0)->get());
        // dd(Product::where('language_abbr', 'ru')->where('slug', 'sofija1_zhk')->first()->address);
        // Product::where('language_abbr', 'uk')->each(function($item) {
            
        //     $ad = $item->address;
        //     $city = City::where('city_id', $item->address['city'])->where('language_abbr', 'uk')->first();

        //     if(!$city)
        //         return;

        //     $city = $city->name;
        //     $address = isset($item->extras_translatable['address_string']) && $item->extras_translatable['address_string']? $item->extras_translatable['address_string'] . ', ' . $city : $city;

        //     if($address) {
        //         $geo = \Geocoder::getCoordinatesForAddress($address);
            
        //         $ad['latlng'] = [
        //             'lat' => $geo['lat'],
        //             'lng' => $geo['lng']
        //         ];
                
        //         $item->update(['address' => $ad]);
    
        //         $item->original()->update(['address' => $ad]);
        //     }
        // });

    
        // dd(Product::where('extras->phone', '!=', null)->where('old_id', 1185)->first());
        // $items = \Storage::disk('common')->get('cont.xlsx');

        // Excel::import(new ContactsImport, 'контакты_новостройки.xlsx');

        // $items = json_decode(\Storage::disk('common')->get('cttg_statistics.json'));

        // dd($items[2]->data[0]->zones);
        
        // dd(Product::first()->extras_translatable);
        // $products = $products->join('areas', function($join) use ($string) {
        //     $join->on('areas.area_id', '=', 'products.address->area')->where('areas.name', 'like', '%' . $string . '%');
        //   });


        // $products = $products->where('products.name', 'like', '%' . $string . '%');

        // 9 Строится
        // 1 Вилла

        // Category::whereIn('parent_id', [351,352])->orWhereIn('id', [351,352])->each(function($item) {
        //     $item->update(['seo_text' => null]);
        // });

        
        // foreach($cttg_content_1 as $item) {
        //     Article::where('old_id', $item['id'])->each(function($article) use ($item) {
        //         $article->update(['category_id' => Category::where('language_abbr', $article->language_abbr)->where('old_id', $item['menu_id'])->first()->id]);
        //     });
        // }

        // foreach($cttg_categories as $item) {
        //     if($item['lang_id'] != 1)
        //         continue;

        //     $new = new Article;
        //     $new->language_abbr = 'ru';
        //     $new->old_id = $item['id'];
        //     $new->category_id = 266;
        //     $new->title = $item['title'];
        //     $new->slug = $item['cnc'];
        //     $new->meta_title = $item['pagetitle'];
        //     $new->meta_desc = $item['description'];
        //     $new->content = $item['announce'];
        //     $new->save();

        // }
        

        // foreach($cttg_categories as $item) {
        //     if($item['lang_id'] != 2)
        //         continue;

        //     $new = new Article;
        //     $new->language_abbr = 'uk';
        //     $new->old_id = $item['id'];
        //     $new->category_id = 277;
        //     $new->original_id = Article::where('category_id', 266)->where('old_id', $item['id'])->first()->id;
        //     $new->title = $item['title'];
        //     $new->slug = $item['cnc'];
        //     $new->meta_title = $item['pagetitle'];
        //     $new->meta_desc = $item['description'];
        //     $new->content = $item['announce'];
        //     $new->save();
        // }

        // foreach($cttg_categories as $item) {
        //     if($item['lang_id'] != 1)
        //         continue;

        //     $new = new Category;
        //     $new->language_abbr = 'ru';
        //     $new->old_id = $item['id'];
        //     $new->parent_id = 349;
        //     $new->name = $item['title'];
        //     $new->slug = $item['cnc'];
        //     $new->meta_title = $item['pagetitle'];
        //     $new->meta_desc = $item['description'];
        //     $new->content = $item['announce'];
        //     $new->seo_text = $item['promotion'];
        //     $new->is_active = $item['active'];
        //     $new->save();

        // }
        

        // foreach($cttg_categories as $item) {
        //     if($item['lang_id'] != 2)
        //         continue;

        //     $new = new Category;
        //     $new->language_abbr = 'uk';
        //     $new->old_id = $item['id'];
        //     $new->parent_id = 350;
        //     $new->original_id = Category::where('parent_id', 349)->where('old_id', $item['id'])->first()->id;
        //     $new->name = $item['title'];
        //     $new->slug = $item['cnc'];
        //     $new->meta_title = $item['pagetitle'];
        //     $new->meta_desc = $item['description'];
        //     $new->content = $item['announce'];
        //     $new->seo_text = $item['promotion'];
        //     $new->is_active = $item['active'];
        //     $new->save();
        // }


        // DICTIONARY UK
        // $text = explode('</strong></span></p><hr /><p class="MsoNormal"><span style="font-family: Tahoma;">', $cttg_content_2[0]['text'])[1];
        
        // $items = explode('<strong>', $text);

        // $array = [];
        // // dd($items);
        // foreach($items as $item) {
        //     if(!$item)
        //         continue;

        //     $exploded = explode('</strong>', $item);
        //     $key = strip_tags($exploded[0]);

        //     if(strlen($key) < 3)
        //         continue;

        //     $array[$key] = strip_tags($exploded[1]);
        // }

        // // dd($array);
        
        // foreach($array as $name => $content) {
        //     $new = new Term;
        //     $new->language_abbr = 'uk';
        //     $new->name = $name;
        //     $new->definition = $content;
        //     $new->save();
        // }
        
        // DICTIONARY RU
        // $text = explode('</meta></meta></meta></meta></meta></meta></meta></meta></meta></meta></div><p>', $cttg_content_1[0]['text'])[1];
        
        // $items = explode('<span style="color: rgb(128, 0, 0);"><strong>', $text);
        // // dd($items);

        // $array = [];
        
        // foreach($items as $item) {
        //     if(!$item)
        //         continue;

        //     $exploded = explode('</strong></span>', $item);

        //     $array[$exploded[0]] = strip_tags($exploded[1]);
        // }

        // dd($array);
        
        // foreach($array as $name => $content) {
        //     $new = new Term;
        //     $new->language_abbr = 'ru';
        //     $new->name = $name;
        //     $new->definition = $content;
        //     $new->save();
        // }
    }

    public function tags()
    {
        (new Article)->clearGlobalScopes();
        $existing_ids = Article::where('language_abbr', 'ru')->where('created_at', '>', now()->subDays(2))->pluck('old_id')->toArray();
        dd('stop');
        $items = json_decode(\Storage::disk('common')->get('cttg_content_to_tips (2).json'));
        
        foreach($items[2]->data as $item) {
            if(!in_array($item->content_id, $existing_ids))
                continue;

            Article::where('language_abbr', 'ru')->where('old_id', $item->content_id)->first()->tags()->attach(Tag::where('language_abbr', 'ru')->where('old_id', $item->tips_id)->first()->id);
        }


        dd('done');
      dd($cttg_tips[0]);
      foreach($cttg_tips as $item) {
          if($item['lang_id'] != 1)
            continue;

        $new = new Tag;
        $new->language_abbr = 'ru';
        $new->old_id = $item['id'];
        $new->name = $item['title'];
        $new->save();
      }

      foreach($cttg_tips as $item) {
        if($item['lang_id'] != 1)
          continue;

        $new = new Tag;
        $new->language_abbr = 'uk';
        $new->original_id = Tag::where('language_abbr', 'ru')->where('old_id', $item['id'])->first()->id;
        $new->old_id = $item['id'];
        $new->name = $item['title'];
        $new->save();
      }
    }

    public function brands()
    {
        dd($cttg_db_firms[0]);

        foreach($cttg_db_firms as $item) {
            if($item['lang_id'] != 1) // Сначала русская версия
                continue; 

            if(Brand::where('language_abbr', 'ru')->where('old_id', $item['id'])->first())
                continue;

            $new = new Brand;
            $new->old_id = $item['id'];
            $new->language_abbr = 'ru';
            $new->name = $item['title'];
            $new->slug = $item['slug'];
            $new->images = [
                'logo' => '',
                'image' => $item['photo'],
                'business_card' => ''
            ];
            $new->category_id = BrandCategory::where('language_abbr', 'ru')->where('old_id', $item['specialization'])->first()? BrandCategory::where('language_abbr', 'ru')->where('old_id', $item['specialization'])->first()->id : null;
            $new->contacts = [
                'fb' => '',
                'inst' => '',
                'map' => '',
                'phone' => $item['phone'],
                'site' => $item['site'],
                'email' => $item['mail'],
            ];
            $new->is_popular = $item['hot'];
            $new->is_active = $item['visibility'];
            $new->extras = [
                'videos' => [],
                'activity' => '',
                'seo_desc' => '',
                'meta_desc' => '',
                'seo_title' => '',
                'meta_title' => '',
                'statistics' => '',
                'address_string' => $item['address']
            ];
            $new->address = [
                'region' => $item['region'],
                'area' => $item['area'],
                'city' => $item['city'],
            ];

            $new->save();
        }

        foreach($cttg_db_firms as $item) {
            if($item['lang_id'] != 2) // Теперь украинская версия
                continue; 

            if(Brand::where('language_abbr', 'uk')->where('old_id', $item['id'])->first())
                continue;

            $new = new Brand;
            $new->old_id = $item['id'];
            $new->language_abbr = 'uk';
            $new->original_id = Brand::where('language_abbr', 'ru')->where('old_id', $item['id'])->first()->id;
            $new->name = $item['title'];
            $new->slug = $item['slug'];
            $new->images = [
                'logo' => '',
                'image' => $item['photo'],
                'business_card' => ''
            ];
            $new->category_id = BrandCategory::where('language_abbr', 'uk')->where('old_id', $item['specialization'])->first()? BrandCategory::where('language_abbr', 'ru')->where('old_id', $item['specialization'])->first()->id : null;
            $new->contacts = [
                'fb' => '',
                'inst' => '',
                'map' => '',
                'phone' => $item['phone'],
                'site' => $item['site'],
                'email' => $item['mail'],
            ];
            $new->is_popular = $item['hot'];
            $new->is_active = $item['visibility'];
            $new->extras = [
                'videos' => [],
                'activity' => '',
                'seo_desc' => '',
                'meta_desc' => '',
                'seo_title' => '',
                'meta_title' => '',
                'statistics' => '',
                'address_string' => $item['address']
            ];
            $new->address = [
                'region' => $item['region'],
                'area' => $item['area'],
                'city' => $item['city'],
            ];

            $new->save();
        }
    }

    public function cottages()
    {
        (new Product)->clearGlobalScopes();

        $statuses = [
            0 => 'Заморожено',
            1 => "Строится",
            2 => "Проект",
            3 => "Построено",
            5 => "Строится",
            6 => "Проект",
            7 => "Построено",
            18 => "Покупка",
            17 => "Аренда",
            16 => "Продажа",
            13 => "Строится",
            14 => "Проект",
            15 => "Построено",
        ];
        
        $types = [
            1 => "Таунхаус",
            2 => "Коттедж",
            4 => "Вилла",
            5 => "Дуплекс",
            6 => "Эллинг",
            23 => "Апартаменты",
            88 => "Бунгало",
            89 => "Гостиница",
            93 => "Резиденция",
            87 => "Квартира",
            96 => "Шале",
            95 => "Усадьба",
            94 => "Ресторан",
            41 => "Таунхаус",
            42 => "Коттедж",
            43 => "Апартаменты",
            44 => "Вилла",
            45 => "Дуплекс",
            46 => "Эллинг",
            47 => "Квартира",
            48 => "Земельный участок",
            90 => "Замок",
            51 => "Дом",
            92 => "Офис",
            91 => "Курортный комплекс",
            97 => "Торговая недвижимость",
            61 => "Таунхаус",
            62 => "Коттедж",
            63 => "Апартаменты",
            64 => "Вилла",
            65 => "Дуплекс",
            66 => "Элинг",
            67 => "Квартира",
            68 => "Земельный участок",
            70 => "Земля ОСГ",
            71 => "Дом",
            72 => "Дача",
            76 => "Новостройка в пригороде",
            77 => "Часть дома",
            78 => "Часть дачи",
            79 => "Часть таунхауса",
            98 => "Отель",
            99 => "Таунхаус",
            100 => "Коттедж",
            102 => "Вилла",
            103 => "Дуплекс",
            105 => "Земельный участок",
            107 => "Дом",
            108 => "Дача",
            110 => "Часть дома",
            111 => "Часть дачи",
            112 => "Часть таунхауса",
            113 => "Земельный участок",
            114 => "Инвестиционный проект",
            115 => "Магазин",
            116 => "Квадрекс",
        ];

        $communications = [
            "ru" => [
                1 => "газ",
                2 => "электричество",
                3 => "водоснабжение",
                4 => "отопление",
                5 => "котел",
                6 => "канализация центральная",
                7 => "скважина",
                8 => "централизованные коммуникации",
                10 => "газ",
                11 => "электричество",
                12 => "водоснабжение",
                13 => "отопление",
                14 => "котел",
                15 => "канализация центральная",
                16 => "скважина",
                17 => "централизованные коммуникации",
                20 => "газ",
                21 => "электричество",
                22 => "водоснабжение",
                23 => "отопление",
                24 => "котел",
                25 => "канализация центральная",
                26 => "скважина",
                27 => "централизованные коммуникации",
                29 => "газ",
                30 => "электричество",
                31 => "водоснабжение",
                32 => "отопление",
                33 => "котел",
                34 => "канализация центральная",
                35 => "скважина",
                36 => "централизованные коммуникации",
                37 => "все автономно",
                38 => "колодец",
                39 => "геотермальный насос",
                40 => "септик",
            ],
            "uk" => [
                1 => "газ",
                2 => "електрика",
                3 => "водопостачання",
                4 => "опалення",
                5 => "котел",
                6 => "каналізація центральна",
                7 => "свердловина",
                8 => "централізовані комунікації",
                10 => "газ",
                11 => "електрика",
                12 => "водопостачання",
                13 => "опалення",
                14 => "котел",
                15 => "каналізація центральна",
                16 => "свердловина",
                17 => "централізовані комунікації",
                20 => "газ",
                21 => "електрика",
                22 => "водопостачання",
                23 => "опалення",
                24 => "котел",
                25 => "каналізація центральна",
                26 => "свердловина",
                27 => "централізовані комунікації",
                29 => "газ",
                30 => "електрика",
                31 => "водопостачання",
                32 => "опалення",
                33 => "котел",
                34 => "каналізація центральна",
                35 => "свердловина",
                36 => "централізовані комунікації",
                37 => "усе автономно",
                38 => "колодязь",
                39 => "геотермальний насос",
                40 => "септик",
            ]
        ];

        $cttg_db_cottages = []; // тут массив с товарами

        // dd($cttg_db_cottages[0]);
        dd(Product::where('is_active', 0)->where('language_abbr', 'ru')->count());
        // foreach($cttg_db_cottages as $item) {
        //     if($item['lang_id'] != 1)
        //         continue;

        //     Product::where('language_abbr', 'ru')->where('old_id', $item['id'])->update(['is_active' => $item['approved']]);
        // }
        // foreach($cttg_db_cottages as $item) {
        //     if($item['lang_id'] != 1)
        //         continue;

        //     $product = Product::where('category_id', 1)->where('old_id', $item['id'])->where('language_abbr', 'ru')->first();
        //     $product->address = [
        //         'region' => $item['region'],
        //         'area' => $item['area'],
        //         'city' => $item['city'],
        //         'latlng' => [
        //             'lat' => $item['coordinate']? json_decode($item['coordinate'])->lat : '',
        //             'lng' => $item['coordinate']? json_decode($item['coordinate'])->lng : '',
        //         ]
        //     ];
        //     $product->rating = null;
        //     $product->old_rating = $item['rate'];
        //     $product->old_rating_count = $item['rate_count'];
        //     $product->is_active = $item['approved'];
        //     $product->save();
        // }

        // foreach($cttg_db_cottages as $item) {
        //     if($item['lang_id'] != 2)
        //         continue;

        //     if(!Product::where('old_id', $item['id'])->where('language_abbr', 'uk')->first())
        //         continue;

        //     $product = Product::where('old_id', $item['id'])->where('language_abbr', 'uk')->first();
        //     $product->address = [
        //         'region' => $item['region'],
        //         'area' => $item['area'],
        //         'city' => $item['city'],
        //         'latlng' => [
        //             'lat' => $item['coordinate']? json_decode($item['coordinate'])->lat : '',
        //             'lng' => $item['coordinate']? json_decode($item['coordinate'])->lng : '',
        //         ]
        //     ];
        //     $product->rating = null;
        //     $product->old_rating = $item['rate'];
        //     $product->old_rating_count = $item['rate_count'];
        //     $product->is_active = $item['approved'];
        //     $product->save();
        // }

        // foreach($cttg_db_cottages as $item) {
        //     if($item['lang_id'] != 1) // Сначала русская версия
        //         continue; 

        //     if(Product::where('category_id', 1)->where('old_id', $item['id'])->where('language_abbr', 'ru')->first())
        //         continue;

        //     $product_status = 'done';

        //     if($item['restage'] == 1 || $item['restage'] == 5 || $item['restage'] == 13)
        //         $product_status = 'building';

        //     if($item['restage'] == 2 || $item['restage'] == 6 || $item['restage'] == 14)
        //         $product_status = 'project';

        //     $product_communications = '';

        //     if($item['communication']) {
        //         $first = true;
        //         foreach(explode(',', $item['communication']) as $com) {
        //             if($com != '') {
        //                 if($first)
        //                     $product_communications = $communications['ru'][$com];
        //                 else
        //                     $product_communications .= ', ' . $communications['ru'][$com];

        //                 $first = false;
        //             }
        //         }
        //     }

        //     $new = new Product;
        //     $new->old_id = $item['id'];
        //     $new->language_abbr = 'ru';
        //     $new->name = $item['title'];
        //     $new->slug = $item['slug'];
        //     $new->image = $item['photo'];
        //     $new->category_id = 1;
        //     $new->is_active = $item['approved'];
        //     // $new->brand_id = XXXXXXXXXXXXXX;
        //     $new->rating = $item['rate']; // У нас расчитывается по отзывам, могут быть проблемы
        //     $new->is_hit = $item['hot'];
        //     $new->extras = [
        //         "map" => $item['map'], // тут просто "Область, город, улица", а у нас html-код карты (iframe)
        //         "area" => $item['building_area'],
        //         "status" => $product_status,
        //         "videos" => [],
        //         "distance" => $item['distance'],
        //         "is_frozen" => $item['freeze'],
        //         "roof_material" => $item['roof_material'],
        //         "wall_material" => $item['wall_material'],
        //     ];
        //     $new->extras_translatable = [
        //         "address_string" => $item['address'],
        //         "communications" => $product_communications,
        //         "infrastructure" => $item['infrastructure'],
        //     ];
        //     $new->address = [
        //         'administrative' => isset($this->regions[$item['region']])? $this->regions[$item['region']] : '',
        //         'name' => isset($cities[$item['city']])? $cities[$item['city']] : '',
        //         'county' => isset($areas[$item['area']])? $areas[$item['area']] : '',
        //         'country' => 'Украина',
        //         'value' => isset($cities[$item['city']]) && isset($this->regions[$item['region']])? $cities[$item['city']] . ', ' . $this->regions[$item['region']] . ', Украина' : 'Украина',
        //         'latlng' => [
        //             'lat' => $item['coordinate']? json_decode($item['coordinate'])->lat : '',
        //             'lng' => $item['coordinate']? json_decode($item['coordinate'])->lng : '',
        //         ]
        //     ];
        //     $new->description = $item['additionalinfo'];

        //     $new->save();

        //     $images = [];
        //     if($item['photo1'])
        //         $images[] = $item['photo1'];
        //     if($item['photo2'])
        //         $images[] = $item['photo2'];
        //     if($item['photo3'])
        //         $images[] = $item['photo3'];
        //     if($item['photo4'])
        //         $images[] = $item['photo4'];
        //     if($item['photo5'])
        //         $images[] = $item['photo5'];

        //     $base_mod = new Modification;
        //     $base_mod->product_id = $new->id;
        //     $base_mod->language_abbr = 'ru';
        //     $base_mod->name = 'base';
        //     $base_mod->images = $images;
        //     $base_mod->is_default = 1;
        //     $base_mod->in_stock = 1;
        //     $base_mod->save();

        //     $type = new AttributeModification;
        //     $type->modification_id = $base_mod->id;
        //     $type->attribute_id = 1;
        //     $type->value = $types[$item['realestatetype']];
        //     $type->save();

        //     $floors = new AttributeModification;
        //     $floors->modification_id = $base_mod->id;
        //     $floors->attribute_id = 2;
        //     $floors->value = $item['floor_number'];
        //     $floors->save();

        //     $bedrooms = new AttributeModification;
        //     $bedrooms->modification_id = $base_mod->id;
        //     $bedrooms->attribute_id = 3;
        //     $bedrooms->value = 0;
        //     $bedrooms->save();

        //     $area = new AttributeModification;
        //     $area->modification_id = $base_mod->id;
        //     $area->attribute_id = 4;
        //     $area->value = $item['owner_area_min'];
        //     $area->save();

        //     $status_project = new AttributeModification;
        //     $status_project->modification_id = $base_mod->id;
        //     $status_project->attribute_id = 6;
        //     $status_project->value = $product_status == 'project'? $item['owner_number'] : 0;
        //     $status_project->save();

        //     $status_build = new AttributeModification;
        //     $status_build->modification_id = $base_mod->id;
        //     $status_build->attribute_id = 7;
        //     $status_build->value = $product_status == 'building'? $item['owner_number'] : 0;
        //     $status_build->save();

        //     $status_done = new AttributeModification;
        //     $status_done->modification_id = $base_mod->id;
        //     $status_done->attribute_id = 8;
        //     $status_done->value = $product_status == 'done'? $item['owner_number'] : 0;
        //     $status_done->save();

        //     $status = new AttributeModification;
        //     $status->modification_id = $base_mod->id;
        //     $status->attribute_id = 9;
        //     $status->value = $statuses[$item['restage']];
        //     $status->save();

        //     $mod = new Modification;
        //     $mod->product_id = $new->id;
        //     $mod->language_abbr = 'ru';
        //     $mod->name = $types[$item['realestatetype']];
        //     $mod->price = $item['price_min'];
        //     $mod->old_price = $item['price_max']; // максимальная цена, у нас не используется
        //     $mod->images = $images;
        //     $mod->is_default = 0;
        //     $mod->save();

        //     $type = new AttributeModification;
        //     $type->modification_id = $mod->id;
        //     $type->attribute_id = 1;
        //     $type->value = $types[$item['realestatetype']];
        //     $type->save();

        //     $floors = new AttributeModification;
        //     $floors->modification_id = $mod->id;
        //     $floors->attribute_id = 2;
        //     $floors->value = $item['floor_number'];
        //     $floors->save();

        //     $bedrooms = new AttributeModification;
        //     $bedrooms->modification_id = $mod->id;
        //     $bedrooms->attribute_id = 3;
        //     $bedrooms->value = 0;
        //     $bedrooms->save();

        //     $area = new AttributeModification;
        //     $area->modification_id = $mod->id;
        //     $area->attribute_id = 4;
        //     $area->value = $item['owner_area_min'];
        //     $area->save();

        //     $status_project = new AttributeModification;
        //     $status_project->modification_id = $mod->id;
        //     $status_project->attribute_id = 6;
        //     $status_project->value = $product_status == 'project'? $item['owner_number'] : 0;
        //     $status_project->save();

        //     $status_build = new AttributeModification;
        //     $status_build->modification_id = $mod->id;
        //     $status_build->attribute_id = 7;
        //     $status_build->value = $product_status == 'building'? $item['owner_number'] : 0;
        //     $status_build->save();

        //     $status_done = new AttributeModification;
        //     $status_done->modification_id = $mod->id;
        //     $status_done->attribute_id = 8;
        //     $status_done->value = $product_status == 'done'? $item['owner_number'] : 0;
        //     $status_done->save();

        //     $status = new AttributeModification;
        //     $status->modification_id = $mod->id;
        //     $status->attribute_id = 9;
        //     $status->value = $statuses[$item['restage']];
        //     $status->save();
        // }

        // foreach($cttg_db_cottages as $item) {
        //     if($item['lang_id'] != 2) // Теперь украинская версия
        //         continue; 

        //     if(Product::where('category_id', 6)->where('old_id', $item['id'])->where('language_abbr', 'uk')->first())
        //         continue;

        //     if(!Product::where('category_id', 6)->where('old_id', $item['id'])->first())
        //         continue;

        //     $product_status = 'done';

        //     if($item['restage'] == 1 || $item['restage'] == 5 || $item['restage'] == 13)
        //         $product_status = 'building';

        //     if($item['restage'] == 2 || $item['restage'] == 6 || $item['restage'] == 14)
        //         $product_status = 'project';

        //     $product_communications = '';

        //     if($item['communication']) {
        //         $first = true;
        //         foreach(explode(',', $item['communication']) as $com) {
        //             if($com != '') {
        //                 if($first)
        //                     $product_communications = $communications['uk'][$com];
        //                 else
        //                     $product_communications .= ', ' . $communications['uk'][$com];

        //                 $first = false;
        //             }
        //         }
        //     }

        //     $new = new Product;
        //     $new->original_id = Product::where('category_id', 1)->where('old_id', $item['id'])->first()->id;
        //     $new->old_id = $item['id'];
        //     $new->language_abbr = 'uk';
        //     $new->name = $item['title'];
        //     $new->slug = $item['slug'];
        //     $new->image = $item['photo'];
        //     $new->category_id = 6;
        //     // $new->brand_id = XXXXXXXXXXXXXX;
        //     $new->rating = $item['rate']; // У нас расчитывается по отзывам, могут быть проблемы
        //     $new->is_hit = $item['hot'];
        //     $new->extras = [
        //         "map" => $item['map'], // тут просто "Область, город, улица", а у нас html-код карты (iframe)
        //         "area" => $item['building_area'],
        //         "status" => $product_status,
        //         "videos" => [],
        //         "distance" => $item['distance'],
        //         "is_frozen" => $item['freeze'],
        //         "roof_material" => $item['roof_material'],
        //         "wall_material" => $item['wall_material'],
        //     ];
        //     $new->extras_translatable = [
        //         "address_string" => $item['address'],
        //         "communications" => $product_communications,
        //         "infrastructure" => $item['infrastructure'],
        //     ];
        //     $new->address = [
        //         'administrative' => isset($this->regions[$item['region']])? $this->regions[$item['region']] : '',
        //         'name' => isset($cities[$item['city']])? $cities[$item['city']] : '',
        //         'county' => isset($areas[$item['area']])? $areas[$item['area']] : '',
        //         'country' => 'Украина',
        //         'value' => isset($cities[$item['city']]) && isset($this->regions[$item['region']])? $cities[$item['city']] . ', ' . $this->regions[$item['region']] . ', Украина' : 'Украина',
        //         'latlng' => [
        //             'lat' => $item['coordinate']? json_decode($item['coordinate'])->lat : '',
        //             'lng' => $item['coordinate']? json_decode($item['coordinate'])->lng : '',
        //         ]
        //     ];
        //     $new->description = $item['additionalinfo'];

        //     $new->save();

        //     $images = [];
        //     if($item['photo1'])
        //         $images[] = $item['photo1'];
        //     if($item['photo2'])
        //         $images[] = $item['photo2'];
        //     if($item['photo3'])
        //         $images[] = $item['photo3'];
        //     if($item['photo4'])
        //         $images[] = $item['photo4'];
        //     if($item['photo5'])
        //         $images[] = $item['photo5'];

        //     if(!Product::where('category_id', 1)->where('old_id', $item['id'])->first()->modifications->where('is_default', 1)->first())
        //         dd('Не найдена базовая модификация. old_id ' . $item['id']);

        //     $base_mod = new Modification;
        //     $base_mod->original_id = Product::where('category_id', 1)->where('old_id', $item['id'])->first()->modifications->where('is_default', 1)->first()->id;
        //     $base_mod->product_id = $new->id;
        //     $base_mod->language_abbr = 'uk';
        //     $base_mod->name = 'base';
        //     $base_mod->images = $images;
        //     $base_mod->is_default = 1;
        //     $base_mod->in_stock = 1;
        //     $base_mod->save();

        //     $type = new AttributeModification;
        //     $type->modification_id = $base_mod->id;
        //     $type->attribute_id = 1;
        //     $type->value = $types[$item['realestatetype']];
        //     $type->save();

        //     $floors = new AttributeModification;
        //     $floors->modification_id = $base_mod->id;
        //     $floors->attribute_id = 2;
        //     $floors->value = $item['floor_number'];
        //     $floors->save();

        //     $bedrooms = new AttributeModification;
        //     $bedrooms->modification_id = $base_mod->id;
        //     $bedrooms->attribute_id = 3;
        //     $bedrooms->value = $item['floor_number'];
        //     $bedrooms->save();

        //     $area = new AttributeModification;
        //     $area->modification_id = $base_mod->id;
        //     $area->attribute_id = 4;
        //     $area->value = $item['owner_area_min'];
        //     $area->save();

        //     $status_project = new AttributeModification;
        //     $status_project->modification_id = $base_mod->id;
        //     $status_project->attribute_id = 6;
        //     $status_project->value = $product_status == 'project'? $item['owner_number'] : 0;
        //     $status_project->save();

        //     $status_build = new AttributeModification;
        //     $status_build->modification_id = $base_mod->id;
        //     $status_build->attribute_id = 7;
        //     $status_build->value = $product_status == 'building'? $item['owner_number'] : 0;
        //     $status_build->save();

        //     $status_done = new AttributeModification;
        //     $status_done->modification_id = $base_mod->id;
        //     $status_done->attribute_id = 8;
        //     $status_done->value = $product_status == 'done'? $item['owner_number'] : 0;
        //     $status_done->save();

        //     $status = new AttributeModification;
        //     $status->modification_id = $base_mod->id;
        //     $status->attribute_id = 9;
        //     $status->value = $statuses[$item['restage']];
        //     $status->save();

        //     if(!Product::where('category_id', 1)->where('old_id', $item['id'])->first()->modifications->where('is_default', 0)->first())
        //         dd('Не найдена модификация. old_id ' . $item['id']);

        //     $mod = new Modification;
        //     $mod->original_id = Product::where('category_id', 1)->where('old_id', $item['id'])->first()->modifications->where('is_default', 0)->first()->id;
        //     $mod->product_id = $new->id;
        //     $mod->language_abbr = 'uk';
        //     $mod->name = $types[$item['realestatetype']];
        //     $mod->price = $item['price_min'];
        //     $mod->old_price = $item['price_max']; // максимальная цена, у нас не используется
        //     $mod->images = $images;
        //     $mod->is_default = 0;
        //     $mod->save();

        //     $type = new AttributeModification;
        //     $type->modification_id = $mod->id;
        //     $type->attribute_id = 1;
        //     $type->value = $types[$item['realestatetype']];
        //     $type->save();

        //     $floors = new AttributeModification;
        //     $floors->modification_id = $mod->id;
        //     $floors->attribute_id = 2;
        //     $floors->value = $item['floor_number'];
        //     $floors->save();

        //     $bedrooms = new AttributeModification;
        //     $bedrooms->modification_id = $mod->id;
        //     $bedrooms->attribute_id = 3;
        //     $bedrooms->value = $item['floor_number'];
        //     $bedrooms->save();

        //     $area = new AttributeModification;
        //     $area->modification_id = $mod->id;
        //     $area->attribute_id = 4;
        //     $area->value = $item['owner_area_min'];
        //     $area->save();

        //     $status_project = new AttributeModification;
        //     $status_project->modification_id = $mod->id;
        //     $status_project->attribute_id = 6;
        //     $status_project->value = $product_status == 'project'? $item['owner_number'] : 0;
        //     $status_project->save();

        //     $status_build = new AttributeModification;
        //     $status_build->modification_id = $mod->id;
        //     $status_build->attribute_id = 7;
        //     $status_build->value = $product_status == 'building'? $item['owner_number'] : 0;
        //     $status_build->save();

        //     $status_done = new AttributeModification;
        //     $status_done->modification_id = $mod->id;
        //     $status_done->attribute_id = 8;
        //     $status_done->value = $product_status == 'done'? $item['owner_number'] : 0;
        //     $status_done->save();

        //     $status = new AttributeModification;
        //     $status->modification_id = $mod->id;
        //     $status->attribute_id = 9;
        //     $status->value = $statuses[$item['restage']];
        //     $status->save();
        // }
    }

    public function newbuilds()
    {
        // Modification::whereHas('product', function($q) {
        //     $q->where('category_id', 2);
        // })->each(function($mod) {
        //     $mod->attrs()->syncWithoutDetaching([
        //         6 => ['value' => null],
        //         7 => ['value' => null],
        //         8 => ['value' => null],
        //     ]);
        // });
        // dd(AttributeModification::where('value', null)->count());
        // (new Product)->clearGlobalScopes();
        // Product::where('category_id', 2)->each(function($product) {
        //     $product->modifications->each(function($mod) {
        //         $mod->attrs()->updateExistingPivot(8, ['value' => null]);
        //     });
        // });
        // dd('done');
        $statuses = [
            0 => 'Заморожено',
            1 => "Строится",
            2 => "Проект",
            3 => "Построено",
            5 => "Строится",
            6 => "Проект",
            7 => "Построено",
            18 => "Покупка",
            17 => "Аренда",
            16 => "Продажа",
            13 => "Строится",
            14 => "Проект",
            15 => "Построено",
        ];
        
        $types = [
            1 => "Таунхаус",
            2 => "Коттедж",
            4 => "Вилла",
            5 => "Дуплекс",
            6 => "Эллинг",
            23 => "Апартаменты",
            88 => "Бунгало",
            89 => "Гостиница",
            93 => "Резиденция",
            87 => "Квартира",
            96 => "Шале",
            95 => "Усадьба",
            94 => "Ресторан",
            41 => "Таунхаус",
            42 => "Коттедж",
            43 => "Апартаменты",
            44 => "Вилла",
            45 => "Дуплекс",
            46 => "Эллинг",
            47 => "Квартира",
            48 => "Земельный участок",
            90 => "Замок",
            51 => "Дом",
            92 => "Офис",
            91 => "Курортный комплекс",
            97 => "Торговая недвижимость",
            61 => "Таунхаус",
            62 => "Коттедж",
            63 => "Апартаменты",
            64 => "Вилла",
            65 => "Дуплекс",
            66 => "Элинг",
            67 => "Квартира",
            68 => "Земельный участок",
            70 => "Земля ОСГ",
            71 => "Дом",
            72 => "Дача",
            76 => "Новостройка в пригороде",
            77 => "Часть дома",
            78 => "Часть дачи",
            79 => "Часть таунхауса",
            98 => "Отель",
            99 => "Таунхаус",
            100 => "Коттедж",
            102 => "Вилла",
            103 => "Дуплекс",
            105 => "Земельный участок",
            107 => "Дом",
            108 => "Дача",
            110 => "Часть дома",
            111 => "Часть дачи",
            112 => "Часть таунхауса",
            113 => "Земельный участок",
            114 => "Инвестиционный проект",
            115 => "Магазин",
            116 => "Квадрекс",
        ];

        $communications = [
            "ru" => [
                1 => "газ",
                2 => "электричество",
                3 => "водоснабжение",
                4 => "от��пление",
                5 => "котел",
                6 => "канализация центральная",
                7 => "скважина",
                8 => "централизованные коммуникации",
                10 => "газ",
                11 => "электричество",
                12 => "водоснабжение",
                13 => "отопление",
                14 => "котел",
                15 => "канализация центральная",
                16 => "скважина",
                17 => "централизованные коммуникации",
                20 => "газ",
                21 => "электричество",
                22 => "водоснабжение",
                23 => "отопление",
                24 => "котел",
                25 => "канализация центральная",
                26 => "скважина",
                27 => "централизованные коммуникации",
                29 => "газ",
                30 => "электричество",
                31 => "водоснабжение",
                32 => "отопление",
                33 => "котел",
                34 => "канализация центральная",
                35 => "скважина",
                36 => "централизованные коммуникации",
                37 => "все автономно",
                38 => "к��лодец",
                39 => "геотермальный насос",
                40 => "септик",
            ],
            "uk" => [
                1 => "газ",
                2 => "електрика",
                3 => "водопостачання",
                4 => "опалення",
                5 => "котел",
                6 => "каналізація центральна",
                7 => "свердловина",
                8 => "централізовані комунікації",
                10 => "газ",
                11 => "електрика",
                12 => "водопостачання",
                13 => "опалення",
                14 => "котел",
                15 => "каналізація центральна",
                16 => "свердловина",
                17 => "централізовані комунікації",
                20 => "газ",
                21 => "електрика",
                22 => "водопостачання",
                23 => "опалення",
                24 => "котел",
                25 => "каналізація центральна",
                26 => "свердловина",
                27 => "централізовані комунікації",
                29 => "газ",
                30 => "електрика",
                31 => "водопостача����ня",
                32 => "опалення",
                33 => "котел",
                34 => "каналізація центральна",
                35 => "свердловина",
                36 => "централізовані комунікації",
                37 => "усе автономно",
                38 => "колодязь",
                39 => "геотермальний насос",
                40 => "септик",
            ]
        ];

        // dd('asd');
        // foreach($cttg_db_complex as $item) {
        //     if($item['lang_id'] != 1) // Сначала русская версия
        //         continue; 

        //     if(Product::where('category_id', 2)->where('old_id', $item['id'])->where('language_abbr', 'ru')->first())
        //         continue;

        //     $product_status = 'done';

        //     if($item['restage'] == 1 || $item['restage'] == 5 || $item['restage'] == 13)
        //         $product_status = 'building';

        //     if($item['restage'] == 2 || $item['restage'] == 6 || $item['restage'] == 14)
        //         $product_status = 'project';

        //     $product_communications = '';

        //     if($item['communication']) {
        //         $first = true;
        //         foreach(explode(',', $item['communication']) as $com) {
        //             if($com != '') {
        //                 if($first)
        //                     $product_communications = $communications['ru'][$com];
        //                 else
        //                     $product_communications .= ', ' . $communications['ru'][$com];

        //                 $first = false;
        //             }
        //         }
        //     }


        //     $new = new Product;
        //     $new->old_id = $item['id'];
        //     $new->language_abbr = 'ru';
        //     $new->name = $item['title'];
        //     $new->slug = $item['slug'];
        //     $new->image = $item['photo'];
        //     $new->category_id = 2;
        //     $new->is_active = $item['approved'];
        //     // $new->brand_id = XXXXXXXXXXXXXX;
        //     $new->rating = null;
        //     $new->old_rating = $item['rate'];
        //     $new->old_rating_count = $item['rate_count'];
        //     $new->is_active = $item['approved'];
        //     $new->is_hit = $item['hot'];
        //     $new->extras = [
        //         "map" => $item['map'], // тут просто "Область, город, улица", а у нас html-код карты (iframe)
        //         "area" => $item['building_area'],
        //         "status" => $product_status,
        //         "videos" => [],
        //         "distance" => $item['distance'],
        //         "is_frozen" => $item['freeze'],
        //         "wall_material" => $item['wall_material'],
        //         'newbuild_type' => $types[$item['realestatetype']],
        //         'floors' => $item['floor_number'],
        //     ];
        //     $new->extras_translatable = [
        //         "address_string" => $item['address'],
        //         "communications" => $product_communications,
        //         "infrastructure" => $item['infrastructure'],
        //     ];
        //     $new->address = [
        //         'region' => $item['region'],
        //         'area' => $item['area'],
        //         'city' => $item['city'],
        //         'latlng' => [
        //             'lat' => $item['coordinate']? json_decode($item['coordinate'])->lat : '',
        //             'lng' => $item['coordinate']? json_decode($item['coordinate'])->lng : '',
        //         ]
        //     ];
        //     $new->description = $item['additionalinfo'];

        //     $new->save();

        //     $images = [];
        //     if($item['photo1'])
        //         $images[] = $item['photo1'];
        //     if($item['photo2'])
        //         $images[] = $item['photo2'];
        //     if($item['photo3'])
        //         $images[] = $item['photo3'];
        //     if($item['photo4'])
        //         $images[] = $item['photo4'];
        //     if($item['photo5'])
        //         $images[] = $item['photo5'];

        //     $base_mod = new Modification;
        //     $base_mod->product_id = $new->id;
        //     $base_mod->language_abbr = 'ru';
        //     $base_mod->name = 'base';
        //     $base_mod->images = $images;
        //     $base_mod->is_default = 1;
        //     $base_mod->in_stock = 1;
        //     $base_mod->save();

        //     $area = new AttributeModification;
        //     $area->modification_id = $base_mod->id;
        //     $area->attribute_id = 4;
        //     $area->value = $item['owner_area_min'];
        //     $area->save();

        //     $status_project = new AttributeModification;
        //     $status_project->modification_id = $base_mod->id;
        //     $status_project->attribute_id = 6;
        //     $status_project->value = $product_status == 'project'? $item['owner_number'] : 0;
        //     $status_project->save();

        //     $status_build = new AttributeModification;
        //     $status_build->modification_id = $base_mod->id;
        //     $status_build->attribute_id = 7;
        //     $status_build->value = $product_status == 'building'? $item['owner_number'] : 0;
        //     $status_build->save();

        //     $status_done = new AttributeModification;
        //     $status_done->modification_id = $base_mod->id;
        //     $status_done->attribute_id = 8;
        //     $status_done->value = $product_status == 'done'? $item['owner_number'] : 0;
        //     $status_done->save();

        //     $status = new AttributeModification;
        //     $status->modification_id = $base_mod->id;
        //     $status->attribute_id = 9;
        //     $status->value = $statuses[$item['restage']];
        //     $status->save();

        //     $rooms_attr = new AttributeModification;
        //     $rooms_attr->modification_id = $base_mod->id;
        //     $rooms_attr->attribute_id = 10;
        //     $rooms_attr->value = 1;
        //     $rooms_attr->save();

        //     $rooms = explode(',', str_replace(' ', '', str_replace('.', ',', $item['rooms'])));

        //     foreach($rooms as $key => $room) {

        //         $mod = new Modification;
        //         $mod->product_id = $new->id;
        //         $mod->language_abbr = 'ru';
        //         $mod->name = $room . '-к квартира';
        //         $mod->price = $item['price_min'];
        //         $mod->old_price = $item['price_max']; // максимальная цена, у нас не используется
        //         $mod->images = $images;
        //         $mod->is_default = 0;
        //         $mod->save();

        //         $area = new AttributeModification;
        //         $area->modification_id = $mod->id;
        //         $area->attribute_id = 4;

        //         if($key == 0)
        //             $area->value = $item['owner_area_min'];
        //         elseif($key == count($rooms) - 1)
        //             $area->value = $item['owner_area_max'];
        //         else
        //             $area->value = null;

        //         $area->save();

        //         $status_project = new AttributeModification;
        //         $status_project->modification_id = $mod->id;
        //         $status_project->attribute_id = 6;
        //         $status_project->value = $product_status == 'project'? $item['owner_number'] / count($rooms) : 0;
        //         $status_project->save();

        //         $status_build = new AttributeModification;
        //         $status_build->modification_id = $mod->id;
        //         $status_build->attribute_id = 7;
        //         $status_build->value = $product_status == 'building'? $item['owner_number'] / count($rooms) : 0;
        //         $status_build->save();

        //         $status_done = new AttributeModification;
        //         $status_done->modification_id = $mod->id;
        //         $status_done->attribute_id = 8;
        //         $status_done->value = $product_status == 'done'? $item['owner_number'] / count($rooms) : 0;
        //         $status_done->save();

        //         $status = new AttributeModification;
        //         $status->modification_id = $mod->id;
        //         $status->attribute_id = 9;
        //         $status->value = $statuses[$item['restage']];
        //         $status->save();

        //         $rooms_attr = new AttributeModification;
        //         $rooms_attr->modification_id = $mod->id;
        //         $rooms_attr->attribute_id = 10;
        //         $rooms_attr->value = $room;
        //         $rooms_attr->save();
        //     }
        // }

        // foreach($cttg_db_complex as $item) {
        //     if($item['lang_id'] != 2) // Теперь украинская версия
        //         continue; 

        //     if(Product::withoutGlobalScopes()->where('category_id', 7)->where('old_id', $item['id'])->where('language_abbr', 'uk')->first())
        //         continue;

        //     if(!Product::withoutGlobalScopes()->where('category_id', 2)->where('old_id', $item['id'])->first())
        //         continue;

        //     $product_status = 'done';

        //     if($item['restage'] == 1 || $item['restage'] == 5 || $item['restage'] == 13)
        //         $product_status = 'building';

        //     if($item['restage'] == 2 || $item['restage'] == 6 || $item['restage'] == 14)
        //         $product_status = 'project';

        //     // $product_communications = '';

        //     // if($item['communication']) {
        //     //     $first = true;
        //     //     foreach(explode(',', $item['communication']) as $com) {
        //     //         if($com != '') {
        //     //             if($first)
        //     //                 $product_communications = $communications['uk'][$com];
        //     //             else
        //     //                 $product_communications .= ', ' . $communications['uk'][$com];

        //     //             $first = false;
        //     //         }
        //     //     }
        //     // }

        //     $new = new Product;
        //     $new->original_id = Product::withoutGlobalScopes()->where('category_id', 2)->where('old_id', $item['id'])->first()->id;
        //     $new->old_id = $item['id'];
        //     $new->language_abbr = 'uk';
        //     $new->name = $item['title'];
        //     $new->slug = $item['slug'];
        //     $new->image = $item['photo'];
        //     $new->category_id = 7;
        //     // $new->brand_id = XXXXXXXXXXXXXX;
        //     $new->rating = null;
        //     $new->old_rating = $item['rate'];
        //     $new->old_rating_count = $item['rate_count'];
        //     $new->is_active = $item['approved'];
        //     $new->is_hit = $item['hot'];
        //     $new->extras = Product::withoutGlobalScopes()->where('category_id', 2)->where('old_id', $item['id'])->first()->extras;
        //     $new->extras_translatable = [
        //         "address_string" => $item['address'],
        //         // "communications" => $product_communications,
        //         "infrastructure" => $item['infrastructure'],
        //     ];
        //     $new->address = Product::withoutGlobalScopes()->where('category_id', 2)->where('old_id', $item['id'])->first()->address;
        //     $new->description = $item['additionalinfo'];

        //     $new->save();

        //     $images = [];
        //     if($item['photo1'])
        //         $images[] = $item['photo1'];
        //     if($item['photo2'])
        //         $images[] = $item['photo2'];
        //     if($item['photo3'])
        //         $images[] = $item['photo3'];
        //     if($item['photo4'])
        //         $images[] = $item['photo4'];
        //     if($item['photo5'])
        //         $images[] = $item['photo5'];

        //     if(!Product::withoutGlobalScopes()->where('category_id', 2)->where('old_id', $item['id'])->first()->modifications->where('is_default', 1)->first())
        //         dd('Не найдена базовая модификация. old_id ' . $item['id']);

        //     $base_mod = new Modification;
        //     $base_mod->original_id = Product::withoutGlobalScopes()->where('category_id', 2)->where('old_id', $item['id'])->first()->modifications->where('is_default', 1)->first()->id;
        //     $base_mod->product_id = $new->id;
        //     $base_mod->language_abbr = 'uk';
        //     $base_mod->name = 'base';
        //     $base_mod->images = $images;
        //     $base_mod->is_default = 1;
        //     $base_mod->in_stock = 1;
        //     $base_mod->save();

        //     $area = new AttributeModification;
        //     $area->modification_id = $base_mod->id;
        //     $area->attribute_id = 4;
        //     $area->value = $item['owner_area_min'];
        //     $area->save();

        //     $status_project = new AttributeModification;
        //     $status_project->modification_id = $base_mod->id;
        //     $status_project->attribute_id = 6;
        //     $status_project->value = $product_status == 'project'? $item['owner_number'] : 0;
        //     $status_project->save();

        //     $status_build = new AttributeModification;
        //     $status_build->modification_id = $base_mod->id;
        //     $status_build->attribute_id = 7;
        //     $status_build->value = $product_status == 'building'? $item['owner_number'] : 0;
        //     $status_build->save();

        //     $status_done = new AttributeModification;
        //     $status_done->modification_id = $base_mod->id;
        //     $status_done->attribute_id = 8;
        //     $status_done->value = $product_status == 'done'? $item['owner_number'] : 0;
        //     $status_done->save();

        //     $status = new AttributeModification;
        //     $status->modification_id = $base_mod->id;
        //     $status->attribute_id = 9;
        //     $status->value = $statuses[$item['restage']];
        //     $status->save();

        //     $rooms_attr = new AttributeModification;
        //     $rooms_attr->modification_id = $base_mod->id;
        //     $rooms_attr->attribute_id = 10;
        //     $rooms_attr->value = 1;
        //     $rooms_attr->save();

        //     $rooms = explode(',', str_replace(' ', '', str_replace('.', ',', $item['rooms'])));

        //     foreach($rooms as $key => $room) {

        //         $mod = new Modification;
        //         $mod->product_id = $new->id;
        //         $mod->language_abbr = 'ru';
        //         $mod->name = $room . '-к квартира';
        //         $mod->price = $item['price_min'];
        //         $mod->old_price = $item['price_max']; // максимальная цена, у нас не используется
        //         $mod->images = $images;
        //         $mod->is_default = 0;
        //         $mod->save();

        //         $area = new AttributeModification;
        //         $area->modification_id = $mod->id;
        //         $area->attribute_id = 4;

        //         if($key == 0)
        //             $area->value = $item['owner_area_min'];
        //         elseif($key == count($rooms) - 1)
        //             $area->value = $item['owner_area_max'];
        //         else
        //             $area->value = null;

        //         $area->save();

        //         $status_project = new AttributeModification;
        //         $status_project->modification_id = $mod->id;
        //         $status_project->attribute_id = 6;
        //         $status_project->value = 0;
        //         $status_project->save();

        //         $status_build = new AttributeModification;
        //         $status_build->modification_id = $mod->id;
        //         $status_build->attribute_id = 7;
        //         $status_build->value = 0;
        //         $status_build->save();

        //         $status_done = new AttributeModification;
        //         $status_done->modification_id = $mod->id;
        //         $status_done->attribute_id = 8;
        //         $status_done->value = 0;
        //         $status_done->save();

        //         $status = new AttributeModification;
        //         $status->modification_id = $mod->id;
        //         $status->attribute_id = 9;
        //         $status->value = $statuses[$item['restage']];
        //         $status->save();

        //         $rooms_attr = new AttributeModification;
        //         $rooms_attr->modification_id = $mod->id;
        //         $rooms_attr->attribute_id = 10;
        //         $rooms_attr->value = $room;
        //         $rooms_attr->save();
        //     }
        // }
    }

    public function themes()
    {
        (new Category)->clearGlobalScopes();
        // dd(Article::where('category_id', null)->count());
        // $category_ids = Category::where('language_abbr', 'ru')->pluck('id', 'old_id')->toArray();
        dd('stop');
        $existing_ids = Article::where('language_abbr', 'uk')->where('created_at', '>', now()->subDays(3))->pluck('old_id')->toArray();
        
        $items = json_decode(\Storage::disk('common')->get('cttg_content_bookmarks (1).json'));

        foreach($items[2]->data as $item) {
            
            if(!in_array($item->content_id, $existing_ids))
                continue;
                
            if($item->bookmarks_id != 31)
                continue;

            Article::where('language_abbr', 'uk')->where('old_id', $item->content_id)->update(['category_id' => 125]);
            // dd($item);

            // if(!in_array($item->content_id, $existing_ids))
            //     continue;

            // if(!isset($category_ids[$item->bookmarks_id])) {
            //     continue;
            // }

            // Article::where('language_abbr', 'ru')->where('old_id', $item->content_id)->update(['category_id' => $category_ids[$item->bookmarks_id]]);
        }

        // dd($cttg_bookmarks[0]);

        // foreach($cttg_bookmarks as $item) {
        //     if($item['lang_id'] != 1)
        //         continue;


        //     if(Category::where('language_abbr', 'ru')->where('old_id', $item['id'])->first())
        //         continue;

        //     $new = new Category;
        //     $new->old_id = $item['id'];
        //     $new->language_abbr = 'ru';
        //     $new->name = $item['title'];
        //     $new->slug = $item['alias'];
        //     $new->meta_desc = $item['description'];
        //     $new->seo_text = $item['text'];

        //     // $new->parent_id = ?????;

        //     $new->save();
        // }

        // foreach($cttg_bookmarks as $item) {
        //     if($item['lang_id'] != 2)
        //         continue;

        //     if(Category::where('language_abbr', 'uk')->where('old_id', $item['id'])->first())
        //         continue;

        //     $new = new Category;
        //     $new->old_id = $item['id'];
        //     $new->language_abbr = 'uk';
        //     $new->original_id = Category::where('language_abbr', 'ru')->where('old_id', $item['id'])->first()->id;
        //     $new->name = $item['title'];
        //     $new->slug = $item['alias'];
        //     $new->meta_desc = $item['description'];
        //     $new->seo_text = $item['text'];

        //     // $new->parent_id = ?????;

        //     $new->save();
        // }
    }

    public function news()
    {
        (new Article)->clearGlobalScopes();
        dd('stop');
        // $news = json_decode(\Storage::disk('common')->get('cttg_content_1 (1).json'));
        $news = json_decode(\Storage::disk('common')->get('cttg_content_region (2).json'));
        // dd('stop');
        // $ids = array_map(function($item) {
        //     return $item->id;
        // }, $news[2]->data);
        
        $existing_ids = Article::where('language_abbr', 'ru')->where('created_at', '>', now()->subDays(3))->pluck('old_id', 'old_id');
        
        foreach($news[2]->data as $item) {

            
        if(!isset($existing_ids[$item->content_id]))
            continue;

        Article::where('old_id', $item->content_id)->update(['region' => $item->region_id]);
            
            // if(Article::where('language_abbr', 'uk')->where('old_id', $item->id)->first())
            //     continue;

            
        // if(Article::where('language_abbr', 'ru')->where('old_id', $item->id)->first())
        //     continue;

        // if(!Article::where('language_abbr', 'ru')->where('old_id', $item->id)->first())
        //     continue;

        // if(!isset($existing_ids[$item->id]))
        //     continue;

        // Article::where('old_id', $item->id)->update(['region' => $item->place]);
        

        // Article::where('old_id', $item->id)->update(['image' => $item->imgurl]);
        //   $new = new Article;
        //   $new->old_id = $item->id;
        //   $new->original_id = Article::where('language_abbr', 'ru')->where('old_id', $item->id)->first()->id;
        //   $new->language_abbr = 'uk';
        //   $new->title = $item->title;
        //   $new->slug = $item->cnc;
        //   $new->content = $item->text;
        //   $new->short_desc = $item->announcement;
        //   $new->image = $item->imgurl;
        //   $new->status = $item->active? 'PUBLISHED' : 'DRAFTED';
        //   $new->date = $item->createdate == 0? null : $item->createdate;
        //   $new->meta_title = $item->pagetitle;
        //   $new->meta_desc = $item->description;
        //   $new->save();
        }
    }

    public function reviews()
    {
        $types = [
            "cottages" => "cottage",
            "complex" => "newbuild",
            "content" => "article"
        ];
        $categories = [
            "cottages" => 1,
            "complex" => 2,
        ];
        $reviewable = [
            "cottages" => "Aimix\Shop\app\Models\Product",
            "complex" => "Aimix\Shop\app\Models\Product",
            "content" => "Backpack\NewsCRUD\app\Models\Article"
        ];
        dd('asd');
          
        foreach($cttg_comments as $item){
            if($item['object'] == 'foreignrealty' || $item['object'] == 'content')
                continue;

            // if(!Article::where('language_abbr', 'ru')->where('old_id', $item['object_id'])->first() && !Product::where('language_abbr', 'ru')->where('old_id', $item['object_id'])->first())
            //     continue;

            $type = $types[$item['object']];
            $category_id = $categories[$item['object']];

            if(!Product::where('language_abbr', 'ru')->where('category_id', $category_id)->where('old_id', $item['object_id'])->first())
                continue;

            // if(Review::where('text', $item['text'])->first())
            //     continue;

            $new = new Review;
            $new->type = $types[$item['object']];
            $new->reviewable_type = $reviewable[$item['object']];
            $new->language_abbr = 'ru';
            // if($item['object'] == 'content')
            //     $new->reviewable_id = Article::where('language_abbr', 'ru')->where('old_id', $item['object_id'])->first()->id;
            // else
            $new->reviewable_id = Product::where('language_abbr', 'ru')->where('category_id', $category_id)->where('old_id', $item['object_id'])->first()->id;
            $new->email = $item['email'];
            $new->name = $item['name'];
            $new->text = $item['text'];
            $new->is_moderated = $item['active'];
            $new->save();
            
        }
    }

    public function statistics()
    {
        ini_set("memory_limit", "2000M");
        $items = json_decode(\Storage::disk('common')->get('cttg_statistics.json'));

        dd($items[2]->data[0]);
    }
  }