@extends('layouts.app', [
  'meta_title' => $category ? ($category->meta_title ?: $category->name) : $page->meta_title,
  'meta_desc' => $category ? ($category->meta_desc ?: $category->name) : $page->meta_desc,
])

@section('content')
<main>
    <div class="decor-background" style="background-image:url({{ url('img/background-img-2.png') }})"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
            <div class="breadcrumbs__list">
                <a href="{{ $lang === 'ru'? url('/') : url($lang) }}" class="breadcrumbs__link">{{ __('main.Главная') }}</a>
                <a href="#" class="breadcrumbs__link" @click.prevent="query.category = null">{{ __('main.Компании') }}</a>
                <a href="#" class="breadcrumbs__link" v-if="query.category" v-cloak>@{{ categories[query.category] }}</a>
            </ul>
        </div>
    </section>
    <section class="companies">
        <div class="companies__wrapper container">
            @if($popular_categories->count())
            <div class="best-company__container" v-show="!query.category">
                <h2 class="main-caption-l main-caption-l--transform">{{ __('main.Лучшие компании') }}</h2>
                <ul class="best-company__list">
                    <li class="best-company__item" v-for="category in popularCategories" @click.prevent="query.category = category.slug">
                        <a :href="category.link">
                            <img :src="category.image" :alt="'Фото: ' + category.name" :title="'Картинка: ' + category.name">
                        </a>
                        <div class="best-company__name">
                            <a :href="category.link">
                                <h5 v-cloak>@{{ category.name }}</h5>
                                <span v-cloak>@{{ category.brands_count }}</span>
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
            @endif
            <div class="best-company-info__container">
                <!-- <h2 class="main-caption-l">{{ __('main.Все компании') }}</h2> -->
                <h1 class="main-caption-l" v-cloak>@{{ seo_title }} <span v-if="query.page > 1"> ➨ {{ __('main.страница') }} @{{ query.page }}</span></h1>
                <div class="main-filtration__wrapper main-filtration__wrapper-best-company">
                    <div class="catalog-filter__form">
                        <label class="input__wrapper js-filter" data-target="filter">
                            <input type="text" class="main-input" placeholder="{{ __('main.Вся Украина') }}" v-model="selectedAddress" readonly>
                            <span class="icon-place-big"></span>
                            <span class="line"></span>
                        </label>
                        <label class="input__wrapper">
                            <input type="text" class="main-input main-input-filter" placeholder="{{ __('forms.placeholders.Название компании') }}" v-model="searchValue">
                        </label>
                        <button class="catalog__filter-button" @click="launchSearch()">
                            <span class="icon-search"></span>
                        </button>
                    </div>
                    <div class="catalog-filter__drop js-filter-drop catalog-drop" data-target="filter">
                        <div class="wrapper active" :class="{'mobile-active': !region}">
                            <input type="text" placeholder="{{ __('main.Выберите область') }}" v-model="search.region" class="caption">
                            <div class="general-drop__container">
                                <div class="general-drop__wrapper">
                                    <ul class="general-drop__list">
                                        <li class="general-drop__item" :class="{active: region == null}" @click="region = null">
                                            <span>{{ __('main.Все области') }}</span>
                                        </li>
                                        <template v-for="(item, key) in regions">
                                            <li class="general-drop__item" v-if="item.toLowerCase().includes(search.region.toLowerCase())" :class="{active: region == key}" @click="region = key">
                                                <span>@{{ item }}</span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="wrapper" :class="{active: region && areas && Object.keys(areas).length, 'mobile-active': region && !area}">
                            <input type="text" placeholder="{{ __('main.Выберите район') }}" v-model="search.area" class="caption">
                            <div class="general-drop__container">
                                <div class="general-drop__wrapper">
                                    <ul class="general-drop__list">
                                        <li class="general-drop__item" :class="{active: area == null}" @click="area = null">
                                            <span>{{ __('main.Все районы') }}</span>
                                        </li>
                                        <template v-for="(item, key) in areas">
                                            <li class="general-drop__item" v-if="item.toLowerCase().includes(search.area.toLowerCase())" :class="{active: area == key}" @click="area = key">
                                                <span>@{{ item }}</span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="wrapper" :class="{active: region && area && cities && Object.keys(cities).length, 'mobile-active': area}">
                            <input type="text" placeholder="{{ __('main.Выберите нас пункт') }}" v-model="search.city" class="caption">
                            <div class="general-drop__container">
                                <div class="general-drop__wrapper">
                                    <ul class="general-drop__list">
                                        <li class="general-drop__item" :class="{active: city == null}" @click="city = null">
                                            <span>{{ __('main.Все нас пункты') }}</span>
                                        </li>
                                        <template v-for="(item, key) in cities">
                                            <li class="general-drop__item" v-if="item.toLowerCase().includes(search.city.toLowerCase())" :class="{active: city == key}" @click="city = key">
                                                <span>@{{ item }}</span>
                                            </li>
                                        </template>
                                    </ul>
                                    <!-- <ul class="general-drop__list">
                                        <li class="general-drop__item">
                                            <span>Одесса</span>
                                            <span class="icon-drop"></span>
                                        </li>
                                        <li class="general-drop__item">
                                            <span>Киевская</span>
                                            <span class="icon-drop"></span>
                                        </li>
                                        <li class="general-drop__item">
                                            <span>Одесса</span>
                                            <span class="icon-drop"></span>
                                        </li>
                                        <li class="general-drop__item">
                                            <span>Ивано-Франковская</span>
                                            <span class="icon-drop"></span>
                                        </li>
                                    </ul> -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <ul class="general-filter__list">
                        <li class="general-filter__item">
                            <div class="general-drop js-drop-item">
                                <button type="button" class="general-filter__button js-drop-button">
                                    <input type="text" class="general-drop-input js-drop-input" :value="query.category? categories[query.category] : '{{ __('main.Все специализации') }}'" readonly="">
                                    <span class="icon-drop"></span>
                                </button>
                                <div class="general-drop__wrapper">
                                    <ul class="general-drop__list">
                                        <li class="general-drop__item js-drop-contains" @click="query.category = null" :class="{active: query.category == null}">{{ __('main.Все специализации') }}</li>
                                        <li class="general-drop__item js-drop-contains" v-for="(item, slug) in categories" @click="query.category = slug" :class="{active: query.category == slug}">@{{ item }}</li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div v-if="loading" class="best-company-info__list" style="height: 500px">
                    <img src="/img/preload-for-files.gif" style="height: 2px;margin: auto;" alt="">
                </div>
                <ul class="best-company-info__list" v-else>
                    <companycard v-for="(company, key) in companies.data" :key="key" :data-company="company" @add-to-favorites="addToFavorites" @add-to-notifications="addToNotifications"></companycard>
                    <li class="best-company-info__item" v-if="!companies.total" style="pointer-events:none">
                        {{ __('main.По Вашему запросу не найдено компаний') }}.
                    </li>
                </ul>

                <div class="pagination__wrapper" v-if="companies.data.length && companies.last_page != 1" v-cloak>
                    <div class="pagination__container">
                        <button class="general-button" @click="query.page = 1" v-bind:class="{disabled: query.page == 1}">
                            <span class="icon-pagi-left"></span>
                        </button>
                        <button class="general-button" v-bind:class="{disabled: companies.current_page == 1}" @click="query.page--">
                            <span class="icon-arrow-pagi-left"></span>
                        </button>
                        <ul class="pagination__list">
                            <li class="pagination__item" @click="query.page = 1" v-bind:class="{active: query.page == 1}">
                                <button>1</button>
                            </li>
                            <li class="pagination__item dots" v-if="companies.last_page > 7 && query.page - 1 > 3">
                                <button>...</button>
                            </li>
                            <li class="pagination__item" v-for="page in (companies.last_page - 1)" @click="query.page = page" v-bind:class="{active: page == query.page}" v-show="page != 1 && ((query.page == 1 && page <= 6) || (query.page == companies.last_page && page >= companies.last_page - 5) || (Math.abs(query.page - page) < 3) || (query.page <= 3 && page <= 6) || (query.page >= companies.last_page - 3 && page >= companies.last_page - 6))">
                                <button>@{{ page }}</button>
                            </li>
                            <li class="pagination__item dots" v-if="companies.last_page > 7 && companies.last_page - query.page > 3">
                                <button>...</button>
                            </li>
                            <li class="pagination__item" @click="query.page = companies.last_page" v-if="companies.last_page != 1" v-bind:class="{active: companies.last_page == query.page}">
                                <button>@{{ companies.last_page }}</button>
                            </li>
                        </ul>
                        <button class="general-button" v-bind:class="{disabled: companies.current_page == companies.last_page}" @click="query.page++">
                            <span class="icon-arrow-pagi-right"></span>
                        </button>
                        <button class="general-button" @click="query.page = companies.last_page" v-if="companies.last_page != 1" v-bind:class="{disabled: companies.last_page == query.page}">
                            <span class="icon-pagi-right"></span>
                        </button>
                    </div>
                    <button @click="loadmore()" v-if="companies.current_page != companies.last_page" class="main-button-more">
                        <span class="text">{{ __('main.Показать больше') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </section>
    @if($promotions->count())
    <section class="product product-compaines">
        <div class="product__wrapper slider-infinity">
            <div class="general-heading container">
                <h2 class="main-caption-l">{{ __('main.Акции от застройщиков') }}</h2>
                <p class="calc-product">{{ $promotions_count }} <span>{{ __('main.Всего') }}</span></p>
            </div>
            <ul class="product__list product__list-sale product-slider__list js-infinity-slider-list">
                <promotioncard v-for="(promotion, key) in promotions" :key="key" :data-promotion="promotion" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'" @add-to-favorites="addToFavorites"></promotioncard>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity container">
                <div class="wrapper @if($promotions->count() < 5) hide @endif">
                    <button class="general-button prev">
                        <span class="icon-arrow-left"></span>
                    </button>
                    <button class="general-button next">
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
    <section class="seo-block" v-if="seo_text && query.page == 1">
        <div class="seo-block__wrapper container">
            <!-- <h2 class="main-caption-l">@{{ seo_title }}</h2> -->
            <div class="seo-block__content">
                <div class="wrapper" v-html="seo_text"></div>
            </div>
        </div>
    </section>
</main>
@endsection

@push('scripts')
<script>
  var companies = @json($companies);
  var categories = @json($categories);
  var popularCategories = @json($popular_categories);
  var currentCategorySlug = @json($category? $category->slug : null);
  var promotions = @json($promotions);
  var regions = @json($regions);
  var seo_title = @json($seo_title);
  var seo_text = @json($seo_desc);
  var page = @json(request('page')? request('page') : 1);
</script>
<script src="{{ url('js/companies/companies.js?v=' . $version) }}"></script>
@endpush