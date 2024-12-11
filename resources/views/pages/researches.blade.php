@extends('layouts.app', [
  'meta_title' => $slug? $page[$slug . '_meta_title']: $page->main_meta_title,
  'meta_desc' => $slug? $page[$slug . '_meta_desc']: $page->main_meta_desc,
])

@section('content')
<main>
    <div class="decor-background" style="background-image:url({{ url('img/background-img-2.png') }})"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
            {{ Breadcrumbs::render($slug? 'research' : 'researches', $slug? $page[$slug . '_title'] : '') }}
        </div>
    </section>
    <section class="researches">
        <div class="researches__wrapper container">
            @if(!$slug)
            <h2 class="main-caption-l main-caption-l--transform">{{ __('main.Маркетинговые исследования') }}</h2>
            <ul class="researches__list">
                <li class="researches__item">
                    @if($page->all_image)
                    <div class="img" style="background-image:url({{ url($page->all_image) }})"></div>
                    @endif
                    <a href="{{ url($page_slug . '/all') }}" class="researches__link">
                        <span class="text">{{ $page->all_title }}</span>
                        <span class="icon-arrow-more"></span>
                    </a>
                </li>
                <li class="researches__item">
                    @if($page->cottage_image)
                    <div class="img" style="background-image:url({{ url($page->cottage_image) }})"></div>
                    @endif
                    <a href="{{ url($page_slug . '/cottage') }}" class="researches__link">
                        <span class="text">{{ $page->cottage_title }}</span>
                        <span class="icon-arrow-more"></span>
                    </a>
                </li>
                <li class="researches__item">
                    @if($page->realexpo_image)
                    <div class="img" style="background-image:url({{ url($page->realexpo_image) }})"></div>
                    @endif
                    <a href="{{ url($page_slug . '/realexpo') }}" class="researches__link">
                        <span class="text">{{ $page->realexpo_title }}</span>
                        <span class="icon-arrow-more"></span>
                    </a>
                </li>
            </ul>
            <h1 class="main-caption-l main-caption-l--transform">{{ $page->main_title }}</h1>
            <div class="researches__text">{!! $page->main_description !!}</div>
            @else
            <h1 class="main-caption-l main-caption-l--transform">{{ $page[$slug . '_title'] }}</h1>
            <div class="researches__text">{!! $page[$slug . '_description'] !!}</div>
            @endif
            @if(!$slug)
            <div class="researches__content">
                <form action="{{ url('research/create') }}" method="post" class="researches__form">
                    @csrf
                    <input type="hidden" name="type" value="{{ $slug? $slug : 'individual' }}">
                    <label class="textarea__wrapper @error('theme') error @enderror">
                        <span class="input__caption">1. {{ __('main.Тематика исследования (цели, задачи, проблемы и т д )') }}*</span>
                        <textarea class="main-textarea" name="theme" placeholder="{{ __('forms.placeholders.Опишите ключевые пункты') }}">{{ old('theme') }}</textarea>
                        @error('theme')
                        <span class="error-text" role="alert">
                            {{ $message }}
                        </span>
                        @enderror
                    </label>
                    <label class="input__wrapper @error('region') error @enderror">
                        <span class="input__caption">2. {{ __('main.Регион исследования') }}*</span>
                        <input type="text" class="main-input" name="region" value="{{ old('region') }}" placeholder="{{ __('forms.placeholders.Укажите регион') }}">
                        @error('region')
                        <span class="error-text" role="alert">
                            {{ $message }}
                        </span>
                        @enderror
                    </label>
                    <div class="input__wrapper">
                        <span class="input__caption">3. {{ __('main.Методика исследования') }}</span>
                        <div class="general-drop general-top__drop js-drop-item">
                            <button type="button" class="general-top__drop__button js-drop-button general-drop__text"> 
                                <span class="text">{{ old('method')? old('method') : __('forms.placeholders.Выберите методику') }}</span>
                                <span class="icon-drop"></span>
                            </button>
                            <input type="hidden" name="method" class="js-drop-input" value="{{ old('method') }}">
                            <div class="general-drop__wrapper">
                                <ul class="general-drop__list">
                                    @foreach(json_decode($page->methods) as $method)
                                    <li class="general-drop__item js-drop-contains @if(old('method') == $method->name) active @endif">{{ $method->name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <label class="input__wrapper">
                        <span class="input__caption">4. {{ __('main.Структура и объем выборки') }}</span>
                        <input type="text" class="main-input" name="structure" value="{{ old('structure') }}" placeholder="{{ __('forms.placeholders.Укажите структуру и объем') }}">
                    </label>
                    <label class="textarea__wrapper">
                        <span class="input__caption">5. {{ __('main.Дополнительная информация') }}</span>
                        <textarea class="main-textarea" name="info" placeholder="{{ __('forms.placeholders.Напишите дополнительно') }}">{{ old('info') }}</textarea>
                    </label>
                    <label class="input__wrapper @error('organization') error @enderror">
                        <span class="input__caption">{{ __('main.Организация') }}*</span>
                        <input type="text" class="main-input" name="organization" placeholder="{{ __('forms.placeholders.Название организации') }}" value="{{ old('organization') }}">
                        @error('organization')
                        <span class="error-text" role="alert">
                            {{ $message }}
                        </span>
                        @enderror
                    </label>
                    <label class="input__wrapper @error('name') error @enderror">
                        <span class="input__caption">{{ __('main.ФИО') }}*</span>
                        <input type="text" class="main-input" name="name" placeholder="{{ __('forms.placeholders.Как к вам обращаться?') }}" value="{{ old('name') }}">
                        @error('name')
                        <span class="error-text" role="alert">
                            {{ $message }}
                        </span>
                        @enderror
                    </label>
                    <label class="input__wrapper @error('phone') error @enderror">
                        <span class="input__caption">{{ __('main.Контактный телефон') }}*</span>
                        <input type="tel" class="main-input" name="phone" placeholder="{{ __('forms.placeholders.Номер телефона') }}" value="{{ old('phone') }}">
                        @error('phone')
                        <span class="error-text" role="alert">
                            {{ $message }}
                        </span>
                        @enderror
                    </label>
                    <label class="input__wrapper @error('email') error @enderror">
                        <span class="input__caption">Email*</span>
                        <input type="email" class="main-input" name="email" placeholder="{{ __('forms.placeholders.Ваш электронный адрес') }}" value="{{ old('email') }}">
                        @error('email')
                        <span class="error-text" role="alert">
                            {{ $message }}
                        </span>
                        @enderror
                    </label>
                    <button class="main-button">{{ __('main.Отправить') }}</button>
                </form>
            </div>
            @endif
            <div class="article-page__more-news">
                <div class="general-heading">
                    <h2 class="main-caption-l main-caption-l--transform">{{ __('main.Читайте также') }}</h2>
                </div>
                <ul class="popular__block__list popular__block__list-more">
                    <articlecard v-for="(article, key) in articles" :key="key" :data-article="article" @add-to-favorites="addToFavorites"></articlecard>
                </ul>
            </div>
            <div class="subscribe-block subscribe-block-alone">
                <h5 class="subscribe-block__text">{{ __('main.Нашли полезную информацию?') }}<br>{{ __('main.Подписывайтесь на актуальные публикации') }}:</h5>
                @include('modules.subscription')
            </div>
        </div>
    </section>
    
    @if($slug? $page[$slug . '_seo_text']: $page->main_seo_text)
    <section class="info-block">
        <div class="info-block__wrapper container">
            <div class="info-block__container">
                <div class="info-block__inner">
                {!! $slug? $page[$slug . '_seo_text']: $page->main_seo_text !!}
                </div>
            </div>
        </div>
    </section>
    @endif
</main>
@endsection

@push('scripts')
<script>
  var articles = @json($articles);
</script>
<script src="{{ url('js/researches/researches.js?v=' . $version) }}"></script>
@endpush