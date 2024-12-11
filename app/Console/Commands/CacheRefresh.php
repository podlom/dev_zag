<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Aimix\Shop\app\Models\Category;
use App\Region;
use App\Area;
use App\City;
use App\Kyivdistrict;

class CacheRefresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh Redis cache data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set('memory_limit','-1');
        $statuses = ["done", "building", "project", "sold", "frozen"];
        $cottage_types = array_keys(__('attributes.cottage_types'));
        $newbuild_types = array_keys(__('attributes.newbuild_types'));
        $regions = Region::pluck('region_id');
        $areas = Area::pluck('area_id');
        $cities = City::pluck('city_id');
        $kyivdistricts = Kyivdistrict::pluck('kyivdistrict_id');

        // START COTTAGES
        $categories = Category::withoutGlobalScopes()->whereIn('id', [1, 6])->pluck('slug');

        foreach($categories as $category) {
            $this->sendRequest([
                'category' => $category
            ]);

            // START STATUS
            foreach($statuses as $status) {
                $this->sendRequest([
                    'category' => $category,
                    'status' => $status
                ]);

                foreach($regions as $region) {
                    $this->sendRequest([
                        'category' => $category,
                        'region' => $region,
                        'status' => $status
                    ]);
                }
    
                foreach($areas as $area) {
                    $this->sendRequest([
                        'category' => $category,
                        'area' => $area,
                        'status' => $status
                    ]);
                }
    
                foreach($cities as $city) {
                    $this->sendRequest([
                        'category' => $category,
                        'city' => $city,
                        'status' => $status
                    ]);
                }
    
                foreach($kyivdistricts as $kyivdistrict) {
                    $this->sendRequest([
                        'category' => $category,
                        'kyivdistrict' => $kyivdistrict,
                        'status' => $status
                    ]);
                }
            }
            // END STATUS

            // START TYPE
            foreach($cottage_types as $type) {
                $this->sendRequest([
                    'category' => $category,
                    'type' => $type
                ]);

                foreach($regions as $region) {
                    $this->sendRequest([
                        'category' => $category,
                        'region' => $region,
                        'type' => $type
                    ]);
                }
    
                foreach($areas as $area) {
                    $this->sendRequest([
                        'category' => $category,
                        'area' => $area,
                        'type' => $type
                    ]);
                }
    
                foreach($cities as $city) {
                    $this->sendRequest([
                        'category' => $category,
                        'city' => $city,
                        'type' => $type
                    ]);
                }
    
                foreach($kyivdistricts as $kyivdistrict) {
                    $this->sendRequest([
                        'category' => $category,
                        'kyivdistrict' => $kyivdistrict,
                        'type' => $type
                    ]);
                }
                
                // START TYPE + STATUS
                foreach($statuses as $status) {
                    $this->sendRequest([
                        'category' => $category,
                        'type' => $type,
                        'status' => $status
                    ]);

                    foreach($regions as $region) {
                        $this->sendRequest([
                            'category' => $category,
                            'region' => $region,
                            'type' => $type,
                            'status' => $status
                        ]);
                    }
        
                    foreach($areas as $area) {
                        $this->sendRequest([
                            'category' => $category,
                            'area' => $area,
                            'type' => $type,
                            'status' => $status
                        ]);
                    }
        
                    foreach($cities as $city) {
                        $this->sendRequest([
                            'category' => $category,
                            'city' => $city,
                            'type' => $type,
                            'status' => $status
                        ]);
                    }
        
                    foreach($kyivdistricts as $kyivdistrict) {
                        $this->sendRequest([
                            'category' => $category,
                            'kyivdistrict' => $kyivdistrict,
                            'type' => $type,
                            'status' => $status
                        ]);
                    }
                }
                // END TYPE + STATUS
            }
            // END TYPE

            foreach($regions as $region) {
                $this->sendRequest([
                    'category' => $category,
                    'region' => $region
                ]);
            }

            foreach($areas as $area) {
                $this->sendRequest([
                    'category' => $category,
                    'area' => $area
                ]);
            }

            foreach($cities as $city) {
                $this->sendRequest([
                    'category' => $category,
                    'city' => $city
                ]);
            }

            foreach($kyivdistricts as $kyivdistrict) {
                $this->sendRequest([
                    'category' => $category,
                    'kyivdistrict' => $kyivdistrict
                ]);
            }
        }
        // END COTTAGES

        // START NEWBUILDS
        $categories = Category::withoutGlobalScopes()->whereIn('id', [2, 7])->pluck('slug');

        foreach($categories as $category) {
            $this->sendRequest([
                'category' => $category
            ]);

            // START STATUS
            foreach($statuses as $status) {
                $this->sendRequest([
                    'category' => $category,
                    'status' => $status
                ]);

                foreach($regions as $region) {
                    $this->sendRequest([
                        'category' => $category,
                        'region' => $region,
                        'status' => $status
                    ]);
                }
    
                foreach($areas as $area) {
                    $this->sendRequest([
                        'category' => $category,
                        'area' => $area,
                        'status' => $status
                    ]);
                }
    
                foreach($cities as $city) {
                    $this->sendRequest([
                        'category' => $category,
                        'city' => $city,
                        'status' => $status
                    ]);
                }
    
                foreach($kyivdistricts as $kyivdistrict) {
                    $this->sendRequest([
                        'category' => $category,
                        'kyivdistrict' => $kyivdistrict,
                        'status' => $status
                    ]);
                }
            }
            // END STATUS

            // START TYPE
            foreach($newbuild_types as $type) {
                $this->sendRequest([
                    'category' => $category,
                    'type' => $type
                ]);
                
                // START TYPE + STATUS
                foreach($statuses as $status) {
                    $this->sendRequest([
                        'category' => $category,
                        'type' => $type,
                        'status' => $status
                    ]);

                    foreach($regions as $region) {
                        $this->sendRequest([
                            'category' => $category,
                            'region' => $region,
                            'type' => $type,
                            'status' => $status
                        ]);
                    }
        
                    foreach($areas as $area) {
                        $this->sendRequest([
                            'category' => $category,
                            'area' => $area,
                            'type' => $type,
                            'status' => $status
                        ]);
                    }
        
                    foreach($cities as $city) {
                        $this->sendRequest([
                            'category' => $category,
                            'city' => $city,
                            'type' => $type,
                            'status' => $status
                        ]);
                    }
        
                    foreach($kyivdistricts as $kyivdistrict) {
                        $this->sendRequest([
                            'category' => $category,
                            'kyivdistrict' => $kyivdistrict,
                            'type' => $type,
                            'status' => $status
                        ]);
                    }
                }
                // END TYPE + STATUS
            }
            // END TYPE

            foreach($regions as $region) {
                $this->sendRequest([
                    'category' => $category,
                    'region' => $region
                ]);
            }

            foreach($areas as $area) {
                $this->sendRequest([
                    'category' => $category,
                    'area' => $area
                ]);
            }

            foreach($cities as $city) {
                $this->sendRequest([
                    'category' => $category,
                    'city' => $city
                ]);
            }

            foreach($kyivdistricts as $kyivdistrict) {
                $this->sendRequest([
                    'category' => $category,
                    'kyivdistrict' => $kyivdistrict
                ]);
            }
        }
        // END NEWBUILDS

        // START PRECATALOG RU
        $regions = Region::withoutGlobalScope('language')->where('language_abbr', 'ru')->pluck('slug');
        $areas = Area::withoutGlobalScope('language')->where('language_abbr', 'ru')->pluck('slug');
        $cities = City::withoutGlobalScope('language')->where('language_abbr', 'ru')->pluck('slug');
        $kyivdistricts = Kyivdistrict::withoutGlobalScope('language')->where('language_abbr', 'ru')->pluck('slug');

        $categories = Category::withoutGlobalScopes()->where('language_abbr', 'ru')->pluck('slug');

        foreach($categories as $category_slug) {
            $link = url("/ru/$category_slug");
            $param = '?caching=1';
            file_get_contents($link . $param);
    
            foreach($regions as $region) {
                file_get_contents("$link/region/$region" . $param);
            }
    
            foreach($areas as $area) {
                file_get_contents("$link/area/$area" . $param);
            }
    
            foreach($cities as $city) {
                file_get_contents("$link/city/$city" . $param);
            }
    
            foreach($kyivdistricts as $kyivdistrict) {
                file_get_contents("$link/kyivdistrict/$kyivdistrict" . $param);
            }
        }

        // END PRECATALOG RU

        
        // START PRECATALOG UK
        $regions = Region::withoutGlobalScope('language')->where('language_abbr', 'uk')->pluck('slug');
        $areas = Area::withoutGlobalScope('language')->where('language_abbr', 'uk')->pluck('slug');
        $cities = City::withoutGlobalScope('language')->where('language_abbr', 'uk')->pluck('slug');
        $kyivdistricts = Kyivdistrict::withoutGlobalScope('language')->where('language_abbr', 'uk')->pluck('slug');

        $categories = Category::withoutGlobalScopes()->where('language_abbr', 'uk')->pluck('slug');

        foreach($categories as $category_slug) {
            $link = url("/uk/$category_slug");
            $param = '?caching=1';
            file_get_contents($link . $param);
    
            foreach($regions as $region) {
                file_get_contents("$link/region/$region" . $param);
            }
    
            foreach($areas as $area) {
                file_get_contents("$link/area/$area" . $param);
            }
    
            foreach($cities as $city) {
                file_get_contents("$link/city/$city" . $param);
            }
    
            foreach($kyivdistricts as $kyivdistrict) {
                file_get_contents("$link/kyivdistrict/$kyivdistrict" . $param);
            }
        }

        // END PRECATALOG UK
        
    }

    private function sendRequest(array $params)
    {
        $controller = new \App\Http\Controllers\CatalogController;
        $numbers = [0, 6, 999999];

        foreach($numbers as $number) {
            $request = new Request;
            $request->setMethod('POST');
            $request->request->add([
                'caching' => true,
                'number' => $number
            ]);
            $request->request->add($params);
            $controller->getProducts($request);
        }
    }
}
