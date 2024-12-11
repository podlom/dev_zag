@extends('companies.layouts.app')

@section('company_content')
<div class="company-page__content__info">
    <div class="company-page__full-info">
        <div class="company-page__full-info__text">
            {!! $company->description !!}
        </div>
    </div>
    @if($company->activity)
    <div class="company-page__actions">
        <h3 class="company-page__info-caption">{{ __('main.Деятельность компании') }}</h3>
        <ul class="company-page__actions__list">
            @foreach(json_decode($company->activity) as $item)
            <li class="company-page__actions__item">{{ $item->text }}</li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
@if($company->achievements)
<div class="company-page__slider slider-infinity">
    <h3 class="company-page__info-caption">{{ __('main.Достижения') }}</h3>
    <ul class="company-page__slider__list js-infinity-slider-list">
        @foreach($company->achievements as $key => $item)
        <li class="company-page__slider__item js-slider-item-infinity @if($key == 0) show @endif">
            <div class="company-page__slider__img">
                <img src="{{ url($item['image']) }}" alt="{{ $item['name'] }}">
            </div>
            <p class="company-page__slider__description">{{ $item['name'] }}</p>
        </li>
        @endforeach
    </ul>
    @if(count($company->achievements) > 4)
    <div class="general-button__wrapper js-arrow-infinity container">
        <div class="wrapper">
            <button class="general-button prev">
                <span class="icon-arrow-left"></span>
            </button>
            <button class="general-button next">
                <span class="icon-arrow-right"></span>
            </button>
        </div>
    </div>
    @endif
</div>
@endif
@endsection