@extends('layouts.app', [
  'meta_title' => str_replace(' - Zagorodna.com', '', $meta_title),
  'meta_desc' => $meta_desc,
])

@php
    $start = microtime(true);
    $kyivdistrict_id = $kyivdistrict? $kyivdistrict->kyivdistrict_id : null;
    $city_id = $city? $city->city_id : null;
    $area_id = $area? $area->area_id : ($city? $city->area_id : null);
    $region_id = $region? $region->region_id : ($area? $area->region_id : ($city? $city->area->region_id : ($kyivdistrict? 29 : null)));
@endphp

@section('content')

<main>
    <div class="decor-background decor-background--pre-catalog" style="background-image:url({{ url('image/background-img-1.png?q=60&fm=pjpg') }})"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
            {{ Breadcrumbs::render('precatalog',
            $category,
            $region_id? \App\Region::where('region_id', $region_id)->first() : null,
            $area_id? \App\Area::where('area_id', $area_id)->first() : null,
            $city_id? \App\City::where('city_id', $city_id)->first() : null,
            $kyivdistrict_id? \App\Kyivdistrict::where('kyivdistrict_id', $kyivdistrict_id)->first() : null) }}
        </div>
    </section>
    <section class="pre-catalog-filter">
        <div class="catalog-filter__wrapper container">
            <form action="{{ route($lang . '_catalog', $category->slug) }}" method="get" class="catalog-filter__form">
                <label class="input__wrapper js-filter" data-target="filter">
                    <input type="hidden" name="address[region]" v-model="address.region" v-if="address.region">
                    <input type="hidden" name="address[city]" v-model="address.city" v-if="address.city">
                    <template v-if="address.area || address.kyivdistrict">
                        <input type="hidden" name="address[area]" v-model="address.area" v-if="address.region != 29">
                        <input type="hidden" name="address[kyivdistrict]" v-model="address.kyivdistrict" v-else>
                        <input type="hidden" name="latlng[lat]" v-model="latlng.lat">
                        <input type="hidden" name="latlng[lng]" v-model="latlng.lng">
                        <input type="hidden" name="radius" v-model="radius">
                    </template>
                    <input type="text" class="main-input" :value="fullAddress" placeholder="{{ __('main.Вся Украина') }}" readonly>
                    <span class="icon-place-big"></span>
                    <button type="button" class="button-distance js-filter" v-cloak data-target="distance" v-if="address.area">+@{{ radius }} км</button>
                    <span class="line"></span>
                </label>
                <label class="input__wrapper">
                    <input type="text" class="main-input main-input-filter" name="filters[search_value]" placeholder="{{ __('forms.placeholders.КГ, адрес или компания') }}">
                </label>
                <button class="catalog__filter-button">
                    <span class="icon-search"></span>
                    <span class="filter-button__text">{{ __('main.искать в каталоге') }}</span>
                </button>
            </form>
            <div class="catalog-filter__distance catalog-drop js-filter-drop" data-target="distance" v-if="address.area">
                <p class="caption">{{ __('main.Расстояние в радиусе') }}, км</p>
                <div class="range-slider">
                    <vue-slider v-model="radius"></vue-slider>
                </div>
            </div>
            <div class="catalog-filter__drop js-filter-drop catalog-drop" data-target="filter">
                <div class="wrapper active" :class="{'mobile-active': !address.region}">
                    <input type="text" placeholder="{{ __('main.Выберите область') }}" v-model="search.region" class="caption">
                    <div class="general-drop__container">
                        <div class="general-drop__wrapper">
                            <ul class="general-drop__list">
                                <li class="general-drop__item" :class="{active: !address.region}" @click="address.region = ''">
                                    <span>{{ __('main.Вся Украина') }}</span>
                                    <span class="icon-drop"></span>
                                </li>
                                <template v-for="(region, key) in regions">
                                    <li class="general-drop__item"  :class="{active: address.region == key}" @click="address.region = key" v-if="region.toLowerCase().includes(search.region.toLowerCase())">
                                        <span>@{{ region }}</span>
                                        <span class="icon-drop"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="wrapper" :class="{active: address.region, 'mobile-active': address.region && !address.area}">
                    <input type="text" placeholder="{{ __('main.Выберите район') }}" v-model="search.area" class="caption">
                    <div class="general-drop__container">
                        <div class="general-drop__wrapper">
                            <ul class="general-drop__list">
                                <li class="general-drop__item" :class="{active: !address.area && !address.kyivdistrict, 'mobile-active': address.area || address.kyivdistrict}" @click="address.area = '', address.kyivdistrict = ''">
                                    <span>{{ __('main.Все районы') }}</span>
                                    <span class="icon-drop"></span>
                                </li>
                                <template v-for="(area, key) in areas">
                                    <li class="general-drop__item"  :class="{active: address.area == key || address.kyivdistrict == key}" @click="address.region == 29? address.kyivdistrict = key : address.area = key" v-if="area.toLowerCase().includes(search.area.toLowerCase())">
                                        <span>@{{ area }}</span>
                                        <span class="icon-drop"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="wrapper" :class="{active: address.area && address.region != 29, 'mobile-active': address.area && address.region != 29}">
                    <input type="text" placeholder="{{ __('main.Выберите нас пункт') }}" v-model="search.city" class="caption">
                    <div class="general-drop__container">
                        <div class="general-drop__wrapper">
                            <ul class="general-drop__list">
                                <li class="general-drop__item" :class="{active: !address.city}" @click="address.city = ''">
                                    <span>{{ __('main.Все нас пункты') }}</span>
                                    <span class="icon-drop"></span>
                                </li>
                                <template v-for="(city, key) in cities">
                                    <li class="general-drop__item"  :class="{active: address.city == key}" @click="address.city = key" v-if="city.toLowerCase().includes(search.city.toLowerCase())">
                                        <span>@{{ city }}</span>
                                        <span class="icon-drop"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="filter-selected__wrapper">
                <h1 class="filter-selected__caption">{{ $h1 }}</h1>
                @if($region || ($area && $area->is_center))
                <h5 class="filter-selected__sub-caption">{{ __('main.Районы') }}</h5>
                @elseif(($area && !$area->is_center) || $city)
                <h5 class="filter-selected__sub-caption">{{ __('main.Населенные пункты') }}</h5>
                @else
                <h5 class="filter-selected__sub-caption">{{ __('main.Области') }}</h5>
                @endif
                <div class="filter-selected__list-wrapper js-drop-item">
                    @if($region || $area || $city || $kyivdistrict)
                    @php
                        if($region || $kyivdistrict)
                            $link = route($lang . '_precatalog', $category->slug);
                        elseif($area)
                            $link = route($lang . '_precatalog', $category->slug) . '/region/' . $area->region->slug;
                        else {
                            $link = route($lang . '_precatalog', $category->slug) . '/area/' . $city->area->slug;
                        }

                        $link = $status? $link . '/' . \Str::slug($status) : $link;
                        $link = $objectType? $link . '/' . \Str::slug($objectType) : $link;
                    @endphp
                    <a href="{{ $link }}" class="filter-selected__back general-button">
                        <span class="icon-arrow-more"></span>
                    </a>
                    @endif
                    <div class="filter-selected__list-container">
                        <ul class="filter-selected__list js-filter-selected">
                            @if(!$region  && !$area && !$city && !$kyivdistrict)

                            @foreach($regions_collection as $item)
                            @php
                            $cache_key = 'count_region:' . $item->region_id . '.category:' . $category->id;

                            if($objectType)
                                $cache_key .= '.type:' . $objectType;

                            if($status)
                                $cache_key .= '.status:' . $status;

                            if(!request('caching') && !($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
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

                            @if($prodCount)
                            @php
                                $link = route($lang . '_precatalog', $category->slug) . '/region/' . $item->slug;
                                $link = $status? $link . '/' . \Str::slug($status) : $link;
                                $link = $objectType? $link . '/' . \Str::slug($objectType) : $link;
                            @endphp
                                @if($type === 'cottage' || $item->region_id != 29)
                                <li class="filter-selected__item"><a href="{{ $link }}">{{ $item->name }}</a></li>
                                @endif
                            @endif
                            @endforeach

                            @elseif($kyivdistrict || ($region && $region->region_id === 29))

                            @foreach($kyivdistricts as $item)
                            @php
                            $cache_key = 'count_kyivdistrict:' . $item->kyivdistrict_id . '.category:' . $category->id;

                            if($objectType)
                                $cache_key .= '.type:' . $objectType;

                            if($status)
                                $cache_key .= '.status:' . $status;

                            if(!request('caching') && !($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
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
                            @if($prodCount)
                            @php
                                $link = route($lang . '_precatalog', $category->slug) . '/kyivdistrict/' . $item->slug;
                                $link = $status? $link . '/' . \Str::slug($status) : $link;
                                $link = $objectType? $link . '/' . \Str::slug($objectType) : $link;
                            @endphp
                                <li class="filter-selected__item @if($kyivdistrict && $kyivdistrict->kyivdistrict_id === $item->kyivdistrict_id) active @endif"><a href="{{ $link }}">{{ $item->name }}</a></li>
                            @endif
                            @endforeach

                            @elseif($region || ($area && $area->is_center))

                            @foreach($areas as $item)
                            @php
                            $cache_key = 'count_area:' . $item->area_id . '.category:' . $category->id;

                            if($objectType)
                                $cache_key .= '.type:' . $objectType;

                            if($status)
                                $cache_key .= '.status:' . $status;

                            if(!request('caching') && !($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
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
                            @if($prodCount)
                            @php
                                $link = route($lang . '_precatalog', $category->slug) . '/area/' . $item->slug;
                                $link = $status? $link . '/' . \Str::slug($status) : $link;
                                $link = $objectType? $link . '/' . \Str::slug($objectType) : $link;
                            @endphp
                            <li class="filter-selected__item @if($area && $area->area_id === $item->area_id) active @endif"><a href="{{ $link }}">{{ $item->name }}</a></li>
                            @endif
                            @endforeach

                            @elseif(($area && !$area->is_center) || $city)

                            @foreach($cities as $item)
                            @php
                            $cache_key = 'count_city:' . $item->city_id . '.category:' . $category->id;

                            if($objectType)
                                $cache_key .= '.type:' . $objectType;

                            if($status)
                                $cache_key .= '.status:' . $status;

                            if(!request('caching') && !($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
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
                            @if($prodCount)
                            @php
                                $link = route($lang . '_precatalog', $category->slug) . '/city/' . $item->slug;
                                $link = $status? $link . '/' . \Str::slug($status) : $link;
                                $link = $objectType? $link . '/' . \Str::slug($objectType) : $link;
                            @endphp
                            <li class="filter-selected__item @if($city && $city->city_id === $item->city_id) active @endif"><a href="{{ $link }}">{{ $item->name }}</a></li>
                            @endif
                            @endforeach
                            @endif
                        </ul>
                    </div>
                    <div class="filter-selected__more js-filter-selected-more">
                        <button class="catalog__open-more js-drop-button"></button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="product product-pre-catalog">
        <form action="{{ route($lang . '_catalog', $category->slug) }}" method="get" class="product__wrapper product__wrapper-list-position container">
            <ul class="product__list" :style="{height: products.data.length? 'auto' : '300px'}">
                <template v-if="products.data.length">
                    <productcard v-for="(product, key) in products.data" :key="key" :data-product="product" @add-to-favorites="addToFavorites" @add-to-comparison="addToComparison"></productcard>
                </template>
                <img src="{{ url('img/preload-for-files.gif') }}" style="margin:auto" alt="" v-else>
            </ul>

            @if($region_id)
            <input type="hidden" name="address[region]" value="{{ $region_id }}">
            @endif
            @if($city_id)
            <input type="hidden" name="address[city]" value="{{ $city_id }}">
            @endif
            @if($area_id)
            <input type="hidden" name="address[area]" value="{{ $area_id }}">
            @endif
            @if($kyivdistrict_id)
            <input type="hidden" name="address[kyivdistrict]" value="{{ $kyivdistrict_id }}">
            @endif
            @if($type == 'cottage' && $objectType)
            <input type="hidden" name="filters[attributes][1][]" value="{{ str_replace('_', ' ', $objectType) }}">
            @elseif($type == 'newbuild' && $objectType)
            <input type="hidden" name="filters[product_attributes][newbuild_type][]" value="{{ str_replace('_', ' ', $objectType) }}">
            @endif
            @if($status)
            <input type="hidden" name="filters[product_attributes][status][]" value="{{ $status }}">
            @endif
            <button class="main-button-more product">{{ __('main.Смотреть каталог') }}</button>
        </form>
    </section>

    <!-- section class="call-back" v-lazy:background-image="'{{ url('image/call-back-bg.png?q=60&fm=pjpg') }}'">
        <div class="call-back__wrapper container">
            <h4 class="call-back__caption">{{ __('main.Подписывайтесь на обновления по выбранному местоположению') }}!</h4>
            <form action="{{ route('subscribe') }}" method="post" class="call-back__form" id="subscription_product">
                @csrf
                <input type="hidden" name="subscription_type" value="product">
                <input type="hidden" name="subscription_region" v-model="address.region" v-if="!address.area">
                <input type="hidden" name="subscription_latlng[lat]" v-model="latlng.lat" v-if="address.area">
                <input type="hidden" name="subscription_latlng[lng]" v-model="latlng.lng" v-if="address.area">
                <div class="call-back__header">
                    <button class="js-filter callback-region-button" type="button" data-target="filter-callback">
                        <input type="hidden" :value="fullAddress" placeholder="{{ __('main.Вся Украина') }}">
                        <span class="icon-place-big"></span>
                        <p class="call-back__name" v-if="fullAddress">@{{ fullAddress }}</p>
                        <p class="call-back__name" v-else>{{ __('main.Вся Украина') }}</p>
                    </button>
                    <template v-if="address.area">
                        <button type="button" class="button-distance js-filter" v-cloak data-target="distance-callback">+@{{ radius }} км</button>
                        <div class="catalog-filter__distance catalog-drop js-filter-drop" data-target="distance-callback" >
                            <input type="hidden" name="subscription_radius" v-model="radius">
                            <p class="caption">{{ __('main.Расстояние в радиусе') }}, км</p>
                            <div  class="range-slider">
                                <vue-slider v-model="radius"></vue-slider>
                            </div>
                        </div>
                    </template>
                    <div class="catalog-filter__drop js-filter-drop catalog-drop callback-drop" data-target="filter-callback">
                    <div class="wrapper active" :class="{'mobile-active': !address.region}">
                        <input type="text" placeholder="{{ __('main.Выберите область') }}" v-model="search.region" class="caption">
                        <div class="general-drop__container">
                            <div class="general-drop__wrapper">
                                <ul class="general-drop__list">
                                    <li class="general-drop__item" :class="{active: !address.region}" @click="address.region = ''">
                                        <span>{{ __('main.Вся Украина') }}</span>
                                        <span class="icon-drop"></span>
                                    </li>
                                    <template v-for="(region, key) in regions">
                                        <li class="general-drop__item"  :class="{active: address.region == key}" @click="address.region = key" v-if="region.toLowerCase().includes(search.region.toLowerCase())">
                                            <span>@{{ region }}</span>
                                            <span class="icon-drop"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="wrapper" :class="{active: address.region, 'mobile-active': address.region && !address.area}">
                        <input type="text" placeholder="{{ __('main.Выберите район') }}" v-model="search.area" class="caption">
                        <div class="general-drop__container">
                            <div class="general-drop__wrapper">
                                <ul class="general-drop__list">
                                    <li class="general-drop__item" :class="{active: !address.area && !address.kyivdistrict, 'mobile-active': address.area || address.kyivdistrict}" @click="address.area = '', address.kyivdistrict = ''">
                                        <span>{{ __('main.Все районы') }}</span>
                                        <span class="icon-drop"></span>
                                    </li>
                                    <template v-for="(area, key) in areas">
                                        <li class="general-drop__item"  :class="{active: address.area == key || address.kyivdistrict == key}" @click="address.region == 29? address.kyivdistrict = key : address.area = key" v-if="area.toLowerCase().includes(search.area.toLowerCase())">
                                            <span>@{{ area }}</span>
                                            <span class="icon-drop"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="wrapper" :class="{active: address.area && address.region != 29, 'mobile-active': address.area && address.region != 29}">
                        <input type="text" placeholder="{{ __('main.Выберите нас пункт') }}" v-model="search.city" class="caption">
                        <div class="general-drop__container">
                            <div class="general-drop__wrapper">
                                <ul class="general-drop__list">
                                    <li class="general-drop__item" :class="{active: !address.city}" @click="address.city = ''">
                                        <span>{{ __('main.Все нас пункты') }}</span>
                                        <span class="icon-drop"></span>
                                    </li>
                                    <template v-for="(city, key) in cities">
                                        <li class="general-drop__item"  :class="{active: address.city == key}" @click="address.city = key" v-if="city.toLowerCase().includes(search.city.toLowerCase())">
                                            <span>@{{ city }}</span>
                                            <span class="icon-drop"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="general-drop general-top__drop js-drop-item call-back__drop">
                        <button type="button" class="general-top__drop__button js-drop-button general-drop__text">
                            <span class="text">{{ __('main.Типы объектов') }}</span>
                            <span class="icon-drop"></span>
                        </button>
                        <div class="general-drop__wrapper">
                            <ul class="general-drop__list">
                                @foreach(__('attributes.' . $type . '_types') as $key => $item)
                                <li class="general-drop__item">
                                    <label class="checkbox__wrapper">
                                        <input type="checkbox" class="input-checkbox" name="subscription_types[]" value="{{ $key }}" checked>
                                        <span class="custome-checkbox">
                                            <span class="icon-active"></span>
                                        </span>
                                        <span class="checkbox-text">{{ $item }}</span>
                                    </label>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="call-back__body">
                    <label class="checkbox__wrapper">
                        <input type="checkbox" class="input-checkbox" name="subscription_adding" value="1" checked>
                        <span class="custome-checkbox">
                            <span class='icon-active'></span>
                        </span>
                        <span class="checkbox-text">{{ __('main.Добавление новой недвижимости') }}</span>
                    </label>
                    <label class="checkbox__wrapper">
                        <input type="checkbox" class="input-checkbox" name="subscription_status" value="1" checked>
                        <span class="custome-checkbox">
                            <span class='icon-active'></span>
                        </span>
                        <span class="checkbox-text">{{ __('main.Изменение статуса') }}</span>
                    </label>
                    <label class="checkbox__wrapper">
                        <input type="checkbox" class="input-checkbox" name="subscription_price" value="1" checked>
                        <span class="custome-checkbox">
                            <span class='icon-active'></span>
                        </span>
                        <span class="checkbox-text">{{ __('main.Изменение цен') }}</span>
                    </label>
                </div>
                <div class="call-back__footer">
                    <label class="input__wrapper @error('subscription_email_product') error @enderror">
                        <input type="email" class="main-input" name="subscription_email_product" placeholder="{{ __('forms.placeholders.Ваш электронный адрес') }}">
                        @error('subscription_email_product')
                            <span class="error-text" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </label>
                    <button class='call-back__button'>{{ __('main.Подписаться') }}</button>
                </div>
            </form>
        </div>
    </section -->

    <!-- OBJECTS STATUS: DONE -->
    @if($status != 'done')
    <section class="product" v-if="done.total">
        <form action="{{ route($lang . '_catalog', $category->slug) }}" method="get" class="product__wrapper slider-infinity">
            <div class="general-heading container">
                @if($objectType && $objectType != 'Земельный_участок')
                <h2 class="main-caption-l main-caption-l--transform">{{ __('main.Построенные', ['type' => mb_strtolower($objectTypePlural)]) }} {{ $region_name_genitive }}</h2>
                @elseif($objectType)
                <h2 class="main-caption-l main-caption-l--transform">{{ __('main.Застроенные', ['type' => mb_strtolower($objectTypePlural)]) }} {{ $region_name_genitive }}</h2>
                @else
                <h2 class="main-caption-l main-caption-l--transform">{{ $type === 'cottage'? __('main.Построенные коттеджные городки') : __('main.Построенные новостройки') }} {{ $region_name_genitive }}</h2>
                @endif
                <p class="calc-product">
                @php
                $link = route($lang . '_catalog', $category->slug) . '?filters[product_attributes][status][]=done';
                $link = $region_id? $link . '&address[region]=' . $region_id : $link;
                $link = $area_id? $link . '&address[area]=' . $area_id : $link;
                $link = $city_id? $link . '&address[city]=' . $city_id : $link;
                $link = $kyivdistrict_id? $link . '&address[kyivdistrict]=' . $kyivdistrict_id : $link;
                if($type == 'cottage' && $objectType)
                    $link = $link . '&filters[attributes][1][]=' . str_replace('_', ' ', $objectType);
                elseif($type == 'newbuild' && $objectType)
                    $link = $link . '&filters[product_attributes][newbuild_type][]' . str_replace('_', ' ', $objectType);
                @endphp
                    <a href="{{ $link }}" v-cloak>@{{ done.total }}</a>
                    <span>{{ __('main.Всего') }}</span>
                </p>
            </div>
            <ul class="product__list product-slider__list js-infinity-slider-list">
                <template v-if="done.data.length">
                    <productcard v-for="(product, key) in done.data" :key="key" :data-product="product" @add-to-favorites="addToFavorites" @add-to-comparison="addToComparison" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'"></productcard>
                </template>
                <img src="{{ url('img/preload-for-files.gif') }}" style="margin:auto" alt="" v-else>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity container">
                <div class="wrapper" :class="{hide: done.total < 5}">
                    <button type="button" class="general-button prev">
                        <span class="icon-arrow-left"></span>
                    </button>
                    <button type="button" class="general-button next">
                        <span class="icon-arrow-right"></span>
                    </button>
                </div>
                @if($region_id)
                <input type="hidden" name="address[region]" value="{{ $region_id }}">
                @endif
                @if($city_id)
                <input type="hidden" name="address[city]" value="{{ $city_id }}">
                @endif
                @if($area_id)
                <input type="hidden" name="address[area]" value="{{ $area_id }}">
                @endif
                @if($type == 'cottage' && $objectType)
                <input type="hidden" name="filters[attributes][1][]" value="{{ str_replace('_', ' ', $objectType) }}">
                @elseif($type == 'newbuild' && $objectType)
                <input type="hidden" name="filters[product_attributes][newbuild_type][]" value="{{ str_replace('_', ' ', $objectType) }}">
                @endif
                <input type="hidden" name="filters[product_attributes][status][]" value="done">
                <button type="submit" class="main-button-more">
                    <span class="text">{{ __('main.Смотреть все') }}</span>
                    <span class="icon-arrow-more"></span>
                </button>
            </div>
        </form>
    </section>
    @endif
    <!-- OBJECTS STATUS: BUILDING -->
    @if($status != 'building')
    <section class="product" v-if="building.total">
        <form action="{{ route($lang . '_catalog', $category->slug) }}" method="get" class="product__wrapper slider-infinity">
            <div class="general-heading container">
                @if($objectType && $objectType != 'Земельный_участок')
                <h2 class="main-caption-l main-caption-l--transform">{{ __('main.Строящиеся', ['type' => mb_strtolower($objectTypePlural)]) }} {{ $region_name_genitive }}</h2>
                @elseif($objectType)
                <h2 class="main-caption-l main-caption-l--transform">{{ __('main.Застраиваемые', ['type' => mb_strtolower($objectTypePlural)]) }} {{ $region_name_genitive }}</h2>
                @else
                <h2 class="main-caption-l main-caption-l--transform">{{ $type === 'cottage'? __('main.Строящиеся коттеджные городки') : __('main.Строящиеся новостройки') }} {{ $region_name_genitive }}</h2>
                @endif
                <p class="calc-product">
                @php
                $link = route($lang . '_catalog', $category->slug) . '?filters[product_attributes][status][]=building';
                $link = $region_id? $link . '&address[region]=' . $region_id : $link;
                $link = $area_id? $link . '&address[area]=' . $area_id : $link;
                $link = $city_id? $link . '&address[city]=' . $city_id : $link;
                $link = $kyivdistrict_id? $link . '&address[kyivdistrict]=' . $kyivdistrict_id : $link;
                if($type == 'cottage' && $objectType)
                    $link = $link . '&filters[attributes][1][]=' . str_replace('_', ' ', $objectType);
                elseif($type == 'newbuild' && $objectType)
                    $link = $link . '&filters[product_attributes][newbuild_type][]' . str_replace('_', ' ', $objectType);
                @endphp
                    <a href="{{ $link }}" v-cloak>@{{ building.total }}</a>
                    <span>{{ __('main.Всего') }}</span>
                </p>
            </div>
            <ul class="product__list product-slider__list js-infinity-slider-list">
                <template v-if="building.data.length">
                    <productcard v-for="(product, key) in building.data" :key="key" :data-product="product" @add-to-favorites="addToFavorites" @add-to-comparison="addToComparison" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'"></productcard>
                </template>
                <img src="{{ url('img/preload-for-files.gif') }}" style="margin:auto" alt="" v-else>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity container">
                <div class="wrapper" :class="{hide: building.total < 5}">
                    <button type="button" class="general-button prev">
                        <span class="icon-arrow-left"></span>
                    </button>
                    <button type="button" class="general-button next">
                        <span class="icon-arrow-right"></span>
                    </button>
                </div>

                @if($region_id)
                <input type="hidden" name="address[region]" value="{{ $region_id }}">
                @endif
                @if($city_id)
                <input type="hidden" name="address[city]" value="{{ $city_id }}">
                @endif
                @if($area_id)
                <input type="hidden" name="address[area]" value="{{ $area_id }}">
                @endif
                @if($type == 'cottage' && $objectType)
                <input type="hidden" name="filters[attributes][1][]" value="{{ str_replace('_', ' ', $objectType) }}">
                @elseif($type == 'newbuild' && $objectType)
                <input type="hidden" name="filters[product_attributes][newbuild_type][]" value="{{ str_replace('_', ' ', $objectType) }}">
                @endif
                <input type="hidden" name="filters[product_attributes][status][]" value="building">
                <button type="submit" class="main-button-more">
                    <span class="text">{{ __('main.Смотреть все') }}</span>
                    <span class="icon-arrow-more"></span>
                </button>
            </form>
        </div>
    </section>
    @endif
    <!-- OBJECTS STATUS: PROJECT -->
    @if($status != 'project')
    <section class="product" v-if="project.total">
        <form action="{{ route($lang . '_catalog', $category->slug) }}" method="get" class="product__wrapper slider-infinity">
            <div class="general-heading container">
                @if($objectType)
                <h2 class="main-caption-l main-caption-l--transform">{{ __('main.Проектируемые', ['type' => mb_strtolower($objectTypePlural)]) }} {{ $region_name_genitive }}</h2>
                @else
                <h2 class="main-caption-l main-caption-l--transform">{{ $type === 'cottage'? __('main.Проектируемые коттеджные городки') : __('main.Проектируемые новостройки') }} {{ $region_name_genitive }}</h2>
                @endif
                <p class="calc-product">
                @php
                $link = route($lang . '_catalog', $category->slug) . '?filters[product_attributes][status][]=project';
                $link = $region_id? $link . '&address[region]=' . $region_id : $link;
                $link = $area_id? $link . '&address[area]=' . $area_id : $link;
                $link = $city_id? $link . '&address[city]=' . $city_id : $link;
                $link = $kyivdistrict_id? $link . '&address[kyivdistrict]=' . $kyivdistrict_id : $link;
                if($type == 'cottage' && $objectType)
                    $link = $link . '&filters[attributes][1][]=' . str_replace('_', ' ', $objectType);
                elseif($type == 'newbuild' && $objectType)
                    $link = $link . '&filters[product_attributes][newbuild_type][]' . str_replace('_', ' ', $objectType);
                @endphp
                    <a href="{{ $link }}" v-cloak>@{{ project.total }}</a>
                    <span>{{ __('main.Всего') }}</span>
                </p>
            </div>
            <ul class="product__list product-slider__list js-infinity-slider-list">
                <template v-if="project.data.length">
                    <productcard v-for="(product, key) in project.data" :key="key" :data-product="product" @add-to-favorites="addToFavorites" @add-to-comparison="addToComparison" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'"></productcard>
                </template>
                <img src="{{ url('img/preload-for-files.gif') }}" style="margin:auto" alt="" v-else>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity container">
                <div class="wrapper" :class="{hide: project.total < 5}">
                    <button type="button" class="general-button prev">
                        <span class="icon-arrow-left"></span>
                    </button>
                    <button type="button" class="general-button next">
                        <span class="icon-arrow-right"></span>
                    </button>
                </div>

                @if($region_id)
                <input type="hidden" name="address[region]" value="{{ $region_id }}">
                @endif
                @if($city_id)
                <input type="hidden" name="address[city]" value="{{ $city_id }}">
                @endif
                @if($area_id)
                <input type="hidden" name="address[area]" value="{{ $area_id }}">
                @endif
                @if($type == 'cottage' && $objectType)
                <input type="hidden" name="filters[attributes][1][]" value="{{ str_replace('_', ' ', $objectType) }}">
                @elseif($type == 'newbuild' && $objectType)
                <input type="hidden" name="filters[product_attributes][newbuild_type][]" value="{{ str_replace('_', ' ', $objectType) }}">
                @endif
                <input type="hidden" name="filters[product_attributes][status][]" value="project">
                <button type="submit" class="main-button-more">
                    <span class="text">{{ __('main.Смотреть все') }}</span>
                    <span class="icon-arrow-more"></span>
                </button>
            </div>
        </form>
    </section>
    @endif
    <!-- OBJECTS STATUS: SOLD -->
    @if($status != 'sold')
    <section class="product" v-if="sold.total">
        <form action="{{ route($lang . '_catalog', $category->slug) }}" method="get" class="product__wrapper slider-infinity">
            <div class="general-heading container">
                @if($objectType)
                <h2 class="main-caption-l main-caption-l--transform">{{ __('main.Проданные', ['type' => mb_strtolower($objectTypePlural)]) }} {{ $region_name_genitive }}</h2>
                @else
                <h2 class="main-caption-l main-caption-l--transform">{{ $type === 'cottage'? __('main.Проданные коттеджные городки') : __('main.Проданные новостройки') }} {{ $region_name_genitive }}</h2>
                @endif
                <p class="calc-product">
                @php
                $link = route($lang . '_catalog', $category->slug) . '?filters[product_attributes][status][]=sold';
                $link = $region_id? $link . '&address[region]=' . $region_id : $link;
                $link = $area_id? $link . '&address[area]=' . $area_id : $link;
                $link = $city_id? $link . '&address[city]=' . $city_id : $link;
                $link = $kyivdistrict_id? $link . '&address[kyivdistrict]=' . $kyivdistrict_id : $link;
                if($type == 'cottage' && $objectType)
                    $link = $link . '&filters[attributes][1][]=' . str_replace('_', ' ', $objectType);
                elseif($type == 'newbuild' && $objectType)
                    $link = $link . '&filters[product_attributes][newbuild_type][]' . str_replace('_', ' ', $objectType);
                @endphp
                    <a href="{{ $link }}" v-cloak>@{{ sold.total }}</a>
                    <span>{{ __('main.Всего') }}</span>
                </p>
            </div>
            <ul class="product__list product-slider__list js-infinity-slider-list">
                <template v-if="sold.data.length">
                    <productcard v-for="(product, key) in sold.data" :key="key" :data-product="product" @add-to-favorites="addToFavorites" @add-to-comparison="addToComparison" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'"></productcard>
                </template>
                <img src="{{ url('img/preload-for-files.gif') }}" style="margin:auto" alt="" v-else>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity container">
                <div class="wrapper" :class="{hide: sold.total < 5}">
                    <button type="button" class="general-button prev">
                        <span class="icon-arrow-left"></span>
                    </button>
                    <button type="button" class="general-button next">
                        <span class="icon-arrow-right"></span>
                    </button>
                </div>

                @if($region_id)
                <input type="hidden" name="address[region]" value="{{ $region_id }}">
                @endif
                @if($city_id)
                <input type="hidden" name="address[city]" value="{{ $city_id }}">
                @endif
                @if($area_id)
                <input type="hidden" name="address[area]" value="{{ $area_id }}">
                @endif
                @if($type == 'cottage' && $objectType)
                <input type="hidden" name="filters[attributes][1][]" value="{{ str_replace('_', ' ', $objectType) }}">
                @elseif($type == 'newbuild' && $objectType)
                <input type="hidden" name="filters[product_attributes][newbuild_type][]" value="{{ str_replace('_', ' ', $objectType) }}">
                @endif
                <input type="hidden" name="filters[product_attributes][status][]" value="sold">
                <button type="submit" class="main-button-more">
                    <span class="text">{{ __('main.Смотреть все') }}</span>
                    <span class="icon-arrow-more"></span>
                </button>
            </div>
        </form>
    </section>
    @endif
    <!-- OBJECTS STATUS: FROZEN -->
    @if($status != 'frozen')
    <section class="product" v-if="frozen.total">
        <form action="{{ route($lang . '_catalog', $category->slug) }}" method="get" class="product__wrapper slider-infinity">
            <div class="general-heading container">
                @if($objectType)
                <h2 class="main-caption-l main-caption-l--transform">{{ __('main.Замороженные', ['type' => mb_strtolower($objectTypePlural)]) }} {{ $region_name_genitive }}</h2>
                @else
                <h2 class="main-caption-l main-caption-l--transform">{{ $type === 'cottage'? __('main.Замороженные коттеджные городки') : __('main.Замороженные новостройки') }} {{ $region_name_genitive }}</h2>
                @endif
                <p class="calc-product">
                @php
                $link = route($lang . '_catalog', $category->slug) . '?filters[product_attributes][status][]=frozen';
                $link = $region_id? $link . '&address[region]=' . $region_id : $link;
                $link = $area_id? $link . '&address[area]=' . $area_id : $link;
                $link = $city_id? $link . '&address[city]=' . $city_id : $link;
                $link = $kyivdistrict_id? $link . '&address[kyivdistrict]=' . $kyivdistrict_id : $link;
                if($type == 'cottage' && $objectType)
                    $link = $link . '&filters[attributes][1][]=' . str_replace('_', ' ', $objectType);
                elseif($type == 'newbuild' && $objectType)
                    $link = $link . '&filters[product_attributes][newbuild_type][]' . str_replace('_', ' ', $objectType);
                @endphp
                    <a href="{{ $link }}" v-cloak>@{{ frozen.total }}</a>
                    <span>{{ __('main.Всего') }}</span>
                </p>
            </div>
            <ul class="product__list product-slider__list js-infinity-slider-list">
                <template v-if="frozen.data.length">
                    <productcard v-for="(product, key) in frozen.data" :key="key" :data-product="product" @add-to-favorites="addToFavorites" @add-to-comparison="addToComparison" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'"></productcard>
                </template>
                <img src="{{ url('img/preload-for-files.gif') }}" style="margin:auto" alt="" v-else>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity container">
                <div class="wrapper" :class="{hide: frozen.total < 5}">
                    <button type="button" class="general-button prev">
                        <span class="icon-arrow-left"></span>
                    </button>
                    <button type="button" class="general-button next">
                        <span class="icon-arrow-right"></span>
                    </button>
                </div>

                @if($region_id)
                <input type="hidden" name="address[region]" value="{{ $region_id }}">
                @endif
                @if($city_id)
                <input type="hidden" name="address[city]" value="{{ $city_id }}">
                @endif
                @if($area_id)
                <input type="hidden" name="address[area]" value="{{ $area_id }}">
                @endif
                @if($type == 'cottage' && $objectType)
                <input type="hidden" name="filters[attributes][1][]" value="{{ str_replace('_', ' ', $objectType) }}">
                @elseif($type == 'newbuild' && $objectType)
                <input type="hidden" name="filters[product_attributes][newbuild_type][]" value="{{ str_replace('_', ' ', $objectType) }}">
                @endif
                <input type="hidden" name="filters[product_attributes][status][]" value="frozen">
                <button type="submit" class="main-button-more">
                    <span class="text">{{ __('main.Смотреть все') }}</span>
                    <span class="icon-arrow-more"></span>
                </button>
            </div>
        </form>
    </section>
    @endif
    <!-- OBJECT TYPES -->
    @if(!$region && !$area && !$city && !$kyivdistrict && !$objectType && !$status)
    <section class="best-company">
        <div class="best-company__wrapper container">
            <div class="general-heading">
                <h2 class="main-caption-l main-caption-l--transform">{{ $type === 'cottage'? __('main.Коттеджи') : __('main.Новостройки') }} {{ $region_name_genitive }} {{ __('main.по типу') }}</h2>
                <!-- <p class="calc-product">240 <span>{{ __('main.Всего') }}</span></p> -->
            </div>
            <ul class="best-company__list">
                @foreach(__('attributes.' . $type . '_types') as $key => $item)
                @if($key !== 'Эллинг')
                @php
                    $link = route($lang . '_precatalog', $category->slug);

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
                @endphp
                <li class="best-company__item">
                    <a href="{{ $link }}">
                        <img v-lazy="'{{ url('common/' . $page[$type . '_types_' . $key] . '?w=350&fm=pjpg&q=80') }}'" alt="{{ __('main.Фото') }}: {{ __('plural.nominative.' . $key) }} {{ $region_name_genitive }}" title="{{ __('main.Картинка') }}: {{ __('plural.nominative.' . $key) }} {{ $region_name_genitive }}">
                    </a>
                    <div class="best-company__name">
                        <a href="{{ $link }}">
                            <h5>{{ __('plural.nominative.' . $key) }}</h5>
                            <!-- <span>575</span> -->
                        </a>
                    </div>
                </li>
                @endif
                @endforeach
            </ul>
            <div class="best-company__text">{!! $page[$type . '_types_text'] !!}</div>
        </div>
    </section>
    @endif
    <!-- OBJECTS FREE SEARCH -->
    @include('includes.freeSearch')

    <!-- OBJECT'S REVIEWS -->
    @if($reviews_total)
    <section class="reviews">
        <div class="reviews__wrapper container slider-infinity">
            <div class="general-heading">
                <h2 class="main-caption-l main-caption-l--transform">{{ __('main.Отзывы') }} {{ $type === 'cottage'? __('main.о коттеджных городках') : __('main.о новостройках') }} {{ $region_name_genitive }}</h2>
                <p class="calc-product">
                    <a href="{{ route($lang . '_reviews') }}">{{ $reviews_total }}</a>
                    <span>{{ __('main.Всего') }}</span>
                </p>
            </div>
            <ul class="reviews__list reviews__list-construction js-infinity-slider-list reviews-slider__list">
                <template v-if="reviews.length">
                    <reviewCard v-for="(review, key) in reviews" :data-review="review" data-type="precatalog" :key="key" :data-classes="key == 0? 'reviews-slider__item js-slider-item-infinity show' : 'reviews-slider__item js-slider-item-infinity'"></reviewCard>
                </template>
                <img src="{{ url('img/preload-for-files.gif') }}" style="margin:auto" alt="" v-else>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity container">
                <div class="wrapper @if($reviews_total < 4) hide @endif">
                    <button type="button" class="general-button prev">
                        <span class="icon-arrow-left"></span>
                    </button>
                    <button type="button" class="general-button next">
                        <span class="icon-arrow-right"></span>
                    </button>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- OBJECTS OTHER CATEGORY -->
    <section class="product" v-if="other_category.total">
        <form action="{{ route($lang . '_catalog', $other_category->slug) }}" method="get" class="product__wrapper slider-infinity">
            <div class="general-heading container">
                <h2 class="main-caption-l">{{ $type === 'cottage'? __('main.Новостройки') : __('main.Коттеджные городки и поселки') }} {{ $region_name_genitive }}</h2>
                <p class="calc-product">
                @php
                $link = route($lang . '_catalog', $other_category->slug) . '?filters[search_value]=';
                $link = $region_id? $link . '&address[region]=' . $region_id : $link;
                $link = $area_id? $link . '&address[area]=' . $area_id : $link;
                $link = $city_id? $link . '&address[city]=' . $city_id : $link;
                $link = $kyivdistrict_id? $link . '&address[kyivdistrict]=' . $kyivdistrict_id : $link;
                $link = $status? $link . '&filters[product_attributes][status][]' . $status : $link;
                @endphp
                    <a href="{{ $link }}" v-cloak>@{{ other_category.total }}</a>
                    <span>{{ __('main.Всего') }}</span>
                </p>
            </div>
            <ul class="product__list product-slider__list js-infinity-slider-list">
                <template v-if="other_category.data.length">
                    <productcard v-for="(product, key) in other_category.data" :key="key" :data-product="product" @add-to-favorites="addToFavorites" @add-to-comparison="addToComparison" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'"></productcard>
                </template>
                <img src="{{ url('img/preload-for-files.gif') }}" style="margin:auto" alt="" v-else>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity container">
                <div class="wrapper" :class="{hide: other_category.total < 5}">
                    <button type="button" class="general-button prev">
                        <span class="icon-arrow-left"></span>
                    </button>
                    <button type="button" class="general-button next">
                        <span class="icon-arrow-right"></span>
                    </button>
                </div>
                @if($region_id)
                <input type="hidden" name="address[region]" value="{{ $region_id }}">
                @endif
                @if($city_id)
                <input type="hidden" name="address[city]" value="{{ $city_id }}">
                @endif
                @if($area_id)
                <input type="hidden" name="address[area]" value="{{ $area_id }}">
                @endif
                @if($type == 'cottage' && $objectType)
                <input type="hidden" name="filters[attributes][1][]" value="{{ str_replace('_', ' ', $objectType) }}">
                @elseif($type == 'newbuild' && $objectType)
                <input type="hidden" name="filters[product_attributes][newbuild_type][]" value="{{ str_replace('_', ' ', $objectType) }}">
                @endif
                @if($status)
                <input type="hidden" name="filters[product_attributes][status][]" value="{{ $status }}">
                @endif
                <button type="submit" class="main-button-more">
                    <span class="text">{{ __('main.Смотреть все') }}</span>
                    <span class="icon-arrow-more"></span>
                </button>
            </div>
        </form>
    </section>


    <!-- BEST COMPANIES -->
    <section class="best-company-info" v-if="companies.total">
        <div class="ts-lang-{{ $lang }} ts-precatalog best-company-info__wrapper container">
            <div class="general-heading">
                <h2 class="main-caption-l main-caption-l--transform">{{ __('main.Лучшие компании') }} {{ $region_name_genitive }}</h2>
                <p class="calc-product">
                    <a href="{{ route($lang . '_companies') }}">@{{ companies.total }}</a>
                    <span>{{ __('main.Всего') }}</span>
                </p>
            </div>

            @if(!$region && !$area && !$city && !$kyivdistrict && !$objectType && !$status)
            <div class="best-company-info__text">{!! $page[$type . '_companies_text'] !!}</div>
            @endif
            <ul class="best-company-info__list">
                <companycard v-for="(company, key) in companies.data" :key="key" :data-company="company" @add-to-favorites="addToFavorites" @add-to-notifications="addToNotifications"></companycard>
            </ul>
            <a href="{{ route($lang . '_companies') }}" class="main-button-more">
                <span class="text">{{ __('main.Смотреть все компании') }}</span>
                <span class="icon-arrow-more"></span>
            </a>
        </div>
    </section>

    <!-- PROMOTIONS -->
    @if($promotions->count())
    <section class="product">
        <div class="product__wrapper slider-infinity">
            <div class="general-heading container">
                <h2 class="main-caption-l main-caption-l--transform">{{ __('main.Акции от застройщиков') }} {{ $region_name_genitive }}</h2>
                <p class="calc-product">
                    <a href="{{ route($lang . '_promotions') }}">{{ $promotions_total }}</a>
                    <span>{{ __('main.Всего') }}</span>
                </p>
            </div>
            <ul class="product__list product__list-sale product-slider__list js-infinity-slider-list">
                <promotioncard v-for="(promotion, key) in promotions" :key="key" :data-promotion="promotion" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'" @add-to-favorites="addToFavorites"></promotioncard>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity container">
                <div class="wrapper @if($promotions->count() < 5) hide @endif">
                    <button type="button" class="general-button prev">
                        <span class="icon-arrow-left"></span>
                    </button>
                    <button type="button" class="general-button next">
                        <span class="icon-arrow-right"></span>
                    </button>
                </div>
                <a href="{{ route($lang . '_promotions') }}" class="main-button-more">
                    <span class="text">{{ __('main.Смотреть все акции') }}</span>
                    <span class="icon-arrow-more"></span>
                </a>
            </div>
        </div>
    </section>
    @endif

    <!-- NEWS -->
    <section class="popular">
        <div class="popular__wrapper container">
            <div class="general-heading more">
                <h2 class="main-caption-l main-caption-l--transform">{{ __('main.Статьи о недвижимости') }} {{ $article_region_name_genitive }}</h2>
                <a :href="newsCategoryLink" class="read-more">
                    <span>{{ __('main.Читать все статьи') }}</span>
                    <span class="icon-arrow-more"></span>
                </a>
            </div>
            <div class="popular__block">
                <div class="popular__block__header">
                    <div class="wrapper">
                        <p class="popular__category-name">{{ __('main.Новости') }}</p>
                        <ul class="popular-sub-name__list">
                            <li class="popular-sub-name__item" :class="{active: articleTab == 0}" @click="articleTab = 0">{{ __('main.Недвижимость') }}</li>
                        </ul>
                    </div>
                    <div class="wrapper">
                        <p class="popular__category-name">{{ __('main.Статьи') }}</p>
                        <ul class="popular-sub-name__list">
                            <li class="popular-sub-name__item" :class="{active: articleTab == 1}" @click="articleTab = 1">{{ __('main.Строительство') }}</li>
                            <li class="popular-sub-name__item" :class="{active: articleTab == 2}" @click="articleTab = 2">{{ __('main.Недвижимость') }}</li>
                            <li class="popular-sub-name__item" :class="{active: articleTab == 3}" @click="articleTab = 3">{{ __('main.Аналитика') }}</li>
                        </ul>
                    </div>
                </div>
                <div class="popular__block__body">
                    <ul class="popular__block__list popular__block__list-main">
                        <articlecard v-for="(article, key) in articles" :key="key" :data-article="article" @add-to-favorites="addToFavorites"></articlecard>
                    </ul>
                </div>
            </div>
            <div class="subscribe-block">
                <h5 class="subscribe-block__text">{{ __('main.Нашли полезную информацию?') }}<br> {{ __('main.Подписывайтесь на актуальные публикации') }}:</h5>
                @include('modules.subscription')
            </div>
        </div>
    </section>

    @if($status != 'frozen')
    <section class="price-block" v-if="pricesProducts.length">
        <div class="price-block__wrapper container">
            <div class="price-block__header">
                <div class="price-block__main">
                    @php
                        $title = $status? mb_strtolower(__('main.product_statuses_plural_prepositional.' . $status)) : '';
                        $title = $objectType? $title . ' ' . mb_strtolower(__('plural.prepositional.' . $objectType)) : $title . ' ' . mb_strtolower(__('main.type_' . $type . '_plural_prepositional'));
                        $title = $title . ' ' . $region_name_genitive;
                        $unit = $objectType == 'Земельный_участок'? 'сот' : 'кв.м';
                    @endphp
                    <p class="main-caption-l main-caption-l--transform ">{{ __('main.Средняя цена') . ' ' . __('main.в') . ' ' . $title }}</p>
                    <p class="price-block__main__number" v-cloak>@{{ prices.avg }}<span>грн/{{ $unit }}</span></p>
                </div>
                <div class="price-block__header__container">
                    <div class="price-block__header__item">
                        <p class="price-block__header__item-caption">{{ __('main.Минимальная цена') }}</p>
                        <p class="price-block__header__item-sub">{{ __('main.в') }} {{ $title }}</p>
                        <p class="price-block__header__item-number" v-cloak>@{{ prices.min }}<span>грн/{{ $unit }}</span></p>
                    </div>
                    <div class="price-block__header__item">
                        <p class="price-block__header__item-caption">{{ __('main.Максимальная цена') }}</p>
                        <p class="price-block__header__item-sub">{{ __('main.в') }} {{ $title }}</p>
                        <p class="price-block__header__item-number" v-cloak>@{{ prices.max }}<span>грн/{{ $unit }}</span></p>
                    </div>
                </div>
            </div>
            <div class="rating-block__table rating-block__table--price">
                <div class="rating-block__table__container">
                    <div class="rating-block__table__caption">
                        <p class="table-type">{{ __('main.Название') }}</p>
                        <p class="table-description">{{ __('main.Застройщик') }}</p>
                        <p class="table-price">{{ __('main.Цена') }}, грн/{{ $unit }}</p>
                    </div>
                    <div class="wrapper" v-cloak>
                        <div class="rating-block__table__item" v-for="item in pricesProducts">
                            <a :href="item.link" class="table-type">@{{ item.name }}</a>
                            <p class="table-description">@{{ item.brand_name? item.brand_name : '-' }}</p>
                            @if($objectType !== 'Земельный_участок')
                            <p class="table-price">@{{ item.statistics_price }}</p>
                            @else
                            <p class="table-price">@{{ item.statistics_price_plot }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- RATINGS/STATISTICS -->
    @if(!$region_id && !$status && !$objectType)
    <section class="rating-block rating-block-info">
        <div class="rating-block__wrapper container">
            <div class="js-drop-item rating-drop__wrapper">
                <button class="rating__button-mobile js-drop-button">
                    <span class="rating-icon"></span>
                    <span>{{ __('main.Рейтинги' ) }} {{ mb_strtolower(__('main.type_' . $type . '_plural_genitive')) }} {{ $region_name_genitive }}</span>
                    <span class="icon-drop"></span>
                </button>
                <h2 class="main-caption-l rating-caption"><span class="rating-icon"></span>{{ __('main.Рейтинги' ) }} {{ mb_strtolower(__('main.type_' . $type . '_plural_genitive')) }} {{ $region_name_genitive }} </h2>
                <ul class="rating-block__list">
                    <li class="rating-block__item">
                        <div class="rating-block__item__header">
                            <span class="rating-block-icon"></span>
                            <h3 class="rating-block__item__caption"><span>ТОП-10</span>{{ mb_strtolower(__('main.type_' . $type . '_plural_genitive')) }}</h3>
                        </div>
                        <div class="rating-block__table">
                            <div class="rating-block__table__caption">
                                <p class="table-number">№</p>
                                <p class="table-name">{{ __('main.Название') }}</p>
                                <p class="table-rating">{{ __('main.баллы') }}</p>
                            </div>
                            <div class="wrapper">
                                @foreach($top_rating as $key => $item)
                                @php
                                    $item_type = $item->category_id == 1 || $item->category->id == 6? 'cottage' : 'complex';
                                @endphp
                                <div class="rating-block__table__item">
                                    <p class="table-number">{{ $key + 1 }}</p>
                                    <a href="{{ $item->link . '/rating' }}" class="table-name">{{ $item->name }}</a>
                                    <p class="table-rating">{{ $item->top_rating }}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </li>
                    <li class="rating-block__item rating-block__item-assessment">
                        <div class="rating-block__item__header">
                            <span class="rating-block-icon-man"></span>
                            <h3 class="rating-block__item__caption">{{ __('main.Народный рейтинг') }} {{ mb_strtolower(__('main.type_' . $type . '_plural_genitive')) }}</h3>
                        </div>
                        <div class="rating-block__table">
                            <div class="rating-block__table__caption">
                                <p class="table-number">№</p>
                                <p class="table-name">{{ __('main.Название') }}</p>
                                <p class="table-calc">{{ __('main.Кол-во') }}</p>
                                <p class="table-rating">{{ __('main.Оценка') }}</p>
                            </div>
                            <div class="wrapper">
                                @foreach($reviews_rating as $key => $item)
                                <div class="rating-block__table__item">
                                    <p class="table-number">{{ $key + 1 }}</p>
                                    <a href="{{ $item->link }}" class="table-name">{{ $item->name }}</a>
                                    <p class="table-calc">{{ $item->old_rating_count }}</p>
                                    <p class="table-rating">{{ round($item->old_rating / $item->old_rating_count, 1) }}</p>
                                </div>

                                @endforeach
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="rating-drop__info">
                <div class="js-drop-item rating-drop__wrapper">
                    <button class="rating__button-mobile js-drop-button">
                        <span class="rating-icon"></span>
                        <span>
                            <span>{{ __('main.Статистика') }} {{ mb_strtolower(__('main.type_' . $type . '_plural_genitive')) }}</span>
                        </span>
                        <span class="icon-drop"></span>
                    </button>
                    <div class="rating-block__general-wrapper">
                        <h2 class="main-caption-l rating-caption">
                            <span class="rating-icon"></span>
                            <span>{{ __('main.Статистика') }} {{ mb_strtolower(__('main.type_' . $type . '_plural_genitive')) }}</span>
                        </h2>
                        <ul class="rating-block__list rating-block__list-diagram">
                            <li class="rating-block__item">
                                <div class="rating-block__item__header">
                                    <span class="rating-block-icon-diagram"></span>
                                    <h3 class="rating-block__item__caption">{{ __('main.Статистика') }} (грн) - {{ __('main.type_' . $type . '_plural') }}</h3>
                                </div>
                                <div class="rating-block__table">
                                    <div class="wrapper">
                                        <div class="rating-block__general-info">
                                            <a href="{{ route($lang . '_precatalog', $category_slug) }}" class="name">{{ __('main.Украина') }}</a>
                                            <p class="date">{{ $statistics->first()->date }} - <span>{{ $statistics->first()->total }}</span></p>
                                            <p class="date">{{ $statistics->last()->date }} - <span>{{ $statistics->last()->total }}</span></p>
                                        </div>
                                        @if($type == 'cottage')
                                        <div class="rating-block__general-info">
                                            <a href="{{ route($lang . '_precatalog', $category_slug) . '/region/' . \App\Region::where('region_id', 29)->first()->slug }}" class="name">{{ __('main.Киев') }}</a>
                                            <p class="date">{{ $statistics->first()->date }} - <span>{{ $statistics->first()->data['29'] }}</span></p>
                                            <p class="date">{{ $statistics->last()->date }} - <span>{{ $statistics->last()->data['29'] }}</span></p>
                                        </div>
                                        @else
                                        <div class="rating-block__general-info">
                                            <a href="{{ route($lang . '_precatalog', $category_slug) . '/region/' . \App\Region::where('region_id', 11)->first()->slug }}" class="name">{{ __('main.Киевская') }}</a>
                                            <p class="date">{{ $statistics->first()->date }} - <span>{{ $statistics->first()->data['11'] }}</span></p>
                                            <p class="date">{{ $statistics->last()->date }} - <span>{{ $statistics->last()->data['11'] }}</span></p>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="rating-block__table__caption">
                                        <p class="table-number">№</p>
                                        <p class="table-area">{{ __('main.Область') }}</p>
                                        <p class="table-date">{{ $statistics->first()->date }}</p>
                                        <p class="table-date">{{ $statistics->last()->date }}</p>
                                    </div>
                                    <div class="wrapper">
                                        @php
                                        $i = 1;
                                        $data = $statistics->first()->data;
                                        arsort($data);
                                        @endphp
                                        @foreach($data as $key => $item)
                                        @if($key != 5 && $key != 13) <!-- Луганская и Донецкая -->
                                        @if((($type == 'cottage' && $key != 29) || ($type == 'newbuild' && $key != 11)) && $item && $i <= 10)
                                        @php
                                        $reg = \App\Region::where('region_id', $key)->first();
                                        @endphp
                                        <div class="rating-block__table__item">
                                            <p class="table-number">{{ $i++ }}</p>
                                            <a href="{{ route($lang . '_precatalog', $category_slug) . '/region/' . $reg->slug }}" class="table-name">{{ $reg->name }}</a>
                                            <p class="table-rating">{{ $item }}</p>
                                            <p class="table-rating">{{ $statistics->last()->data[$key] }}</p>
                                        </div>
                                        @endif
                                        @endif
                                        @endforeach
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="js-drop-item rating-drop__wrapper">
                    <button class="rating__button-mobile js-drop-button">
                        <span class="rating-block-icon-info"></span>
                        <span>{{ __('main.Земли Украины') }}</span>
                        <span class="icon-drop"></span>
                    </button>
                    <div class="rating-block__general-wrapper">
                        <h2 class="main-caption-l rating-caption">{{ __('main.Земли Украины') }}</h2>
                        <ul class="rating-block__list">
                            <li class="rating-block__item">
                                <div class="rating-block__item__header">
                                    <span class="rating-block-icon-info"></span>
                                    <h3 class="rating-block__item__caption">{{ __('main.Справочная информация') }}</h3>
                                </div>
                                <div class="rating-block__table rating-block__table-info">
                                    <ul class="rating-block__table-list">
                                        @foreach($land_articles as $item)
                                        <li class="rating-block__table-item">
                                            <a href="{{ $item->link }}" class="rating-block__table-link">
                                                <span class="text">{{ $item->title }}</span>
                                                <span class="icon-arrow-more"></span>
                                            </a>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <a href="{{ route($lang . '_precatalog_statistics', $category_slug) }}" class="main-button-more">{{ __('main.Показать все области') }}</a>
        </div>
    </section>
    @endif

    <!-- MAP -->
    <section class="info-block info-block_map container ts-precatalog">
        @php
            $title = $status? __('main.product_statuses_plural.' . $status) : '';
            if($title) {
                $title = $objectType? $title . ' ' . mb_strtolower(__('plural.nominative.' . $objectType)) : $title . ' ' . mb_strtolower(__('main.type_' . $type . '_plural'));
            } else {
                $title = $objectType? $title . ' ' . __('plural.nominative.' . $objectType) : $title . ' ' . __('main.type_' . $type . '_plural');
            }
            $title = $title . ' ' . $region_name_genitive . ' ' . __('main.на карте');
        @endphp
        <div class="general-heading">
            <h2 class="main-caption-l main-caption-l--transform">{{ $title }}</h2>
        </div>
        <div id="general__map" style="height:650px"></div>
    </section>
    <!-- REGION ARTICLE -->
    @if($region_article)
    <section class="info-block">
        <div class="info-block__wrapper container">
            <div class="general-heading">
                <h2 class="main-caption-l main-caption-l--transform">{{ $region_article->title }}</h2>
            </div>
            <div class="info-block__container">
                <div class="info-block__inner info-block__inner_region">
                    {!! $region_article->content !!}
                </div>
            </div>
        </div>
    </section>
    @endif
    <!-- CLASSIFICATION ARTICLE -->
    @if($classification_article)
    <section class="info-block">
        <div class="info-block__wrapper container">
            <div class="general-heading">
                <h2 class="main-caption-l main-caption-l--transform">{{ $type == 'cottage'? $classification_article->name : $classification_article->title }}</h2>
            </div>
            <div class="info-block__container">
                <div class="info-block__inner">
                    {!! $classification_article->content !!}
                </div>
                <!-- <a href="#" class="read-more">
                    <span>Читать полностью</span>
                    <span class="icon-arrow-more"></span>
                </a> -->
            </div>
        </div>
    </section>
    @endif
    <!-- SEO TEXT -->
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
    <!-- FAQ -->
    @if($questions && $questions->count())
    <section class="info-block">
        <div class="info-block__wrapper container">
            <div class="general-heading more">
                <h2 class="main-caption-l main-caption-l--transform">{{ __('main.Частые вопросы и ответы') }} {{ $type === 'cottage'? __('main.о коттеджных городках') : __('main.о новостройках') }}</h2>
                <a href="{{ route($lang . '_faq') }}" class="read-more">
                    <span>{{ __('main.Читать все вопросы') }}</span>
                    <span class="icon-arrow-more"></span>
                </a>
            </div>
            <div class="info-block__container">
                <div class="info-block__inner">
                    <ul class="info-block__spoiler__list">
                        @foreach($questions as $question)
                        <li class="info-block__spoiler__item js-drop-item">
                            <button class="info-block__spoiler__button js-drop-button">
                                <span class="text">{{ $question->question }}</span>
                                <span class="icon-drop"></span>
                            </button>
                            <div class="info__wrapper">
                                {!! $question->answer !!}
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>
    @endif
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

                        if(!request('caching') && !($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
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
                            $link = route($lang . '_precatalog', $category->slug) . '/kyivdistrict/' . $item->slug;
                            $link = $status? $link . '/' . \Str::slug($status) : $link;
                            $link = $objectType? $link . '/' . \Str::slug($objectType) : $link;

                            $title = $status? __('main.product_statuses_plural.' . $status) : '';
                            $title = $objectType? $title . ' ' . mb_strtolower(__('plural.nominative.' . $objectType)) : $title . ' ' . mb_strtolower(__('main.type_' . $type . '_plural'));
                            $title = $item->name_genitive? $title . ' ' . $item->name_genitive . ' ' . __('main.района') : $title . ' ' . $item->name . ' ' . __('main.район');
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

                            if(!request('caching') && !($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
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
                                $link = route($lang . '_precatalog', $category->slug) . '/area/' . $item->slug;
                                $link = $status? $link . '/' . \Str::slug($status) : $link;
                                $link = $objectType? $link . '/' . \Str::slug($objectType) : $link;

                                $title = $status? __('main.product_statuses_plural.' . $status) : '';
                                $title = $objectType? $title . ' ' . mb_strtolower(__('plural.nominative.' . $objectType)) : $title . ' ' . mb_strtolower(__('main.type_' . $type . '_plural'));
                                $title = $title . ' ' . $item->name_genitive;
                                $title = $item->is_center? $title : $title . ' ' . __('main.района');
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

                            if(!request('caching') && !($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
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
                                $link = route($lang . '_precatalog', $category->slug) . '/city/' . $item->slug;
                                $link = $status? $link . '/' . \Str::slug($status) : $link;
                                $link = $objectType? $link . '/' . \Str::slug($objectType) : $link;

                                $title = $status? __('main.product_statuses_plural.' . $status) : '';
                                $title = $objectType? $title . ' ' . mb_strtolower(__('plural.nominative.' . $objectType)) : $title . ' ' . mb_strtolower(__('main.type_' . $type . '_plural'));
                                $title = $item->name_genitive? $title . ' ' . $item->name_genitive : $title . ' ' . $item->name;
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

                            if(!request('caching') && !($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
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
                                $link = route($lang . '_precatalog', $category->slug) . '/region/' . $item->slug;
                                $link = $status? $link . '/' . \Str::slug($status) : $link;
                                $link = $objectType? $link . '/' . \Str::slug($objectType) : $link;

                                $title = $status? __('main.product_statuses_plural.' . $status) : '';
                                $title = $objectType? $title . ' ' . mb_strtolower(__('plural.nominative.' . $objectType)) : $title . ' ' . mb_strtolower(__('main.type_' . $type . '_plural'));
                                $title = $title . ' ' . $item->name_genitive;
                                $title = $item->region_id == 29? $title : $title . ' ' . __('main.области');
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

                            if(!request('caching') && !($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
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
                            $link = route($lang . '_precatalog', $category->slug);

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


                            if(!request('caching') && !($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
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
                            $link = route($lang . '_precatalog', $category->slug);

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
</main>

@endsection

@push('styles')
<!-- link href='https://api.mapbox.com/mapbox-gl-js/v1.11.0/mapbox-gl.css' rel='' data-style="mapbox" / -->
<link href='https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.css' rel='stylesheet' />
<style>
.best-company__item img[lazy="loading"] {
    height: 1px;
    width: auto;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}
.call-back[lazy="loading"] {
    background-image: none !important;
}
</style>
@endpush

@push('scripts')
<!-- <script src='https://api.mapbox.com/mapbox-gl-js/v1.11.0/mapbox-gl.js'></script> -->
<script src='https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.js'></script>

<script>

    window.addEventListener("load", function(){
        setTimeout(() => {
            var scriptElement = document.createElement('script');
            scriptElement.type = 'text/javascript';
            scriptElement.src = 'https://api.mapbox.com/mapbox-gl-js/v1.11.0/mapbox-gl.js';
            document.head.appendChild(scriptElement);

            document.querySelector('link[data-style="mapbox"]').setAttribute('rel', 'stylesheet');

            setTimeout(() => {
                mapboxgl.accessToken = '{{ config('services.mapbox.token') }}';

                document.map = new mapboxgl.Map({
                    language: '{{ $lang }}',
                    container: 'general__map',
                    style: 'mapbox://styles/mapbox/streets-v11',
                    center: [31.4827777778,49.0275],
                    zoom: 5,
                });

                document.map.on('load', function() {
                    document.map.getStyle().layers.forEach(function(thisLayer){
                        if(thisLayer.type == 'symbol'){
                            document.map.setLayoutProperty(thisLayer.id, 'text-field', ['get','name_ru'])
                        }
                    });
                });
            }, 500);
        }, 3000);
    });
</script>
<script>

    var promotions = @json($promotions);
    var regions = @json($regions);
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

</script>
<script src="{{ url('js/catalog/precatalog.js?v=' . $version) }}"></script>
<script>
    // Select block
    let slectedList = document.querySelector('.js-filter-selected');
    function hideMoreButton() {
        if(slectedList.offsetHeight <= 140) {
            document.querySelector('.js-filter-selected-more').classList.add("hide");
        }else {
            document.querySelector('.js-filter-selected-more').classList.remove("hide");
        }
    }
    hideMoreButton();
    window.addEventListener('resize', function() {
        hideMoreButton();
    });
    // Select block
</script>

@endpush
