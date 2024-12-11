@extends('layouts.app', [
  'meta_title' => str_replace(' - Zagorodna.com', '', $meta_title),
  'meta_desc' => $meta_desc,
])

@php
    $kyivdistrict_id = $kyivdistrict? $kyivdistrict->kyivdistrict_id : null;
    $city_id = $city? $city->city_id : null;
    $area_id = $area? $area->area_id : ($city? $city->area_id : null);
    $region_id = $region? $region->region_id : ($area? $area->region_id : ($city? $city->area->region_id : ($kyivdistrict? 29 : null)));
@endphp

@section('content')
<main>
    <div class="decor-background decor-background--pre-catalog" style="background-image:url({{ url('img/background-img-1.png') }})"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
            {{ Breadcrumbs::render('map',
            $category->slug,
            str_replace(' - Zagorodna.com', '', $h1)) }}
        </div>
    </section>
    <!-- MAP -->
    <section class="info-block info-block_map container">
        <div class="general-heading">
            <h1 class="main-caption-l main-caption-l--transform">{{ $h1 }}</h1>
        </div>
        <div id="general__map" class="ts-map-ln-28 ts-catalog-map-blade" style="height:650px"></div>
    </section>

    <!-- START INTERLINKING -->
    <section class="category-links">
        <div class="category-links__wrapper container">
            <ul class="category-links__list">
                <!-- START KYIVDISTRICTS / CITIES / AREAS / REGIONS -->
                <li class="category-links__item js-drop-item js-catagory-links-item">
                    <button class="category-links__mobile-button js-drop-button">
                    @if($city)
                        <span>{{ __('main.type_' . $type . '_plural') }} {{ __('main.по городам') }}</span>
                    @elseif($kyivdistrict || $area)
                        <span>{{ __('main.type_' . $type . '_plural') }} {{ __('main.по районам') }}</span>
                    @else
                        <span>{{ __('main.type_' . $type . '_plural') }} {{ __('main.по регионам') }}</span>
                    @endif
                        <span class="icon-drop"></span>
                    </button>
                    @if($city)
                        <h5 class="category-links__caption">{{ __('main.type_' . $type . '_plural') }} {{ __('main.по городам') }}</h5>
                    @elseif($kyivdistrict || $area || ($region && $region->region_id == 29))
                        <h5 class="category-links__caption">{{ __('main.type_' . $type . '_plural') }} {{ __('main.по районам') }}</h5>
                    @else
                        <h5 class="category-links__caption">{{ __('main.type_' . $type . '_plural') }} {{ __('main.по регионам') }}</h5>
                    @endif
                    <ul class="category-links-sub__list">
                    <!-- START KYIVDISTRICTS -->
                    @if($kyivdistrict || ($region && $region->region_id == 29))
                    @foreach($kyivdistricts as $item)
                    @php
                        $cache_key = 'count_kyivdistrict:' . $item->kyivdistrict_id . '.category:' . $category->id;

                        if($objectType)
                            $cache_key .= '.type:' . $objectType;

                        if($status)
                            $cache_key .= '.status:' . $status;

                        if(!($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
                            $prodCount = \Aimix\Shop\app\Models\Product::active()->where('category_id', $category->id)->where('address->kyivdistrict', $item->kyivdistrict_id);

                            if($objectType) {
                                $prodCount = $type == 'cottage'? $prodCount->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', str_replace('_', ' ', $objectType))->select('products.*') : $prodCount->whereJsonContains('extras->newbuild_type', $objectType);
                            }

                            if($status) {
                                switch ($status) {
                                        case 'frozen':
                                            $prodCount = $prodCount->where('products.extras->is_frozen', 1);
                                            break;

                                        case 'sold':
                                            $prodCount = $prodCount->where('products.is_sold', 1);
                                            break;

                                        default:
                                            $prodCount = $prodCount->where('products.extras->status', $status);
                                            break;
                                    }
                            }

                            $prodCount = $prodCount->count();
                            \Illuminate\Support\Facades\Redis::set($cache_key, $prodCount, 'EX', 108000);
                        }

                        @endphp
                        @if((!$kyivdistrict || $kyivdistrict->kyivdistrict_id != $item->kyivdistrict_id) && $prodCount)
                        @php
                            $link = route($lang . '_map', $category->slug) . '/kyivdistrict/' . $item->slug;
                            $link = $status? $link . '/' . \Str::slug($status) : $link;
                            $link = $objectType? $link . '/' . \Str::slug($objectType) : $link;

                            $title = $status? __('main.product_statuses_plural.' . $status) : '';
                            $title = $objectType? $title . ' ' . mb_strtolower(__('plural.nominative.' . $objectType)) : $title . ' ' . mb_strtolower(__('main.type_' . $type . '_plural'));
                            $title = $item->name_genitive? $title . ' ' . $item->name_genitive . ' ' . __('main.района') : $title . ' ' . $item->name . ' ' . __('main.район');
                            $title .= ' ' . __('main.на карте');
                        @endphp
                        <li class="category-links-sub__item js-sub-link">
                            <a href="{{ $link }}" class="category-links-sub__links" title="{{ $title }}">{{ $title }}</a>
                        </li>
                        @endif
                    @endforeach
                    <!-- END KYIVDISTRICTS -->
                    <!-- START AREAS -->
                    @elseif($area)
                        @foreach($areas as $item)
                        @php
                            $cache_key = 'count_area:' . $item->area_id . '.category:' . $category->id;

                            if($objectType)
                                $cache_key .= '.type:' . $objectType;

                            if($status)
                                $cache_key .= '.status:' . $status;

                            if(!($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
                                $prodCount = \Aimix\Shop\app\Models\Product::active()->where('category_id', $category->id)->where('address->area', $item->area_id);

                                if($objectType) {
                                    $prodCount = $type == 'cottage'? $prodCount->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', str_replace('_', ' ', $objectType))->select('products.*') : $prodCount->whereJsonContains('extras->newbuild_type', $objectType);
                                }

                                if($status) {
                                    switch ($status) {
                                            case 'frozen':
                                                $prodCount = $prodCount->where('products.extras->is_frozen', 1);
                                                break;

                                            case 'sold':
                                                $prodCount = $prodCount->where('products.is_sold', 1);
                                                break;

                                            default:
                                                $prodCount = $prodCount->where('products.extras->status', $status);
                                                break;
                                        }
                                }

                                $prodCount = $prodCount->count();
                                \Illuminate\Support\Facades\Redis::set($cache_key, $prodCount, 'EX', 108000);
                            }

                            @endphp
                            @if($area->area_id != $item->area_id && $prodCount)
                            @php
                                $link = route($lang . '_map', $category->slug) . '/area/' . $item->slug;
                                $link = $status? $link . '/' . \Str::slug($status) : $link;
                                $link = $objectType? $link . '/' . \Str::slug($objectType) : $link;

                                $title = $status? __('main.product_statuses_plural.' . $status) : '';
                                $title = $objectType? $title . ' ' . mb_strtolower(__('plural.nominative.' . $objectType)) : $title . ' ' . mb_strtolower(__('main.type_' . $type . '_plural'));
                                $title = $title . ' ' . $item->name_genitive;
                                $title = $item->is_center? $title : $title . ' ' . __('main.района');
                                $title .= ' ' . __('main.на карте');
                            @endphp
                            <li class="category-links-sub__item js-sub-link">
                                <a href="{{ $link }}" class="category-links-sub__links" title="{{ $title }}">{{ $title }}</a>
                            </li>
                            @endif
                        @endforeach
                    <!-- END AREAS -->
                    <!-- START CITIES -->
                    @elseif($city)
                        @foreach($cities as $item)
                        @php
                            $cache_key = 'count_city:' . $item->city_id . '.category:' . $category->id;

                            if($objectType)
                                $cache_key .= '.type:' . $objectType;

                            if($status)
                                $cache_key .= '.status:' . $status;

                            if(!($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
                                $prodCount = \Aimix\Shop\app\Models\Product::active()->where('category_id', $category->id)->where('address->city', $item->city_id);

                                if($objectType) {
                                    $prodCount = $type == 'cottage'? $prodCount->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', str_replace('_', ' ', $objectType))->select('products.*') : $prodCount->whereJsonContains('extras->newbuild_type', $objectType);
                                }

                                if($status) {
                                    switch ($status) {
                                            case 'frozen':
                                                $prodCount = $prodCount->where('products.extras->is_frozen', 1);
                                                break;

                                            case 'sold':
                                                $prodCount = $prodCount->where('products.is_sold', 1);
                                                break;

                                            default:
                                                $prodCount = $prodCount->where('products.extras->status', $status);
                                                break;
                                        }
                                }

                                $prodCount = $prodCount->count();
                                \Illuminate\Support\Facades\Redis::set($cache_key, $prodCount, 'EX', 108000);
                            }

                            @endphp
                            @if($city->city_id != $item->city_id && $prodCount)
                            @php
                                $link = route($lang . '_map', $category->slug) . '/city/' . $item->slug;
                                $link = $status? $link . '/' . \Str::slug($status) : $link;
                                $link = $objectType? $link . '/' . \Str::slug($objectType) : $link;

                                $title = $status? __('main.product_statuses_plural.' . $status) : '';
                                $title = $objectType? $title . ' ' . mb_strtolower(__('plural.nominative.' . $objectType)) : $title . ' ' . mb_strtolower(__('main.type_' . $type . '_plural'));
                                $title = $item->name_genitive? $title . ' ' . $item->name_genitive : $title . ' ' . $item->name;
                                $title .= ' ' . __('main.на карте');
                            @endphp
                            <li class="category-links-sub__item js-sub-link">
                                <a href="{{ $link }}" class="category-links-sub__links" title="{{ $title }}">{{ $title }}</a>
                            </li>
                            @endif
                        @endforeach
                    <!-- END CITIES -->
                    <!-- START REGIONS -->
                    @else
                        @foreach($regions_collection as $item)
                        @php
                            $cache_key = 'count_region:' . $item->region_id . '.category:' . $category->id;

                            if($objectType)
                                $cache_key .= '.type:' . $objectType;

                            if($status)
                                $cache_key .= '.status:' . $status;

                            if(!($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
                                $prodCount = \Aimix\Shop\app\Models\Product::active()->where('category_id', $category->id)->where('address->region', $item->region_id);

                                if($objectType) {
                                    $prodCount = $type == 'cottage'? $prodCount->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', str_replace('_', ' ', $objectType))->select('products.*') : $prodCount->whereJsonContains('extras->newbuild_type', $objectType);
                                }

                                if($status) {
                                    switch ($status) {
                                            case 'frozen':
                                                $prodCount = $prodCount->where('products.extras->is_frozen', 1);
                                                break;

                                            case 'sold':
                                                $prodCount = $prodCount->where('products.is_sold', 1);
                                                break;

                                            default:
                                                $prodCount = $prodCount->where('products.extras->status', $status);
                                                break;
                                        }
                                }

                                $prodCount = $prodCount->count();
                                \Illuminate\Support\Facades\Redis::set($cache_key, $prodCount, 'EX', 108000);
                            }

                            @endphp
                            @if(($type === 'cottage' || $item->region_id != 29) && (!$region || $region->region_id != $item->region_id) && $prodCount)
                            @php
                                $link = route($lang . '_map', $category->slug) . '/region/' . $item->slug;
                                $link = $status? $link . '/' . \Str::slug($status) : $link;
                                $link = $objectType? $link . '/' . \Str::slug($objectType) : $link;

                                $title = $status? __('main.product_statuses_plural.' . $status) : '';
                                $title = $objectType? $title . ' ' . mb_strtolower(__('plural.nominative.' . $objectType)) : $title . ' ' . mb_strtolower(__('main.type_' . $type . '_plural'));
                                $title = $title . ' ' . $item->name_genitive;
                                $title = $item->region_id == 29? $title : $title . ' ' . __('main.области');
                                $title .= ' ' . __('main.на карте');
                            @endphp
                            <li class="category-links-sub__item js-sub-link">
                                <a href="{{ $link }}" class="category-links-sub__links" title="{{ $title }}">{{ $title }}</a>
                            </li>
                            @endif
                        @endforeach
                    @endif
                    <!-- END REGIONS -->
                    </ul>
                    <button class="category__sub-button js-drop-button js-category-button">
                        <span class="icon-drop"></span>
                    </button>
                </li>
                <!-- END KYIVDISTRICTS / CITIES / AREAS / REGIONS -->
                <!-- START TYPES -->
                <li class="category-links__item js-drop-item js-catagory-links-item">
                    <button class="category-links__mobile-button js-drop-button">
                        <span>{{ __('main.type_' . $type . '_plural') }} {{ __('main.по типу') }}</span>
                        <span class="icon-drop"></span>
                    </button>
                    <h5 class="category-links__caption">{{ __('main.type_' . $type . '_plural') }} {{ __('main.по типу') }}</h5>
                    <ul class="category-links-sub__list">
                        @foreach(__('attributes.' . $type . '_types') as $key => $item)
                        @php
                            $cache_key = 'count_type:' . $key . '.category:' . $category->id;

                            if($kyivdistrict)
                                $cache_key .= '.kyivdistrict:' . $kyivdistrict->kyivdistrict_id;
                            elseif($city)
                                $cache_key .= '.city:' . $city->city_id;
                            elseif($area)
                                $cache_key .= '.area:' . $area->area_id;
                            elseif($region)
                                $cache_key .= '.region:' . $region->region_id;

                            if($status)
                                $cache_key .= '.status:' . $status;

                            if(!($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
                                $prodCount = \Aimix\Shop\app\Models\Product::active()->where('category_id', $category->id);
                                $prodCount = $region? $prodCount->where('address->region', $region->region_id) : $prodCount;
                                $prodCount = $area? $prodCount->where('address->area', $area->area_id) : $prodCount;
                                $prodCount = $city? $prodCount->where('address->city', $city->city_id) : $prodCount;
                                $prodCount = $kyivdistrict? $prodCount->where('address->kyivdistrict', $kyivdistrict->kyivdistrict_id) : $prodCount;

                                if($status) {
                                    switch ($status) {
                                        case 'frozen':
                                            $prodCount = $prodCount->where('products.extras->is_frozen', 1);
                                            break;

                                        case 'sold':
                                            $prodCount = $prodCount->where('products.is_sold', 1);
                                            break;

                                        default:
                                            $prodCount = $prodCount->where('products.extras->status', $status);
                                            break;
                                    }
                                }

                                $prodCount = $type == 'cottage'? $prodCount->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', str_replace('_', ' ', $key))->select('products.*')->count() : $prodCount->whereJsonContains('extras->newbuild_type', $key)->count();

                                \Illuminate\Support\Facades\Redis::set($cache_key, $prodCount, 'EX', 108000);
                            }

                        @endphp
                        @if($key !== 'Эллинг' && $key !== $objectType && $prodCount)
                        @php
                            $link = route($lang . '_map', $category->slug);

                            if($kyivdistrict)
                                $link = $link . '/kyivdistrict/' . $kyivdistrict->slug;
                            if($city)
                                $link = $link . '/city/' . $city->slug;
                            elseif($area)
                                $link = $link . '/area/' . $area->slug;
                            elseif($region)
                                $link = $link . '/region/' . $region->slug;

                            $link = $status? $link . '/' . \Str::slug($status) : $link;
                            $link = $link . '/' . \Str::slug($key);

                            $title = $status? __('main.product_statuses_plural.' . $status) : '';
                            $title = $title? $title . ' ' . mb_strtolower(__('plural.nominative.' . $key)) : mb_strtolower(__('plural.nominative.' . $key));
                            $title = $title . ' ' . $region_name_genitive;
                            $title .= ' ' . __('main.на карте');
                        @endphp
                        <li class="category-links-sub__item js-sub-link">
                            <a href="{{ $link }}" class="category-links-sub__links" title="{{ $title }}">{{ $title }}</a>
                        </li>
                        @endif
                        @endforeach
                    </ul>
                    <button class="category__sub-button js-drop-button js-category-button">
                        <span class="icon-drop"></span>
                    </button>
                </li>
                <!-- END TYPES -->
                <!-- START STATUSES -->
                <li class="category-links__item js-drop-item js-catagory-links-item">
                    <button class="category-links__mobile-button js-drop-button">
                        <span>{{ __('main.type_' . $type . '_plural') }} {{ __('main.по статусу') }}</span>
                        <span class="icon-drop"></span>
                    </button>
                    <h5 class="category-links__caption">{{ __('main.type_' . $type . '_plural') }} {{ __('main.по статусу') }}</h5>
                    <ul class="category-links-sub__list">
                        @foreach(['done','building','project','frozen','sold'] as $item)
                        @php
                            $cache_key = 'count_status:' . $item . '.category:' . $category->id;

                            if($objectType)
                                $cache_key .= '.type:' . $objectType;

                            if($kyivdistrict)
                                $cache_key .= '.kyivdistrict:' . $kyivdistrict->kyivdistrict_id;
                            elseif($city)
                                $cache_key .= '.city:' . $city->city_id;
                            elseif($area)
                                $cache_key .= '.area:' . $area->area_id;
                            elseif($region)
                                $cache_key .= '.region:' . $region->region_id;


                            if(!($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
                                $prodCount = \Aimix\Shop\app\Models\Product::active()->where('category_id', $category->id);
                                $prodCount = $region? $prodCount->where('address->region', $region->region_id) : $prodCount;
                                $prodCount = $area? $prodCount->where('address->area', $area->area_id) : $prodCount;
                                $prodCount = $city? $prodCount->where('address->city', $city->city_id) : $prodCount;
                                $prodCount = $kyivdistrict? $prodCount->where('address->kyivdistrict', $kyivdistrict->kyivdistrict_id) : $prodCount;

                                if($objectType) {
                                    $prodCount = $type == 'cottage'? $prodCount->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', str_replace('_', ' ', $objectType))->select('products.*') : $prodCount->whereJsonContains('extras->newbuild_type', $objectType);
                                }
                                if($item == 'frozen')
                                    $prodCount = $prodCount->where('products.extras->is_frozen', 1)->count();
                                elseif($item == 'sold')
                                    $prodCount = $prodCount->where('products.is_sold', 1)->count();
                                else
                                    $prodCount = $prodCount->where('products.extras->status', $item)->count();

                                \Illuminate\Support\Facades\Redis::set($cache_key, $prodCount, 'EX', 108000);
                            }

                        @endphp
                        @if($status != $item && $prodCount)
                        @php
                            $link = route($lang . '_map', $category->slug);

                            if($kyivdistrict)
                                $link = $link . '/kyivdistrict/' . $kyivdistrict->slug;
                            if($city)
                                $link = $link . '/city/' . $city->slug;
                            elseif($area)
                                $link = $link . '/area/' . $area->slug;
                            elseif($region)
                                $link = $link . '/region/' . $region->slug;

                            $link = $link  . '/' . $item;
                            $link = $objectType? $link . '/' . \Str::slug($objectType) : $link;

                            $title = __('main.product_statuses_plural.' . $item);
                            $title = $objectType? $title . ' ' . mb_strtolower(__('plural.nominative.' . $objectType)) : $title . ' ' . mb_strtolower(__('main.type_' . $type . '_plural'));
                            $title = $title . ' ' . $region_name_genitive;
                            $title .= ' ' . __('main.на карте');
                        @endphp
                        <li class="category-links-sub__item js-sub-link">
                            <a href="{{ $link }}" class="category-links-sub__links" title="{{ $title }}">{{ $title }}</a>
                        </li>
                        @endif
                        @endforeach
                    </ul>
                    <button class="category__sub-button js-drop-button js-category-button">
                        <span class="icon-drop"></span>
                    </button>
                </li>
                <!-- END STATUSES -->
            </ul>
        </div>
    </section>
    <!-- END INTERLINKING -->

    @if($seo_text)
    <section class="info-block">
        <div class="info-block__wrapper container">
            @if($seo_title)
            <div class="general-heading">
                <h2 class="main-caption-l main-caption-l--transform">{{ $seo_title }}</h2>
            </div>
            @endif
            <div class="info-block__container">
                <div class="info-block__inner info-block__inner__classification">
                    {!! $seo_text !!}
                </div>
            </div>
        </div>
    </section>
    @endif
</main>

@endsection

@push('styles')
    <!-- link href='https://api.mapbox.com/mapbox-gl-js/v1.11.0/mapbox-gl.css' rel='stylesheet' / -->

    <link href='https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.css' rel='stylesheet' />

    <!-- link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/ -->
@endpush

@push('scripts')
    <!-- script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script -->

    <!-- script src='https://api.mapbox.com/mapbox-gl-js/v1.11.0/mapbox-gl.js'></script -->

    <script src='https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.js'></script>

<script>
	mapboxgl.accessToken = '{{ config('services.mapbox.token') }}';

    document.addEventListener("DOMContentLoaded", function(){
        document.map = new mapboxgl.Map({
            container: 'general__map',
            style: 'mapbox://styles/mapbox/streets-v11',
            center: [31.4827777778,49.0275],
            zoom: 5,
            minZoom: 5
        });

        document.map.on('load', function() {
            document.map.getStyle().layers.forEach(function(thisLayer){
                if(thisLayer.type == 'symbol'){
                    document.map.setLayoutProperty(thisLayer.id, 'text-field', ['get','name_ru'])
                }
            });
        });

    });
</script>
<script>
    var address = {
        region: @json($region_id),
        area: @json($area_id),
        city: @json($city_id),
        kyivdistrict: @json($kyivdistrict_id),
    };
    var category_slug = @json($category_slug);
    var other_category_slug = @json($other_category->slug);
    var type = @json($type);
    var status = '{{ $status }}';
    var objectType = '{{ $objectType }}';
    var tsLang = '{{ $lang }}';

</script>
<script src="{{ url('js/catalog/map.js?v=' . $version . '&lang=' . $lang) }}"></script>
@endpush
