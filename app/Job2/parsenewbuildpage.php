<?php

namespace App\Jobs2;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Symfony\Component\DomCrawler\Crawler;
use Aimix\Shop\app\Models\Product;
use Aimix\Shop\app\Models\Modification;
use Aimix\Shop\app\Models\AttributeModification;
use Aimix\Shop\app\Models\Brand;
use Aimix\Promotion\app\Models\Promotion;
use App\Region;
use App\Area;
use App\City;
use App\Kyivdistrict;
use UploadImage;
use App\ParsingLog;
use Image;
use Illuminate\Support\Facades\Storage;

class ParseNewbuildPage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 36000;
    private $site;
    private $link;
    private $product = [
        'brand_id' => null,
        'category_id' => 2,
        'extras' => [
            'communications' => [],
            'infrastructure' => '',
            'is_frozen' => 0,
            'distance' => 0,
            'roof_material' => 0,
            'wall_material' => 0,
            'site' => '',
            'area' => 0,
            'floors' => 0,
            'newbuild_type' => 'Квартира'
        ],
        'address' => [
            'region' => null,
            'area' => null,
            'city' => null
        ],
        'extras_translatable' => [
            'address_string' => '',
            'infrastructure' => '',
            'insulation' => '',
            'closed_area' => '',
            'class' => '',
            'technology' => '',
            'ceilings' => '',
            'condition' => '',
            'parking' => '',
        ],
        'images' => []
    ];
    private $product_translation = [
        'extras_translatable' => [
            'address_string' => '',
            'infrastructure' => '',
            'insulation' => '',
            'closed_area' => '',
            'class' => '',
            'technology' => '',
            'ceilings' => '',
            'condition' => '',
            'parking' => '',
        ]
    ];

    private $modifications = [];

    private $attributes = [];
    private $layouts = [];
    private $amount = [
        'project' => 0,
        'building' => 0,
        'done' => 0
    ];
    private $product_status = 'project';
    private $log;
    private $log_details;
    private $base_product = null;
    private $promotions = [];
    private $company = null;
    private $existing_company = null;
    private $modification = [];
    private $progresses = [];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($site, $path)
    {
        $this->site = $site;
        $this->link = $site . $path;
        $this->product['parsing_link'] = $this->link;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $link = $this->link;
        // Get html remote text.
        $html = file_get_contents($link);

        // TEST
        // $file = json_decode(\Storage::disk('common')->get('parsing_test.json'));
        // $file[] = $link;
        // \Storage::disk('common')->put('parsing_test.json', json_encode(array_values($file), JSON_PRETTY_PRINT));
        // /TEST

        // Create new instance for parser.
        $crawler = new Crawler(null, $link);
        $crawler->addHtmlContent($html, 'UTF-8');

        $this->product['language_abbr'] = 'ru';

        // site & phone
        $crawler->filter('.ContactsActions-row a')->each(function (Crawler $node, $i) {
            $placeholder = $node->filter('.UITwoLinerButton-caption');
            if(!$placeholder->count())
                return;

            $placeholder = $placeholder->text();

            switch ($placeholder) {
                case 'Официальный сайт':
                    $site = explode('&to=', $node->attr('href'))[1];
                    $site = str_replace(['&event_category=view_building_about', '%3A', '%2F'], ['', ':', '/'], $site);
                    $this->product['extras']['site'] = $site;
                    break;

                case 'Позвонить':
                case 'Отдел продаж':
                    // extract phone from href="tel:XXX"
                    $this->product['extras']['phone'] = explode(':', $node->attr('href'))[1];
                    break;
            }
        });

        // region/area/city
        $crawler->filter('.BuildingContacts-breadcrumbs > *')->each(function (Crawler $node, $i) {
            $value = $node->text();

            if(mb_strpos($value, 'область') != false) {
                $region = Region::withoutGlobalScopes()->where('language_abbr', 'ru')->where('name', explode(' ', $value)[0])->first();
                $this->product['address']['region'] = $region? $region->region_id : null;
                return;
            }

            if(mb_strpos($value, 'р-н') != false && !$this->product['address']['area']) {
                // Для Киева указан только город "Киев" и расстояние до ближайших станций метро.
                // if($this->product['address']['region'] == 29)
                //     $this->product['address']['kyivdistrict'] = Kyivdistrict::where('name', explode(' р-н', $value)[0])->first()? Kyivdistrict::where('name', explode(' р-н', $value)[0])->first()->kyivdistrict_id : null;
                // else

                $area = Area::withoutGlobalScopes()->where('language_abbr', 'ru')->where('name', explode(' р-н', $value)[0]);

                if($this->product['address']['region'])
                    $area = $area->where('region_id', $this->product['address']['region']);

                $area = $area->first();

                $this->product['address']['area'] = $area? $area->area_id : null;

                return;
            }

            if(!$this->product['address']['area']) {
                $area = Area::withoutGlobalScopes()->where('language_abbr', 'ru')->where('name', $value);

                if($this->product['address']['region'])
                    $area = $area->where('region_id', $this->product['address']['region']);

                $area = $area->first();

                $this->product['address']['area'] = $area? $area->area_id : null;
            }

            $city = City::withoutGlobalScopes()->select('cities.*')->distinct('cities.id')->where('cities.language_abbr', 'ru')->where(function($query) use ($value) {
                $query->where('cities.name', 'like', str_replace(['с. ', 'пгт ', 'г. '], '', $value))->orWhere('cities.name', 'like', 'cities.name', 'like', str_replace(['-', 'с. ', 'пгт ', 'г. '], [' ', ''], $value))->orWhere('cities.name', 'like', $value . ' город');
            });

            if($this->product['address']['area'])
                $city = $city->where('cities.area_id', $this->product['address']['area']);

            if($this->product['address']['region'])
                $city = $city->join('areas', 'areas.area_id', '=', 'cities.area_id')->where('areas.region_id', $this->product['address']['region']);

            $city = $city->first();

            $this->product['address']['city'] = $city? $city->city_id : null;

            if($this->product['address']['city'] && (!$this->product['address']['area'] || !$this->product['address']['region'])) {
                $this->product['address']['area'] = $city? $city->area_id : null;
                $this->product['address']['region'] = $city && $city->area? $city->area->region_id : null;
            }
        });


        $this->product['name'] = $crawler->filter('.BuildingContacts h1')->text();

        $this->base_product = $this->getBaseProduct();

        // Upload images and save path
        if(!$this->base_product || !$this->base_product->images || !count($this->base_product->images))
            $this->product['images'] = $this->getImages($crawler, '.BuildingGallery-slider img');

        // Video
        // Видео появляется после загрузки страницы, эта строка не работает
        // $this->product['video_iframe'] = $crawler->filter('.BuildingGallery-player')->html();

        // Address
        $address_string = $crawler->filter('.BuildingLocation .UISubtitle-content');
        if($address_string->count())
            $this->product['extras_translatable']['address_string'] = $address_string->text();

        // Extras
        $crawler->filter('.BuildingAttributes-item')->each(function (Crawler $node, $i) {
            $placeholder = $node->filter('.BuildingAttributes-name');
            $value = $node->filter('.BuildingAttributes-value');

            if(!$placeholder->count() || !$value->count())
                return;

            $placeholder = $placeholder->text();
            $value = $value->text();

            switch ($placeholder) {
                case 'Стены':
                    $wall_materials = array_flip(__('attributes.wall_materials'));
                    $value = explode(',', $value)[0];
                    $this->product['extras']['wall_material'] = isset($wall_materials[$value])? $wall_materials[$value] : null;
                    break;

                case 'Отопление':
                    $this->product['extras']['communications'][] = 'отопление';
                    break;

                case 'Этажность':
                    $this->product['extras']['floors'] = $value;
                    break;

                case 'Класс':
                    $this->product['extras_translatable']['class'] = $value;
                    break;

                case 'Технология строительства':
                    $this->product['extras_translatable']['technology'] = $value;
                    break;

                case 'Утепление':
                    $this->product['extras_translatable']['insulation'] = $value;
                    break;

                case 'Высота потолков':
                    $this->product['extras_translatable']['ceilings'] = $value;
                    break;

                case 'Состояние квартиры':
                    $this->product['extras_translatable']['condition'] = $value;
                    break;

                case 'Закрытая территория':
                    $this->product['extras_translatable']['closed_area'] = $value;
                    break;

                case 'Паркинг':
                    $this->product['extras_translatable']['parking'] = $value;
                    break;

                case 'Количество квартир':
                    $this->product['extras']['flats_count'] = $value;
                    break;
            }
        });

        $this->product['parsed_desc'] = $crawler->filter('.BuildingDescription-text')->count()? $crawler->filter('.BuildingDescription-text')->html() : null;

        // type (default = 'Квартира')
        if(count($crawler->filter('.BuildingPrices-header .UIMainTitle .h2')) && strpos($crawler->filter('.BuildingPrices-header .UIMainTitle .h2')->text(), 'апартаментов') !== false)
            $this->product['extras']['newbuild_type'] = 'Апартаменты';

        // Modifications page (product link + '/планировки')
        $modifications_link = $this->link . '/%D0%BF%D0%BB%D0%B0%D0%BD%D0%B8%D1%80%D0%BE%D0%B2%D0%BA%D0%B8';

        if($this->get_http_response_code($modifications_link) == "200"){
            $this->parseModificationsPage($modifications_link);
        }

        // status
        $crawler->filter('.BuildingConstruction-item ')->each(function (Crawler $node, $i) {
            $this->progresses[] = count($node->filter('.BuildingConstruction-progress .-primary'));

            if(count($node->filter('.BuildingConstruction-progress .-error')))
                $this->product['extras']['is_frozen'] = 1;
        });

        if(in_array(2, $this->progresses) || in_array(3, $this->progresses) || (in_array(0, $this->progresses) || in_array(1, $this->progresses) && in_array(4, $this->progresses)))
            $this->product['extras']['status'] = 'building';
        elseif(in_array(0, $this->progresses) || in_array(1, $this->progresses))
            $this->product['extras']['status'] = 'project';
        elseif(in_array(4, $this->progresses))
            $this->product['extras']['status'] = 'done';
        else
            $this->product['extras']['status'] = 'building';

        // Modifications without links
        $crawler->filter('div[data-table="0"] div.BuildingPrices-row')->each(function (Crawler $node, $i) {
            $this->modifications[1000 + $i] = $this->parseModificationWithoutPage($node);
        });

        // promotions
        // $crawler->filter('a.BuildingAction-inner')->each(function (Crawler $node, $i) {
        //     $promotion = [];
        //     $promotion_link = $node->attr('href');

        //     if(strpos($promotion_link, '&to=') === false)
        //         return;

        //     $this->promotions[$i] = $this->parsePromotion($promotion_link, $node);
        // });

        // promotions
        $crawler->filter('.BuildingAction .UIGrid-col-4')->each(function (Crawler $node, $i) {
            $promotion = [];

            $promotion_link = $node->filter('a.UICardLink');

            if(!count($promotion_link))
                return;

            $promotion_link = $promotion_link->attr('href');

            if(strpos($promotion_link, '&to=') === false)
                return;

            $this->promotions[$i] = $this->parsePromotion($promotion_link, $node);
        });


        // translation (should be after promotions!)
        $translation_path = $crawler->filter('[data-switcher="language"]')->attr('href');
        $this->parseProductTranslation($this->site . $translation_path);

        // company
        $company = $crawler->filter('a.BuildingContacts-developer');

        if(count($company)) {
            $company_name = $company->filter('.BuildingContacts-developer-name span')->text();
        } else {
            $company = $crawler->filter('.BuildingContacts-developer-name a');

            if(count($company))
                $company_name = $company->text();
        }

        if(count($company)) {
            $this->company = [
                'name' => $company_name,
                'contacts' => [
                    'fb' => '',
                    'inst' => '',
                    'site' => '',
                    'email' => '',
                    'phone' => ''
                ],
                'images' => [
                    'logo' => '',
                    'image' => '',
                    'business_card' => ''
                ],
                'extras' => [
                    "videos" => [],
                    "activity" => "",
                    "seo_desc" => "",
                    "meta_desc" => "",
                    "seo_title" => "",
                    "meta_title" => ""
                ],
                'extras_translatable' => [
                    "statistics" => [],
                    "address_string" => ""
                ],
                'address' => [
                    'region' => 0,
                    'area' => 0,
                    'city' => 0
                ],
                'translation' => [
                    'extras_translatable' => [
                        'statistics' => [],
                        "address_string" => ""
                    ],
                    'parsed_desc' => null
                ],
                'parsed_desc' => null
            ];

            $this->existing_company = Brand::withoutGlobalScopes()->where('language_abbr', 'ru')->where('name', $this->company['name'])->where('category_id', 139)->first();
            $company_link = $this->site . $company->attr('href');

            if($this->existing_company) {
                $this->product['brand_id'] = $this->existing_company->id;
            }

            $this->parseCompanyPage($company_link);
        }

        $this->log = ParsingLog::latest()->first();
        $this->log_details = $this->log->details? $this->log->details : [];

        // TEST
        // $file = json_decode(\Storage::disk('common')->get('parsing_test.json'));

        // if(in_array($this->link, $file))
        //     unset($file[array_search($this->link, $file)]);

        // \Storage::disk('common')->put('parsing_test.json', json_encode(array_values($this->product), JSON_PRETTY_PRINT));
        // /TEST


        if($this->company)
            $this->createOrUpdateCompany();

        if(!$this->base_product)
            $this->createProduct();
        else
            $this->updateProduct();

        $this->log->update(['details' => $this->log_details]);

        // // TEST
        // $file = json_decode(\Storage::disk('common')->get('parsing_test.json'));

        // // if(in_array($this->link, $file))
        // //     unset($file[array_search($this->link, $file)]);

        // \Storage::disk('common')->put('parsing_test.json', json_encode(array_values($this->modifications), JSON_PRETTY_PRINT));
        // // /TEST

    }

    /**
     * Get product from parsed name
     *
     * @return \Aimix\Shop\app\Models\Product
    */
    private function getBaseProduct()
    {
        $name = $this->product['name'];
        $names = [$name];
        $names[] = 'Новостройка на ' . $name;
        $names[] = 'Новостройка на ' . str_replace('ул. ', '', $name);
        if(mb_strpos($name, 'ЖК') !== false) {
            // ЖК Шевченковский / ЖК «Шевченковский» => Шевченковский ЖК / «Шевченковский» ЖК
            $name_alt = str_replace('ЖК ', '', $name);
            $names[] = $name_alt . ' ЖК';
            if(mb_strpos($name_alt, '«') === false) {
                $names[] = '«' . $name_alt . '»' . ' ЖК';
                $names[] = 'ЖК «' . $name_alt . '»';
            } else {
                $names[] = str_replace(['«', '»'], '', $name_alt) . ' ЖК';
                $names[] = 'ЖК' . str_replace(['«', '»'], '', $name_alt);
            }
        } elseif(mb_strpos($name, 'ЖД') !== false) {
            // ЖД Шевченковский / ЖД «Шевченковский» => Шевченковский ЖК / «Шевченковский» ЖК / ЖК «Шевченковский» / ЖК Шевченковский / «Шевченковский» ЖД / Шевченковский ЖД
            $name_alt = str_replace('ЖД ', '', $name);
            $names[] = $name_alt . ' ЖК'; // Шевченковский ЖК / «Шевченковский» ЖК
            $names[] = 'ЖК ' . $name_alt; // ЖК Шевченковский / ЖК «Шевченковский»
            $names[] = $name_alt . ' ЖД'; // Шевченковский ЖД / «Шевченковский» ЖД
            if(mb_strpos($name_alt, '«') === false) {
                $names[] = '«' . $name_alt . '»' . ' ЖК'; // «Шевченковский» ЖК
                $names[] = 'ЖК «' . $name_alt . '»'; // ЖК «Шевченковский»
                $names[] = '«' . $name_alt . '»' . ' ЖД'; // «Шевченковский» ЖД
            } else {
                $names[] = str_replace(['«', '»'], '', $name_alt) . ' ЖК'; // Шевченковский ЖК
                $names[] = 'ЖК ' . str_replace(['«', '»'], '', $name_alt); // ЖК Шевченковский
                $names[] = str_replace(['«', '»'], '', $name_alt) . ' ЖД'; // Шевченковский ЖД
            }
        } elseif(mb_strpos($name, 'ЗК') !== false) {
            // ЗК Шевченковский / ЗК «Шевченковский» => Шевченковский ЖК / «Шевченковский» ЖК / ЖК «Шевченковский» / ЖК Шевченковский / «Шевченковский» ЗК / Шевченковский ЗК
            $name_alt = str_replace('ЗК ', '', $name);
            $names[] = $name_alt . ' ЖК'; // Шевченковский ЖК / «Шевченковский» ЖК
            $names[] = 'ЖК ' . $name_alt; // ЖК Шевченковский / ЖК «Шевченковский»
            $names[] = '«' . $name_alt . '»' . ' ЗК'; // «Шевченковский» ЗК
            if(mb_strpos($name_alt, '«') === false) {
                $names[] = '«' . $name_alt . '»' . ' ЖК'; // «Шевченковский» ЖК
                $names[] = 'ЖК «' . $name_alt . '»'; // ЖК «Шевченковский»
                $names[] = '«' . $name_alt . '»' . ' ЗК'; // «Шевченковский» ЗК
            } else {
                $names[] = str_replace(['«', '»'], '', $name_alt) . ' ЖК'; // Шевченковский ЖК
                $names[] = 'ЖК ' . str_replace(['«', '»'], '', $name_alt); // ЖК Шевченковский
                $names[] = str_replace(['«', '»'], '', $name_alt) . ' ЗК'; // Шевченковский ЗК
            }
        } elseif(mb_strpos($name, 'МЖК') !== false) {
            // МЖК Шевченковский / МЖК «Шевченковский» => Шевченковский ЖК / «Шевченковский» ЖК / ЖК «Шевченковский» / ЖК Шевченковский / «Шевченковский» МЖК / Шевченковский МЖК
            $name_alt = str_replace('МЖК ', '', $name);
            $names[] = $name_alt . ' ЖК'; // Шевченковский ЖК / «Шевченковский» ЖК
            $names[] = 'ЖК ' . $name_alt; // ЖК Шевченковский / ЖК «Шевченковский»
            $names[] = '«' . $name_alt . '»' . ' МЖК'; // «Шевченковский» МЖК
            if(mb_strpos($name_alt, '«') === false) {
                $names[] = '«' . $name_alt . '»' . ' ЖК'; // «Шевченковский» ЖК
                $names[] = 'ЖК «' . $name_alt . '»'; // ЖК «Шевченковский»
                $names[] = '«' . $name_alt . '»' . ' МЖК'; // «Шевченковский» МЖК
            } else {
                $names[] = str_replace(['«', '»'], '', $name_alt) . ' ЖК'; // Шевченковский ЖК
                $names[] = 'ЖК ' . str_replace(['«', '»'], '', $name_alt); // ЖК Шевченковский
                $names[] = str_replace(['«', '»'], '', $name_alt) . ' МЖК'; // Шевченковский МЖК
            }
        } elseif(mb_strpos($name, 'ЖМ') !== false) {
            // ЖМ Шевченковский / ЖМ «Шевченковский» => Шевченковский ЖК / «Шевченковский» ЖК / ЖК «Шевченковский» / ЖК Шевченковский
            $name_alt = str_replace('ЖМ ', '', $name);
            $names[] = $name_alt . ' ЖК'; // Шевченковский ЖК / «Шевченковский» ЖК
            $names[] = 'ЖК ' . $name_alt; // ЖК Шевченковский / ЖК «Шевченковский»
            $names[] = '«' . $name_alt . '»' . ' ЖМ'; // «Шевченковский» ЖМ
            if(mb_strpos($name_alt, '«') === false) {
                $names[] = '«' . $name_alt . '»' . ' ЖК'; // «Шевченковский» ЖК
                $names[] = 'ЖК «' . $name_alt . '»'; // ЖК «Шевченковский»
                $names[] = '«' . $name_alt . '»' . ' ЖМ'; // «Шевченковский» ЖМ
            } else {
                $names[] = str_replace(['«', '»'], '', $name_alt) . ' ЖК'; // Шевченковский ЖК
                $names[] = 'ЖК ' . str_replace(['«', '»'], '', $name_alt); // ЖК Шевченковский
                $names[] = str_replace(['«', '»'], '', $name_alt) . ' ЖМ'; // Шевченковский ЖМ
            }
        } elseif(mb_strpos($name, 'Апарт-комплекс') !== false) {
            // Апарт-комплекс Шевченковский / Апарт-комплекс «Шевченковский» => Шевченковский ЖК / «Шевченковский» ЖК / Шевченковский апартаменты / Апартаменты Шевченковский / Апартаменты «Шевченковский» / «Шевченковский» апартаменты
            $name_alt = str_replace('Апарт-комплекс ', '', $name);
            $names[] = $name_alt . ' ЖК';
            $names[] = $name_alt . ' апартаменты';
            $names[] = 'Апартаменты ' . $name_alt;
            if(mb_strpos($name_alt, '«') === false) {
                $names[] = '«' . $name_alt . '»' . ' ЖК';
                $names[] = 'Апартаменты «' . $name_alt . '»';
            }
            else {
                $names[] = str_replace(['«', '»'], '', $name_alt) . ' ЖК';
                $names[] = 'Апартаменты ' . str_replace(['«', '»'], '', $name_alt);
            }
        } else {
            // Шевченковский / «Шевченковский» => «Шевченковский» ЖК
            $names[] = mb_strpos($name, '«') !== false? $name . ' ЖК' : '«' . $name . '»' . ' ЖК';
        }

        foreach($names as $item) {
            // «Шевченковский 2» ЖК => «Шевченковский-2» ЖК
            $number = preg_replace('/[^0-9]/', '', $item);
            if($number && mb_strpos($item, ' ' . $number) !== false) {
                $names[] = str_replace(' ' . $number, '-' . $number, $item);
            }
        }

        foreach($names as $item) {
            // «Шевченковский» ЖК => "Шевченковский" ЖК
            if(mb_strpos($item, '«') !== false)
                $names[] = str_replace(['«', '»'], '"', $item);
        }

        return Product::withoutGlobalScopes()->where('language_abbr', 'ru')->where(function($q) use ($names) {
            $q->where(function($q2) use ($names) {
                $q2->where('address->city', $this->product['address']['city'])->whereIn('name', $names);
            })->orWhere('parsing_link', $this->product['parsing_link']);
        })->first();
    }

        /**
     * Get images from page using selector
     *
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @param  string  $selector
     * @return array
    */
    private function getImages($crawler, $selector)
    {
        // Get path for images store.
        $savePathArray = \Config::get('upload-image')['image-settings'];
        $savePath = UploadImage::load($savePathArray['editor_folder']);
        $contentType = $savePathArray['editor_folder'];

        $images = $crawler->filter($selector)->each(function(Crawler $node, $i) use ($contentType, $savePath, $savePathArray) {
            if($i > 2)
                return;

            $file = $node->image()->getUri();

            if($this->get_http_response_code($file) != "200")
                return;

            try {
                $image = UploadImage::upload($file, $contentType)->getImageName();
            } catch (UploadImageException $e) {
                throw $e->getMessage() . '<br>';
            }

            $storePath = strpos($savePathArray['baseStore'], '/') == 0? substr($savePathArray['baseStore'], 1) : $savePathArray['baseStore'];

            $image_path = $storePath . 's/' . $savePathArray['original'] . $image;

            // cut watermark
            $img = Image::make('public/' . $image_path);

            $height = $img->height() - 50;
            $width = $img->width();

            $img->crop($width, $height, 0, 0);
            $img->save('public/' . $image_path);

            return $image_path;
        });

        return array_filter($images, function($item) {
            return $item;
        });
    }

    /**
     *  Parse project (modification) page
     *
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @return array
    */
    private function parseModificationWithoutPage($crawler)
    {
        $this->modification = [
            'name' => '',
            'images' => [],
            'layouts' => [],
            'price' => 0,
            'old_price' => 0,
            'amount' => [
                'project' => 0,
                'building' => 0,
                'done' => 0,
            ]
        ];

        $this->attributes = [];

        $name = $crawler->filter('.BuildingPrices-subrow:first-child .BuildingPrices-main');

        if(count($name)) {
            $name = $name->text();
            $this->modification['name'] = $name;
        } else {
            $name = '';
        }

        switch ($this->product['extras']['status']) {
            case 'project':
                $this->attributes['status'] = 'Проект';
                break;

            case 'done':
                $this->attributes['status'] = 'Построено';
                break;

            default:
                $this->attributes['status'] = 'Строится';
                break;
        }

        // $progress = $crawler->filter('.BuildingConstruction-cell:first-child .BuildingConstruction-progress .-primary');

        // switch (count($progress)) {
        //     case 0:
        //     case 1:
        //         $this->attributes['status'] = 'Проект';
        //         break;

        //     case 4:
        //         $this->attributes['status'] = 'Построено';
        //         break;

        //     default:
        //         $this->attributes['status'] = 'Строится';
        //         break;
        // }

        if(count($crawler->filter('.BuildingPrices-subrow.-sold')))
            $this->attributes['status'] = 'Продано';

        $this->attributes['rooms'] = strpos($name, '-') !== false? explode('-', $name)[0] : 0;
        $this->attributes['area'] = 0;


        $area = $crawler->filter('.BuildingPrices-subrow:first-child .BuildingPrices-additional');

        if(count($area))
            $this->attributes['area'] = +explode('...', explode('-', explode(' ', $area->text())[0])[0])[0];

        $price = $crawler->filter('.BuildingPrices-subrow:nth-child(2) .BuildingPrices-additional');

        if(count($price)) {
            $price = explode(' грн/м²', $price->text())[0];

            if(strpos($price, '—'))
                $price = explode(' —', $price)[0];


            $string = htmlentities($price, null, 'utf-8');
            $price = str_replace("&nbsp;", "", $string);
            $price = html_entity_decode($price);

            $this->modification['price'] = $price;
        }

        // $crawler->filter('.BuildingPrices-cell')->each(function (Crawler $node, $i) {
        //     $text = $node->text();

        //     if(strpos($text, ' м²') !== false)
        //         $this->attributes['area'] = +explode('...', explode('-', explode(' ', $text)[0])[0])[0];

        //     if(strpos($text, 'грн/м²') !== false) {
        //         $price = explode(' грн/м²', $text)[0];

        //         if(strpos($price, '—'))
        //             $price = explode(' —', $price)[0];

        //         $string = htmlentities($price, null, 'utf-8');
        //         $price = str_replace("&nbsp;", "", $string);
        //         $price = html_entity_decode($price);

        //         $this->modification['price'] = $price;
        //     }
        // });

        $this->modification['attributes'] = $this->attributes;

        return $this->modification;
    }

    /**
     *  Parse projects (modifications) page
     *
     * @param  string  $link
    */
    private function parseModificationsPage($link)
    {
        // Get html remote text.
        $html = file_get_contents($link);

        // Create new instance for parser.
        $crawler = new Crawler(null, $link);
        $crawler->addHtmlContent($html, 'UTF-8');

        // modification cards
        $crawler->filter('a.PlansCard')->each(function (Crawler $node, $i) {
            $modification_link = $this->site . $node->attr('href');
            $name = $node->filter('.PlansCard-area .placeholder')->text();
            $this->attributes = [
                'area' => 0,
                'rooms' => 0,
            ];
            $modification = $this->parseModification($modification_link, $name);

            $this->attributes['area'] = 0;
            $area = $node->filter('.PlansCard-area b');

            if(count($area)) {
                $area = explode('-', explode(' м²', $area->text())[0])[0];
                $this->attributes['area'] = +$area;
            }

            if(strpos($name, '-комнатная') !== false)
                $this->attributes['rooms'] = explode('-комнатная', $name)[0];

            $prices = $node->filter('.PlansCard-price [data-currency="uah"]');
            if(count($prices)) {
                $prices = $prices->text();
                $prices = str_replace(' грн/м²', '', $prices);

                $string = htmlentities($prices, null, 'utf-8');
                $prices = str_replace("&nbsp;", "", $string);
                $prices = html_entity_decode($prices);

                if(strpos($prices, ' — ') === false) {
                    $modification['price'] = $prices;
                    $modification['old_price'] = $prices;
                } else {
                    $modification['price'] = explode(' — ', $prices)[0];
                    $modification['old_price'] = explode(' — ', $prices)[1];
                }

            }

            $modification['attributes'] = $this->attributes;
            $this->modifications[$i] = $modification;
        });

        // translation (name and layouts)
        $translation_path = $crawler->filter('[data-switcher="language"]')->attr('href');
        $this->parseModificationsTranslationPage($this->site . $translation_path);
    }

    /**
     * Parse modifications translation page
     *
     * @param  string  $link
     * @return array
    */
    public function parseModificationsTranslationPage($link)
    {
        // Get html remote text.
        $html = file_get_contents($link);

        // Create new instance for parser.
        $crawler = new Crawler(null, $link);
        $crawler->addHtmlContent($html, 'UTF-8');

        // modification cards
        $crawler->filter('a.PlansCard')->each(function (Crawler $node, $i) {
            $name = $area = $node->filter('.PlansCard-area .placeholder')->text();
            $this->modifications[$i]['translation']['name'] = $name;
        });
    }

    /**
     *  Parse project (modification) page
     *
     * @param  string  $link
     * @param  string  $name
     * @return array
    */
    public function parseModification($link, $name)
    {
        // Get html remote text.
        $html = file_get_contents($link);

        // Create new instance for parser.
        $crawler = new Crawler(null, $link);
        $crawler->addHtmlContent($html, 'UTF-8');

        $modification = [
            'name' => $name,
            'layouts' => [],
            'images' => [],
            'amount' => [
                'project' => 0,
                'building' => 0,
                'done' => 0,
            ],
            'price' => 0,
            'old_price' => 0
        ];

        // if(!$this->base_product || !$this->base_product->modifications->where('name', $modification['name'])->first()) {
            // images
            $modification['images'] = $this->getImages($crawler, '.PlanColumn-canvas[data-canvas-for="developer"] img');
        // }

        $progress = $crawler->filter('.BuildingConstruction-progress .-primary');

        switch (count($progress)) {
            case 0:
            case 1:
                $this->attributes['status'] = 'Проект';
                break;

            case 4:
                $this->attributes['status'] = 'Построено';
                break;

            default:
                $this->attributes['status'] = 'Строится';
                break;
        }

        // Get path for images store.
        $savePathArray = \Config::get('upload-image')['image-settings'];
        $savePath = UploadImage::load($savePathArray['editor_folder']);
        $contentType = $savePathArray['editor_folder'];

        $image = $crawler->filter('.PlanView-canvas[data-canvas-for="developer"] img');

        if(count($image)) {
            $image = $image->image()->getUri();
            $modification['images'][] = $savePath . UploadImage::upload($image, $contentType)->getImageName();
        }

        return $modification;
    }

        /**
     *  Parse promotion
     *
     * @param  string  $promotion_link
     * @param  \Symfony\Component\DomCrawler\Crawler  $node
     * @return array
    */
    private function parsePromotion($promotion_link, $node)
    {
        $promotion['link'] = str_replace(['%3A', '%2F'], [':', '/'], explode('&to=', $promotion_link)[1]);
        $promotion['title'] = str_replace(['Акция ・ ', 'Акція ・ '], '', $node->filter('.UICardLink-title')->text());
        $promotion['desc'] = $node->filter('.UICardLink-description')->text();
        $promotion['start'] = null;
        $promotion['end'] = null;
        if(count($node->filter('script[type="application/ld+json"]'))) {
            $schema = json_decode($node->filter('script[type="application/ld+json"]')->text());
            $promotion['start'] = explode('T', $schema->startDate)[0];
            $promotion['end'] = explode('T', $schema->endDate)[0];
        }

        return $promotion;
    }

    /**
     * Parse product translation page
     *
     * @param  string  $link
    */
    private function parseProductTranslation($link)
    {
        // Get html remote text.
        $html = file_get_contents($link);

        // Create new instance for parser.
        $crawler = new Crawler(null, $link);
        $crawler->addHtmlContent($html, 'UTF-8');

        $this->product_translation['name'] = $crawler->filter('.BuildingContacts h1')->text();
        $this->product_translation['parsed_desc'] = $crawler->filter('.BuildingDescription-text')->count()? $crawler->filter('.BuildingDescription-text')->html() : null;

        // Address
        $address_string = $crawler->filter('.BuildingLocation .UISubtitle-content');
        if($address_string->count())
            $this->product_translation['extras_translatable']['address_string'] = $address_string->text();

        // Extras
        $crawler->filter('.BuildingAttributes-item')->each(function (Crawler $node, $i) {
            $placeholder = $node->filter('.BuildingAttributes-name');
            $value = $node->filter('.BuildingAttributes-value');

            if(!$placeholder->count() || !$value->count())
                return;

            $placeholder = $placeholder->text();
            $value = $value->text();

            switch ($placeholder) {
                case 'Клас':
                    $this->product_translation['extras_translatable']['class'] = $value;
                    break;

                case 'Технологія будівництва':
                    $this->product_translation['extras_translatable']['technology'] = $value;
                    break;

                case 'Утеплення':
                    $this->product_translation['extras_translatable']['insulation'] = $value;
                    break;

                case 'Висота стелі':
                    $this->product_translation['extras_translatable']['ceilings'] = $value;
                    break;

                case 'Стан квартири':
                    $this->product_translation['extras_translatable']['condition'] = $value;
                    break;

                case 'Закрита територія':
                    $this->product_translation['extras_translatable']['closed_area'] = $value;
                    break;

                case 'Паркінг':
                    $this->product_translation['extras_translatable']['parking'] = $value;
                    break;
            }
        });

        // Modifications without links
        $crawler->filter('div[data-table="0"] div.BuildingPrices-row')->each(function (Crawler $node, $i) {
            $this->modifications[1000 + $i]['translation']['name'] = $node->filter('.BuildingPrices-subrow:first-child .BuildingPrices-main')->text();
        });

        // promotions
        $crawler->filter('.BuildingAction .UIGrid-col-4')->each(function (Crawler $node, $i) {
            $promotion = [];

            $promotion_link = $node->filter('a.UICardLink');

            if(!count($promotion_link))
                return;

            $promotion_link = $promotion_link->attr('href');

            if(strpos($promotion_link, '&to=') === false)
                return;

            $this->promotions[$i]['translation'] = $this->parsePromotion($promotion_link, $node);
        });
    }

    /**
     * Parse company page
     *
     * @param  string  $link
    */
    private function parseCompanyPage($link)
    {
        // Get html remote text.
        $html = file_get_contents($link);

        // Create new instance for parser.
        $crawler = new Crawler(null, $link);
        $crawler->addHtmlContent($html, 'UTF-8');

        // basic page description
        $desc = $crawler->filter('.seo-information');
        // premium page description
        if(!count($desc))
            $desc = $crawler->filter('.Developer-premium-seo');

        if(count($desc))
            $this->company['parsed_desc'] = $desc->html();

        // basic page statistics
        $crawler->filter('.DeveloperBasic-about > div')->each(function (Crawler $node, $i) {
            $number = $node->filter('b');
            if(!count($number))
                return;

            $number = $number->text();
            $item = [
                'number' => $number,
                'text' => str_replace($number . ' ', '', $node->text())
            ];
            $this->company['extras_translatable']['statistics'][] = $item;
        });

        // premium page statistics
        $crawler->filter('.DeveloperPremium-about > div')->each(function (Crawler $node, $i) {
            $item = [
                'number' => $node->filter('.DeveloperPremium-number')->text(),
                'text' => $node->filter('.DeveloperPremium-label')->text()
            ];
            $this->company['extras_translatable']['statistics'][] = $item;
        });

        $this->company['extras_translatable']['statistics'] = json_encode($this->company['extras_translatable']['statistics']);

        // basic page phone
        $buttons = $crawler->filter('.DeveloperBasic .DeveloperLinks a');
        if(count($buttons)) {
            $buttons->each(function(Crawler $node, $i) {
                $label = $node->filter('.UIButton-caption');
                if(!count($label))
                    return;

                $label = $label->text();
                if($label === 'Номер телефона')
                    $this->company['contacts']['phone'] = str_replace('tel:', '', $node->attr('href'));
            });
        }

        $links = $crawler->filter('.DeveloperLinks a')->extract(['href']);

        foreach($links as $item) {
            if(strpos($item, 'facebook') !== false)
                $this->company['contacts']['fb'] = $item;
            elseif(strpos($item, 'instagram') !== false)
                $this->company['contacts']['inst'] = $item;
            elseif(strpos($item, 'developer_page') !== false)
                $this->company['contacts']['site'] = str_replace(['&event_category=view_developer', '%3A', '%2F'], ['', ':', '/'], explode('&to=', $item)[1]);
        }

        if(!$this->existing_company) {
            // images
            // Get path for images store.
            $savePathArray = \Config::get('upload-image')['image-settings'];
            $savePath = UploadImage::load($savePathArray['editor_folder']);
            $contentType = $savePathArray['editor_folder'];

            // basic page logo
            $logo = $crawler->filter('.DeveloperBasic-logo img');
            // premium page logo
            if(!count($logo))
                $logo = $crawler->filter('.DeveloperPremium-logo img');

            if(count($logo))
                $logo = $logo->image()->getUri();
            else
                $logo = null;

            if($logo && strpos($logo, '.svg') === false)
                $this->company['images']['logo'] = $savePath . UploadImage::upload($logo, $contentType)->getImageName();
            elseif($logo && strpos($logo, '/images/default_company.svg') === false)
                $this->company['images']['logo'] = $this->uploadSvgImage($logo);

            $this->company['images']['business_card'] = $this->company['images']['logo'];

            $image = $crawler->filter('.DeveloperPremium');

            if(count($image)) {
                // get image link from style="background-image:..."
                $image = explode('url(', $image->attr('style'))[1];
                $image = explode(');', $image)[0];
                $this->company['images']['image'] = $savePath . UploadImage::upload($image, $contentType)->getImageName();
            }
        }

        // translation
        $translation_path = $crawler->filter('[data-switcher="language"]')->attr('href');
        $translation_link = $this->site . $translation_path;

        // Get html remote text.
        $html = file_get_contents($translation_link);

        // Create new instance for parser.
        $crawler = new Crawler(null, $link);
        $crawler->addHtmlContent($html, 'UTF-8');

        // description
        $desc = $crawler->filter('.seo-information');
        if(count($desc))
            $this->company['translation']['parsed_desc'] = $desc->html();

        // basic page name
        $name = $crawler->filter('.DeveloperBasic-title .h1');
        // premium page name
        if(!count($name))
            $name = $crawler->filter('.DeveloperPremium-name');

        $this->company['translation']['name'] = $name->text();

        // basic page statistics
        $crawler->filter('.DeveloperBasic-about > div')->each(function (Crawler $node, $i) {
            $number = $node->filter('b');
            if(!count($number))
                return;

            $number = $number->text();
            $item = [
                'number' => $number,
                'text' => str_replace($number . ' ', '', $node->text())
            ];
            $this->company['translation']['extras_translatable']['statistics'][] = $item;
        });

        // premium page statistics
        $crawler->filter('.DeveloperPremium-about > div')->each(function (Crawler $node, $i) {
            $item = [
                'number' => $node->filter('.DeveloperPremium-number')->text(),
                'text' => $node->filter('.DeveloperPremium-label')->text()
            ];
            $this->company['translation']['extras_translatable']['statistics'][] = $item;
        });

        $this->company['translation']['extras_translatable']['statistics'] = json_encode($this->company['translation']['extras_translatable']['statistics']);
    }

        /**
     *  Parse svg image and save it in storage
     *
     * @param  string  $link
     * @return  string
    */
    private function uploadSvgImage($link)
    {
        // Get html remote text.
        $html = file_get_contents($link);

        // Create new instance for parser.
        $crawler = new Crawler(null, $link);
        $crawler->addHtmlContent($html, 'UTF-8');

        $svg_html = $crawler->html();
        $svg_html = str_replace(['<body>', '</body>'], '', $svg_html);
        $svg_name = now()->timestamp . '.svg';
        $path = 'uploads/new_images/' . $svg_name;

        Storage::disk('common')->put($path, $svg_html);

        return $path;
    }

        /**
     *  Create product
     *
    */
    private function createProduct()
    {
        $product = new Product;
        $product->is_parsed = 1;
        $product->parsing_link = $this->product['parsing_link'];
        $product->is_active = 0;
        $product->brand_id = $this->product['brand_id'];
        $product->name = $this->product['name'];
        $product->address = $this->product['address'];
        $product->extras = $this->product['extras'];
        $product->extras_translatable = $this->product['extras_translatable'];
        $product->parsed_desc = $this->product['parsed_desc'];
        $product->category_id = $this->product['category_id'];
        $product->language_abbr = $this->product['language_abbr'];
        if(count($this->product['images'])) {
            $product->image = $this->product['images'][0];
            unset($this->product['images'][0]);
        }
        $product->save();

        $this->log_details[] = [
            'type' => 'newbuild',
            'is_new' => 1,
            'name' => $product->name,
            'link' => $product->link,
            'admin_link' => url('admin/product/' . $product->id . '/edit'),
            'time' => now()->format('d.m.Y H:i:s')
        ];

        $base_mod = new Modification;
        $base_mod->product_id = $product->id;
        $base_mod->language_abbr = 'ru';
        $base_mod->name = 'base';
        $base_mod->slug = 'base-' . now()->timestamp . rand(0, 9);
        $base_mod->images = $this->product['images'];
        $base_mod->is_default = 1;
        $base_mod->in_stock = 1;
        $base_mod->save();

        $area = new AttributeModification;
        $area->modification_id = $base_mod->id;
        $area->attribute_id = 4;
        $area->value = 0;
        $area->save();

        $status_project = new AttributeModification;
        $status_project->modification_id = $base_mod->id;
        $status_project->attribute_id = 6;
        $status_project->value = 0;
        $status_project->save();

        $status_build = new AttributeModification;
        $status_build->modification_id = $base_mod->id;
        $status_build->attribute_id = 7;
        $status_build->value = 0;
        $status_build->save();

        $status_done = new AttributeModification;
        $status_done->modification_id = $base_mod->id;
        $status_done->attribute_id = 8;
        $status_done->value = 0;
        $status_done->save();

        $status = new AttributeModification;
        $status->modification_id = $base_mod->id;
        $status->attribute_id = 9;
        $status->value = 'Строится';
        $status->save();

        $status = new AttributeModification;
        $status->modification_id = $base_mod->id;
        $status->attribute_id = 10;
        $status->value = '';
        $status->save();

        $product->save();

        // translation
        $product_uk = new Product;
        $product_uk->is_parsed = 1;
        $product_uk->is_active = 0;
        $product_uk->name = $this->product_translation['name'];
        $product_uk->address = $this->product['address'];
        $product_uk->extras = $this->product['extras'];
        $product_uk->extras_translatable = $this->product_translation['extras_translatable'];
        $product_uk->parsed_desc = $this->product_translation['parsed_desc'];
        $product_uk->category_id = 6;
        $product_uk->language_abbr = 'uk';
        $product_uk->image = $product->image;
        $product_uk->original_id = $product->id;
        $product_uk->save();

        // create modifications
        foreach($this->modifications as $item) {
            if($item)
                $this->createNotBaseModification($product, $item);
        }

        // $base_mod_translation = new Modification;
        // $base_mod_translation->product_id = $product_uk->id;
        // $base_mod_translation->original_id = $base_mod->id;
        // $base_mod_translation->language_abbr = 'uk';
        // $base_mod_translation->code = $base_mod->code;
        // $base_mod_translation->name = $base_mod->name;
        // $base_mod_translation->slug = 'base_uk-' . now()->timestamp;
        // $base_mod_translation->price = $base_mod->price;
        // $base_mod_translation->old_price = $base_mod->old_price;
        // $base_mod_translation->images = $base_mod->images;
        // $base_mod_translation->is_default = $base_mod->is_default;
        // $base_mod_translation->is_active = $base_mod->is_active;
        // $base_mod_translation->in_stock = $base_mod->in_stock;
        // $base_mod_translation->extras = $base_mod->extras;
        // $base_mod_translation->save();

        // foreach($base_mod->attrs as $attr) {
        //     $newAttr = new AttributeModification;
        //     $newAttr->attribute_id = $attr->id;
        //     $newAttr->modification_id = $base_mod_translation->id;
        //     $newAttr->value = $attr->pivot->value;
        //     $newAttr->save();
        // }

        foreach($this->promotions as $promotion) {
            $this->createPromotion($promotion, $product);
        }
    }

    /**
     *  Update product
     *
    */
    private function updateProduct()
    {
        (new Product)->clearGlobalScopes();

        $base_product = $this->base_product;

        // if no_parse is true return and no product update
        // +kotovv 08.11.2021 swith off
        // if($base_product->no_parse)
        // 	return;
// -kotovv 08.11.2021

        if(!$base_product->parsing_link)
            $base_product->parsing_link = $this->product['parsing_link'];

        $base_product->brand_id = $this->product['brand_id'];

        $this->log_details[] = [
            'type' => 'newbuild',
            'is_new' => 0,
            'name' => $base_product->name,
            'link' => $base_product->link,
            'admin_link' => url('admin/product/' . $base_product->id . '/edit'),
            'time' => now()->format('d.m.Y H:i:s')
        ];

        // Если у объекта нет изображений - добавляем, если есть - ничего не делаем.
        if(!count($base_product->images))
            $base_product->baseModification->update(['images' => $this->product['images']]);

        if(!$base_product->parsed_desc)
            $base_product->parsed_desc = $this->product['parsed_desc'];

        $extras_translatable = $base_product->extras_translatable;
        $new_extras_translatable = $this->product['extras_translatable'];

        if(!isset($extras_translatable['address_string']) && isset($new_extras_translatable['address_string'])) {
            $extras_translatable['address_string'] = $new_extras_translatable['address_string'];
        }

        $props = ['insulation', 'closed_area', 'class', 'technology', 'ceilings', 'condition', 'parking'];

        foreach($props as $prop) {
            if($new_extras_translatable[$prop]) {
                $extras_translatable[$prop] = $new_extras_translatable[$prop];
            }
        }

        $base_product->extras_translatable = $extras_translatable;

        $extras = $base_product->extras;
        $new_extras = $this->product['extras'];

        foreach($new_extras as $key => $item) {
            if($item)
                $extras[$key] = $item;
        }

        $base_product->extras = $extras;

        $address = $base_product->address;

        if(!$address['region'])
            $address['region'] = $this->product['address']['region'];

        if(!$address['area'])
            $address['area'] = $this->product['address']['area'];

        if(!$address['city'])
            $address['city'] = $this->product['address']['city'];

        $base_product->address = $address;

        $base_product->save();

        $base_product->notBaseModifications->each(function($item) {
            $item->delete();
        });

        foreach($this->modifications as $item) {
            if(!$item)
                continue;

            // if(!$base_product->modifications()->where('name', $item['name'])->select('modifications.*')->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 4)->whereJsonContains('attribute_modification.value', $item['attributes']['area'])->first())
                $this->createNotBaseModification($base_product, $item);
            // else
            //     $this->updateNotBaseModification($base_product, $item);
        }

        foreach($this->promotions as $promotion) {
            if(!Promotion::where('product_id', $base_product->id)->where('title', $promotion['title'])->first())
                $this->createPromotion($promotion, $base_product);
            else
                $this->updatePromotion($promotion, $base_product);
        }

        // translation
        $base_product_translation = $base_product->translations->first();

        if(!$base_product_translation)
            return;

        $extras_translatable = $base_product_translation->extras_translatable;
        $new_extras_translatable = $this->product_translation['extras_translatable'];

        if(!isset($extras_translatable['address_string']) && isset($new_extras_translatable['address_string'])) {
            $extras_translatable['address_string'] = $new_extras_translatable['address_string'];
        }

        $props = ['insulation', 'closed_area', 'class', 'technology', 'ceilings', 'condition', 'parking'];

        foreach($props as $prop) {
            if($new_extras_translatable[$prop]) {
                $extras_translatable[$prop] = $new_extras_translatable[$prop];
            }
        }

        $base_product_translation->extras_translatable = $extras_translatable;

        $base_product_translation->save();
    }

    /**
     *  Create new modification
     *
     * @param  \Aimix\Shop\app\Models\Product  $product
     * @param  array  $item
    */
    private function createNotBaseModification($product, $item)
    {
        (new Product)->clearGlobalScopes();

        $mod = new Modification;
        $mod->product_id = $product->id;
        $mod->language_abbr = 'ru';
        $mod->name = $item['name'];
        $mod->trans_name = isset($item['translation'])? $item['translation']['name'] : null;
        $mod->slug = $this->makeUniqueSlug($item['name']);
        $mod->price = $item['price'];
        $mod->old_price = $item['old_price']; // max price
        $mod->images = $item['images'];
        $mod->layouts = $item['layouts'];
        $mod->is_default = 0;
        $mod->is_active = 1;
        $mod->save();

        $area = new AttributeModification;
        $area->modification_id = $mod->id;
        $area->attribute_id = 4;
        $area->value = $item['attributes']['area'];
        $area->save();

        $status_project = new AttributeModification;
        $status_project->modification_id = $mod->id;
        $status_project->attribute_id = 6;
        $status_project->value = $item['amount']['project'];
        $status_project->save();

        $status_build = new AttributeModification;
        $status_build->modification_id = $mod->id;
        $status_build->attribute_id = 7;
        $status_build->value = $item['amount']['building'];
        $status_build->save();

        $status_done = new AttributeModification;
        $status_done->modification_id = $mod->id;
        $status_done->attribute_id = 8;
        $status_done->value = $item['amount']['done'];
        $status_done->save();

        $status = new AttributeModification;
        $status->modification_id = $mod->id;
        $status->attribute_id = 9;
        $status->value = $item['attributes']['status'];
        $status->save();

        $rooms = new AttributeModification;
        $rooms->modification_id = $mod->id;
        $rooms->attribute_id = 10;
        $rooms->value = $item['attributes']['rooms'];
        $rooms->save();

        $mod->save();

        sleep(3);

        // if(isset($item['translation']) && $mod->translations->first()) {
        //     $mod_translation = $mod->translations->first();
        //     $mod_translation->name = $item['translation']['name'];
        //     $mod_translation->save();
        // }

        // translation
        // $mod_translation = new Modification;
        // $mod_translation->product_id = $mod->product->translations->first()->id;
        // $mod_translation->original_id = $mod->id;
        // $mod_translation->language_abbr = 'uk';
        // $mod_translation->code = $mod->code;
        // $mod_translation->name = isset($item['translation'])? $item['translation']['name'] : $item['name'];
        // $mod_translation->price = $mod->price;
        // $mod_translation->old_price = $mod->old_price;
        // $mod_translation->images = $mod->images;
        // $mod_translation->is_default = $mod->is_default;
        // $mod_translation->is_active = $mod->is_active;
        // $mod_translation->in_stock = $mod->in_stock;
        // $mod_translation->is_active = $mod->in_stock;
        // $mod_translation->extras = $mod->extras;

        // $layouts = [];
        // foreach($mod->layouts as $key => $layout) {
        //     $layouts[$key] = [
        //         'name' => isset($item['translation'])? $item['translation']['layouts'][$key]['name'] : $layout['name'],
        //         'image' => $layout['image']
        //     ];
        // }

        // $mod_translation->layouts = $mod->layouts;
        // $mod_translation->save();

        // foreach($mod->attrs as $attr) {
        //     $newAttr = new AttributeModification;
        //     $newAttr->attribute_id = $attr->id;
        //     $newAttr->modification_id = $mod_translation->id;
        //     $newAttr->value = $attr->pivot->value;
        //     $newAttr->save();
        // }
    }

    private function updateNotBaseModification($product, $item)
    {
        $mod = $product->modifications()->where('name', $item['name'])->select('modifications.*')->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 4)->whereJsonContains('attribute_modification.value', $item['attributes']['area'])->first();

        $mod->price = $item['price'];
        $mod->old_price = $item['old_price']; // max price
        $mod->save();

        $status_project = AttributeModification::where('attribute_id', 6)->where('modification_id', $mod->id)->first();
        $status_project->value = $item['amount']['project'];
        $status_project->save();

        $status_build = AttributeModification::where('attribute_id', 7)->where('modification_id', $mod->id)->first();
        $status_build->value = $item['amount']['building'];
        $status_build->save();

        $status_done = AttributeModification::where('attribute_id', 8)->where('modification_id', $mod->id)->first();
        $status_done->value = $item['amount']['done'];
        $status_done->save();

        $status = AttributeModification::where('attribute_id', 9)->where('modification_id', $mod->id)->first();
        $status->value = $item['attributes']['status'];
        $status->save();

        if(isset($item['translation']) && $mod->translations->first()) {
            $mod_translation = $mod->translations->first();
            $mod_translation->name = $item['translation']['name'];
            $mod_translation->save();
        }
    }

    /**
     *  Create new modification
     *
     * @param array $promotion
     * @param  \Aimix\Shop\app\Models\Product  $product
    */
    private function createPromotion($promotion, $product)
    {
        $new = new Promotion;
        $new->is_parsed = 1;
        $new->is_active = 0;
        $new->product_id = $product->id;
        $new->language_abbr = 'ru';
        $new->image = $product->image;
        $new->title = $promotion['title'];
        $new->desc = $promotion['desc'];
        $new->link = $promotion['link'];
        $new->start = $promotion['start'];
        $new->end = $promotion['end'];
        $new->save();

        $this->log_details[] = [
            'type' => 'promotion',
            'is_new' => 1,
            'name' => $new->title,
            'link' => $new->product->link . '/promotions',
            'admin_link' => url('admin/promotion/' . $new->id . '/edit'),
            'time' => now()->format('d.m.Y H:i:s')
        ];

        if(!isset($promotion['translation']))
            return;

        $new_uk = new Promotion;
        $new_uk->is_parsed = 1;
        $new_uk->is_active = 0;
        $new_uk->original_id = $new->id;
        $new_uk->language_abbr = 'uk';
        $new_uk->image = $product->image;
        $new_uk->title = $promotion['translation']['title'];
        $new_uk->desc = $promotion['translation']['desc'];
        $new_uk->link = $promotion['translation']['link'];
        $new_uk->start = $promotion['start'];
        $new_uk->end = $promotion['end'];
        $new_uk->save();
    }

    private function updatePromotion($promotion, $product)
    {
        $old = Promotion::where('product_id', $product->id)->where('title', $promotion['title'])->first();

        $old->start = $promotion['start'];
        $old->end = $promotion['end'];

        $old->save();
    }

    private function createOrUpdateCompany()
    {
        (new Brand)->clearGlobalScopes();

        // update company
        if($this->existing_company) {
            // $contacts = $this->existing_company->contacts;

            // if($this->company['contacts']['fb'])
            //     $contacts['fb'] = $this->company['contacts']['fb'];
            // if($this->company['contacts']['inst'])
            //     $contacts['inst'] = $this->company['contacts']['inst'];
            // if($this->company['contacts']['site'])
            //     $contacts['site'] = $this->company['contacts']['site'];
            // if($this->company['contacts']['email'])
            //     $contacts['email'] = $this->company['contacts']['email'];
            // if($this->company['contacts']['phone'])
            //     $contacts['phone'] = $this->company['contacts']['phone'];

            $update = ['parsed_desc' => $this->company['parsed_desc']];

            if(!$this->existing_company->extras_translatable || !isset($this->existing_company->extras_translatable['address_string']))
                $update['extras_translatable'] = $this->company['extras_translatable'];

            $this->existing_company->update($update);

            if($translation = $this->existing_company->translations->first()) {
                $update = ['parsed_desc' => $this->company['translation']['parsed_desc']];

                if(!$translation->extras_translatable || !isset($translation->extras_translatable['address_string']))
                    $update['extras_translatable'] = $this->company['translation']['extras_translatable'];

                $translation->update($update);
            }

            $this->log_details[] = [
                'type' => 'brand',
                'is_new' => 0,
                'name' => $this->existing_company->name,
                'link' => $this->existing_company->link,
                'admin_link' => url('admin/brand/' . $this->existing_company->id . '/edit'),
                'time' => now()->format('d.m.Y H:i:s')
            ];

            return;
        }

        // create company
        $company = new Brand;
        $company->is_parsed = 1;
        $company->is_active = 0;
        $company->language_abbr = 'ru';
        $company->name = $this->company['name'];
        $company->category_id = 139;
        $company->extras_translatable = $this->company['extras_translatable'];
        $company->extras = $this->company['extras'];
        $company->contacts = $this->company['contacts'];
        $company->images = $this->company['images'];
        $company->address = $this->company['address'];
        $company->parsed_desc = $this->company['parsed_desc'];
        $company->save();

        // translation
        $company_uk = new Brand;
        $company_uk->is_parsed = 1;
        $company_uk->is_active = 0;
        $company_uk->language_abbr = 'uk';
        $company_uk->original_id = $company->id;
        $company_uk->name = $this->company['translation']['name'];
        $company_uk->category_id = 140;
        $company_uk->extras_translatable = $this->company['translation']['extras_translatable'];
        $company_uk->extras = $this->company['extras'];
        $company_uk->contacts = $this->company['contacts'];
        $company_uk->images = $this->company['images'];
        $company_uk->address = $this->company['address'];
        $company_uk->parsed_desc = $this->company['translation']['parsed_desc'];
        $company_uk->save();

        $this->log_details[] = [
            'type' => 'brand',
            'is_new' => 1,
            'name' => $company->name,
            'link' => $company->link,
            'admin_link' => url('admin/brand/' . $company->id . '/edit'),
            'time' => now()->format('d.m.Y H:i:s')
        ];

        $this->product['brand_id'] = $company->id;
    }

    private function get_http_response_code($url) {
        $headers = get_headers($url);
        return substr($headers[0], 9, 3);
    }

    private function makeUniqueSlug($name)
    {
        $slug = \Str::slug($name) . now()->timestamp;

      if(Modification::withoutGlobalScopes()->where('slug', $slug)->first())
        return $this->makeUniqueSlug($name . rand(0, 99));
      else
        return $slug;
    }
}
