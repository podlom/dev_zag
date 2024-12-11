@extends('layouts.app', [
  'meta_title' => $page->meta_title,
  'meta_desc' => $page->meta_desc,
])

@push('header_scripts')
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "ContactPage",
        "headline": "{{ $page->main_title }}",
        "image": [
            "{{ url('files/47/karta2_office.jpg') }}"
        ]
    }
</script>
@endpush

@section('content')
<main>
    <div class="decor-background" style="background-image:url({{ url('img/background-img-2.png') }})"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
          {{ Breadcrumbs::render('page', $page->main_title) }}
        </div>
    </section>
    <section class="contact">
        <div class="contact__wrapper container">
            <div class="general-heading">
                <h1 class="main-caption-l main-caption-l--transform">{{ $page->main_title }}</h1>
            </div>
            <ul class="contact__list">
                <li class="contact__item">
                    <div class="contact__item__header">
                        <span class="icon-place-big contact__icon"></span>
                        <p class="contact__item__caption">{{ __('main.Адрес') }}</p>
                    </div>
                    <div class="contact__address">
                        {!! config('settings.address_' . $lang) !!}
                    </div>
                </li>
                <li class="contact__item">
                    <div class="contact__item__header">
                        <span class="icon-phone-outline contact__icon"></span>
                        <p class="contact__item__caption">{{ __('main.Позвоните нам') }}</p>
                    </div>
                    <div class="contact__phone">
                        <a href="tel:{{ explode(',', config('settings.phone'))[0] }}" class="contact__phone__button" title="{{ __('main.Позвонить') }}">
                            <svg class="contact__phone__icon" viewBox="0 0 24 24">
                                <path d="M20 15.5C18.8 15.5 17.5 15.3 16.4 14.9H16.1C15.8 14.9 15.6 15 15.4 15.2L13.2 17.4C10.4 15.9 8 13.6 6.6 10.8L8.8 8.6C9.1 8.3 9.2 7.9 9 7.6C8.7 6.5 8.5 5.2 8.5 4C8.5 3.5 8 3 7.5 3H4C3.5 3 3 3.5 3 4C3 13.4 10.6 21 20 21C20.5 21 21 20.5 21 20V16.5C21 16 20.5 15.5 20 15.5M5 5H6.5C6.6 5.9 6.8 6.8 7 7.6L5.8 8.8C5.4 7.6 5.1 6.3 5 5M19 19C17.7 18.9 16.4 18.6 15.2 18.2L16.4 17C17.2 17.2 18.1 17.4 19 17.4V19M21 6V11H19.5V7.5H13.87L16.3 9.93L15.24 11L11 6.75L15.24 2.5L16.3 3.57L13.87 6H21Z" />
                            </svg>
                        </a>
                        <div class="contact__phone__call-back">
                            @foreach(explode(',', config('settings.phone')) as $tel)
                            <a href="tel:{{ config('settings.phone') }}" class="contact__phone__link">{{ trim($tel) }}</a>
                            @endforeach
                            <p class="contact__time-works">{{ config('settings.schedule') }}</p>
                        </div>
                    </div>
                </li>
                <li class="contact__item">
                    <div class="contact__item__header">
                        <span class="icon-message contact__icon"></span>
                        <p class="contact__item__caption">{{ config('settings.email') }}</p>
                    </div>
                    <ul class="general__socila-list">
                        <li class="general__social-item">
                            <a href="{{ config('settings.fb') }}" class="general__social-link social-facebook" title="Facebook">
                                <span class="icon-facebook"></span>
                            </a>
                        </li>
                        <li class="general__social-item">
                            <a href="{{ config('settings.tw') }}" class="general__social-link social-twitter" title="Twitter">
                                <span class="icon-twitter"></span>
                            </a>
                        </li>
                        <li class="general__social-item">
                            <a href="{{ config('settings.inst') }}" class="general__social-link social-instagram" title="Instagram">
                                <span class="icon-instagram"></span>
                            </a>
                        </li>
                        <li class="general__social-item">
                            <a href="tg://resolve?domain={{ config('settings.tg') }}" class="general__social-link social-telegram" title="Telegram">
                                <span class="icon-telegram"></span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <div class="contact__map">
                <img src="{{ url('files/47/karta2_office.jpg') }}" alt="Фото: {{ __('main.Карта') }}" title="Картинка: {{ __('main.Карта') }}">
            </div>
        </div>
    </section>
    @if($page->seo_text)
    <section class="info-block">
        <div class="info-block__wrapper container">
            <!-- <div class="general-heading">
                <h2 class="main-caption-l main-caption-l--transform"></h2>
            </div> -->
            <div class="info-block__container">
                <div class="info-block__inner">
                {!! $page->seo_text !!}
                </div>
            </div>
        </div>
    </section>
    @endif
</main>
@endsection

@push('scripts')
<script src="{{ url('js/app.js?v=' . $version) }}"></script>
@endpush