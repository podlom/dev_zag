@extends('companies.layouts.app')

@section('company_content')
<div class="company-page__content-tabs">
    <h4 class="product-page__caption product-page__caption-l">{{ __('main.Местоположение') }}</h4>
    <p class="product-page__map-address">
        <span class="icon-map-marker-outline"></span>
        <span>{{ $company->address_string }}</span>
    </p>
    @if(isset($company->address['latlng']))
    <div class="product-page__map-container" id="general__map">
    </div>
    @else
    <div class="product-page__map-empty"><p>{{ __('main.Карта отсутствует') }}</p></div>
    @endif
</div>
@endsection