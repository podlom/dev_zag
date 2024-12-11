@extends('layouts.app', [
    'meta_title' => $page[$type . '_meta_title'],
    'meta_desc' => $page[$type . '_meta_desc'],
])

@section('content')
<main>
    <div class="decor-background" style="background-image:url({{ url('img/background-img-2.png') }})"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
            {{ Breadcrumbs::render('precatalog_statistics', $category) }}
        </div>
    </section>
    <section class="rating-block rating-block-info" style="padding-top:30px">
        <div class="general-heading container">
            <h1 class="main-caption-l main-caption-l--transform">{{ $page[$type . '_title'] }}</h1>
        </div>
        <div class="rating-block__wrapper container">
            <div class="rating-drop__info">
                <div class="js-drop-item rating-drop__wrapper active">
                    <div class="rating-block__general-wrapper">
                        <ul class="rating-block__list rating-block__list-diagram">
                            <li class="rating-block__item">
                                <div class="rating-block__item__header">
                                    <span class="rating-block-icon-diagram"></span>
                                    <h3 class="rating-block__item__caption">{{ __('main.Статистика') }} (грн) - {{ __('main.type_' . $type . '_plural') }}</h3>
                                </div>
                                <div class="rating-block__table">
                                    <div class="wrapper">
                                        <div class="rating-block__general-info">
                                            <a href="{{ route($lang . '_precatalog', $category->slug) }}" class="name">{{ __('main.Украина') }}</a>
                                            <p class="date">{{ $statistics->first()->date }} - <span>{{ $statistics->first()->total }}</span></p>
                                            <p class="date">{{ $statistics->last()->date }} - <span>{{ $statistics->last()->total }}</span></p>
                                        </div>
                                        @if($type == 'cottage')
                                        <div class="rating-block__general-info">
                                            <a href="{{ route($lang . '_precatalog', $category->slug) . '/region/' . \App\Region::where('region_id', 29)->first()->slug }}" class="name">{{ __('main.Киев') }}</a>
                                            <p class="date">{{ $statistics->first()->date }} - <span>{{ $statistics->first()->data['29'] }}</span></p>
                                            <p class="date">{{ $statistics->last()->date }} - <span>{{ $statistics->last()->data['29'] }}</span></p>
                                        </div>
                                        @else
                                        <div class="rating-block__general-info">
                                            <a href="{{ route($lang . '_precatalog', $category->slug) . '/region/' . \App\Region::where('region_id', 11)->first()->slug }}" class="name">{{ __('main.Киевская') }}</a>
                                            <p class="date">{{ $statistics->first()->date }} - <span>{{ $statistics->first()->data['11'] }}</span></p>
                                            <p class="date">{{ $statistics->last()->date }} - <span>{{ $statistics->last()->data['11'] }}</span></p>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="rating-block__table__caption">
                                        <p class="table-number">№</p>
                                        <p class="table-area">{{ __('main.Область') }}</p>
                                        <p class="table-date">{{ $statistics->first()->date }}</p>
                                        <p class="table-date">{{ $statistics->last()->date }}</p>
                                    </div>
                                    <div class="wrapper">
                                        @php
                                        $i = 1;
                                        $data = $statistics->first()->data;
                                        arsort($data);
                                        @endphp
                                        @foreach($data as $key => $item)
                                        @if($key != 5 && $key != 13) <!-- Луганская и Донецкая -->
                                        @if((($type == 'cottage' && $key != 29) || ($type == 'newbuild' && $key != 11)) && $item)
                                        @php
                                        $reg = \App\Region::where('region_id', $key)->first();
                                        @endphp
                                        <div class="rating-block__table__item">
                                            <p class="table-number">{{ $i++ }}</p>
                                            <a href="{{ route($lang . '_precatalog', $category->slug) . '/region/' . $reg->slug }}" class="table-name">{{ $reg->name }}</a>
                                            <p class="table-rating">{{ $item }}</p>
                                            <p class="table-rating">{{ $statistics->last()->data[$key] }}</p>
                                        </div>
                                        @endif
                                        @endif
                                        @endforeach
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @if($page[$type . '_seo_text'])
    <section class="info-block">
        <div class="info-block__wrapper container">
            <!-- <div class="general-heading">
                <h2 class="main-caption-l main-caption-l--transform"></h2>
            </div> -->
            <div class="info-block__container">
                <div class="info-block__inner">
                {!! $page[$type . '_seo_text'] !!}
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