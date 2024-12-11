@extends('layouts.app', [
  'meta_title' => $page->meta_title,
  'meta_desc' => $page->meta_desc,
])

@section('content')
<main>
		@if($banners->count())
    <section class="main-section">
        <div class="main-slider__wrapper container js-slider">
            <ul class="main-slider__list">
                @php
                    $width = 1115;
                    if(Browser::isTablet())
                        $width = 950;
                    elseif(Browser::isMobile())
                        $width = 335;

                    if(count($banners) === 1) {
                        $banners[] = $banners[0];
                        $banners[] = $banners[0];
                    } elseif(count($banners) === 2) {
                        $banners[] = $banners[0];
                        $banners[] = $banners[1];
                    }
                @endphp

                @foreach($banners as $key => $banner)
                <li class="main-slider__item js-main-slider @if($key == 0) show @elseif($key == 1) next @elseif($key == count($banners) - 1) prev @endif" data-index="{{ $key + 1 }}" v-lazy:background-image="'{{ url(str_replace('files', 'glide', $banner->image) . '?w=' . $width . '&fm=pjpg&q=75') }}'">
                    <div class="main-slider__item__info">
                        <h2 class="main-slider__name">{{ $banner->title }}</h2>
                        @if($banner->short_desc)
                        	<p class="main-slider__info"><span>{{ __('main.от') }}</span>{{ $banner->short_desc }} <span>грн/кв.м</span></p>
                        @endif
                        @if($banner->link)
                        	<a rel="nofollow" href="{{ url($banner->link) }}" target="_blank" class="main-button">{{ $banner->button_text }}</a>
                        @endif
                    </div>
                </li>
                @endforeach
            </ul>
            <div class="main-slider__dots">
              @foreach($banners as $key => $banner)
                <button class="main-slider__dots-button js-dot @if($key == 0) active @endif" data-index="{{ $key + 1 }}"></button>
              @endforeach
            </div>
            <div class="general-button__wrapper general-button__wrapper-main js-arrows-main">
                <button class="general-button prev">
                    <span class="icon-main-arrow-left"></span>
                </button>
                <button class="general-button next">
                    <span class="icon-main-arrow-right"></span>
                </button>
            </div>
        </div>

        <div class="general-filter">
            <div class="general-filter__wrapper container">
                <form :action="searchFormAction" class="general-filter__form">
                <input type="hidden" name="address[region]" v-model="address.region" v-if="address.region">
                <input type="hidden" name="address[area]" v-model="address.area" v-if="address.area">
                <input type="hidden" name="address[city]" v-model="address.city" v-if="address.city">
                <input type="hidden" name="price[0]" v-model="price.min" v-if="price.min">
                <input type="hidden" name="price[1]" v-model="price.max" v-if="price.max">
                    <div class="filter-check">
                        <label class="filter-check__wrapper">
                            <input type="checkbox" class='filter-check__input' name="new-houses" v-model="main_search_type" disabled>
                            <h4 class="general-filter__caption"></h4>
                            <span class="filter-check__decor-wrapper">
                                <span class="filter-check__decor"></span>
                            </span>
                        </label>
                    </div>
                    <ul class="general-filter__list">
                        <li class="general-filter__item ">
                            <h4 class="general-filter__caption">{{ __('main.Область') }}</h4>
                            <div class="general-drop js-drop-item">
                                <button type="button" class="general-filter__button js-drop-button">
                                    <input type="text" class="general-drop-input js-drop-input" :value="address.region? regions[address.region] : '{{ __('main.Все области') }}'" readonly>
                                    <span class="icon-drop"></span>
                                </button>
                                <div class="general-drop__container">
                                    <div class="general-drop__wrapper">
                                        <ul class="general-drop__list">
                                            <li class="general-drop__item js-drop-contains" v-for="(region, key) in regions" :class="{active: address.region == key}" @click="address.region = key">@{{ region }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="general-filter__item">
                            <h4 class="general-filter__caption">{{ __('main.Район') }}</h4>
                            <div class="general-drop js-drop-item">
                                <button type="button" class="general-filter__button js-drop-button">
                                    <input type="text" class="general-drop-input js-drop-input" :value="address.area? areas[address.area] : '{{ __('main.Все районы') }}'" readonly>
                                    <span class="icon-drop"></span>
                                </button>
                                <div class="general-drop__container">
                                    <div class="general-drop__wrapper" v-if="Object.keys(areas).length">
                                        <ul class="general-drop__list">
                                            <li class="general-drop__item js-drop-contains" v-for="(area, key) in areas" :class="{active: address.area == key}" @click="address.area = key">@{{ area }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="general-filter__item">
                            <h4 class="general-filter__caption">{{ __('main.Населенный пункт') }}</h4>
                            <div class="general-drop js-drop-item">
                                <button type="button" class="general-filter__button js-drop-button">
                                    <input type="text" class="general-drop-input js-drop-input" :value="address.city? cities[address.city] : '{{ __('main.Все нас пункты') }}'" readonly>
                                    <span class="icon-drop"></span>
                                </button>
                                <div class="general-drop__container">
                                    <div class="general-drop__wrapper" v-if="Object.keys(cities).length">
                                        <ul class="general-drop__list">
                                            <li class="general-drop__item js-drop-contains" v-for="(city, key) in cities" :class="{active: address.city == key}" @click="address.city = key">@{{ city }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <label class="input__wrapper">
                        <input type="number" :min="main_search_type? range_options.min_2 : range_options.min_1" :max="main_search_type? range_options.max_2 : range_options.max_1" class="main-input" placeholder="{{ __('main.Ср. цена') }}, грн/м2" v-model="average_price" @change="checkAveragePrice()">
                    </label>
                    <button class="filter-button">
                        <span class="filter-button__decor"></span>
                        <span class="filter-button__text">{{ __('main.Подобрать') }}</span>
                    </button>
                </form>
            </div>
        </div>
    </section>
    @endif

    <div class="general-text container">
        <h1 class="main-caption-xl">{{ $page->entry_title }}</h1>
        {!! $page->entry_text !!}
    </div>
    @if($hits_count)
    <section class="product">
        <div class="product__wrapper slider-infinity">
            <div class="general-heading container">
                <h2 class="main-caption-l">{{ $page->hot_title }}</h2>
                <p class="calc-product">@{{ hits.total }} <span>{{ __('main.Всего') }}</span></p>
            </div>
            <div class="general-text container">
            {!! $page->hot_text !!}
            </div>
            <ul class="product__list product-slider__list js-infinity-slider-list">
                <template v-if="hits.total">
                    <productcard v-for="(product, key) in hits.data" :key="key" :data-product="product" @add-to-favorites="addToFavorites" @add-to-comparison="addToComparison" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'"></productcard>
                </template>
                <img src="{{ url('img/preload-for-files.gif') }}" style="margin:auto" v-else>
            </ul>

            <div class="general-button__wrapper js-arrow-infinity general-button--hide container">
                <div class="wrapper @if($hits_count < 5) hide @endif">
                    <button class="general-button prev">
                        <span class="icon-arrow-left"></span>
                    </button>
                    <button class="general-button next">
                        <span class="icon-arrow-right"></span>
                    </button>
                </div>
            </div>
        </div>
    </section>
    @endif
    @if($cottages_count)
    <section class="product">
        <div class="product__wrapper slider-infinity">
            <div class="general-heading container">
                <h2 class="main-caption-l">{{ $page->cottage_title }}</h2>
                <p class="calc-product">
                    <a href="{{ route($lang . '_precatalog', $cottage_slug) }}">@{{ cottages.total }}</a>
                    <span>{{ __('main.Всего') }}</span>
                </p>
            </div>
            <div class="general-text container">
            {!! $page->cottage_text !!}
            </div>
            <ul class="product__list product-slider__list js-infinity-slider-list">
                <template v-if="cottages.total">
                    <productcard v-for="(product, key) in cottages.data" :key="key" :data-product="product" @add-to-favorites="addToFavorites" @add-to-comparison="addToComparison" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'"></productcard>
                </template>
                <img src="{{ url('img/preload-for-files.gif') }}" style="margin:auto" v-else>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity container">
                <div class="wrapper @if($cottages_count < 5) hide @endif">
                    <button class="general-button prev">
                        <span class="icon-arrow-left"></span>
                    </button>
                    <button class="general-button next">
                        <span class="icon-arrow-right"></span>
                    </button>
                </div>
                <a href="{{ route($lang . '_precatalog', $cottage_slug) }}" class="main-button-more">
                    <span class="text">{{ $page->cottage_button_text }}</span>
                    <span class="icon-arrow-more"></span>
                </a>
            </div>
        </div>
    </section>
    @endif
    @if($newbuilds_count)
    <section class="product">
        <div class="product__wrapper slider-infinity">
            <div class="general-heading container">
                <h2 class="main-caption-l">{{ $page->newbuild_title }}</h2>
                <p class="calc-product">
                    <a href="{{ route($lang . '_precatalog', $newbuild_slug) }}">@{{ newbuilds.total }}</a>
                    <span>{{ __('main.Всего') }}</span>
                </p>
            </div>
            <div class="general-text container">
            {!! $page->newbuild_text !!}
            </div>
            <ul class="product__list product-slider__list js-infinity-slider-list">
                <template v-if="newbuilds.total">
                    <productcard v-for="(product, key) in newbuilds.data" :key="key" :data-product="product" @add-to-favorites="addToFavorites" @add-to-comparison="addToComparison" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'"></productcard>
                </template>
                <img src="{{ url('img/preload-for-files.gif') }}" style="margin:auto" v-else>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity container">
                <div class="wrapper @if($newbuilds_count < 5) hide @endif">
                    <button class="general-button prev">
                        <span class="icon-arrow-left"></span>
                    </button>
                    <button class="general-button next">
                        <span class="icon-arrow-right"></span>
                    </button>
                </div>
                <a href="{{ route($lang . '_precatalog', $newbuild_slug) }}" class="main-button-more">
                    <span class="text">{{ $page->newbuild_button_text }}</span>
                    <span class="icon-arrow-more"></span>
                </a>
            </div>
        </div>
    </section>
    @endif
    @if($promotions_count)
    <section class="product">
        <div class="product__wrapper slider-infinity">
            <div class="general-heading container">
                <h2 class="main-caption-l">{{ $page->promotions_title }}</h2>
                <p class="calc-product">
                    <a href="{{ route($lang . '_promotions') }}">{{ $promotions_count }}</a>
                    <span>{{ __('main.Всего') }}</span>
                </p>
            </div>
            <div class="general-text container">
            {!! $page->promotions_text !!}
            </div>
            <ul class="product__list product__list-sale product-slider__list js-infinity-slider-list">
                <template v-if="promotions.length">
                    <promotioncard v-for="(promotion, key) in promotions" :key="key" :data-promotion="promotion" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'" @add-to-favorites="addToFavorites"></promotioncard>
                </template>
                <img src="{{ url('img/preload-for-files.gif') }}" style="margin:auto" v-else>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity container">
                <div class="wrapper @if($promotions_count < 5) hide @endif">
                    <button class="general-button prev">
                        <span class="icon-arrow-left"></span>
                    </button>
                    <button class="general-button next">
                        <span class="icon-arrow-right"></span>
                    </button>
                </div>
                <a href="{{ route($lang . '_promotions') }}" class="main-button-more">
                    <span class="text">{{ $page->promotions_button_text }}</span>
                    <span class="icon-arrow-more"></span>
                </a>
            </div>
        </div>
    </section>
    @endif
    <section class="popular">
        <div class="popular__wrapper container">
            <div class="general-heading more">
                <h2 class="main-caption-l">{{ $page->news_title }}</h2>
                <a :href="newsCategoryLink" class="read-more">
                    <span>{{ $page->news_button_text }}</span>
                    <span class="icon-arrow-more"></span>
                </a>
            </div>
            <div class="popular__text">{!! $page->news_text !!}</div>
            <div class="popular__block">
                <div class="popular__block__header">
                    <div class="wrapper">
                        <p class="popular__category-name">{{ __('main.Новости') }}</p>
                        <ul class="popular-sub-name__list">
                            <li class="popular-sub-name__item" :class="{active: articleTab == 0}" @click="articleTab = 0">{{ __('main.Недвижимость') }}</li>
                        </ul>
                    </div>
                    <div class="wrapper">
                        <p class="ts-articles-ln-298 popular__category-name">{{ __('main.Статьи') }}</p>
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
        <!-- <div class="general-text container">
        </div> -->
    </section>

    <section class="best-company">
        <div class="best-company__wrapper container">
            <div class="general-heading">
                <h2 class="main-caption-l">{{ $page->companies_title }}</h2>
                <p class="calc-product">
                    <a href="{{ route($lang . '_companies') }}">{{ $brands_count }}</a>
                    <span>{{ __('main.Всего') }}</span>
                </p>
            </div>
            <ul class="best-company__list">
                @foreach($company_categories as $category)
                <li class="best-company__item">
                    <a href="{{ $category->link }}">
                        <img src="{{ $category->image? url($category->image) : url('image/company-cover.jpg?w=350&q=75') }}" alt="Фото: {{ $category->name }}" alt="Картинка: {{ $category->name }}">
                    </a>
                    <div class="best-company__name">
                        <a href="{{ $category->link }}">
                            <h5>{{ $category->name }}</h5>
                            <span>{{ $category->brands->count() }}</span>
                        </a>
                    </div>
                </li>
                @endforeach
            </ul>
            <div class="best-company__text">{!! $page->companies_text !!}</div>
            <a href="{{ route($lang . '_companies') }}" class="main-button-more">
                <span class="text">{{ $page->companies_button_text }}</span>
                <span class="icon-arrow-more"></span>
            </a>
        </div>
    </section>
    @if($reviews->count())
    <section class="reviews">
        <div class="reviews__wrapper slider-infinity container">
            <div class="general-heading">
                <h2 class="main-caption-l">{{ $page->reviews_title }}</h2>
                <p class="calc-product">
                    <a href="{{ route($lang . '_reviews') }}">{{ $reviews_count }}</a>
                    <span>{{ __('main.Всего') }}</span>
                </p>
            </div>
            <ul class="reviews__list js-infinity-slider-list reviews-slider__list">
                <reviewCard v-for="(review, key) in reviews" :data-review="review" data-type="zagorodna" :key="key" :data-classes="key == 0? 'reviews-slider__item js-slider-item-infinity show' : 'reviews-slider__item js-slider-item-infinity'"></reviewCard>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity">
                <div class="wrapper @if($reviews->count() < 3) hide @endif">
                    <button class="general-button prev">
                        <span class="icon-arrow-left"></span>
                    </button>
                    <button class="general-button next">
                        <span class="icon-arrow-right"></span>
                    </button>
                </div>
                <a href="{{ route($lang . '_reviews') }}" class="main-button-more">
                    <span class="text">{{ $page->reviews_button_text }}</span>
                    <span class="icon-arrow-more"></span>
                </a>
            </div>
        </div>
    </section>
    @endif
    <!-- <section class="rating-block">
        <div class="rating-block__wrapper container">
            <div class="js-drop-item rating-drop__wrapper">
                <button class="rating__button-mobile js-drop-button">
                    <span class="rating-icon"></span>
                    <span>Рейтинги недвижимости</span>
                    <span class="icon-drop"></span>
                </button>
                <h2 class="main-caption-l rating-caption"><span class="rating-icon"></span>Рейтинги недвижимости</h2>
                <ul class="rating-block__list">
                    <li class="rating-block__item">
                        <div class="rating-block__item__header">
                            <span class="rating-block-icon"></span>
                            <h3 class="rating-block__item__caption"><span>ТОП-10</span>новостроек</h3>
                        </div>
                        <div class="rating-block__table">
                            <div class="rating-block__table__caption">
                                <p class="table-number">№</p>
                                <p class="table-name">Название</p>
                                <p class="table-rating">баллы</p>
                            </div>
                            <div class="wrapper">
                                <div class="rating-block__table__item">
                                    <p class="table-number">1</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">2503</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">2</p>
                                    <p class="table-name">Новый Коралловый Риф</p>
                                    <p class="table-rating">2203</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">3</p>
                                    <p class="table-name">Морская жемчужина</p>
                                    <p class="table-rating">2100</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">4</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">2003</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">5</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">1503</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">6</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">1203</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">7</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">253</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">8</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">250</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">9</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">203</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">10</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">03</p>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="rating-block__item">
                        <div class="rating-block__item__header">
                            <span class="rating-block-icon"></span>
                            <h3 class="rating-block__item__caption"><span>ТОП-10</span>новостроек</h3>
                        </div>
                        <div class="rating-block__table">
                            <div class="rating-block__table__caption">
                                <p class="table-number">№</p>
                                <p class="table-name">Название</p>
                                <p class="table-rating">баллы</p>
                            </div>
                            <div class="wrapper">
                                <div class="rating-block__table__item">
                                    <p class="table-number">1</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">2503</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">2</p>
                                    <p class="table-name">Новый Коралловый Риф</p>
                                    <p class="table-rating">2203</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">3</p>
                                    <p class="table-name">Морская жемчужина</p>
                                    <p class="table-rating">2100</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">4</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">2003</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">5</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">1503</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">6</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">1203</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">7</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">253</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">8</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">250</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">9</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">203</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">10</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">03</p>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="rating-block__item rating-block__item-assessment">
                        <div class="rating-block__item__header">
                            <span class="rating-block-icon-man"></span>
                            <h3 class="rating-block__item__caption">Народный рейтинг коттеджных городков</h3>
                        </div>
                        <div class="rating-block__table">
                            <div class="rating-block__table__caption">
                                <p class="table-number">№</p>
                                <p class="table-name">Название</p>
                                <p class="table-calc">Кол-во</p>
                                <p class="table-rating">Оценка</p>
                            </div>
                            <div class="wrapper">
                                <div class="rating-block__table__item">
                                    <p class="table-number">1</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">2</p>
                                    <p class="table-name">Новый Коралловый Риф</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.2</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">3</p>
                                    <p class="table-name">Морская жемчужина</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">4</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">5</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">6</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">100</p>
                                    <p class="table-rating">4.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">7</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">3.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">8</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">9</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">10</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.3</p>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="rating-block__item rating-block__item-assessment">
                        <div class="rating-block__item__header">
                            <span class="rating-block-icon-man"></span>
                            <h3 class="rating-block__item__caption">Народный рейтинг новостроек</h3>
                        </div>
                        <div class="rating-block__table">
                            <div class="rating-block__table__caption">
                                <p class="table-number">№</p>
                                <p class="table-name">Название</p>
                                <p class="table-calc">Кол-во</p>
                                <p class="table-rating">Оценка</p>
                            </div>
                            <div class="wrapper">
                                <div class="rating-block__table__item">
                                    <p class="table-number">1</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">2</p>
                                    <p class="table-name">Новый Коралловый Риф</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.2</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">3</p>
                                    <p class="table-name">Морская жемчужина</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">4</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">5</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">6</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">100</p>
                                    <p class="table-rating">4.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">7</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">3.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">8</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">9</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">10</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.3</p>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="js-drop-item rating-drop__wrapper">
                <button class="rating__button-mobile js-drop-button">
                    <span class="rating-icon"></span>
                    <span>Статистика недвижимости</span>
                    <span class="icon-drop"></span>
                </button>
                <h2 class="main-caption-l rating-caption"><span class="rating-icon"></span>Статистика недвижимости</h2>
                <ul class="rating-block__list rating-block__list-diagram">
                    <li class="rating-block__item">
                        <div class="rating-block__item__header">
                            <span class="rating-block-icon-diagram"></span>
                            <h3 class="rating-block__item__caption">Статистика (грн) - Новостройка</h3>
                        </div>
                        <div class="rating-block__table">
                            <div class="wrapper">
                                <div class="rating-block__general-info">
                                    <p class="name">Украина</p>
                                    <p class="date">01.2020 - <span>15925</span></p>
                                    <p class="date">02.2020 - <span>v 15915</span></p>
                                </div>
                                <div class="rating-block__general-info">
                                    <p class="name">Киев</p>
                                    <p class="date">01.2020 - <span>15925</span></p>
                                    <p class="date">02.2020 - <span>v 15915</span></p>
                                </div>
                            </div>
                            <div class="rating-block__table__caption">
                                <p class="table-number">№</p>
                                <p class="table-area">Район</p>
                                <p class="table-date">01.2020</p>
                                <p class="table-date">02.2020</p>
                            </div>
                            <div class="wrapper">
                                <div class="rating-block__table__item">
                                    <p class="table-number">1</p>
                                    <p class="table-name">Одесская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">2</p>
                                    <p class="table-name">Харьковская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">3</p>
                                    <p class="table-name">Запорожская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">4</p>
                                    <p class="table-name">Киевская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">5</p>
                                    <p class="table-name">Черновицкая</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">6</p>
                                    <p class="table-name">Херсонская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">7</p>
                                    <p class="table-name">Ивано-Франковская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">8</p>
                                    <p class="table-name">Закарпатская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">9</p>
                                    <p class="table-name">Львовская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">10</p>
                                    <p class="table-name">Николаевская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="rating-block__item">
                        <div class="rating-block__item__header">
                            <span class="rating-block-icon-diagram"></span>
                            <h3 class="rating-block__item__caption">Статистика (грн) - Коттеджи</h3>
                        </div>
                        <div class="rating-block__table">
                            <div class="wrapper">
                                <div class="rating-block__general-info">
                                    <p class="name">Украина</p>
                                    <p class="date">01.2020 - <span>15925</span></p>
                                    <p class="date">02.2020 - <span>v 15915</span></p>
                                </div>
                                <div class="rating-block__general-info">
                                    <p class="name">Киев</p>
                                    <p class="date">01.2020 - <span>15925</span></p>
                                    <p class="date">02.2020 - <span>v 15915</span></p>
                                </div>
                            </div>
                            <div class="rating-block__table__caption">
                                <p class="table-number">№</p>
                                <p class="table-area">Район</p>
                                <p class="table-date">01.2020</p>
                                <p class="table-date">02.2020</p>
                            </div>
                            <div class="wrapper">
                                <div class="rating-block__table__item">
                                    <p class="table-number">1</p>
                                    <p class="table-name">Одесская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">2</p>
                                    <p class="table-name">Харьковская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">3</p>
                                    <p class="table-name">Запорожская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">4</p>
                                    <p class="table-name">Киевская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">5</p>
                                    <p class="table-name">Черновицкая</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">6</p>
                                    <p class="table-name">Херсонская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">7</p>
                                    <p class="table-name">Ивано-Франковская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">8</p>
                                    <p class="table-name">Закарпатская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">9</p>
                                    <p class="table-name">Львовская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">10</p>
                                    <p class="table-name">Николаевская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <button class="main-button-more">Показать все области</button>
        </div>
    </section> -->
    <section class="statistic" v-lazy:background-image="'{{ url('/image/bg-zagorodna-in-numbers.jpg?w=1425&h=501&fm=pjpg&q=75') }}'">
        <div class="statistic__wrapper container">
            <h2 class="main-caption-l">{{ $page->numbers_title }}</h2>
            <ul class="statistic__list">
                @foreach(json_decode($page->numbers_content) as $key => $item)
                <li class="statistic__item">
                    <p class="statistic__item__number">{{ $numbers[$key] }}</p>
                    <p class="statistic__item__text">{{ $item->text }}</p>
                    <div class="statistic__item__img" style="background-image: url({{ url('img/zagorodna-number-img-' . ($key + 1) . '.svg') }});"></div>
                </li>
                @endforeach
            </ul>
        </div>
    </section>
    <section class="seo-block">
        <div class="seo-block__wrapper container">
            <h2 class="main-caption-l">{{ $page->seo_title }}</h2>
            <div class="seo-block__content">
                <div class="wrapper">
                  {!! $page->seo_text !!}
                </div>

            </div>
        </div>

    </section>
</main>
@endsection

@push('styles')
<style>
    .main-slider__item[lazy="loading"] {
        background-image: none !important;
    }
    .statistic[lazy="loading"] {
        background-image: none !important;
    }
</style>
@endpush

@push('scripts')
<script>
    var cottages_slug = @json($cottages_slug);
    var newbuilds_slug = @json($newbuilds_slug);
    var reviews = @json($reviews);
    var regions = @json($regions);
    var range_options = @json($range_options);
</script>
<script src="{{ url('js/index/index.js?v=' . $version) }}"></script>

<script>
	//console.log(lang)
	//let newsCategoryLinkWithLang = newsCategoryLink;
</script>
@endpush
