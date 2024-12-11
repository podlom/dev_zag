@extends('layouts.app', [
  'meta_title' => $poll->meta_title? $poll->meta_title : $article->trueTitle,
  'meta_desc' => $poll->meta_desc? $poll->meta_desc : $article->trueTitle,
  'hide_from_index' => $article->hide_from_index
])

@section('content')
<main style="padding-bottom:60px">
    <div class="decor-background" style="background-image:url({{ url('img/background-img-2.png') }})"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
            {{ Breadcrumbs::render('poll_results', $article->category, $poll->h1? $poll->h1 : $article->trueTitle) }}
        </div>
    </section>
    <section class="article-page">
        <div class="article-page__wrapper container">
            <div class="general-heading">
                <h1 class="main-caption-l main-caption-l--transform">{{ $poll->h1? $poll->h1 : $article->trueTitle }}</h1>
            </div>
            @if($lang != 'ru')
            <a href="{{ $article->translation_link }}" class="translate-article">Читать статью на русском</a>
            @else
            <a href="{{ $article->translation_link }}" class="translate-article">Читати статтю українською</a>
            @endif
            <article class="article-main">
                <div class="article-main__header">
                    <p class="name">{{ $article->category->name }}</p>
                </div>
                <div class="article-main__body article-main__body_rating">
                <table>
                <tr>
                    <th></th>
                    <th>{{ __('main.Всего баллов') }}</th>
                @foreach($poll->options as $option)
                    <th>{{ $option->title }}</th>
                @endforeach
                </tr>
                @foreach($poll_answers as $product_id => $item)
                <tr>
                    @php
                    $product = \Aimix\Shop\app\Models\Product::where('id', $product_id)->orWhere('original_id', $product_id)->first();
                  @endphp
                @if($product)
                    <td style="text-align:left" title="{{ $product->name }}"><a href="{{ $product->link }}">{{ $product->name }}</a></td>
                    <td>{{ $item->sum('votes') }}</td>
                  @foreach($poll->options as $option)
                    @php
                      $option_id = $option->original_id? $option->original_id : $option->id;
                    @endphp
                    <td>{{ $item->where('option_id', $option_id)->first()? $item->where('option_id', $option_id)->first()->votes : '0' }}</td>
                  @endforeach
                @endif
                  </tr>
                  @endforeach
                </table>

                </div>
                <div class="article-main__footer">
                    <div class="general-social__wrapper" style="margin-left:auto">
                        <h5>{{ __('main.Поделиться ссылкой') }}:</h5>
                        <ul class="general__socila-list">
                            <li class="general__social-item">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ request()->url() }}" target="_blank" class="general__social-link social-facebook">
                                    <span class="icon-facebook"></span>
                                </a>
                            </li>
                            <li class="general__social-item">
                                <a href="https://twitter.com/intent/tweet?text={{ $article->title }}&url={{ request()->url() }}" target="_blank" class="general__social-link social-twitter">
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
                                <a href="#" title="Скопировать ссылку" class="general__social-link social-link">
                                    <span class="icon-link-variant"></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </article>
        </div>
    </section>
</main>

@endsection

@push('scripts')
<script src="{{ url('js/app.js?v=' . $version) }}"></script>
@endpush