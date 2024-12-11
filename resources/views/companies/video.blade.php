@extends('companies.layouts.app')

@section('company_content')
<div class="company-page__content-tabs">
    @if($company->videos && count($company->videos))
    <h4 class="product-page__caption product-page__caption-l">{{ __('main.Видео') }}</h4>
    <ul class="product-page__video-list">
      @foreach($company->videos as $video)
      <li class="product-page__video-item">
        <video src="{{ url($video) }}" controls="controls"></video>
      </li>
      @endforeach
    </ul>
    @else
    <div>{{ __('main.Нет данных') }}</div>
    @endif
</div>
@endsection