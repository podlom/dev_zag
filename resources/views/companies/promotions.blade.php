@extends('companies.layouts.app')

@section('company_content')
<div class="company-page__content-tabs">
    <h3 class="company-page__info-caption">{{ __('main.Акции') }}</h3>
    @if($company->promotions->count())
    <div class="product__wrapper-list-position">
        <ul class="product__list product__list-sale">
            <promotioncard v-for="(promotion, key) in promotions" :key="key" :data-promotion="promotion" @add-to-favorites="addToFavorites"></promotioncard>
        </ul>
    </div>
    @else
    <div>{{ __('main.Нет данных') }}</div>
    @endif
</div>
@endsection