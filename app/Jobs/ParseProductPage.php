<?php

namespace App\Jobs;

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

class ParseProductPage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 36000;
    private $site;
    private $link;
    private $product = [
        'brand_id' => null,
        'category_id' => 1,
        'extras' => [
            'communications' => [],
            'infrastructure' => '',
            'is_frozen' => 0,
            'distance' => 0,
            'roof_material' => 0,
            'wall_material' => 0,
            'site' => '',
            'area' => 0,
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
        ],
        'images' => [],
        'no_parse' => 0
    ];
    private $product_translation = [
        'extras_translatable' => [
            'address_string' => '',
            'infrastructure' => '',
            'insulation' => '',
            'closed_area' => '',
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
    private $type_statuses = [];
    private $product_status = 'project';
    private $log;
    private $log_details;
    private $modification_links = [];
    private $base_product = null;
    private $promotions = [];
    private $company = null;
    private $existing_company = null;
    private $i = 0;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($site, $path)
    {
        $this->site = $site;
        $this->link = $path;
        $this->product['parsing_link'] = $this->link;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // (new Product)->clearGlobalScopes();

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
                // Для Киева указан только город "Киев" и расстояние до ближайших станций метро, район не указан.
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

        // $this->base_product = Product::withoutGlobalScopes()->where('language_abbr', 'ru')->where(function($query) {
        //     $query->where(function($q) {
        //         $q->where('name', 'like', $this->product['name'])->where('address->city', $this->product['address']['city']);
        //     });

        //     if(isset($this->product['extras']['site']))
        //         $query->orWhere(function($q) {
        //             $q->where('extras->site', 'like', '%' . $this->product['extras']['site'] . '%')->orWhere('extras->contacts', 'like', '%' . $this->product['extras']['site'] . '%');
        //         });
        // });

        // $this->base_product = $this->base_product->first();
        
        // Upload images and save path
        if(!$this->base_product || !count($this->base_product->images))
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
                case 'Размер территории':
                    $this->product['extras']['area'] = explode(' ', $value)[0];
                    break;
                
                case 'Стены':
                    $wall_materials = array_flip(__('attributes.wall_materials'));
                    $this->product['extras']['wall_material'] = isset($wall_materials[$value])? $wall_materials[$value] : null;
                    break;

                case 'Отопление':
                    $this->product['extras']['communications'][] = 'отопление';
                    break;

                case 'Водоснабжение':
                    $this->product['extras']['communications'][] = 'водоснабжение';
                    break;

                case 'Канализация':
                    if($value == 'септик' || $value == 'биосептик')
                        $this->product['extras']['communications'][] = 'септик';
                    elseif($value == 'централизованная')
                        $this->product['extras']['communications'][] = 'канализация центральная';
                    break;

                case 'Утепление':
                    $this->product['extras_translatable']['insulation'] = $value;
                    break;

                case 'Закрытая территория':
                    $this->product['extras_translatable']['closed_area'] = $value;
                    break;

                case 'Размер участка под коттедж':
                    $this->product['extras']['area_cottage'] = $value;
                    break;

                case 'Придомовой участок таунхауса':
                    $this->product['extras']['area_townhouse'] = $value;
                    break;

                case 'Придомовой участок дуплекса':
                    $this->product['extras']['area_duplex'] = $value;
                    break;

                case 'Придомовой участок квадрекса':
                    $this->product['extras']['area_quadrex'] = $value;
                    break;
            }
        });

        $this->product['parsed_desc'] = $crawler->filter('.BuildingDescription-text')->count()? $crawler->filter('.BuildingDescription-text')->html() : null;

        // Modifications
        $crawler->filter('div[data-table="1"] .CottagePrices-title.h5')->each(function (Crawler $node, $i) use ($crawler) {
            $type = $node->text();
            if(!isset($this->modification_links[$type]))
                $this->modification_links[$type] = [];
            
            if($type == 'Участки') {
                $node->nextAll()->first()->filter('div.CottagePrices-item')->each(function (Crawler $node1, $j) use ($crawler, $type) {
                    $this->amount = [
                        'project' => 0,
                        'building' => 0,
                        'done' => 0
                    ];
                    // Put all amounts in 1st modification of this type
                    if($j == 0) {
                        $this->getAmountAndStatus($crawler, $type);
                    }
                    $this->modifications[] = $this->parseModificationWithoutPage($node1, $type);
                });
            } else {
                $modification_links = $node->nextAll()->first()->filter('a.CottagePrices-item')->extract(['href']);
                $this->modification_links[$type] = array_merge($this->modification_links[$type], $modification_links);

                $node->nextAll()->first()->filter('div.CottagePrices-item')->each(function (Crawler $node1, $j) use ($crawler, $type, $modification_links) {
                    $this->amount = [
                        'project' => 0,
                        'building' => 0,
                        'done' => 0
                    ];
                    
                    if($j == 0) {
                        $this->getAmountAndStatus($crawler, $type);
                    }

                    // if there are modifications with pages
                    if(count($modification_links)) {
                        $this->amount = [
                            'project' => 0,
                            'building' => 0,
                            'done' => 0
                        ];
                    }

                    $this->modifications[] = $this->parseModificationWithoutPage($node1, $type);
                });
            }
        });

        foreach($this->modification_links as $type => $links) {
            foreach(array_unique($links) as $key => $item) {
                $this->amount = [
                    'project' => 0,
                    'building' => 0,
                    'done' => 0
                ];
                // Put all amounts in 1st modification of this type
                if($key == 0) {
                    $this->getAmountAndStatus($crawler, $type);
                }
                $delay = random_int(3, 9);
                sleep($delay);
                $this->modifications[] = $this->parseModification($this->site . $item, $type);
            }
        }

        $this->getProductStatus($crawler);
        $this->product['extras']['status'] = $this->product_status;

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

        \Storage::disk('public')->put('parsing_test.json', json_encode($this->modifications));

        if($this->company)
            $this->createOrUpdateCompany();

        if(!$this->base_product)
            $this->createProduct();
        else
            $this->updateProduct();

        $log = ParsingLog::latest()->first();
        $details = $log->details? $log->details : [];

        $log->update(['details' => array_merge($details, $this->log_details)]);
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
        
        if(mb_strpos($name, 'КГ') !== false) {
            // КГ Шевченковский / КГ «Шевченковский» => Шевченковский КГ / «Шевченковский» КГ
            $name_alt = str_replace('КГ ', '', $name);
            $names[] = $name_alt . ' КГ';
            if(mb_strpos($name_alt, '«') === false)
                $names[] = '«' . $name_alt . '»' . ' КГ';
            else
                $names[] = str_replace(['«', '»'], '', $name_alt) . ' КГ';
        } elseif(mb_strpos($name, 'Дуплексы') !== false) {
            // Дуплексы Шевченковский / Дуплексы «Шевченковский» => Шевченковский дуплексы / «Шевченковский» дуплексы
            $name_alt = str_replace('Дуплексы ', '', $name);
            $names[] = $name_alt . ' дуплексы';
            if(mb_strpos($name_alt, '«') === false)
                $names[] = '«' . $name_alt . '»' . ' дуплексы';
            else
                $names[] = str_replace(['«', '»'], '', $name_alt) . ' дуплексы';
        } elseif(mb_strpos($name, 'Таунхаусы') !== false) {
            // Таунхаусы Шевченковский / Таунхаусы «Шевченковский» => Шевченковский таунхаусы / «Шевченковский» таунхаусы
            $name_alt = str_replace('Таунхаусы ', '', $name);
            $names[] = $name_alt . ' таунхаусы';
            if(mb_strpos($name_alt, '«') === false)
                $names[] = '«' . $name_alt . '»' . ' таунхаусы';
            else
                $names[] = str_replace(['«', '»'], '', $name_alt) . ' таунхаусы';
        } elseif(mb_strpos($name, 'Таунхаус ') !== false) {
            // Таунхаус Шевченковский / Таунхаус «Шевченковский» => Шевченковский таунхаусы / «Шевченковский» таунхаусы / Таунхаусы Шевченковский / Таунхаусы «Шевченковский»
            $name_alt = str_replace('Таунхаус ', '', $name);
            $names[] = $name_alt . ' таунхаусы';
            $names[] = 'Таунхаусы ' . $name_alt;
            if(mb_strpos($name_alt, '«') === false) {
                $names[] = '«' . $name_alt . '»' . ' таунхаусы';
                $names[] = 'Таунхаусы ' . '«' . $name_alt . '»';
            }
            else {
                $names[] = str_replace(['«', '»'], '', $name_alt) . ' таунхаусы';
                $names[] = 'Таунхаусы ' . str_replace(['«', '»'], '', $name_alt);
            }
        } elseif(mb_strpos($name, 'Квадрексы') !== false) {
            // Квадрексы Шевченковский / Квадрексы «Шевченковский» => Шевченковский квадрексы / «Шевченковский» квадрексы
            $name_alt = str_replace('Квадрексы ', '', $name);
            $names[] = $name_alt . ' квадрексы';
            if(mb_strpos($name_alt, '«') === false)
                $names[] = '«' . $name_alt . '»' . ' квадрексы';
            else
                $names[] = str_replace(['«', '»'], '', $name_alt) . ' квадрексы';
        } elseif(mb_strpos($name, 'Коттеджный квартал') !== false) {
            // Коттеджный квартал Шевченковский / Коттеджный квартал «Шевченковский» => Шевченковский КГ / Шевченковский коттеджный квартал / «Шевченковский» КГ / «Шевченковский» коттеджный квартал
            $name_alt = str_replace('Коттеджный квартал ', '', $name);
            $names[] = $name_alt . ' КГ';
            $names[] = $name_alt . ' коттеджный квартал';
            if(mb_strpos($name_alt, '«') === false) {
                $names[] = '«' . $name_alt . '»' . ' КГ';
                $names[] = '«' . $name_alt . '»' . ' коттеджный квартал';
            }
            else
                $names[] = str_replace(['«', '»'], '', $name_alt) . ' КГ';
                $names[] = str_replace(['«', '»'], '', $name_alt) . ' коттеджный квартал';
        } else {
            // Шевченковский / «Шевченковский» => «Шевченковский» КГ
            $names[] = mb_strpos($name, '«') !== false? $name . ' КГ' : '«' . $name . '»' . ' КГ';
        }

        foreach($names as $item) {
            // «Шевченковский 2» КГ => «Шевченковский-2» КГ
            $number = preg_replace('/[^0-9]/', '', $item);
            if($number && mb_strpos($item, ' ' . $number) !== false) {
                $names[] = str_replace(' ' . $number, '-' . $number, $item);
            }
        }

        foreach($names as $item) {
            // «Шевченковский» КГ => "Шевченковский" КГ
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
                case 'Утеплення':
                    $this->product_translation['extras_translatable']['insulation'] = $value;
                    break;

                case 'Закрита територія':
                    $this->product_translation['extras_translatable']['closed_area'] = $value;
                    break;
            }
        });

        $this->i = 0;
        // Modifications
        $crawler->filter('div[data-table="1"] .CottagePrices-title.h5')->each(function (Crawler $node, $i) use ($crawler) {
            $type = $node->text();
            
            if($type == 'Участки') {
                $node->nextAll()->first()->filter('div.CottagePrices-item')->each(function (Crawler $node1, $j) use ($crawler, $type) {
                    $this->modifications[$this->i]['translation']['name'] = $node1->filter('.CottagePrices-cell.-name')->text();
                });
            } else {
                $node->nextAll()->first()->filter('div.CottagePrices-item')->each(function (Crawler $node1, $j) use ($crawler, $type) {
                    $this->modifications[$this->i]['translation']['name'] = $node1->filter('.CottagePrices-cell.-name')->text();
                });
            }
            $this->i++;
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
     *  Parse project (modification) page
     *
     * @param  string  $link
     * @param  string  $type
     * @return array
    */
    private function parseModification($link, $type)
    {
        $types = [
            'Коттеджи' => 'Коттедж',
            'Таунхаусы' => 'Таунхаус',
            'Дуплексы' => 'Дуплекс',
            'Виллы' => 'Вилла',
            'Квадрексы' => 'Квадрекс',
            'Эллинги' => 'Эллинг',
        ];
        // Get html remote text.
        $html = file_get_contents($link);

        // Create new instance for parser.
        $crawler = new Crawler(null, $link);
        $crawler->addHtmlContent($html, 'UTF-8');

        $modification = [
            'images' => [],
            'layouts' => [],
        ];

        $modification['name'] = explode(' — ', $crawler->filter('.BuildingHeader h1')->text())[1];

        // if(!$this->base_product || !$this->base_product->modifications->where('name', $modification['name'])->first()) {
            // images
            $modification['images'] = $this->getImages($crawler, '.ProjectGallery img');

            // layouts
            // Get path for images store.
            $savePathArray = \Config::get('upload-image')['image-settings'];
            $savePath = UploadImage::load($savePathArray['editor_folder']);
            $contentType = $savePathArray['editor_folder'];

            $this->layouts = [];

            $crawler->filter('.ProjectLayouts-image')->each(function(Crawler $node, $i) use ($contentType, $savePath, $savePathArray) {
                $title = $node->filter('.ProjectLayouts-floor')->text();
                $file = $node->filter('img')->image()->getUri();
                
                try {
                    $image = UploadImage::upload($file, $contentType)->getImageName();
                } catch (UploadImageException $e) {
                    throw $e->getMessage() . '<br>';
                }

                $storePath = strpos($savePathArray['baseStore'], '/') == 0? substr($savePathArray['baseStore'], 1) : $savePathArray['baseStore'];

                $image_path = $storePath . 's/' . $savePathArray['original'] . $image;
                
                $this->layouts[$i] = [];
                $this->layouts[$i]['name'] = $title;
                $this->layouts[$i]['image'] = $image_path;
            });

            $modification['layouts'] = $this->layouts;
        // } else {
        //     $modification['layouts'] = $this->base_product->modifications->where('name', $modification['name'])->first()->layouts;
        // }

        
        // translation (name and layouts)
        $translation_path = $crawler->filter('[data-switcher="language"]')->attr('href');
        $modification['translation'] = $this->getModificationTranslation($this->site . $translation_path, $modification['layouts']);

        // total price (not per m2)
        $price = $crawler->filter('.ProjectInfo .h2')->text();

        $this->attributes = [];

        $this->attributes['type'] = $types[$type];
        $this->attributes['status'] = $this->type_statuses[$type];
        
        if(mb_strpos($price, 'Цена проекта от') !== false)
            $price = explode('от ', $price)[1];
        elseif(mb_strpos($price, 'Цена проекта — ') !== false)
            $price = explode(' — ', $price)[1];
        else
            $price = 0;
        
        if(!$price) {
            $price = 0;
            $this->attributes['status'] = 'Продано';
        }
        elseif(mb_strpos($price, 'млн') != false)
            $price = (float) explode('млн', $price)[0] * 1000000;
        elseif(mb_strpos($price, 'тыс') != false)
            $price = (float) explode('тыс', $price)[0] * 1000;

        $crawler->filter('.ProjectInfo + .ProjectInfo .ProjectInfo-item')->each(function (Crawler $node, $i) {
            $text = $node->text();
            $placeholder = $node->filter('span:first-child')->text();
            $value = $node->filter('span:last-child')->text();

            switch ($placeholder) {
                case 'Площадь:':
                    $this->attributes['area'] = (int)explode(' ', $value)[0];
                    break;

                case 'Этажность:':
                    $this->attributes['floors'] = $value;
                    break;

                case 'К-во спален:':
                    $this->attributes['bedrooms'] = $value;
                    break;
            }
        });

        $modification['price'] = round($price / $this->attributes['area']);
        $modification['attributes'] = $this->attributes;
        $modification['amount'] = $this->amount;
        
        return $modification;
    }

    /**
     * Parse modification translation page
     *
     * @param  string  $link
     * @param  array  $layouts
     * @return array
    */
    private function getModificationTranslation($link, $layouts)
    {
        // Get html remote text.
        $html = file_get_contents($link);

        // Create new instance for parser.
        $crawler = new Crawler(null, $link);
        $crawler->addHtmlContent($html, 'UTF-8');

        $modification = [
            'layouts' => []
        ];
        $modification['name'] = explode(' — ', $crawler->filter('.BuildingHeader h1')->text())[1];

        $this->layouts = [];

        $crawler->filter('.ProjectLayouts-image')->each(function(Crawler $node, $i) use ($layouts) {
            if(!isset($layouts[$i]))
                return;
            $title = $node->filter('.ProjectLayouts-floor')->text();
            $this->layouts[$i] = [];
            $this->layouts[$i]['name'] = $title;
            $this->layouts[$i]['image'] = $layouts[$i]['image'];
        });

        $modification['layouts'] = $this->layouts;

        return $modification;
    }

    /**
     *  Parse project (modification) page
     *
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @return array
    */
    private function parseModificationWithoutPage($crawler, $type)
    {
        $types = [
            'Коттеджи' => 'Коттедж',
            'Таунхаусы' => 'Таунхаус',
            'Дуплексы' => 'Дуплекс',
            'Виллы' => 'Вилла',
            'Квадрексы' => 'Квадрекс',
            'Эллинги' => 'Эллинг',
            'Участки' => 'Земельный участок',
        ];

        $modification = [
            'images' => [],
            'layouts' => [],
        ];
        $modification['name'] = $crawler->filter('.CottagePrices-cell.-name')->text();

        $modification['amount'] = $this->amount;

        $this->attributes = [];
        
        $this->attributes['type'] = $types[$type];
        $this->attributes['status'] = $this->type_statuses[$type];
        $this->attributes['floors'] = 1;
        $this->attributes['bedrooms'] = 0;
        
        $price = $crawler->filter('.CottagePrices-cell.-price > div:first-child');

        if(count($price)) {
            $price = $price->text();
            if(mb_strpos($price, 'от') !== false)
                $price = explode('от ', $price)[1];
            else
                $price = $price;
        } else {
            $price = 0;
        }

        if(!$price) {
            $price = 0;
            $this->attributes['status'] = 'Продано';
        }
        
        if($price && mb_strpos($price, 'млн') != false)
            $price = (float) explode('млн', $price)[0] * 1000000;
        elseif($price && mb_strpos($price, 'тыс') != false)
            $price = (float) explode('тыс', $price)[0] * 1000;


        $area = count($crawler->filter('.CottagePrices-cell.-area'))? $crawler->filter('.CottagePrices-cell.-area')->text() : 0;
        $this->attributes['area'] = $area? explode(' ', $area)[0] : 0;
        
        $modification['price'] = $this->attributes['area']? round($price / $this->attributes['area']) : 0;
        $modification['attributes'] = $this->attributes;

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
     *  Get amount for 1st modification and status for all modifications of type
     *
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @param  string  $type
    */
    private function getAmountAndStatus($crawler, $type)
    {
        $this->type_statuses[$type] = 'Строится';
        
        $crawler->filter('.CottageConstruction-table tr')->each(function (Crawler $node1, $j) use ($type) {
            $current_type = $node1->filter('td:first-child');
            if(!count($current_type))
                return;

            $project = $current_type->nextAll()->first();
            $building = $project->nextAll()->first();
            $done = $building->nextAll()->first();

            $current_type = $current_type->text();

            if($current_type !== $type)
                return;

            $this->amount = [
                'project' => (int)$project->text(),
                'building' => (int)$building->text(),
                'done' => (int)$done->text()
            ];

            // Status for all modifications of this type 
            $statuses = [
                'project' => 'Проект',
                'building' => 'Строится',
                'done' => 'Построено'
            ];

            $status_key = 'project';

            if($this->amount['building'] || ($this->amount['project'] && $this->amount['done']))
                $status_key = 'building';
            elseif($this->amount['project'])
                $status_key = 'project';
            elseif($this->amount['done'])
                $status_key = 'done';
                
            $this->type_statuses[$type] = $statuses[$status_key];

            // if($type != 'Участки')
            //     $this->product_status = $status_key;
        });
    }

    /**
     *  Get amount for 1st modification and status for all modifications of type
     *
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
    */
    private function getProductStatus($crawler)
    {
        $crawler->filter('.CottageConstruction-table tr')->each(function (Crawler $node1, $j) {
            $current_type = $node1->filter('td:first-child');
            if(!count($current_type))
                return;

            $project = $current_type->nextAll()->first();
            $building = $project->nextAll()->first();
            $done = $building->nextAll()->first();

            $current_type = $current_type->text();

            $amount = [
                'project' => (int)$project->text(),
                'building' => (int)$building->text(),
                'done' => (int)$done->text()
            ];

            // Status for all modifications of this type 
            $statuses = [
                'project' => 'Проект',
                'building' => 'Строится',
                'done' => 'Построено'
            ];

            $status_key = 'project';

            if($amount['building'] || ($amount['project'] && $amount['done']))
                $status_key = 'building';
            elseif($amount['project'])
                $status_key = 'project';
            elseif($amount['done'])
                $status_key = 'done';

            if($current_type != 'Участки' && $this->product_status !== 'building')
                $this->product_status = $status_key;
        });
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
            $this->product['images'] = array_values($this->product['images']);
        }
        $product->save();

        $this->log_details[] = [
            'type' => 'product',
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

        $type = new AttributeModification;
        $type->modification_id = $base_mod->id;
        $type->attribute_id = 1;
        $type->value = '';
        $type->save();

        $floors = new AttributeModification;
        $floors->modification_id = $base_mod->id;
        $floors->attribute_id = 2;
        $floors->value = 1;
        $floors->save();

        $bedrooms = new AttributeModification;
        $bedrooms->modification_id = $base_mod->id;
        $bedrooms->attribute_id = 3;
        $bedrooms->value = 0;
        $bedrooms->save();

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

        // create modifications
        foreach($this->modifications as $item) {
            if($item)
                $this->createNotBaseModification($product, $item);
        }

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

        // translate modification name and layouts
        foreach($this->modifications as $item) {
            if($item && $item['translation']) {
                $modification = $product_uk->modifications->where('name', $item['name'])->first();
                $modification->name = $item['translation']['name'];
                if(count($item['translation']['layouts'])) {
                    $modification->layouts = $item['translation']['layouts'];
                }
                $modification->save();
            }
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

        $product->save();

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
        //\Log::info(print_r($base_product, true));
        if($base_product->no_parse)
        	return;

        if(!$base_product->parsing_link)
            $base_product->parsing_link = $this->product['parsing_link'];

        if(!$base_product->brand_id)
            $base_product->brand_id = $this->product['brand_id'];

        $this->log_details[] = [
            'type' => 'product',
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

        $props = ['insulation', 'closed_area'];

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

            // if(!$base_product->modifications()->select('modifications.*')->where('name', $item['name'])->join('attribute_modification', function($join) {
            //     $join->on('attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 4);
            // })->whereJsonContains('attribute_modification.value', $item['attributes']['area'])->first())
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

        $props = ['insulation', 'closed_area'];

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
        $mod->slug = $this->makeUniqueSlug($item['name']);
        $mod->price = $item['price'];
        $mod->old_price = $item['price']; // max price
        $mod->images = $item['images'];
        $mod->layouts = $item['layouts'];
        $mod->is_default = 0;
        $mod->is_active = 1;
        $mod->save();

        $type = new AttributeModification;
        $type->modification_id = $mod->id;
        $type->attribute_id = 1;
        $type->value = $item['attributes']['type'];
        $type->save();

        $floors = new AttributeModification;
        $floors->modification_id = $mod->id;
        $floors->attribute_id = 2;
        $floors->value = $item['attributes']['floors'];
        $floors->save();

        $bedrooms = new AttributeModification;
        $bedrooms->modification_id = $mod->id;
        $bedrooms->attribute_id = 3;
        $bedrooms->value = $item['attributes']['bedrooms'];
        $bedrooms->save();

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

        $mod->save();
        
        sleep(3);

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
        // $mod_translation->extras = $mod->extras;
        // $mod_translation->is_active = 1;

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
        $mod = $product->modifications()->select('modifications.*')->where('name', $item['name'])->join('attribute_modification', function($join) {
            $join->on('attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 4);
        })->whereJsonContains('attribute_modification.value', $item['attributes']['area'])->first();

        $mod->price = $item['price'];
        $mod->old_price = $item['price']; // max price

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

        $mod->save();

        if(isset($item['translation']) && $mod->translations->first()) {
            $mod_translation = $mod->translations->first();
            $mod_translation->name = $item['translation']['name'];

            if(isset($item['translation']['layouts']))
                $mod_translation->layouts = $item['translation']['layouts'];
                
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

    private function makeUniqueSlug($name)
    {
      $slug = \Str::slug($name) . now()->timestamp;
  
      if(Modification::withoutGlobalScopes()->where('slug', $slug)->first())
        return $this->makeUniqueSlug($name . rand(0, 99));
      else
        return $slug;
    }

    private function get_http_response_code($url) {
        $headers = get_headers($url);
        return substr($headers[0], 9, 3);
    }
}
