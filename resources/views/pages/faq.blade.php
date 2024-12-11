@extends('layouts.app', [
  'meta_title' => $meta_title,
  'meta_desc' => $meta_desc,
])

@section('content')
<main>
    <div class="decor-background" style="background-image:url({{ url('img/background-img-2.png') }})"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
            <div class="breadcrumbs__list">
                <a href="{{ route($lang . '_home') }}" class="breadcrumbs__link">{{ __('main.Главная') }}</a> 
                <a href="#" class="breadcrumbs__link" v-cloak>{{ __('main.Часто задаваемые вопросы по теме') }} "@{{ categories[query.category] }}"</a>
            </div>
        </div>
    </section>
    <section class="faq">
        <div class="faq__wrapper container">
            <h1 class="main-caption-l main-caption-l--transform" v-cloak>{{ __('main.Часто задаваемые вопросы по теме') }} "@{{ categories[query.category] }}"</h1>
            <div class="faq__tabs-adaptation">
                <h4 class="faq__tabs-caption">{{ __('main.Категории вопросов') }}</h4>
                <div class="general-drop general-top__drop js-drop-item">
                    <button type="button" class="general-top__drop__button js-drop-button general-drop__text"> 
                        <span class="text" v-cloak>@{{ categories[query.category] }}</span>
                        <span class="icon-drop"></span>
                    </button>
                    <div class="general-drop__wrapper">
                        <ul class="general-drop__list">
                            <li class="general-drop__item js-drop-contains" v-for="(item, slug) in categories" @click="query.category = slug" :class="{active: query.category == slug}" v-cloak>@{{ item }}</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="faq__container">
                <div class="faq__info">
                    <div v-if="loading" class="info-block__spoiler__list" style="height: 400px;display:flex;">
                        <img src="/img/preload-for-files.gif" style="height: 2px;margin: auto;">
                    </div>
                    <ul class="info-block__spoiler__list" v-else>
                        <li class="info-block__spoiler__item js-drop-item" v-for="(question, key) in questions" v-cloak>
                            <button class="info-block__spoiler__button js-drop-button">
                                <span class="text">@{{ question.question }}</span>
                                <span class="icon-drop"></span>
                            </button>
                            <div class="info__wrapper" v-html="question.answer"></div>
                        </li>
                    </ul>
                </div>
                <div class="faq__tabs">
                    <h4 class="faq__tabs-caption">{{ __('main.Категории вопросов') }}</h4>
                    <ul class="faq__tabs__list">
                        <li class="faq__tabs__item" v-for="(item, slug) in categories" @click="query.category = slug" :class="{active: query.category == slug}" v-cloak>@{{ item }}</li>
                    </ul>
                </div>
            </div>
            <div class="faq__form-wrapper">
                <h2 class="main-caption-l main-caption-l--transform">{{ $page->form_title }}</h2>
                <form action="{{ url('feedback/create/question') }}" method="post" class="faq__form">
                    @csrf
                    <div class="wrapper">
                        <div class="container-form">
                            <label class="input__wrapper">
                                <h5 class="input__caption">{{ __('main.Имя') }}</h5>
                                <input type="text" class="main-input main-input-faq" name="question_name" placeholder="{{ __('forms.placeholders.Как к вам обращаться?') }}" value="{{ old('question_name') }}">
                            </label>
                            <label class="input__wrapper @error('question_email') error @enderror">
                                <h5 class="input__caption">Email</h5>
                                <input type="email" class="main-input main-input-faq" name="question_email" placeholder="{{ __('forms.placeholders.Ваш электронный адрес') }}" value="{{ old('question_email') }}">
                                @error('question_email')
                                <span class="error-text" role="alert">
                                    {{ $message }}
                                </span>
                                @enderror
                            </label>
                        </div>
                        <label class="textarea__wrapper">
                            <h5 class="input__caption">{{ __('main.Вопрос') }}</h5>
                            <textarea class="main-textarea main-textarea-faq" name="question_text" placeholder="Текст">{{ old('question_text') }}</textarea>
                        </label>
                    </div>
                    <button class="main-button">{{ __('main.Отправить') }}</button>
                </form>
            </div>
            <div class="article-page__more-news">
                <div class="general-heading">
                    <h2 class="main-caption-l main-caption-l--transform">{{ $page->news_title }}</h2>
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

    <section class="info-block" v-if="seo_text">
        <div class="info-block__wrapper container">
            <!-- <div class="general-heading">
                <h2 class="main-caption-l main-caption-l--transform"></h2>
            </div> -->
            <div class="info-block__container">
                <div class="info-block__inner" v-html="seo_text">
                </div>
            </div>
        </div>
    </section>
</main>
@endsection

@push('scripts')
<script>
  var questions = @json($questions);
  var categories = @json($categories);
  var currentCategorySlug = @json($category->slug);
  var articles = @json($articles);
  var seo_text = @json($seo_text);
</script>
</script>
<script src="{{ url('js/faq/faq.js?v=' . $version) }}"></script>
@endpush