@extends('layouts.app', [
  'meta_title' => $page->meta_title,
  'meta_desc' => $page->meta_desc,
])

@section('content')
<main>
    <div class="decor-background" style="background-image:url(./img/background-img-2.png)"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
            {{ Breadcrumbs::render('policy', $page->main_title) }}
        </div>
    </section>
    <section class="article-page policy">
        <div class="article-page__wrapper container">
            <div class="general-heading">
                <h1 class="main-caption-l main-caption-l--transform">{{ $page->main_title }}</h1>
            </div>
            @if($lang != 'ru')
            <a href="{{ url( 'ru/'.substr(\Request::path(),3) ) }}" class="translate-article">Читать на русском</a>
            @else
            <a href="{{ url( 'uk/'.substr(\Request::path(),3) ) }}" class="translate-article">Читати українською</a>
            @endif
            <article class="article-main">
                <div class="article-main__body">
                {!! $page->content !!}
                </div>
            </article>
        </div>
    </section>
</main>
@endsection

@push('scripts')
<script src="{{ url('js/app.js?v=' . $version) }}"></script>
@endpush