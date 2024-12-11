@extends('layouts.app', [
  'meta_title' => $meta_title,
  'meta_desc' => $meta_desc,
])

@push('header_scripts')
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "{{ $company->name }}",
        "image": [
            "{{ $company->image? url($company->image) : '' }}"
        ],
        "logo": "{{ $company->logo? url($company->logo) : '' }}",
        "address": "{{ $company->address_string }}",
        "telephone": "{{ explode(', ', $company->phone)[0] }}",
        "url": "{{ $company->link }}"
    }
</script>
@endpush

@section('content')

<main>
    <div class="decor-background" style="background-image:url({{ url('img/background-img-2.png') }})"></div>
    <section class="breadcrumbs breadcrumbs-company">
        <div class="breadcrumbs__wrapper">
            {{ Breadcrumbs::render('company', $company->category, $h1) }}
        </div>
    </section>
    <section class="company-page">
        <div class="company-page__bg" >
            <img src="" v-lazy="'{{ $company->image? url($company->image) : url('/image/company-cover.jpg?q=70&f=pjpg') }}'" alt="{{ $company->name }} фото" title="{{ $company->name }} картинка">
        @if($company->image && isset($company->color) && $company->color)
            <div class="layer" style="background-color:{{ $company->color }}"></div>
        @endif
        </div>
        <div class="company-page__wrapper container">
            <div class="company-page__main">
                <h1 class="company-page__caption">{{ $h1 }}</h1>
                <div class="general-noty__buttons-container">
                    <button class="general-noty__button general-noty__button-favorite" @click="addToFavorites({{ $company }}, 'companies')" :class="{active: favorites['companies'].includes({{ $company->id }}) || favorites['companies'].includes({{ $company->original_id }})}" title="{{ __('main.Добавить в избранное') }}">
                        <span class="icon-heart-outline"></span>
                    </button>
                    @if($company->category_id == 1 || $company->category_id == 18)
                    <button class="general-noty__button general-noty__button-sing-up" @click="addToNotifications({{ $company }}, 'companies')" :class="{active: notifications['companies'].includes({{ $company->id }}) || notifications['products'].includes({{ $company->original_id }})}">
                        <span class="icon-bell-outline"></span>
                    </button>
                    @endif
                </div>
                <div class="company-page__info">
                    <div class="company-page__info__header">
                        <div class="company-page__info__logo">
                            @php
                                if(!$company->logo)
                                    $logo = url('/img/fireplace-circle.svg');
                                elseif(strpos($company->logo, '.svg') !== false)
                                    $logo = url($company->logo);
                                else
                                    $logo = url('common/' . $company->logo . '?w=100');
                            @endphp
                            <img src="{{ $logo }}" alt="{{ $company->name }} логотип фото" title="{{ $company->name }} логотип картинка">
                        </div>
                        <ul class="general__socila-list">
                            @if($company->fb)
                            <li class="general__social-item">
                                <a href="{{ $company->fb }}" class="general__social-link social-facebook">
                                    <span class="icon-facebook"></span>
                                </a>
                            </li>
                            @endif
                            @if($company->inst)
                            <li class="general__social-item">
                                <a href="{{ $company->inst }}" class="general__social-link social-instagram">
                                    <span class="icon-instagram"></span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                    <div class="company-page__info__body">
                        <ul class="company-page__links-list">
                            <li class="company-page__links-item">
                                <span class="icon-place-big"></span>
                                @if($company->city)
                                <a href="{{ $company->link . '/map#content' }}" class="company-page__links-link">{{ $company->city }}</a>
                                @else
                                <div class="company-page__links-link" style="pointer-events:none">н.д.</div>
                                @endif
                            </li>
                            <li class="company-page__links-item">
                                <span class="icon-phone-outline"></span>
                                <a href="tel:{{ explode(', ', $company->phone)[0] }}" class="company-page__links-link" @if(!$company->phone) style="pointer-events:none" @endif>{{ $company->phone? explode(', ', $company->phone)[0] : 'н.д.' }}</a>
                            </li>
                            <li class="company-page__links-item">
                                <span class="icon-globe"></span>
                                <a rel="nofollow" href="{{ Str::startsWith($company->site, ['http://', 'https://']) ? $company->site : 'https://' . $company->site }}" class="company-page__links-link ts--ln-98" @if(!$company->site) style="pointer-events:none" @endif>
                                    {{ $company->site ? $company->site : 'н.д.' }}
                                </a>

                            </li>
                        </ul>
                    </div>
                    @if($company->statistics)
                    <div class="company-page__info__footer">
                        <ul class="company-page__info__statistic-list">
                            @foreach(json_decode($company->statistics) as $key => $item)
                            @if(count((array) $item))
                            <li class="company-page__info__statistic-item">
                                <p class="number">{{ $item->number }}</p>
                                <p class="text">{{ $item->text }}</p>
                            </li>
                            @endif
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
            <div class="company-page__content" id="content">
                <div class="general-tabs">
                    <ul class="general-tabs__list">
                        <li class="general-tabs__item @if(!$tab) active @endif"><a href="{{ $company->link . '#content' }}">{{ __('main.О компании') }}</a></li>
                        <li class="general-tabs__item @if($tab == 'map') active @endif @if(!isset($company->address['latlng'])) disabled @endif"><a href="{{ $company->link . '/map#content' }}">{{ __('main.Карта') }}</a></li>
                        <li class="general-tabs__item @if($tab == 'video') active @endif @if(!$company->videos) disabled @endif"><a href="{{ $company->link . '/video#content' }}">{{ __('main.Видео') }}</a></li>
                        <li class="general-tabs__item @if($tab == 'reviews') active @endif"><a href="{{ $company->link . '/reviews#content' }}">{{ __('main.Отзывы') }}</a></li>
                        <li class="general-tabs__item @if($tab == 'promotions') active @endif @if(!count($company->promotions)) disabled @endif"><a href="{{ $company->link . '/promotions#content' }}">{{ __('main.Акции') }}</a></li>
                        <li class="general-tabs__item general-tabs__item-empty"></li>
                    </ul>
                </div>
