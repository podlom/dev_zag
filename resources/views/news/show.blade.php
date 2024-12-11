@extends('layouts.app', [
  'meta_title' => $article->meta_title? $article->meta_title : $article->title,
  'meta_desc' => $article->meta_desc? $article->meta_desc : $article->title,
  'hide_from_index' => $article->hide_from_index,
  'og_image' => $article->img? url($article->img) : '',
  'og_type' => 'article'
])

@section('content')
<main>
    <div class="decor-background" style="background-image:url({{ url('img/background-img-2.png') }})"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
            {{ Breadcrumbs::render('article', $article->category->parent, $article->category, $article->title) }}
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
                <div class="general-noty__buttons-container">
                    <button class="general-noty__button general-noty__button-favorite" @click="addToFavorites(article, 'articles')" :class="{active: favorites['articles'].includes(article.id) || favorites['articles'].includes(article.original_id)}" title="{{ __('main.Добавить в избранное') }}">
                        <span class="icon-heart-outline"></span>
                    </button>
                    <!-- <button class="general-noty__button general-noty__button-sing-up">
                        <span class="icon-bell-outline"></span>
                    </button> -->
                </div>
            </div>
            @if($lang != 'ru')
            <a href="{{ $article->translation_link }}" class="translate-article">Читать статью на русском</a>
            @elseif($article->translation_link !== url('uk'))
            <a href="{{ $article->translation_link }}" class="translate-article">Читати статтю українською</a>
            @endif
            <article class="article-main">
                <div class="article-main__header">
                    <p class="name" itemprop="about">{{ $article->category->name }}</p>
                    <p class="area" itemprop="contentLocation">{{ $article->region_name }}</p>
                    <meta itemprop="datePublished" content="{{ $article->date->format('Y-m-d') }}">
                    <p class="date">{{ $article->date->format('d.m.Y') }}</p>
                </div>
                <div class="article-main__img">
                    <img src="" v-lazy="'{{ $article->bigImg }}'" alt="{{ $article->title }} фото" title="{{ $article->title }} картинка">
                    <meta itemprop="image" content="{{ $article->bigImg }}">
                </div>
                <div class="article-main__body" itemprop="articleBody">
                    {!! $article->short_desc !!}
                    @if($sameCategoryArticle)
                    <div class="article-main__recommendation">
                        {{ __('main.Читайте также') }}:
                        <a href="{{ $sameCategoryArticle->link }}">{{ $sameCategoryArticle->title }}</a>
                    </div>
                    @endif
                    {!! $article->filtered_content !!}

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
            <div class="article-page__links-wrapper">
                @if($prev)
                <a href="{{ $prev->link }}" class="article-page__link prev">
                    <span class="icon-arrow-small"></span>
                    <span class="text">{{ $prev->title }}</span>
                </a>
                @endif
                @if($next)
                <a href="{{ $next->link }}" class="article-page__link next">
                    <span class="text">{{ $next->title }}</span>
                    <span class="icon-arrow-small"></span>
                </a>
                @endif
            </div>
            <div class="subscribe-block subscribe-block-alone">
                <h5 class="subscribe-block__text">{{ __('main.Нашли_полезную_информацию?') }}<br>{{ __('main.Подписывайтесь на актуальные публикации') }}:</h5>
                @include('modules.subscription')
            </div>
            <div class="article-page__comments ts-news-show-blade__ln-139__2024-08-12">
                <div class="general-heading">
                    <h2 class="main-caption-l main-caption-l--transform">{{ __('main.Комментарии') }}</h2>
                </div>
                <ul class="article-page__comments-list" v-if="reviews.total">
                    <reviewCard v-for="(review, key) in reviews.data" :data-review="review" data-type="article" :key="key"></reviewCard>
                    <div class="pagination__wrapper">
                        <a href="#" class="main-button-more" @click.prevent="loadmore()" v-if="reviews.to != reviews.total">
                            <span class="text">{{ __('main.Показать больше') }}</span>
                        </a>
                    </div>
                </ul>
                <div class="article-page__comments-list" v-else>{{ __('main.К этой новости нет комментариев') }}</div>
                <form action="{{ url('reviews/create/article') }}" method="post" class="article-page__comments-form" id="review_article">
                  @csrf
                    <input type="hidden" name="reviewable_type" value="Backpack\NewsCRUD\app\Models\Article">
                    <input type="hidden" name="reviewable_id" value="{{ $article->id }}">
                    <input type="hidden" name="lang" value="{{ $article->language_abbr }}">
                    <div class="wrapper">
                        <label class="textarea__wrapper @error('article_review_text') error @enderror">
                            <h5 class="input__caption">{{ __('main.Комментарий') }}</h5>
                            <textarea class="main-textarea" name="article_review_text" placeholder="Текст" value="{{ old('article_review_text') }}"></textarea>
                            @error('article_review_text')
                                <span class="error-text" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </label>
                        <div class="container-form">
                            <label class="input__wrapper @error('article_review_name') error @enderror">
                                <h5 class="input__caption">{{ __('main.Имя') }}</h5>
                                <input type="text" class="main-input" name="article_review_name" placeholder="{{ __('forms.placeholders.Введите имя') }}" value="{{ old('article_review_name') }}" autocomplete="name">
                                @error('article_review_name')
                                    <span class="error-text" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </label>
                            <label class="input__wrapper @error('article_review_email') error @enderror">
                                <h5 class="input__caption">Email</h5>
                                <input type="email" class="main-input" name="article_review_email" placeholder="{{ __('forms.placeholders.Ваш электронный адрес') }}" value="{{ old('article_review_email') }}" autocomplete="email">
                                @error('article_review_email')
                                    <span class="error-text" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </label>
                        </div>
                        <button class="subscribe-block__button">{{ __('main.Комментировать') }}</button>
                    </div>
                </form>
            </div>
            <div class="article-page__more-news" v-if="otherArticles.length">
                <div class="general-heading">
                    <h2 class="main-caption-l main-caption-l--transform">{{ __('main.Читайте также') }}</h2>
                </div>
                <ul class="popular__block__list popular__block__list-more">
                    <articlecard v-for="(article, key) in otherArticles" :key="key" :data-article="article" @add-to-favorites="addToFavorites"></articlecard>
                </ul>
            </div>
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
