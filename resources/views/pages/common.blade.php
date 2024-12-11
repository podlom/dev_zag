@extends('layouts.app', [
  'meta_title' => $page->meta_title,
  'meta_desc' => $page->meta_desc,
])

@section('content')
<main>
    <div class="decor-background" style="background-image:url(./img/background-img-2.png)"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
            {{ Breadcrumbs::render('page', $page->title) }}
        </div>
    </section>
    <section class="article-page policy">
        <div class="article-page__wrapper container">
            <div class="general-heading">
                <h1 class="ts-common-blade-pages-ln-17 main-caption-l main-caption-l--transform">{{ $page->title }}</h1>
            </div>
            @if($page->content)
            <article class="article-main">
                <div class="article-main__body">
                {!! $page->content !!}
                </div>
            </article>
            @endif
        </div>
    </section>

    @if($page->seo_text)
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
<script src="{{ url('js/app.js?v=' . $version) }}"></script>
@endpush