@yield('company_content')
            </div>
        </div>
    </section>
    @if($products_count)
    <section class="product">
        <div class="product__wrapper slider-infinity">
            <div class="general-heading container">
                <h2 class="main-caption-l main-caption-l--transform">{{ __('main.Новостройки застройщика') }} "{{ $company->name }}"</h2>
                <p class="calc-product">{{ $products_count }} <span>{{ __('main.Всего') }}</span></p>
            </div>
            <ul class="product__list product-slider__list js-infinity-slider-list">
                <productcard v-for="(product, key) in products.data" :key="key" :data-product="product" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'" @add-to-favorites="addToFavorites"></productcard>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity container">
                <div class="wrapper @if($products->count() < 5) hide @endif">
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
    @if($companies->count())
    <section class="best-company-info best-company-info-company">
        <div class="best-company-info__wrapper container">
            <div class="general-heading">
                <h2 class="main-caption-l main-caption-l--transform">{{ __('main.Другие компании') }}</h2>
                <p class="calc-product">{{ $companies_count }} <span>{{ __('main.Всего') }}</span></p>
            </div>
            <ul class="best-company-info__list">
                <companycard v-for="(company, key) in companies.data" :key="key" :data-company="company" @add-to-favorites="addToFavorites" @add-to-notifications="addToNotifications"></companycard>
            </ul>
            <a href="{{ route($lang . '_companies') }}" class="main-button-more">
                <span class="text">{{ __('main.Смотреть все компании') }}</span>
                <span class="icon-arrow-more"></span>
            </a>
        </div>
    </section>
    @endif
    @if($company->seo_desc)
    <section class="seo-block">
        <div class="seo-block__wrapper container">
            <h2 class="main-caption-l">{{ $company->seo_title }}</h2>
            <div class="seo-block__content">
                <div class="wrapper">
                    {!! $company->seo_desc !!}
                </div>
            </div>
        </div>
    </section>
    @endif
</main>
@endsection

@push('scripts')
<script>
  var company = @json($company);
  var reviews = @json($reviews);
  var products = @json($products);
  var companies = @json($companies);
  var promotions = @json($promotions);
</script>
<script src="{{ url('js/companies/company.js?v=' . $version) }}"></script>
@endpush

@if(isset($company->address['latlng']) && $tab === 'map')
@push('styles')
<!-- link href='https://api.mapbox.com/mapbox-gl-js/v1.11.0/mapbox-gl.css' rel='stylesheet' / -->
<link href='https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.css' rel='stylesheet' />
@endpush

@push('scripts')
<!-- script src='https://api.mapbox.com/mapbox-gl-js/v1.11.0/mapbox-gl.js'></script -->
<script src='https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.js'></script>

<script>
    mapboxgl.accessToken = '{{ config('services.mapbox.token') }}';

document.map = new mapboxgl.Map({
  language: '{{ $lang }}',
  container: 'general__map',
  style: 'mapbox://styles/mapbox/streets-v11',
  center: [{{ $company->address['latlng']['lng'] }}, {{ $company->address['latlng']['lat'] }}],
  zoom: 11,
  minZoom: 3.7
});

var marker = new mapboxgl.Marker()
.setLngLat([{{ $company->address['latlng']['lng'] }}, {{ $company->address['latlng']['lat'] }}])
.setPopup(new mapboxgl.Popup().setText('{{ $company->name }}'))
.addTo(document.map);

currentMarkers.push(marker);

document.map.on('load', function() {
    document.map.getStyle().layers.forEach(function(thisLayer){
        if(thisLayer.type == 'symbol'){
            document.map.setLayoutProperty(thisLayer.id, 'text-field', ['get','name_ru'])
        }
    });
});
</script>
@endpush
@endif
