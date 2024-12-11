@extends('product.layouts.app')

@section('product_content')
<div class="product-page__main product-page__main-tabs">
  <div class="product-page__video">
      <h4 class="ts-product__video-title product-page__caption product-page__caption-l">{{ __('main.Видео о') }} {{ $product->name }}</h4>
      <ul class="product-page__video-list">
        @if($product->youtube_video)
        <li class="product-page__video-item">
            {!! $product->youtube_video !!}
        </li>
        @else
              <p class="text-center">{{ __('main.У этого объекта пока нет видео') }}</p>
        @endif
        @if($product->videos)
        @foreach($product->videos as $video)
          <li class="product-page__video-item">
            <video src="{{ url($video) }}" controls="controls"></video>
          </li>
        @endforeach
        @endif
      </ul>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ url('js/product/default.js?v=' . $version) }}"></script>
@endpush
