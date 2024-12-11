@php
    $type = ($category->id === 1 || $category->original_id === 1)? 'cottages' : 'newbuilds';
@endphp

@extends('layouts.app', [
    'meta_title' => $page[$type . '_meta_title'],
    'meta_desc' => $page[$type . '_meta_desc'],
    'canonical' => $category->link
])

@section('content')
<main>
    <div class="decor-background" style="background-image:url({{ url('img/background-img-2.png') }})"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
            {{ Breadcrumbs::render('catalog',
                $category) }}
        </div>
    </section>
    <section class="catalog">
        <div class="general-sitebar">
            <div class="general__map js-map-wrapper">
                <button class="general__open js-button js-button-map" data-target="full-map" title="{{ __('main.На весь экран') }}">
                    <span class="icon-full"></span>
                </button>
                <div id="general__map" style="height:860px"></div>
            </div>
        </div>
        <div class="general-heading container">
            <h1 class="main-caption-l">{{ $page[$type . '_title'] }} <span v-if="query.page > 1" v-cloak> ➨ {{ __('main.страница') }} @{{ query.page }}</span></h1>
            <div class="catalog__buttons-wrapper">
                <!-- <button class="general-button-color">Купить дом</button> -->
                <button class="filter-button js-filter" data-target="full-filter">
                    <span class="filter-button__decor"></span>
                    <span class="filter-button__text">{{ __('main.Фильтрация') }}</span>
                </button>
            </div>
        </div>
        <div class="catalog__wrapper container">
            <div class="catalog__main-wrapper">
                <div class="catalog-filter__form">
                    <label class="input__wrapper js-filter" data-target="filter">
                        <input type="text" class="main-input" :value="fullAddress" placeholder="{{ __('main.Вся Украина') }}" readonly="readonly">
                        <span class="icon-place-big"></span>
                        <button type="button" class="button-distance js-filter" data-target="distance" v-if="query.address.area" v-cloak>+@{{ query.radius }} км</button>
                        <span class="line"></span>
                    </label>
                    <label class="input__wrapper">
                        <input type="text" class="main-input main-input-filter" v-model="query.filters.search_value" placeholder="{{ __('forms.placeholders.КГ, адрес или компания') }}">
                    </label>
                    <button class="catalog__filter-button" @click="launchSearch()">
                        <span class="icon-search"></span>
                    </button>
                </div>
                <div class="catalog-filter__distance catalog-drop js-filter-drop" data-target="distance">
                    <p class="caption">{{ __('main.Расстояние в радиусе') }}, км</p>
                    <div class="range-slider">
                        <vue-slider v-model="query.radius"></vue-slider>
                    </div>
                </div>
                <div class="catalog-filter__drop js-filter-drop catalog-drop" data-target="filter">
                    <div class="wrapper active" :class="{'mobile-active': !query.address.region}">
                        <input type="text" placeholder="{{ __('main.Выберите область') }}" v-model="search.region" class="caption">
                        <div class="general-drop__container">
                            <div class="general-drop__wrapper">
                                <ul class="general-drop__list">
                                    <li class="general-drop__item" :class="{active: !query.address.region}" @click="query.address.region = ''">
                                        <span>{{ __('main.Вся Украина') }}</span>
                                        <span class="icon-drop"></span>
                                    </li>
                                    <template v-for="(region, key) in regions">
                                        <li class="general-drop__item"  :class="{active: query.address.region == key}" @click="query.address.region = key" v-if="region.toLowerCase().includes(search.region.toLowerCase())">
                                            <span>@{{ region }}</span>
                                            <span class="icon-drop"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="wrapper" :class="{active: query.address.region, 'mobile-active': query.address.region && !query.address.area && !query.address.kyivdistrict}">
                        <input type="text" placeholder="{{ __('main.Выберите район') }}" v-model="search.area" class="caption">
                        <div class="general-drop__container">
                            <div class="general-drop__wrapper">
                                <ul class="general-drop__list">
                                    <li class="general-drop__item" :class="{active: !query.address.area && !query.address.kyivdistrict}" @click="query.address.area = ''">
                                        <span>{{ __('main.Все районы') }}</span>
                                        <span class="icon-drop"></span>
                                    </li>
                                    <template v-for="(area, key) in areas">
                                        <li class="general-drop__item"  :class="{active: query.address.area == key || query.address.kyivdistrict == key}" @click="query.address.region == 29? query.address.kyivdistrict = key : query.address.area = key" v-if="area.toLowerCase().includes(search.area.toLowerCase())">@{{ area }}</li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="wrapper" :class="{active: query.address.area && query.address.region != 29, 'mobile-active': query.address.area && query.address.region != 29}">
                        <input type="text" placeholder="Выберите нас. пункт" v-model="search.city" class="caption">
                        <div class="general-drop__container">
                            <div class="general-drop__wrapper">
                                <ul class="general-drop__list">
                                    <li class="general-drop__item" :class="{active: !query.address.city}" @click="query.address.city = ''">
                                        <span>{{ __('main.Все нас пункты') }}</span>
                                        <span class="icon-drop"></span>
                                    </li>
                                    <template v-for="(city, key) in cities">
                                        <li class="general-drop__item"  :class="{active: query.address.city == key}" @click="query.address.city = key" v-if="city.toLowerCase().includes(search.city.toLowerCase())">@{{ city }}</li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="catalog__filtration__wrapper js-filter-drop" data-target="full-filter">
                    <div class="catalog__filtration__container">
                        <h5 class="catalog__mobile-caption">{{ __('main.Фильтрация') }}</h5>
                        <div class="catalog__filtration__header">
                            @if($category->id === 1 || $category->original_id === 1)
                            <template v-for="(attribute, key) in filters.attributes">
                                <div class="wrapper" v-if="attribute.slug === 'type'">
                                    <h5 class="catalog__filtration__caption">{{ __('main.Тип недвижимости') }}</h5>
                                        <ul class="catalog__filtration__list" >
                                            <li class="catalog__filtration__item" v-for="item in attribute.trans_values" :class="{active: query.filters.attributes[key].includes(item)}">
                                                <label v-if="item != 'Эллинг' && item != 'Елінг'">
                                                    <input type="checkbox" v-model="query.filters.attributes[key]" :value="item">
                                                    @{{ item }}
                                                </label>
                                            </li>
                                        </ul>
                                </div>
                            </template>
                            @else
                            <template>
                                <div class="wrapper" >
                                    <h5 class="catalog__filtration__caption">{{ __('main.Тип недвижимости') }}</h5>
                                        <ul class="catalog__filtration__list" >
                                            <li class="catalog__filtration__item" v-for="(item, key) in filters.newbuild_types" :class="{active: query.filters.product_attributes.newbuild_type.includes(key)}">
                                                <label>
                                                    <input type="checkbox" v-model="query.filters.product_attributes.newbuild_type" :value="key">
                                                    @{{ item }}
                                                </label>
                                            </li>
                                        </ul>
                                </div>
                            </template>
                            @endif
                            <div class="catalog__range-slider">
                                <h5 class="catalog__filtration__caption">{{ __('main.Диапазон цен') }} (грн/м2)</h5>
                                <div class="wrapper">
                                    <div class="range-slider">
                                        <vue-slider
                                        ref="slider"
                                        v-model="query.price"
                                        :min="rangeOptions.min"
                                        :max="rangeOptions.max"
                                        :step="rangeOptions.step"
                                        ></vue-slider>
                                    </div>
                                    <div class="range-slider__values" v-cloak>
                                        <p class="values">@{{ query.price[0] }}</p>
                                        <p class="values">@{{ query.price[1] }}</p>
                                    </div>
                                </div>
                            </div>
                            <button class="filter-button" @click="launchSearch()">
                                <span class="filter-button__decor"></span>
                                <span class="filter-button__text">{{ __('main.Подобрать') }}</span>
                            </button>
                        </div>
                        <div class="catalog__filtration__body js-catalog-more-item">
                            <div class="catalog__filtration__body__wrapper">
                                <div class="catalog__range-slider catalog__range-slider-tablet calc-wrapper">
                                    <h5 class="catalog__filtration__caption">{{ __('main.Диапазон цен') }} (грн/м2)</h5>
                                    <div class="wrapper">
                                        <div class="range-slider">
                                            <vue-slider
                                            ref="slider"
                                            v-model="query.price"
                                            :min="rangeOptions.min"
                                            :max="rangeOptions.max"
                                            :step="rangeOptions.step"
                                            ></vue-slider>
                                        </div>
                                        <div class="range-slider__values">
                                            <p class="values">@{{ query.price[0] }}</p>
                                            <p class="values">@{{ query.price[1] }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="calc-wrapper">
                                    <h5 class="catalog__filtration__caption">Статус</h5>
                                    <ul class="catalog__filtration__list">
                                        <li class="catalog__filtration__item" v-for="(item, key) in filters.statuses" :class="{active: query.filters.product_attributes.status.includes(key)}">
                                            <label>
                                                <input type="checkbox" v-model="query.filters.product_attributes.status" :value="key">
                                                @{{ item }}
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                                <template v-for="(attribute, key) in filters.attributes">
                                    <div class="calc-wrapper" v-if="attribute.slug !== 'type' && attribute.type === 'radio'">
                                        <h5 class="catalog__filtration__caption">@{{ attribute.name }}</h5>
                                        <ul class="catalog__filtration__list" :class="{'catalog__filtration__list-number': attribute.id === 2}">
                                            <li class="catalog__filtration__item" v-for="item in attribute.values" :class="{active: query.filters.attributes[key].includes(item)}">
                                                <label>
                                                    <input type="checkbox" v-model="query.filters.attributes[key]" :value="item">
                                                    @{{ item }}
                                                </label>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="catalog__range-slider calc-wrapper" v-if="attribute.slug !== 'type' && attribute.type === 'number'">
                                        <h5 class="catalog__filtration__caption">@{{ attribute.name }}</h5>
                                        <div class="wrapper">
                                            <div class="range-slider">
                                                <vue-slider
                                                ref="slider"
                                                v-model="query.filters.attributes[key]"
                                                :min="+attribute.values['min']"
                                                :max="+attribute.values['max']"
                                                :step="+attribute.values['step']"
                                                ></vue-slider>
                                            </div>
                                            <div class="range-slider__values">
                                                <p class="values">@{{ query.filters.attributes[key][0] }}</p>
                                                <p class="values">@{{ query.filters.attributes[key][1] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <div class="catalog__filtration__body__wrapper catalog__filtration__body__wrapper-list">
                                <div class="catalog__checkbox-wrapper">
                                    <h5 class="catalog__filtration__caption">{{ __('main.Материал стен') }}</h5>
                                    <ul class="catalog__filtration__list">
                                        <template v-for="(item, key) in filters.wall_materials">
                                            <li class="catalog__filtration__item" v-if="item" :class="{active: query.filters.product_attributes.wall_material.includes(key)}">
                                                <label>
                                                    <input type="checkbox" v-model="query.filters.product_attributes.wall_material" :value="key">
                                                    @{{ item }}
                                                </label>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                                @if($category->id === 1 || $category->original_id === 1)
                                <div class="catalog__checkbox-wrapper">
                                    <h5 class="catalog__filtration__caption">{{ __('main.Материал крыши') }}</h5>
                                    <ul class="catalog__filtration__list">
                                        <template v-for="(item, key) in filters.roof_materials">
                                            <li class="catalog__filtration__item" v-if="item" :class="{active: query.filters.product_attributes.roof_material.includes(key)}">
                                                <label>
                                                    <input type="checkbox" v-model="query.filters.product_attributes.roof_material" :value="key">
                                                    @{{ item }}
                                                </label>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="catalog__filtration__footer">
                            <!-- <label class="checkbox__wrapper">
                                <input type="checkbox" class="input-checkbox">
                                <span class="custome-checkbox">
                                    <span class='icon-active'></span>
                                </span>
                                <span class="checkbox-text">Только акции</span>
                            </label> -->
                            <div class="general-drop js-drop-item">
                                <button type="button" class="general-filter__button js-drop-button">
                                    <input type="text" class="general-drop-input js-drop-input" :value="sorts[query.sort]" readonly="">
                                    <span class="icon-drop"></span>
                                </button>
                                <div class="general-drop__wrapper">
                                    <ul class="general-drop__list">
                                        <li class="general-drop__item js-drop-contains" v-for="(sort, key) in sorts" @click="query.sort = key" :class="{active: query.sort == key}">@{{ sort }}</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="general-drop js-drop-item catalog__filtration__number">
                                <button type="button" class=" js-drop-button general-filter__button">
                                    <span class="text" v-cloak>@{{ query.per_page }}</span>
                                    <span class="icon-drop"></span>
                                </button>
                                <div class="general-drop__wrapper">
                                    <ul class="general-drop__list">
                                        <li class="general-drop__item js-drop-contains" v-for="item in per_page" @click="query.per_page = item" :class="{active: query.per_page === item}">@{{ item }}</li>
                                    </ul>
                                </div>
                            </div>
                            <!-- disabled когда кнопка отключена -->
                            <button class="catalog__clear" @click="clearFilters()">
                                <span class="wrapper">
                                    <span class="icon-clear"></span>
                                    <span class="text">{{ __('main.Сбросить') }}</span>
                                </span>
                            </button>
                            <button class="catalog__open-more js-catalog-more"></button>
                        </div>
                    </div>
                    <div class="catalog__filtration__mobile-panel">
                        <button class="catalog__back-button js-catalog-back">
                            <span class="icon-arrow-more"></span>
                        </button>
                        <button class="filter-button">
                            <span class="filter-button__decor"></span>
                            <span class="filter-button__text">{{ __('main.Подобрать') }}</span>
                        </button>
                        <button class="catalog__clear">
                            <span class="wrapper">
                                <span class="icon-clear"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="product product-catalog">
        <div class="product__wrapper product__wrapper-list-position container">
            <div v-if="loading" class="product__list" style="height: 500px">
                <img src="/img/preload-for-files.gif" style="height: 2px;margin: auto;" alt="">
            </div>

            <ul class="product__list ts-side-catalog-map" :style="{opacity: loading? '0' : '1', position: loading? 'absolute' : 'relative', zIndex: loading? '-1' : '1'}">
                <li class="product__item product-item__map-catalog js-map-wrapper">
                    <button class="general__open js-button js-button-map" data-target="full-map" title="{{ __('main.На весь экран') }}">
                        <span class="icon-full"></span>
                    </button>
                    <div id="general__map" style="height:551px"></div>
                </li>
                <productcard v-for="(product, key) in products.data" :key="key" :data-product="product" @add-to-favorites="addToFavorites" @add-to-comparison="addToComparison"></productcard>
            </ul>

            <div class="pagination__wrapper" v-if="products.data.length && products.last_page != 1" v-cloak>
                <div class="pagination__container">
                    <button class="general-button" @click="query.page = 1" v-bind:class="{disabled: query.page == 1}">
                        <span class="icon-pagi-left"></span>
                    </button>
                    <button class="general-button" v-bind:class="{disabled: products.current_page == 1}" @click="query.page--">
                        <span class="icon-arrow-pagi-left"></span>
                    </button>
                    <ul class="pagination__list">
                        <li class="pagination__item" @click="query.page = 1" v-bind:class="{active: query.page == 1}">
                            <button>1</button>
                        </li>
                        <li class="pagination__item dots" v-if="products.last_page > 7 && query.page - 1 > 3">
                            <button>...</button>
                        </li>
                        <li class="pagination__item" v-for="page in (products.last_page - 1)" @click="query.page = page" v-bind:class="{active: page == query.page}" v-show="page != 1 && ((query.page == 1 && page <= 6) || (query.page == products.last_page && page >= products.last_page - 5) || (Math.abs(query.page - page) < 3) || (query.page <= 3 && page <= 6) || (query.page >= products.last_page - 3 && page >= products.last_page - 6))">
                            <button>@{{ page }}</button>
                        </li>
                        <li class="pagination__item dots" v-if="products.last_page > 7 && products.last_page - query.page > 3">
                            <button>...</button>
                        </li>
                        <li class="pagination__item" @click="query.page = products.last_page" v-if="products.last_page != 1" v-bind:class="{active: products.last_page == query.page}">
                            <button>@{{ products.last_page }}</button>
                        </li>
                    </ul>
                    <button class="general-button" v-bind:class="{disabled: products.current_page == products.last_page}" @click="query.page++">
                        <span class="icon-arrow-pagi-right"></span>
                    </button>
                    <button class="general-button" @click="query.page = products.last_page" v-if="products.last_page != 1" v-bind:class="{disabled: products.last_page == query.page}">
                        <span class="icon-pagi-right"></span>
                    </button>
                </div>
                <button @click="loadmore()" v-if="products.current_page != products.last_page" class="main-button-more">
                    <span class="text">{{ __('main.Показать больше') }}</span>
                </button>
            </div>
        </div>
    </section>
    @if($page[$type . '_seo_text'] && (!request('page') || request('page') == 1))
    <section class="seo-block">
        <div class="seo-block__wrapper container">
            <h2 class="main-caption-l">{{ $page[$type . '_seo_title'] }}</h2>
            <div class="seo-block__content">
                <div class="wrapper">
                    {!! $page[$type . '_seo_text'] !!}
                </div>
            </div>
        </div>
    </section>
    @endif
    <section class="popup popup-full-map" data-target="full-map">
        <div class="popup__wrapper popup__wrapper--map js-container-map">
            <button class="close-popup js-close close-popup-inner">
                <span class="decor"></span>
            </button>
            <div id="general__map_popup" style="height:500px"></div>
        </div>
    </section>

</main>
@endsection

@push('styles')
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.css' rel='stylesheet' />
@endpush
@push('scripts')
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.js'></script>

<script>
    mapboxgl.accessToken = '{{ config('services.mapbox.token') }}';

    document.addEventListener("DOMContentLoaded", function(){
        if(window.innerWidth < 1900)
            document.querySelector('.general-sitebar').remove();

        document.map = new mapboxgl.Map({
            language: '{{ $lang }}',
            container: 'general__map',
            style: 'mapbox://styles/mapbox/streets-v11',
            center: [31.4827777778,49.0275],
            zoom: 3.7,
            minZoom: 3.7,
        });

        document.map.on('load', function() {
            document.map.getStyle().layers.forEach(function(thisLayer){
                if(thisLayer.type == 'symbol'){
                    document.map.setLayoutProperty(thisLayer.id, 'text-field', ['get','name_ru'])
                }
            });
        });
    });

    var filters = @json($filters);
    var selecterFilters = @json($selected_filters);
    var sorts = @json(__('sorts.products'));
    var rangeOptions = @json($range_options);
    var regions = @json($regions);
</script>
<script src="{{ url('js/catalog/catalog.js?v=' . $version) }}"></script>
@endpush
