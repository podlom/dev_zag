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
            {{ Breadcrumbs::render('events', $article->category->parent, $article->category, $article->title) }}
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
                    @if($article->images && count($article->images))
                    <div class="article-gallery">
                        <div class="article-gallery__half">
                        @foreach($article->images as $key => $img)
                            @if(!($key % 2))
                            <img class="article-gallery__img" src="{{ url($img) }}" alt="Фото {{ $key + 1 }}: {{ $article->title }}" title="Картинка {{ $key + 1 }}: {{ $article->title }}">
                            @endif
                        @endforeach
                        </div>
                        <div class="article-gallery__half">
                        @foreach($article->images as $key => $img)
                            @if($key % 2)
                            <img class="article-gallery__img" src="{{ url($img) }}" alt="Фото {{ $key + 1 }}: {{ $article->title }}" title="Картинка {{ $key + 1 }}: {{ $article->title }}">
                            @endif
                        @endforeach
                        </div>
                    </div>
                    @endif
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
            @if($article->show_form)
            <div class="article-page__comments">
                <div class="general-heading">
                    <h2 class="main-caption-l main-caption-l--transform">{{ __('main.Регистрация на мероприятие') }}</h2>
                </div>
                <form action="{{ url('applications/create') }}" method="post" class="article-page__comments-form">
                  @csrf
                    <input type="hidden" name="article_id" value="{{ $article->original_id? $article->original_id : $article->id  }}">
                    <div class="wrapper" id="application">
                        <div class="container-form">
                            <label class="input__wrapper @error('name') error @enderror">
                                <h5 class="input__caption">{{ __('main.ФИО') }} *</h5>
                                <input type="text" class="main-input" name="name" placeholder="{{ __('forms.placeholders.Введите имя') }}" value="{{ old('name') }}" autocomplete="name">
                                @error('name')
                                    <span class="error-text" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </label>
                            <label class="input__wrapper">
                                <h5 class="input__caption">{{ __('main.Должность') }}</h5>
                                <input type="text" class="main-input" name="extras[Должность]" placeholder="{{ __('main.Должность') }}" value="{{ old('extras')? old('extras')['Должность'] : '' }}">
                            </label>
                        </div>
                        <div class="container-form">
                            <label class="input__wrapper @error('organization') error @enderror">
                                <h5 class="input__caption">{{ __('main.Организация') }} *</h5>
                                <input type="text" class="main-input" name="organization" placeholder="{{ __('forms.placeholders.Название организации') }}" value="{{ old('organization') }}" autocomplete="organization">
                                @error('organization')
                                    <span class="error-text" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </label>
                            <label class="input__wrapper">
                                <h5 class="input__caption">{{ __('main.Сфера деятельности') }}</h5>
                                <input type="text" class="main-input" name="extras[Сфера деятельности]" placeholder="{{ __('main.Сфера деятельности') }}" value="{{ old('extras')? old('extras')['Сфера деятельности'] : '' }}">
                            </label>
                        </div>
                        <div class="container-form">
                            <label class="input__wrapper">
                                <h5 class="input__caption">{{ __('main.Страна') }}</h5>
                                <input type="text" class="main-input" name="extras[Страна]" placeholder="{{ __('main.Страна') }}" value="{{ old('extras')? old('extras')['Страна'] : '' }}">
                            </label>
                            <label class="input__wrapper">
                                <h5 class="input__caption">{{ __('main.Область') }}</h5>
                                <input type="text" class="main-input" name="extras[Область]" placeholder="{{ __('main.Область') }}" value="{{ old('extras')? old('extras')['Область'] : '' }}">
                            </label>
                        </div>
                        <div class="container-form">
                            <label class="input__wrapper">
                                <h5 class="input__caption">{{ __('main.Город') }}</h5>
                                <input type="text" class="main-input" name="extras[Город]" placeholder="{{ __('main.Город') }}" value="{{ old('extras')? old('extras')['Город'] : '' }}">
                            </label>
                            <label class="input__wrapper">
                                <h5 class="input__caption">{{ __('main.Почтовый индекс') }}</h5>
                                <input type="text" class="main-input" name="extras[Почтовый индекс]" placeholder="{{ __('main.Почтовый индекс') }}" value="{{ old('extras')? old('extras')['Почтовый индекс'] : '' }}">
                            </label>
                        </div>
                        <div class="container-form">
                            <label class="input__wrapper">
                                <h5 class="input__caption">{{ __('main.Улица') }}</h5>
                                <input type="text" class="main-input" name="extras[Улица]" placeholder="{{ __('main.Улица') }}" value="{{ old('extras')? old('extras')['Улица'] : '' }}">
                            </label>
                            <label class="input__wrapper">
                                <h5 class="input__caption">{{ __('main.Дом') }}</h5>
                                <input type="text" class="main-input" name="extras[Дом]" placeholder="{{ __('main.Дом') }}" value="{{ old('extras')? old('extras')['Дом'] : '' }}">
                            </label>
                            <label class="input__wrapper">
                                <h5 class="input__caption">{{ __('main.Квартира / офис') }} №</h5>
                                <input type="text" class="main-input" name="extras[Квартира / офис]" placeholder="{{ __('main.Квартира / офис') }} №" value="{{ old('extras')? old('extras')['Квартира / офис'] : '' }}">
                            </label>
                        </div>
                        <div class="container-form">
                            <label class="input__wrapper @error('email') error @enderror">
                                <h5 class="input__caption">Email *</h5>
                                <input type="email" class="main-input" name="email" placeholder="{{ __('forms.placeholders.Ваш электронный адрес') }}" value="{{ old('email') }}" autocomplete="email">
                                @error('email')
                                    <span class="error-text" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </label>
                            <label class="input__wrapper @error('phone') error @enderror">
                                <h5 class="input__caption">{{ __('forms.placeholders.Номер телефона') }} *</h5>
                                <input type="phone" class="main-input" name="phone" placeholder="{{ __('forms.placeholders.Номер телефона') }}" value="{{ old('phone') }}" autocomplete="phone">
                                @error('phone')
                                    <span class="error-text" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </label>
                            <input type="hidden" name="is_online" value="1">
                            <!-- <label class="checkbox__wrapper"><input type="checkbox" name="is_online" value="1" class="input-checkbox"> <span class="custome-checkbox"><span class="icon-active"></span></span> <span class="checkbox-text">Онлайн</span></label> -->
                            <!-- <div class="filter-check filter-check_application">
                                <label class="filter-check__wrapper">
                                    <input type="checkbox" name="is_online" class="filter-check__input filter-check__input_application" value="1" @if(old('is_online')) checked @endif> 
                                    <h4 class="general-filter__caption"></h4> 
                                    <span class="filter-check__decor-wrapper">
                                        <span class="filter-check__decor"></span>
                                    </span>
                                </label>
                            </div> -->
                        </div>
                    </div>
                    <button class="subscribe-block__button">{{ __('main.Зарегистрироваться') }}</button>
                </form>
            </div>
            @endif
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