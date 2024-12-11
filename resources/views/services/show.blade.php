@extends('layouts.app', [
  'meta_title' => $article->meta_title? $article->meta_title : $article->title,
  'meta_desc' => $article->meta_desc? $article->meta_desc : $article->title,
  'hide_from_index' => $article->hide_from_index,
  'og_type' => 'article',
])

@section('content')
<main style="padding-bottom:60px">
    <div class="decor-background" style="background-image:url({{ url('img/background-img-2.png') }})"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
            {{ Breadcrumbs::render('services', $article->category, $article->title) }}
        </div>
    </section>
    <section class="article-page" itemscope itemtype="http://schema.org/Article">
        <meta itemprop="mainEntityOfPage" content="{{ $article->link }}">
        <div itemprop="publisher" itemscope itemtype="http://schema.org/Organization">
            <meta itemprop="name" content="Zagorodna.com">
            <div itemprop="logo" itemscope itemtype="http://schema.org/ImageObject">
                <meta itemprop="url" content="{{ url('img/logo.png') }}">
            </div>
        </div>
        <meta itemprop="author" content="Zagorodna.com">
        <meta itemprop="dateModified" content="{{ $article->updated_at->format('Y-m-d') }}">
        <div class="article-page__wrapper container">
            <div class="general-heading">
                <h1 class="main-caption-l main-caption-l--transform" itemprop="headline">{{ $article->title }}</h1>
                <!-- <div class="general-noty__buttons-container">
                    <button class="general-noty__button general-noty__button-favorite">
                        <span class="icon-heart-outline"></span>
                    </button>
                    <button class="general-noty__button general-noty__button-sing-up">
                        <span class="icon-bell-outline"></span>
                    </button>
                </div> -->
            </div>
            @if($lang != 'ru')
            <a href="{{ $article->translation_link }}" class="translate-article">Читать статью на русском</a>
            @elseif($article->translation_link !== url('uk'))
            <a href="{{ $article->translation_link }}" class="translate-article">Читати статтю українською</a>
            @endif
            <article class="article-main">
                <div class="article-main__header">
                    <p class="name" itemprop="about">{{ $article->category->name }}</p>
                </div>
                <div class="article-main__body" itemprop="articleBody">
                    {!! $article->short_desc !!}
                    {!! $article->filtered_content !!}
                    <!-- <div class="article-main__recommendation">
                        Читайте также: <a href="#">“Коронавирус наступает: несколько слов о профилактике и лечении”</a>
                    </div> -->
                </div>
                <div class="article-main__footer">
                    <div class="popular__statistics">
                        <p class="popular__views">
                            <span class="icon-eyes"></span>
                            <span>{{ $article->views }}</span>
                        </p>
                        <p class="popular__comments" itemprop="interactionStatistic" itemscope itemtype="http://schema.org/InteractionCounter">
                            <meta itemprop="interactionType" content="http://schema.org/CommentAction"/>
                            <span class="icon-comment-text-outline"></span>
                            <span itemprop="userInteractionCount">{{ $article->reviews->count() }}</span>
                        </p>
                    </div>
                    <div class="general-social__wrapper">
                        <h5>{{ __('main.Поделиться ссылкой') }}:</h5>
                        <ul class="general__socila-list">
                            <li class="general__social-item">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ request()->url() }}" target="_blank" class="general__social-link social-facebook" title="Facebook" rel="nofollow">
                                    <span class="icon-facebook"></span>
                                </a>
                            </li>
                            <li class="general__social-item">
                                <a href="https://twitter.com/intent/tweet?text={{ $article->title }}&url={{ request()->url() }}" target="_blank" class="general__social-link social-twitter" title="Twitter" rel="nofollow">
                                    <span class="icon-twitter"></span>
                                </a>
                            </li>
                            <li class="general__social-item">
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ request()->url() }}" target="_blank" class="general__social-link social-linkedin" title="Linkedin" rel="nofollow">
                                    <span class="icon-linkedin"></span>
                                </a>
                            </li>
                            <li class="general__social-item">
                                <a href="https://telegram.me/share/url?url={{ request()->url() }}&text={{ $article->title }}" target="_blank" class="general__social-link social-telegram" title="Telegram" rel="nofollow">
                                    <span class="icon-telegram"></span>
                                </a>
                            </li>
                            <li class="general__social-item copy">
                                <input type="text" value="{{ request()->url() }}">
                                <a href="#" title="{{ __('main.Скопировать ссылку') }}" class="general__social-link social-link" rel="nofollow">
                                    <span class="icon-link-variant"></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    @if($article->tags->count())
                    <div class="article-main__tags">
                        <p>Теги:</p>
                          @foreach($article->tags as $key => $tag)
                        <a href="{{ url($lang . '/tags?id=' . $tag->id) }}">
                          {{ $key == 0 ? $tag->name : ', ' . $tag->name  }}
                        </a>
                          @endforeach
                    </div>
                    @endif
                </div>
            </article>
        </div>
    </section>
</main>

@endsection

@push('scripts')
<script>
  var article = @json($article);
  var reviews = @json($reviews);
  var otherArticles = @json($otherArticles);
</script>
<script src="{{ url('js/news/article.js?v=' . $version) }}"></script>
@endpush