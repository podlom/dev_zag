@extends('layouts.app', [
  'meta_title' => $meta_title? $meta_title : __('main.Сервисы'),
  'meta_desc' => $meta_desc? $meta_desc : __('main.Сервисы'),
])

@section('content')
<main>
    <div class="decor-background" style="background-image:url({{ url('img/background-img-2.png') }})"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
            <div class="breadcrumbs__list">
                <a href="{{ $lang === 'ru'? url('/') : url($lang) }}" class="breadcrumbs__link">{{ __('main.Главная') }}</a> 
                <a href="#" class="breadcrumbs__link" v-if="query.parent_category" @click.prevent="query.category = null" v-cloak>@{{ parent_categories[query.parent_category] }}</a>
                <a href="#" class="breadcrumbs__link" v-if="query.category" v-cloak>@{{ categories[query.category] }}</a>
            </div>
        </div>
    </section>
    <section class="news">
        <div class="news__wrapper container">
            <div class="general-heading">
                <h1 class="main-caption-l" v-cloak>@{{ query.category? categories[query.category] : parent_categories[query.parent_category] }} <span v-if="query.page > 1"> ➨ {{ __('main.страница') }} @{{ query.page }}</span></h1>
                <div class="general-drop general-top__drop js-drop-item">
                    <button type="button" class="general-top__drop__button js-drop-button general-drop__text"> 
                        <span class="text parent_category_text" v-if="query.parent_category" v-cloak>@{{ parent_categories[query.parent_category] }}</span>
                        <span v-else>{{ __('main.Не выбрано') }}</span>
                        <span class="icon-drop"></span>
                    </button>
                    <div class="general-drop__wrapper">
                        <ul class="general-drop__list">
                          <li class="general-drop__item js-drop-contains" v-for="(item, slug) in parent_categories" @click="query.parent_category = slug" :class="{active: query.parent_category == slug}">@{{ item }}</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="main-filtration__wrapper main-filtration__wrapper-news" v-show="Object.keys(categories).length">
                <ul class="general-filter__list">
                    <li class="general-filter__item" v-if="Object.keys(categories).length">
                        <h4 class="general-filter__caption">{{ __('main.Категория') }}</h4>
                        <div class="general-drop js-drop-item">
                            <button type="button" class="general-filter__button js-drop-button">
                                <input type="text" class="general-drop-input js-drop-input" v-if="query.category" :value="categories[query.category]" readonly="">
                                <input type="text" class="general-drop-input js-drop-input" v-else value="{{ __('main.Не выбрано') }}" readonly="">
                                <span class="icon-drop"></span>
                            </button>
                            <div class="general-drop__wrapper">
                                <ul class="general-drop__list">
                                  <li class="general-drop__item js-drop-contains" @click="query.category = null" :class="{active: query.category == null}">{{ __('main.Не выбрано') }}</li>
                                  <li class="general-drop__item js-drop-contains" v-for="(item, slug) in categories" @click="query.category = slug" :class="{active: query.category == slug}">@{{ item }}</li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="general-filter__item general-filter__item-sort" v-show="articles.total">
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
                    </li>
                </ul>
            </div>
            <div class="article-content" v-html="content" v-if="!loading"></div>
            <div v-if="loading" class="popular__block__list" style="height: 250px;display: flex;">
                <img src="/img/preload-for-files.gif" style="height: 2px;margin: auto;" alt="">
            </div>
            <ul class="popular__block__list" v-else>
              <articlecard v-for="(article, key) in articles.data" :key="key" :data-article="article" @add-to-favorites="addToFavorites"></articlecard>
            </ul>
            <div class="pagination__wrapper" v-if="articles.last_page != 1" v-cloak>
                <div class="pagination__container">
                    <button class="general-button" @click="query.page = 1" v-bind:class="{disabled: query.page == 1}">
                        <span class="icon-pagi-left"></span>
                    </button>
                    <button class="general-button" v-bind:class="{disabled: articles.current_page == 1}" @click="query.page--">
                        <span class="icon-arrow-pagi-left"></span>
                    </button>
                    <ul class="pagination__list">
                        <li class="pagination__item" @click="query.page = 1" v-bind:class="{active: query.page == 1}">
                            <button>1</button>
                        </li>
                        <li class="pagination__item dots" v-if="articles.last_page > 7 && query.page - 1 > 3">
                            <button>...</button>
                        </li>
                        <li class="pagination__item" v-for="page in (articles.last_page - 1)" @click="query.page = page" v-bind:class="{active: page == query.page}" v-show="page != 1 && ((query.page == 1 && page <= 6) || (query.page == articles.last_page && page >= articles.last_page - 5) || (Math.abs(query.page - page) < 3) || (query.page <= 3 && page <= 6) || (query.page >= articles.last_page - 3 && page >= articles.last_page - 6))">
                            <button>@{{ page }}</button>
                        </li>
                        <li class="pagination__item dots" v-if="articles.last_page > 7 && articles.last_page - query.page > 3">
                            <button>...</button>
                        </li>
                        <li class="pagination__item" @click="query.page = articles.last_page" v-if="articles.last_page != 1" v-bind:class="{active: articles.last_page == query.page}">
                            <button>@{{ articles.last_page }}</button>
                        </li>
                    </ul>
                    <button class="general-button" v-bind:class="{disabled: articles.current_page == articles.last_page}" @click="query.page++">
                        <span class="icon-arrow-pagi-right"></span>
                    </button>
                    <button class="general-button" @click="query.page = articles.last_page" v-if="articles.last_page != 1" v-bind:class="{disabled: articles.last_page == query.page}">
                        <span class="icon-pagi-right"></span>
                    </button>
                </div>
                <button @click="loadmore()" v-if="articles.current_page != articles.last_page" class="main-button-more">
                    <span class="text">{{ __('main.Показать больше') }}</span>
                </button>
            </div>
            <div class="rating-drop__wrapper active" style="margin-top: 40px;" v-if="(top_rating || reviews_rating) && !loading" v-cloak>
                <ul class="rating-block__list">
                    <li class="rating-block__item" v-if="top_rating">
                        <div class="rating-block__table">
                            <div class="rating-block__table__caption">
                                <p class="table-number">№</p>
                                <p class="table-name">{{ __('main.Название') }}</p>
                                <p class="table-rating">{{ __('main.баллы') }}</p>
                            </div>
                            <div class="wrapper">
                                <div class="rating-block__table__item" v-for="(item, key) in top_rating">
                                    <p class="table-number">@{{ key + 1 }}</p>
                                    <a :href="item.link + '/rating'" class="table-name">@{{ item.name }}</a>
                                    <p class="table-rating">@{{ item.top_rating }}</p>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="rating-block__item rating-block__item-assessment" v-if="reviews_rating">
                        <div class="rating-block__table">
                            <div class="rating-block__table__caption">
                                <p class="table-number">№</p>
                                <p class="table-name">{{ __('main.Название') }}</p>
                                <p class="table-calc">{{ __('main.Кол-во') }}</p>
                                <p class="table-rating">{{ __('main.Оценка') }}</p>
                            </div>
                            <div class="wrapper">
                                <div class="rating-block__table__item" v-for="(item, key) in reviews_rating">
                                    <p class="table-number">@{{ key + 1 }}</p>
                                    <a :href="item.link" class="table-name">@{{ item.name }}</a>
                                    <p class="table-calc">@{{ item.old_rating_count }}</p>
                                    <p class="table-rating">@{{ item.reviews_rating }}</p>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="subscribe-block subscribe-block-alone">
                <h5 class="subscribe-block__text">{{ __('main.Нашли полезную информацию?') }}<br>{{ __('main.Подписывайтесь на актуальные публикации') }}:</h5>
                @include('modules.subscription')
            </div>
        </div>
    </section>

    <section class="info-block" v-if="seo_text && query.page == 1">
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
  var articles = @json($articles);
  var parent_categories = @json($parent_categories);
  var categories = @json($categories);
  var currentCategorySlug = @json($category? $category->slug : null);
  var currentThemeSlug = @json($currentThemeSlug);
  var sorts = @json(__('sorts.articles'));
  var seo_text = @json($seo_text);
  var content = @json($content);
  var page = @json(request('page')? request('page') : 1);
  var top_rating = @json($top_rating);
  var reviews_rating = @json($reviews_rating);
</script>
</script>
<script src="{{ url('js/analitics/index.js?v=' . $version) }}"></script>
@endpush