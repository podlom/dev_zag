<!DOCTYPE html>
<html lang="{{ $lang }}">
<head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @php
    $skip_last = $skip_last ?? false;
    $meta_title = isset($meta_title) && $meta_title? ($skip_last? $meta_title: $meta_title . ' – Zagorodna.com') : 'Zagorodna.com';
    $og_title = isset($h1)? $h1 : (isset($meta_title) && $meta_title? $meta_title : 'Zagorodna.com');
    $meta_desc = $meta_desc ?? 'Zagorodna';
    if(request()->page > 1) {
        $meta_title .= ' - ' . __('main.страница') . ' ' . request()->page;
        $og_title .= ' ➨ ' . __('main.страница') . ' ' . request()->page;
        $meta_desc .= ' ➨ ' . __('main.Заходите') . '! ' . mb_ucfirst(__('main.страница')) . ' ' . request()->page;
    }
    @endphp

    <title>{{ $meta_title }}</title>
    <meta name="title" property="og:title" content="{{ $og_title }}">
    <meta name="description" property="og:description" content="{{ $meta_desc }}">
    <meta property="og:url" content="{{ request()->getUri() }}">
    <meta property="og:locale" content="{{ $lang }}">
    <meta property="og:site_name" content="Zagorodna.com">
    @if(isset($hide_from_index) && $hide_from_index)
    <meta name="robots" content="noindex,nofollow">
    @endif
    @if(isset($og_image) && $og_image)
    <meta property="og:image" content="{{ $og_image }}">
    <meta name="twitter:image" content="{{ $og_image }}">
    @endif
    @if(isset($og_type) && $og_type)
    <meta property="og:type" content="{{ $og_type }}">
    @else
    <meta property="og:type" content="article">
    @endif

    @if(strpos(request()->getUri(), '/catalog') === false)

        <link rel="alternate" hreflang="{{ $lang }}" href="{{ request()->getUri() }}" />
        @if(request()->getUri() === url('uk'))
            <link rel="alternate" hreflang="{{ $lang === 'ru'? 'uk' : 'ru' }}" href="{{ url('') }}" />
        @elseif(request()->getUri() === url('/') . '/')
            <link rel="alternate" hreflang="{{ $lang === 'ru'? 'uk' : 'ru' }}" href="{{ url('uk') }}" />
        @elseif(!isset($translation_link) || $translation_link === url('uk'))
        @elseif(isset($translation_link))
            <link rel="alternate" hreflang="{{ $lang === 'ru'? 'uk' : 'ru' }}" href="{{ $translation_link }}" />
        @elseif($lang != 'ru')
            <link rel="alternate" hreflang="{{ $lang === 'ru'? 'uk' : 'ru' }}" href="{{ url( 'ru/'.substr(\Request::path(),3)) }}" />
        @else
            <link rel="alternate" hreflang="{{ $lang === 'ru'? 'uk' : 'ru' }}" href="{{ url( 'uk/'.substr(\Request::path(),3)) }}" />
        @endif

    @endif

    @if(!empty($canonical))
        <link rel="canonical" href="{{ $canonical }}" />
    @else
        <link rel="canonical" href="{{ url()->current() }}" />
    @endif

    <link rel="shortcut icon" href="{{ url('files/favicon.jpg') }}" type="image/x-icon">
    <style>
        .preloader {
            position: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            top: 0;
            left: 0;
            z-index: 100;
            height: 100%;
            width: 100%;
            background-color: #ffffff;
            overflow: hidden;
            animation-delay: 1s;
            opacity: 1;
            transition: opacity .4s, width .4s 1.4s, height .4s 1.4s;
        }
        .preloader.hide {
            opacity: 0;
            width: 0px;
            height: 0px;
        }
        .footer__sub-button::before, .category__sub-button::before {
            content: "{{ __('main.показать все') }}"
        }
        .product-page__characteristic-wrapper .main-button-more::before {
            content: "{{ __('main.Показать все характеристики') }}";
        }
        .category-links__item.active .category__sub-button::before, .footer__category__item.active .footer__sub-button::before {
            content: "{{ __('main.Свернуть') }}";
        }
        .product-page__characteristic-wrapper.active .main-button-more::before {
            content: "{{ __('main.Скрыть все характеристики') }}";
        }
        .filter-check .general-filter__caption::before {
            content: "{{ __('main.Новостройки') }}";
        }
        .filter-check__input:checked + .general-filter__caption::before {
            content: "{{ __('main.Коттеджи') }}";
        }
        .filter-check_application .general-filter__caption::before {
            content: "Офлайн";
        }
        .filter-check__input_application:checked + .general-filter__caption::before {
            content: "Онлайн";
        }
        .reviews-page .general-heading .main-button::before, .product-page__reviews-header .main-button::before {
            content: "{{ __('main.Добавить отзыв') }}";
        }
        .reviews-page .general-heading .main-button.active::before, .product-page__reviews-header .main-button.active::before {
            content: "{{ __('main.Отмена') }}";
        }
    </style>
    <!-- Logo styles -->
    <style>
        .header__logo .st0{fill-rule:evenodd;clip-rule:evenodd;fill:#296799;}
        .header__logo .st1{fill-rule:evenodd;clip-rule:evenodd;fill:#206195;}
        .header__logo .st2{fill-rule:evenodd;clip-rule:evenodd;fill:#11568E;}
        .header__logo .st3{fill-rule:evenodd;clip-rule:evenodd;fill:#7E4918;}
        .header__logo .st4{fill-rule:evenodd;clip-rule:evenodd;fill:#3E7B2A;}
        .header__logo .st5{fill-rule:evenodd;clip-rule:evenodd;fill:#639453;}
        .header__logo .st6{fill-rule:evenodd;clip-rule:evenodd;fill:#EB8F0F;}
        .header__logo .st7{fill-rule:evenodd;clip-rule:evenodd;fill:#4A0F10;}
        .header__logo .st8{fill-rule:evenodd;clip-rule:evenodd;fill:#FFFFFF;}
    </style>
    <!-- /Logo styles -->
    <style>.header__icon-3 .cls-1{fill:none;stroke:#949494;stroke-linecap:round;stroke-linejoin:round;stroke-width:2px;}</style>
    <script>

/*
		document.addEventListener("DOMContentLoaded", () => {
		    let preloader = document.querySelector(".preloader");
            preloader.classList.add('hide');


            function ReLoadImages(){

	            let images = document.querySelectorAll('img[data-lazysrc]');
	            images.forEach(function(image) {
                    let url = image.getAttribute('data-lazysrc');
                    image.setAttribute('src', url);
	            });

	            let imageBackground = document.querySelectorAll('*[data-lazybg]');
	            imageBackground.forEach(function(image) {
                    let url = image.getAttribute('data-lazybg');
                    image.setAttribute('style', url);
                    //image.classList.remove('js-background');
	            });

	        }
	        document.addEventListener('readystatechange', event => {
	            if(event.target.readyState === "interactive") {
	                ReLoadImages();
	            }
	        });

	        ReLoadImages();
		  });
*/

/*
		function load() {
		    return new Promise(function(resolve, reject) {
		        window.onload = resolve;
		    });
		}
*/

		window.onload = function() {
			// let preloader = document.querySelector(".preloader");
            // preloader.classList.add('hide');


            function ReLoadImages(){

	            let images = document.querySelectorAll('img[data-lazysrc]');
	            images.forEach(function(image) {
                    let url = image.getAttribute('data-lazysrc');
                    image.setAttribute('src', url);
	            });

	            let imageBackground = document.querySelectorAll('*[data-lazybg]');
	            imageBackground.forEach(function(image) {
                    let url = image.getAttribute('data-lazybg');
                    image.setAttribute('style', url);
	            });

	        }
	        document.addEventListener('readystatechange', event => {
	            if(event.target.readyState === "interactive") {
	                ReLoadImages();
	            }
	        });

	        ReLoadImages();


	        // var l = document.links;
	        // for (let link of l) {
		    //     let segments = link.href.split('/')

		    //     if(segments[3] != 'ru' && segments[3] != 'ru#' && segments[3] != 'uk' && segments[3] != 'uk#'){
		    //     	segments.splice(3,0, '{{ $lang }}')
		    //     	link.href = segments.join('/')
		    //     }
		    // }
	    }
    </script>

    <script async="" src="https://www.googletagmanager.com/gtag/js?id=G-F2BW60YJD0"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-F2BW60YJD0');
    </script>

    <link rel="stylesheet" href="{{ url('css/main.css?v=' . $version) }}">

    <link rel="preload" href="{{ url('fonts/SFUIText-Light.woff2') }}" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="{{ url('fonts/SFUIText-Regular.woff2') }}" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="{{ url('fonts/SFUIText-Bold.woff2') }}" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="{{ url('fonts/Circe-Bold.woff2') }}" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="{{ url('fonts/icomoon.ttf') }}" as="font" type="font/ttf" crossorigin="anonymous">

    <link href="{{ url('/packages/noty/noty.css') }}" rel="stylesheet">
    @stack('header_scripts')
</head>
    @stack('styles')
<body itemscope itemtype="http://schema.org/WebPage">
<div id="app">
<div class="compare__wrapper" v-if="comparison && comparison.length">
    <a href="{{ route($lang . '_comparison') }}" class="button-compare" title="{{ __('main.Сравнение') }}">
        <span class="icon-compare"></span>
    </a>
</div>
<!-- <div class="preloader">
    <img src="{{ url('img/Preloader.gif') }}" aria-hidden="true">
</div> -->
@include('layouts.header')

@yield('content')

@include('layouts.footer')
<section class="popup" data-target="reviews">
    <div class="popup__wrapper popup-reviews">
        <button class="close-popup js-close">
            <span class="decor"></span>
        </button>
        <div class="reviews__item"></div>
    </div>
</section>
<section class="popup" data-target="call-back">
    <div class="popup__wrapper popup-call-back">
        <button class="close-popup js-close close-popup-inner">
            <span class="decor"></span>
        </button>
        <h5 class="popup-caption">Появились вопросы?</h5>
        <form action="#" class="popup-call-back__form">
            <label class="input__wrapper">
                <span class="input__caption">Имя</span>
                <input type="text" class="main-input" placeholder="Как к вам обращаться?">
            </label>
            <label class="input__wrapper">
                <span class="input__caption">Контактный телефон*</span>
                <input type="tel" class="main-input" placeholder="Куда перезвонить?">
            </label>
            <label class="input__wrapper">
                <span class="input__caption">Email*</span>
                <input type="email" class="main-input" placeholder="Ваш электронный адрес" required>
            </label>
            <label class="textarea__wrapper">
                <span class="input__caption">Дополнительная информация</span>
                <textarea class="main-textarea" placeholder="Напишите дополнительно"></textarea>
            </label>
            <button class="main-button">Отправить</button>
        </form>
    </div>
</section>

<section class="popup" data-target="free_selection" id="free_selection">
    <div class="popup__wrapper popup-call-back">
        <button class="close-popup js-close close-popup-inner">
            <span class="decor"></span>
        </button>
        <h5 class="popup-caption" v-if="!selection_done">{{ __('main.Бесплатный подбор недвижимости') }}</h5>
        <h5 class="popup-caption" v-else>{{ __('main.Подобранные варианты') }}</h5>
        <div class="popup-call-back__form">
            <template v-if="!selection_done">

                <div class="input__wrapper">
                    <span class="input__caption">{{ __('main.Тип недвижимости') }}</span>
                    <ul class="catalog__filtration__list">
                        <li class="catalog__filtration__item" :class="{active: selection.type === 'newbuild'}" @click="selection.type = 'newbuild'">
                        <label>
                        {{ __('main.type_newbuild') }}
                        </label></li>
                        <li class="catalog__filtration__item" :class="{active: selection.type === 'cottage'}" @click="selection.type = 'cottage'">
                        <label>
                        {{ __('main.type_cottage') }}
                        </label></li>
                    </ul>
                </div>
                <div class="input__wrapper" v-if="selection.type === 'cottage'">
                    <ul class="catalog__filtration__list">
                        @foreach(__('attributes.cottage_types') as $key => $item)
                        @if($key !== 'Эллинг')
                        <li class="catalog__filtration__item" :class="{active: selection.cottage_type.includes('{{ str_replace('_', ' ', $key) }}')}"><label>
                        <input type="checkbox" v-model="selection.cottage_type" value="{{ str_replace('_', ' ', $key) }}">
                        {{ $item }}
                        </label></li>
                        @endif
                        @endforeach
                    </ul>
                </div>
                <div class="input__wrapper">
                    <span class="input__caption">{{ __('main.Статус строительства') }}</span>
                    <ul class="catalog__filtration__list">
                        <li class="catalog__filtration__item" :class="{active: selection.status.includes('project')}"><label>
                        <input type="checkbox" v-model="selection.status" value="project">
                        {{ __('main.product_statuses.project') }}
                        </label></li>
                        <li class="catalog__filtration__item" :class="{active: selection.status.includes('building')}"><label>
                        <input type="checkbox" v-model="selection.status" value="building">
                        {{ __('main.product_statuses.building') }}
                        </label></li>
                        <li class="catalog__filtration__item" :class="{active: selection.status.includes('done')}"><label>
                        <input type="checkbox" v-model="selection.status" value="done">
                        {{ __('main.product_statuses.done') }}
                        </label></li>
                    </ul>
                </div>
                <div style="position:relative">
                    <span class="input__caption">{{ __('main.Область') }}/{{ __('main.район') }}/{{ mb_strtolower(__('main.Населенный пункт')) }}</span>
                    <label class="input__wrapper">
                        <input type="text" class="main-input js-filter" :value="fullSelectionAddress" placeholder="{{ __('main.Вся Украина') }}" readonly="readonly" style="cursor:pointer;" data-target="selection">
                    </label>
                    <div class="catalog-filter__drop js-filter-drop catalog-drop" data-target="selection" style="left:0;top: 68px;">
                        <div class="wrapper active" :class="{'mobile-active': !selection.region}">
                            <input type="text" placeholder="{{ __('main.Выберите область') }}" v-model="selection_search.region" class="caption">
                            <div class="general-drop__container">
                                <div class="general-drop__wrapper">
                                    <ul class="general-drop__list">
                                        <li class="general-drop__item" :class="{active: !selection.region}" @click="selection.region = ''">
                                            <span>{{ __('main.Вся Украина') }}</span>
                                            <span class="icon-drop"></span>
                                        </li>
                                        <template v-for="(region, key) in selection_regions">
                                            <li class="general-drop__item"  :class="{active: selection.region == key}" @click="selection.region = key" v-if="region.toLowerCase().includes(selection_search.region.toLowerCase())">
                                                <span>@{{ region }}</span>
                                                <span class="icon-drop"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="wrapper" :class="{active: selection.region, 'mobile-active': selection.region && !selection.area && !selection.kyivdistrict}">
                            <input type="text" placeholder="{{ __('main.Выберите район') }}" v-model="selection_search.area" class="caption">
                            <div class="general-drop__container">
                                <div class="general-drop__wrapper">
                                    <ul class="general-drop__list">
                                        <li class="general-drop__item" :class="{active: !selection.area && !selection.kyivdistrict}" @click="selection.area = ''">
                                            <span>{{ __('main.Все районы') }}</span>
                                            <span class="icon-drop"></span>
                                        </li>
                                        <template v-for="(area, key) in selection_areas">
                                            <li class="general-drop__item" :class="{active: selection.area == key || selection.kyivdistrict == key}" @click="selection.region == 29? selection.kyivdistrict = key : selection.area = key" v-if="area.toLowerCase().includes(selection_search.area.toLowerCase())">@{{ area }}</li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="wrapper" :class="{active: selection.area && selection.region != 29, 'mobile-active': selection.area && selection.region != 29}">
                            <input type="text" placeholder="Выберите нас. пункт" v-model="selection_search.city" class="caption">
                            <div class="general-drop__container">
                                <div class="general-drop__wrapper">
                                    <ul class="general-drop__list">
                                        <li class="general-drop__item" :class="{active: !selection.city}" @click="selection.city = ''">
                                            <span>{{ __('main.Все нас пункты') }}</span>
                                            <span class="icon-drop"></span>
                                        </li>
                                        <template v-for="(city, key) in selection_cities">
                                            <li class="general-drop__item" :class="{active: selection.city == key}" @click="selection.city = key" v-if="city.toLowerCase().includes(selection_search.city.toLowerCase())">@{{ city }}</li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="input__wrapper">
                    <span class="input__caption">{{ __('main.Размеры дома') }}, {{ __('main.квартиры') }}, кв.м</span>
                    <input type="number" class="main-input" v-model="selection.size">
                </div>
                <button class="main-button" @click="getSelection()" :class="{disabled: selection_started}">{{ __('main.Подобрать') }}</button>

            </template>
            <template v-else>

            <div class="header__livesearch__body">
                    <ul class="header__livesearch__list" style="height:auto;">
                        <template v-for="item in selection_products">
                            <li class="header__livesearch__item">
                                <a :href="item.link" class="header__livesearch__item__link">
                                    <div class="header__livesearch__item__img" :style="{backgroundImage:'url(' + item.image + ')'}"></div>
                                    <div class="header__livesearch__item-info">
                                        <div class="header__livesearch__item__info__header">
                                            <h4>@{{ item.name }}</h4>
                                        </div>
                                        <div class="header__livesearch__item__info__body">
                                            <p class="name">@{{ item.type }}</p>
                                            <p>@{{ item.brand_name }}</p>
                                        </div>
                                        <div class="header__livesearch__item__info__footer" style="display:flex;justify-content:space-between;">
                                            <p>@{{ item.city }}</p>
                                            <p v-if="item.price">{{ __('main.от') }} @{{ item.price }} грн/@{{ item.area_unit }}</p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        </template>
                    </ul>
                    <div class="header__livesearch__list" style="pointer-events:none;height:auto;" v-if="!selection_products.length"><div class="header__livesearch__item"><div class="header__livesearch__item__link">{{ __('main.По вашему запросу ничего не найдено') }}.</div></div></div>
                </div>

                <button class="main-button" @click="restartSelection()">{{ __('main.Подобрать') }} {{ __('main.еще') }}</button>
            </template>
        </div>
    </div>
</section>
@if(!$allow_cookies)
<div class="cookies" v-if="show_cookies">
    <p>{{ __('main.Используя сервисы') }} <a href="{{ route($lang . '_home') }}">Zagorodna.com</a>, {{ __('main.вы соглашаетесь с') }} <a href="{{ route($lang . '_cookies') }}">{{ __('main.Политикой использования файлов cookie') }}</a>. {{ __('main.Мы используем файлы cookie, необходимые для аналитики, персонализации и рекламы') }}.</p>
    <button @click="allowCookies()">{{ __('main.Принять') }}</button>
</div>
@endif
</div>

<script src="{{ url('/packages/noty/noty.js') }}" type="text/javascript"></script>
<script src="{{ url('/js/main.js?v=1.1') }}" type="text/javascript"></script>
<script type="text/javascript" charset="UTF-8" src="//sinoptik.ua/informers_js.php?title=4&amp;wind=2&amp;cities=303010783&amp;lang=ru" async></script>
<script>
    var lang = @json($lang);
    var from = "{{ __('main.от') }}";
    var read_more = "{{ __('main.Читать далее') }}";
    var read_more_reviews = "{{ __('main.Читать еще отзывы') }}";
    var currentMarkers = [];
    var compare_text = "{{ __('main.Добавить в сравнение') }}";
    var favorite_text = "{{ __('main.Добавить в избранное') }}";
    var regions = @json($regions);
    var max_area = @json($max_area);
</script>
@stack('scripts')

<script>
@if(session('message') && session('type'))
  noty("{{ session('type') }}", "{!! session('message') !!}");
@endif

@if(session('status'))
  noty("success", "{!! session('status') !!}");
@endif

@if($errors->any())
  noty("error", "{{ __('forms.error') }}");
@endif

function noty(type, message){
  new Noty({
    type: type,
    theme: 'push_notification__item',
    text: message + '<button class="close-popup"><span class="decor"></span></button>',
    timeout: 5000,
    container: '.push_notification__list',

  }).show();
}
</script>
<script>
    let copyButton = document.querySelector(".general__social-item.copy a");

    if(copyButton) {
        copyButton.addEventListener('click',function(event){
            let copyText = document.querySelector(".general__social-item.copy input");
            event.preventDefault();
            copyText.select();
            copyText.setSelectionRange(0, 99999); /*For mobile devices*/
            document.execCommand("copy");
            noty("success", "Ссылка скопирована!");
        });
    }
</script>
</body>
</html>
