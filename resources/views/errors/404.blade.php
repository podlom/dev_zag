@extends('layouts.app', [
  'translation_link' => $lang === 'ru'? url('uk') : url('ru')
  ])

@section('content')
  <div class="page-404">
    <div class="container">
      <h1 class="page-404_title">{{ __('main.Похоже, то что вы искали') }} <br> {{ __('main.где-то потерялось, днем с огнем не сыщешь') }}</h1>
      <a href="{{ url('/') }}" class="main-button-more page-404_button">{{ __('main.На главную') }}</a>
    </div>
  </div>
@endsection

@push('styles')
<style>
  .page-404 {
    height: 50vw;
    width: 100%;
    background: 50% 0% / cover no-repeat;
    background-image: url("{{ url('img/404.png') }}");
    padding-top: 90px;
  }
  .page-404_title {
    font-size: 24px;
    line-height: 1.5;
    font-weight: 800;
    text-transform: uppercase;
    color: #fff;
    text-align: center;
    margin-bottom: 27vw;
  }
  .page-404_button {
    margin: auto;
    width: 255px;
  }
  @media screen and (max-width: 1480px) {
    .page-404 {
      padding-top: 60px;
    }
    .page-404_title {
      margin-bottom: calc(29vw - 50px);
    }
  }
  @media screen and (max-width: 1169px) {
    .page-404_title {
      font-size: 20px;
      margin-bottom: calc(29vw - 75px);
    }
  }
  @media screen and (max-width: 767px) {
    .page-404 {
      padding-top: 30px;
      height: 300px;
      background-size: calc(40vw + 500px);
    }
    .page-404_title {
      font-size: 11px;
      margin-bottom: 150px;
    }
  }
</style>
@endpush

@push('scripts')
<script src="{{ url('js/app.js?v=' . $version) }}"></script>
@endpush