@extends('layouts.app', [
  'meta_title' => $page->meta_title,
  'meta_desc' => $page->meta_desc,
])

@section('content')
<main>
    <div class="decor-background" style="background-image:url(./img/background-img-2.png)"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
                {{ Breadcrumbs::render('page', $page->main_title) }}
        </div>
    </section>
    <section class="dictionary">
        <div class="dictionary__wrapper container">
            <h1 class="main-caption-l main-caption-l--transform">{{ $page->main_title }}</h1>
            <div class="dictionaty__text">{!! $page->main_text !!}</div>
            <div class="dictionary__letter__header js-dictionary-header">
                <div class="dictionary__letter__header__wrapper js-dictionary-wrapper">
                    <ul class="dictionary__letter-list">
                        @foreach($letters as $letter => $terms)
                        <li class="dictionary__letter-item">
                            <a class="dictionary__letter-link" href="#{{ $letter }}">{{ $letter }}</a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <ul class="dictionary__letter-content-list js-dictionary-body">
                @foreach($letters as $letter => $terms)
                <li class="dictionary__letter-content-item" id="{{ $letter }}">
                    <div class="caption">
                        <h3>{{ $letter }}</h3>
                    </div>
                    <ul class="dictionary__word-list">
                        @foreach($terms as $term)
                        <li class="dictionary__word-item">
                            <p class="name">{{ $term->name }}</p>
                            <p class="description">{{ $term->definition }}</p>
                        </li>
                        @endforeach
                    </ul>
                </li>
                @endforeach
            </ul>
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
</main>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function(){
        function showDictionaryList() {
            let stikyBlockHeight = document.querySelector('.js-dictionary-wrapper').offsetHeight;
            let stikyBlock = document.querySelector('.js-dictionary-wrapper');
            let hideBlock = document.querySelector('.js-dictionary-header');
            let dictionaryBodyBottom = document.querySelector('.js-dictionary-body').getBoundingClientRect().bottom;
            
            if(dictionaryBodyBottom < stikyBlockHeight) {
                stikyBlock.setAttribute('style', 'position: absolute');
                return;
            }else {
                stikyBlock.setAttribute('style', 'position: fixed');
            }
        
            if(hideBlock.getBoundingClientRect().top < 10) {
                hideBlock.style.height = stikyBlockHeight + "px";
                stikyBlock.setAttribute('style', 'position: fixed');
                stikyBlock.classList.add("fixed");
            }else {
                hideBlock.style.height = stikyBlockHeight + "px";
                stikyBlock.setAttribute('style', 'position: absolute');
                stikyBlock.classList.remove("fixed");
            }
        }
        
        window.addEventListener("resize",function(){
            showDictionaryList();
        });
        
        showDictionaryList()
        
        document.addEventListener("scroll",function(){
            showDictionaryList()
        });
    
});
</script>
<script>
  var articles = @json($articles);
</script>
<script src="{{ url('js/dictionary/dictionary.js?v=' . $version) }}"></script>
@endpush