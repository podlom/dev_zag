@extends('layouts.app', [
	'skip_last' => true,
  'meta_title' => $page->meta_title . ' | ' . ucfirst($tag->name),
  'meta_desc' =>  $page->meta_desc . ' | ' . ucfirst($tag->name),
])

@section('content')
<main>
    <div class="decor-background" style="background-image:url({{ url('img/background-img-2.png') }})"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
            <div class="breadcrumbs__list">
                <a href="{{ $lang === 'ru'? url('/') : url($lang) }}" class="breadcrumbs__link">{{ __('main.Главная') }}</a> 
                <a href="#" class="breadcrumbs__link" >{{ __('main.Поиск по тегу') }} "{{ $tag->name }}"</a>
            </div>
        </div>
    </section>
    <section class="news">
        <div class="news__wrapper container">
            <div class="general-heading">
                <h1 class="main-caption-l" v-cloak>{{ __('main.Поиск по тегу') }} "{{ $tag->name }}"</h1>
            </div>
            <ul class="popular__block__list">
              <articlecard v-for="(article, key) in articles.data" :key="key" :data-article="article" @add-to-favorites="addToFavorites"></articlecard>
            </ul>
            <div class="pagination__wrapper" v-if="articles.last_page != 1">
                <div class="pagination__container">
                    <button class="general-button" @click="query.page = 1" v-bind:class="{disabled: query.page == 1}">
                        <span class="icon-pagi-left"></span>
                    </button>
                    <button class="general-button" v-bind:class="{disabled: articles.current_page == 1}" @click="query.page--">
                        <span class="icon-arrow-pagi-left"></span>
                    </button>
                    <ul class="pagination__list" v-cloak>
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
            <div class="subscribe-block subscribe-block-alone">
                <h5 class="subscribe-block__text">{{ __('main.Нашли полезную информацию?') }}<br>{{ __('main.Подписывайтесь на актуальные публикации') }}:</h5>
                @include('modules.subscription')
            </div>
        </div>
    </section>

    @if($page->seo_text && (!request('page') || request('page') == 1))
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
<script>
  var articles = @json($articles);
  var page = @json(request('page')? request('page') : 1);
  var tag_id = @json($tag->id);

  document.querySelectorAll('.header__change-lang a').forEach(function(item) {
    let href = item.getAttribute('href');
    item.setAttribute('href', href + location.search);
  });
</script>
</script>
<script src="{{ url('js/news/tags.js?v=' . $version) }}"></script>
@endpush