@extends('layouts.app', [
'meta_title' => __('main.Сравнение ЖК'),
'meta_desc' => __('main.Сравнение ЖК'),
])

@section('content')

<main>
    <div class="decor-background" style="background-image:url({{ url('img/background-img-2.png') }})"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
            {{ Breadcrumbs::render('page', __('main.Сравнение ЖК')) }}
        </div>
    </section>
    <section class="comparison">
        <div class="general-heading container">
            <h1 class="main-caption-l">{{ __('main.Сравнение ЖК') }}</h1>
            <div class="general-drop general-top__drop js-drop-item">
                <button type="button" class="general-top__drop__button js-drop-button general-drop__text"> 
                    <span class="text">{{ __('main.Параметры сравнения') }}</span>
                    <span class="icon-drop"></span>
                </button>
                <div class="general-drop__wrapper">
                    <ul class="general-drop__list">
                        <li class="general-drop__item">
                            <label class="checkbox__wrapper">
                                <input type="checkbox" class="input-checkbox" v-model="enabledParameters" value="1">
                                <span class="custome-checkbox">
                                    <span class='icon-active'></span>
                                </span>
                                <span class="checkbox-text">{{ __('main.Общее') }}</span>
                            </label>
                        </li>
                        <li class="general-drop__item">
                            <label class="checkbox__wrapper">
                                <input type="checkbox" class="input-checkbox" v-model="enabledParameters" value="2">
                                <span class="custome-checkbox">
                                    <span class='icon-active'></span>
                                </span>
                                <span class="checkbox-text">{{ __('main.Местоположение') }}</span>
                            </label>
                        </li>
                        <li class="general-drop__item">
                            <label class="checkbox__wrapper">
                                <input type="checkbox" class="input-checkbox" v-model="enabledParameters" value="3">
                                <span class="custome-checkbox">
                                    <span class='icon-active'></span>
                                </span>
                                <span class="checkbox-text">{{ __('main.Цены') }}</span>
                            </label>
                        </li>
                        <li class="general-drop__item">
                            <label class="checkbox__wrapper">
                                <input type="checkbox" class="input-checkbox" v-model="enabledParameters" value="4">
                                <span class="custome-checkbox">
                                    <span class='icon-active'></span>
                                </span>
                                <span class="checkbox-text">{{ __('main.Застройщик') }}</span>
                            </label>
                        </li>
                        <!-- <li class="general-drop__item">
                            <label class="checkbox__wrapper">
                                <input type="checkbox" class="input-checkbox" v-model="enabledParameters" value="5">
                                <span class="custome-checkbox">
                                    <span class='icon-active'></span>
                                </span>
                                <span class="checkbox-text">{{ __('main.Ход строительства') }}</span>
                            </label>
                        </li> -->
                        <!-- <li class="general-drop__item">
                            <label class="checkbox__wrapper">
                                <input type="checkbox" class="input-checkbox" v-model="enabledParameters" value="6">
                                <span class="custome-checkbox">
                                    <span class='icon-active'></span>
                                </span>
                                <span class="checkbox-text">{{ __('main.Документы') }}</span>
                            </label>
                        </li> -->
                        <li class="general-drop__item">
                            <label class="checkbox__wrapper">
                                <input type="checkbox" class="input-checkbox" v-model="enabledParameters" value="7">
                                <span class="custome-checkbox">
                                    <span class='icon-active'></span>
                                </span>
                                <span class="checkbox-text">{{ __('main.Характеристики') }}</span>
                            </label>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="comparison__wrapper">
            <div class="comparison__mobile__buttons popup" data-target="comparison-button">
                <div class="wrapper">
                    <a href="tel: 333" class="comparison__button comparison__button-call-back">
                        <span class="icon-phone"></span>
                    </a>
                    <button class="comparison__button comparison__button-write js-button" data-target="questions">
                        <span class="icon-message"></span>
                    </button>
                    <button class="comparison__button comparison__button-meeting js-button" data-target="meeting">
                        <span class="icon-calendar"></span>
                    </button>
                    <button class="comparison__button comparison__button-buy js-button" data-target="help">
                        <span class="icon-key"></span>
                    </button>
                    <button class="comparison__button general-noty__button general-noty__button-sing-up active">
                        <span class="icon-bell-outline"></span>
                    </button>
                    <button class="comparison__button general-noty__button general-noty__button-favorite">
                        <span class="icon-heart-outline"></span>
                    </button>
                </div>
            </div>
            <div class="comparison__header js-comparison-header">
                <div class="comparison__header__container js-comparison-container">
                    <div class="comparison__header__wrapper js-comparison-wrapper-product">
                        <div class="comparison__header-add-new">
                            <a href="{{ $catalog_link }}">
                                <div class="decor">
                                    <p class="plus">
                                        <span class="icon-plus"></span>
                                    </p>
                                </div>
                                <p>{{ __('main.Добавить еще') }}</p>
                            </a>
                        </div>
                        <ul class="comparison__product__list js-mousewheel" v-cloak>
                            <li class="comparison__product__item js-mousewheel-item" v-for="(item, key) in items.data">
                                <div class="comparison__product__item-body" :style='{backgroundImage: "url(" + item.image + ")"}'>
                                    <button class="close-popup comparison__delete" @click="removeItem(key)" title="{{ __('main.Удалить') }}">
                                        <span class="decor"></span>
                                    </button>
                                    <div class="general-noty__buttons-container">
                                        <button class="general-noty__button general-noty__button-sing-up" @click="addToNotifications(item, 'products')" :class="{active: notifications['products'].includes(item.id) || notifications['products'].includes(item.original_id)}" title="{{ __('main.Добавить в уведомления') }}">
                                            <span class="icon-bell-outline"></span>
                                        </button>
                                        <button class="general-noty__button general-noty__button-favorite" @click="addToFavorites(item, 'products')" :class="{active: favorites['products'].includes(item.id) || favorites['products'].includes(item.original_id)}" title="{{ __('main.Добавить в избранное') }}">
                                            <span class="icon-heart-outline"></span>
                                        </button>
                                    </div>
                                    <button class="comparison__product__mobile-button js-button" data-target="comparison-button" style="background-image: url(/img/ellipsis.png)"></button>
                                    <div class="comparison__buttons__wrapper">
                                        <div class="comparison__button__container" v-if="item.brand_phone">
                                            <a :href="'tel:' + item.brand_phone" class="comparison__button comparison__button-call-back js-button">
                                                <span class="icon-phone"></span>
                                            </a>
                                            <div class="comparison__button__placeholder">
                                                <p>{{ __('main.Обратный звонок') }}</p>
                                            </div>
                                        </div>
                                        <div class="comparison__button__container">
                                            <button class="comparison__button comparison__button-write js-button" data-target="questions" @click="formText = item.name">
                                                <span class="icon-message"></span>
                                            </button>
                                            <div class="comparison__button__placeholder">
                                                <p>{{ __('main.Написать нам') }}</p>
                                            </div>
                                        </div>
                                        <!-- <div class="comparison__button__container">
                                            <button class="comparison__button comparison__button-meeting js-button" data-target="meeting" @click="formText = item.name">
                                                <span class="icon-calendar"></span>
                                            </button>
                                            <div class="comparison__button__placeholder">
                                                <p>Назначить визит</p>
                                            </div>
                                        </div> -->
                                        <div class="comparison__button__container">
                                            <button class="comparison__button comparison__button-buy js-button" data-target="help" @click="formText = item.name">
                                                <span class="icon-key"></span>
                                            </button>
                                            <div class="comparison__button__placeholder">
                                                <p>{{ __('main.Подобрать') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="comparison__product__item-footer">
                                    <a :href="item.link" class="comparison__product__link">@{{ item.name }}</a>
                                </div>
                            </li>
                        </ul>
                        <button class="general-button prev js-button-scroll-prev disabled">
                            <span class="icon-arrow-left"></span>
                        </button>
                        <button class="general-button next js-button-scroll-next">
                            <span class="icon-arrow-right"></span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="comparison__body js-comparison-body">
                <div class="comparison__filter-table" v-show="enabledParameters.includes(1)">
                    <div class="comparison__filter-table__header">
                        <p>{{ __('main.Общее') }}</p>
                    </div>
                    <div class="comparison__filter-table__body">
                        <div class="wrapper">
                            <h4 class="comparison__filter-table__caption">{{ __('main.Статус строительства') }}</h4>
                            <ul class="comparison__filter-table__list js-mousewheel">
                                <li class="comparison__filter-table__item" v-for="item in items.data">
                                    <p class="product__status" :class="{'completed': item.extras['status'] == 'done', 'build': item.extras['status'] == 'building'}" v-cloak>@{{ item.status_string }}</p>
                                </li>
                            </ul>
                        </div>
                        <div class="wrapper">
                            <h4 class="comparison__filter-table__caption">{{ __('main.Количество квартир') }}</h4>
                            <ul class="comparison__filter-table__list js-mousewheel">
                                <li class="comparison__filter-table__item" v-for="item in items.data">
                                    <p v-cloak>@{{ item.totalItems? item.totalItems : 'н.д.' }}</p>
                                </li>
                            </ul>
                        </div>
                        <!-- <div class="wrapper">
                            <h4 class="comparison__filter-table__caption">{{ __('main.Парковка') }}</h4>
                            <ul class="comparison__filter-table__list js-mousewheel">
                                <li class="comparison__filter-table__item" v-for="item in items.data">
                                    <p>—</p>
                                </li>
                            </ul>
                        </div> -->
                    </div>
                </div>
                <div class="comparison__filter-table" v-show="enabledParameters.includes(2)">
                    <div class="comparison__filter-table__header">
                        <p>{{ __('main.Местоположение') }}</p>
                    </div>
                    <div class="comparison__filter-table__body">
                        <div class="wrapper">
                            <h4 class="comparison__filter-table__caption">{{ __('main.Город') }}</h4>
                            <ul class="comparison__filter-table__list js-mousewheel">
                                <li class="comparison__filter-table__item" v-for="item in items.data">
                                    <p v-cloak>@{{ item.city }}</p>
                                </li>
                            </ul>
                        </div>
                        <div class="wrapper">
                            <h4 class="comparison__filter-table__caption">{{ __('main.Адрес') }}</h4>
                            <ul class="comparison__filter-table__list js-mousewheel">
                                <li class="comparison__filter-table__item" v-for="item in items.data">
                                    <p v-cloak>@{{ item.extras_translatable['address_string'] }}</p>
                                </li>
                            </ul>
                        </div>
                        <div class="wrapper">
                            <h4 class="comparison__filter-table__caption">{{ __('main.Расстояние до центра') }}</h4>
                            <ul class="comparison__filter-table__list js-mousewheel">
                                <li class="comparison__filter-table__item" v-for="item in items.data">
                                    <p v-cloak>@{{ item.extras['distance'] }} км</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="comparison__filter-table" v-show="enabledParameters.includes(3)">
                    <div class="comparison__filter-table__header">
                        <p>{{ __('main.Цены') }}</p>
                    </div>
                    <div class="comparison__filter-table__body">
                        <div class="wrapper">
                            <h4 class="comparison__filter-table__caption">1-к {{ __('main.Квартира') }}</h4>
                            <ul class="comparison__filter-table__list js-mousewheel">
                                <li class="comparison__filter-table__item" v-for="item in items.data">
                                    <p v-if="item.prices[1]" v-cloak>{{ __('main.от') }} @{{ item.prices[1] }} грн</p>
                                    <p v-else>—</p>
                                </li>
                            </ul>
                        </div>
                        <div class="wrapper">
                            <h4 class="comparison__filter-table__caption">2-к {{ __('main.Квартира') }}</h4>
                            <ul class="comparison__filter-table__list js-mousewheel">
                                <li class="comparison__filter-table__item" v-for="item in items.data">
                                    <p v-if="item.prices[2]" v-cloak>{{ __('main.от') }} @{{ item.prices[2] }} грн</p>
                                    <p v-else>—</p>
                                </li>
                            </ul>
                        </div>
                        <div class="wrapper">
                            <h4 class="comparison__filter-table__caption">3-к {{ __('main.Квартира') }}</h4>
                            <ul class="comparison__filter-table__list js-mousewheel">
                                <li class="comparison__filter-table__item" v-for="item in items.data">
                                    <p v-if="item.prices[3]" v-cloak>{{ __('main.от') }} @{{ item.prices[3] }} грн</p>
                                    <p v-else>—</p>
                                </li>
                            </ul>
                        </div>
                        <!-- <div class="wrapper">
                            <h4 class="comparison__filter-table__caption">4-к {{ __('main.Квартира') }}</h4>
                            <ul class="comparison__filter-table__list js-mousewheel">
                                <li class="comparison__filter-table__item" v-for="item in items.data">
                                    <p v-if="item.prices[4]">{{ __('main.от') }} @{{ item.prices[4] }} грн</p>
                                    <p v-else>—</p>
                                </li>
                            </ul>
                        </div> -->
                    </div>
                </div>
                <div class="comparison__filter-table" v-show="enabledParameters.includes(4)">
                    <div class="comparison__filter-table__header">
                        <p>{{ __('main.Застройщик') }}</p>
                    </div>
                    <div class="comparison__filter-table__body">
                        <div class="wrapper">
                            <h4 class="comparison__filter-table__caption">{{ __('main.Название') }}</h4>
                            <ul class="comparison__filter-table__list js-mousewheel">
                                <li class="comparison__filter-table__item" v-for="item in items.data">
                                    <a :href="item.brand_link" v-cloak>@{{ item.brand_name? item.brand_name : 'н.д' }}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="wrapper">
                            <h4 class="comparison__filter-table__caption">{{ __('main.Сайт') }}</h4>
                            <ul class="comparison__filter-table__list js-mousewheel">
                                <li class="comparison__filter-table__item" v-for="item in items.data">
                                    <a :href="item.brand_site" target="_blank" v-cloak>@{{ item.brand_site? item.brand_site : 'н.д.' }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- <div class="comparison__filter-table" v-show="enabledParameters.includes(5)">
                    <div class="comparison__filter-table__header">
                        <p>{{ __('main.Ход строительства') }}</p>
                    </div>
                    <div class="comparison__filter-table__body">
                        <div class="wrapper">
                            <h4 class="comparison__filter-table__caption">Город</h4>
                            <ul class="comparison__filter-table__list js-mousewheel">
                                <li class="comparison__filter-table__item">
                                    <p class="product__status completed">Завершен</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p class="product__status completed">Завершен</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p class="product__status completed">Завершен</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p class="product__status completed">Завершен</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p class="product__status build">Строится</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p class="product__status build">Строится</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p class="product__status completed">Завершен</p>
                                </li>
                            </ul>
                        </div>
                        <div class="wrapper">
                            <h4 class="comparison__filter-table__caption">Адрес</h4>
                            <ul class="comparison__filter-table__list js-mousewheel">
                                <li class="comparison__filter-table__item">
                                    <p>400</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p>400</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p>400</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p>400</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p>300</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p>300</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p>200</p>
                                </li>
                            </ul>
                        </div>
                        <div class="wrapper">
                            <h4 class="comparison__filter-table__caption">Расстояние до центра</h4>
                            <ul class="comparison__filter-table__list js-mousewheel">
                                <li class="comparison__filter-table__item">
                                    <p>—</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p>—</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p>—</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p>—</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p>500 м</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p>500 м</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p>Рядом</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div> -->
                <!-- <div class="comparison__filter-table" v-show="enabledParameters.includes(6)">
                    <div class="comparison__filter-table__header">
                        <p>{{ __('main.Документы') }}</p>
                    </div>
                    <div class="comparison__filter-table__body">
                        <div class="wrapper">
                            <h4 class="comparison__filter-table__caption">Город</h4>
                            <ul class="comparison__filter-table__list js-mousewheel">
                                <li class="comparison__filter-table__item">
                                    <p class="product__status completed">Завершен</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p class="product__status completed">Завершен</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p class="product__status completed">Завершен</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p class="product__status completed">Завершен</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p class="product__status build">Строится</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p class="product__status build">Строится</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p class="product__status completed">Завершен</p>
                                </li>
                            </ul>
                        </div>
                        <div class="wrapper">
                            <h4 class="comparison__filter-table__caption">Адрес</h4>
                            <ul class="comparison__filter-table__list js-mousewheel">
                                <li class="comparison__filter-table__item">
                                    <p>400</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p>400</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p>400</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p>400</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p>300</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p>300</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p>200</p>
                                </li>
                            </ul>
                        </div>
                        <div class="wrapper">
                            <h4 class="comparison__filter-table__caption">Расстояние до центра</h4>
                            <ul class="comparison__filter-table__list js-mousewheel">
                                <li class="comparison__filter-table__item">
                                    <p>—</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p>—</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p>—</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p>—</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p>500 м</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p>500 м</p>
                                </li>
                                <li class="comparison__filter-table__item">
                                    <p>Рядом</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div> -->
                <div class="comparison__filter-table" v-show="enabledParameters.includes(7)">
                    <div class="comparison__filter-table__header">
                        <p>{{ __('main.Характеристики') }}</p>
                    </div>
                    <div class="comparison__filter-table__body">
                        <div class="wrapper">
                            <h4 class="comparison__filter-table__caption">{{ __('main.Материал стен') }}</h4>
                            <ul class="comparison__filter-table__list js-mousewheel">
                                <li class="comparison__filter-table__item" v-for="item in items.data">
                                    <p class="product__status" v-cloak>@{{ item.wall_material }}</p>
                                </li>
                            </ul>
                        </div>
                        <div class="wrapper">
                            <h4 class="comparison__filter-table__caption">{{ __('main.Коммуникации') }}</h4>
                            <ul class="comparison__filter-table__list js-mousewheel">
                                <li class="comparison__filter-table__item" v-for="item in items.data">
                                    <p v-cloak>@{{ item.communications }}</p>
                                </li>
                            </ul>
                        </div>
                        <div class="wrapper">
                            <h4 class="comparison__filter-table__caption">{{ __('main.Инфраструктура') }}</h4>
                            <ul class="comparison__filter-table__list js-mousewheel">
                                <li class="comparison__filter-table__item" v-for="item in items.data">
                                    <p class="product__status" v-cloak>@{{ item.infrastructure }}</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="product product-comparison" v-if="recent.data.length">
        <div class="product__wrapper slider-infinity">
            <div class="general-heading container">
                <h2 class="main-caption-l main-caption-l--transform">{{ __('main.Последние просмотренные') }}</h2>
            </div>

            <ul class="product__list product-slider__list js-infinity-slider-list">
                <productcard v-for="(product, key) in recent.data" :key="key" :data-product="product" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'" @add-to-favorites="addToFavorites" @add-to-comparison="addToComparison"></productcard>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity general-button--hide container">
                <div class="wrapper" :class="{hide: recent.length < 5}">
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
    <section class="popup @error('question_email') active @enderror" data-target="questions">
        <div class="popup__wrapper popup-call-back">
            <button class="close-popup js-close close-popup-inner">
                <span class="decor"></span>
            </button>
            <h5 class="popup-caption">{{ __('main.Задайте ваш вопрос по наличию и ценам') }}</h5>
            <form action="{{ url('feedback/create/question') }}" method="post" class="popup-call-back__form">
                @csrf
                <label class="input__wrapper">
                    <span class="input__caption">{{ __('main.Имя') }}</span>
                    <input type="text" class="main-input" placeholder="{{ __('forms.placeholders.Как к вам обращаться?') }}" name="question_name" value="{{ old('question_name') }}">
                    @error('question_name')
                        <span class="error-text" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
                </label>
                <label class="input__wrapper">
                    <span class="input__caption">{{ __('main.Контактный телефон') }}*</span>
                    <input type="tel" class="main-input" placeholder="{{ __('forms.placeholders.Номер телефона') }}" name="question_phone" value="{{ old('question_phone') }}">
                    @error('question_phone')
                        <span class="error-text" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
                </label>
                <label class="input__wrapper">
                    <span class="input__caption">Email*</span>
                    <input type="email" class="main-input" placeholder="{{ __('forms.placeholders.Ваш электронный адрес') }}" name="question_email" value="{{ old('question_email') }}">
                    @error('question_email')
                        <span class="error-text" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
                </label>
                <label class="textarea__wrapper">
                    <span class="input__caption">{{ __('main.Какой проект вам нужен?') }}</span>
                    <textarea class="main-textarea" name="question_text" v-model="formText"></textarea>
                </label>
                <button class="main-button">{{ __('main.Отправить') }}</button>
            </form>
        </div>
    </section>
    <section class="popup @error('selection_email') active @enderror" data-target="help">
        <div class="popup__wrapper popup-call-back">
            <button class="close-popup js-close close-popup-inner">
                <span class="decor"></span>
            </button>
            <h5 class="popup-caption">{{ __('main.Поможем подобрать дом в нашем городке') }}</h5>
            <form action="{{ url('feedback/create/selection') }}" method="post" class="popup-call-back__form">
                @csrf
                <label class="input__wrapper">
                    <span class="input__caption">{{ __('main.Имя') }}</span>
                    <input type="text" class="main-input" placeholder="{{ __('forms.placeholders.Как к вам обращаться?') }}" name="selection_name" value="{{ old('selection_name') }}">
                    @error('selection_name')
                        <span class="error-text" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
                </label>
                <label class="input__wrapper">
                    <span class="input__caption">{{ __('main.Контактный телефон') }}*</span>
                    <input type="tel" class="main-input" placeholder="{{ __('forms.placeholders.Номер телефона') }}" name="selection_phone" value="{{ old('selection_phone') }}">
                    @error('selection_phone')
                        <span class="error-text" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
                </label>
                <label class="input__wrapper">
                    <span class="input__caption">Email*</span>
                    <input type="email" class="main-input" placeholder="{{ __('forms.placeholders.Ваш электронный адрес') }}" name="selection_email" value="{{ old('selection_email') }}">
                    @error('selection_email')
                        <span class="error-text" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
                </label>
                <label class="textarea__wrapper">
                    <span class="input__caption">{{ __('main.Какой проект вам нужен?') }}</span>
                    <textarea class="main-textarea" v-model="formText" name="selection_text"></textarea>
                </label>
                <button class="main-button">{{ __('main.Отправить') }}</button>
            </form>
        </div>
    </section>
    <section class="popup @error('visit_email') active @enderror" data-target="meeting">
        <div class="popup__wrapper popup-call-back">
            <button class="close-popup js-close close-popup-inner">
                <span class="decor"></span>
            </button>
            <h5 class="popup-caption">{{ __('main.Назначить визит в отдел продаж') }}</h5>
            <form action="{{ url('feedback/create/visit') }}" method="post" class="popup-call-back__form">
                @csrf
                <label class="input__wrapper">
                    <span class="input__caption">{{ __('main.Имя') }}</span>
                    <input type="text" class="main-input" placeholder="{{ __('forms.placeholders.Как к вам обращаться?') }}" name="visit_name" value="{{ old('visit_name') }}">
                    @error('visit_name')
                        <span class="error-text" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
                </label>
                <div class="popup-call-back__meeting-wrapper">
                    <label class="input__wrapper">
                        <span class="input__caption">{{ __('main.Контактный телефон') }}*</span>
                        <input type="tel" class="main-input" placeholder="{{ __('forms.placeholders.Номер телефона') }}" name="visit_phone" value="{{ old('visit_phone') }}">
                        @error('visit_phone')
                            <span class="error-text" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </label>
                    <label class="input__wrapper">
                        <span class="input__caption">Email*</span>
                        <input type="email" class="main-input" placeholder="{{ __('forms.placeholders.Ваш электронный адрес') }}" name="visit_email" value="{{ old('visit_email') }}">
                        @error('visit_email')
                            <span class="error-text" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </label>
                </div>
                <label class="input__wrapper">
                    <span class="input__caption">{{ __('main.Дата визита') }}*</span>
                    <input type="date" class="main-input" name="visit_extras[Дата визита]" value="{{ old('visit_extras')['Дата визита'] }}" placeholder="{{ __('main.Удобная вам дата визита') }}">
                </label>
                <label class="textarea__wrapper">
                    <span class="input__caption">{{ __('main.Какой проект вам нужен?') }}</span>
                    <textarea class="main-textarea" v-model="formText" name="visit_text"></textarea>
                </label>
                <button class="main-button">{{ __('main.Отправить') }}</button>
            </form>
        </div>
    </section>
</main>

@endsection

@push('scripts')
<script>
    function scrollBloks() {
        let buttonNext = document.querySelector('.js-button-scroll-next');
        let buttonPrev = document.querySelector('.js-button-scroll-prev');
        
        let allScrollsItems = document.querySelectorAll(".js-mousewheel");
        let widthContainer = document.querySelector('.js-mousewheel').offsetWidth;
        let productItem = document.querySelector('.js-mousewheel-item');
        let productItemAll = document.querySelectorAll('.js-mousewheel-item');
        
        let style = productItem.currentStyle || window.getComputedStyle(productItem);
        let marginItem = style.marginRight.replace(/[^\d]/g, "");
        let fullWidthElem = productItem.offsetWidth + +marginItem;
        let maxScrollLeft = (fullWidthElem * productItemAll.length) - widthContainer;

        if(maxScrollLeft <= 0) {
            buttonNext.classList.add('disabled');
        }else {
            buttonNext.classList.remove('disabled');
        }
        
        buttonNext.addEventListener('click', function(){
            buttonPrev.classList.remove('disabled');
            
            allScrollsItems.forEach((item) => {
                if(item.scrollLeft < maxScrollLeft) {
                    item.scrollLeft += fullWidthElem;

                    if(!(item.scrollLeft < (maxScrollLeft - fullWidthElem))){
                        buttonNext.classList.add('disabled');
                    }
                }
            });
        });
        
        buttonPrev.addEventListener('click', function(){
            buttonNext.classList.remove('disabled');
            
            allScrollsItems.forEach((item) => {
                item.scrollLeft -= fullWidthElem;
                
                if(item.scrollLeft < 100) {
                    buttonPrev.classList.add('disabled');
                }
            });
        });
    }
    
document.addEventListener("DOMContentLoaded", function(){
    // Scroll comparison
    
    var startPointX;
    var valueScroll = 0;
    var startPointXMover
    var itemScroll = document.querySelectorAll('.js-mousewheel');
    
    itemScroll.forEach((item) => {
        
        item.addEventListener("touchstart", function(event) {
            startPointX = event.changedTouches[0].screenX;
        }, false);

        
        item.addEventListener("touchmove", function(event) {
            startPointXMover = event.changedTouches[0].screenX;
            
            itemScroll.forEach((allItem) => {
                event.preventDefault();
                
                allItem.scrollLeft = startPointX - startPointXMover + valueScroll;
            });
        }, false);
        
        item.addEventListener("touchend", function(event) {
            valueScroll += (startPointX - startPointXMover);
        }, false)
        
    });
    
    // scrollBloks();
        
    function showProductList() {
        let stikyBlockHeight = document.querySelector('.js-comparison-wrapper-product').offsetHeight;
        let stikyBlock = document.querySelector('.js-comparison-wrapper-product');
        let hideBlock = document.querySelector('.js-comparison-header');
        let comparisonHeaderContainer = document.querySelector('.js-comparison-container');
        let comparisonBodyBottom = document.querySelector('.js-comparison-body').getBoundingClientRect().bottom;
        // hideBlock.style.height = stikyBlockHeight + "px";
        
        if(comparisonBodyBottom < stikyBlockHeight) {
            stikyBlock.setAttribute('style', 'position: absolute');
            comparisonHeaderContainer.setAttribute('style', `position: absolute; bottom: ${stikyBlockHeight}px; width: 100%; left: 0px;`);
            return;
        }else {
            stikyBlock.setAttribute('style', 'position: fixed');
            comparisonHeaderContainer.setAttribute('style', 'position: static');
        }
        
        if(hideBlock.getBoundingClientRect().top < -40) {
            // hideBlock.style.height = stikyBlockHeight + "px";
            stikyBlock.setAttribute('style', 'position: fixed');
            stikyBlock.classList.add("fixed");
        }else {
            // hideBlock.style.height = stikyBlockHeight + "px";
            stikyBlock.setAttribute('style', 'position: absolute');
            stikyBlock.classList.remove("fixed");
        }
    }
    
    window.addEventListener("resize",function(){
        showProductList();
        scrollBloks();
    });
    
    showProductList()
    
    document.addEventListener("scroll",function(){
        showProductList()
    });
        
});
</script>
<script>
    var formText = "{{ old('question_text')? old('question_text') : (old('visit_text')? old('visit_text') : old('selection_text')) }}";
</script>
<script src="{{ url('js/comparison/comparison.js?v=' . $version) }}"></script>
@endpush