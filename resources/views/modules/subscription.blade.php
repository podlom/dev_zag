<form action="{{ route('subscribe') }}" method="post" class="subscribe-block__form" id="subscription">
    @csrf
    <input type="hidden" name="subscription_news" value="1">
    <label class="input__wrapper @error('subscription_email') error @enderror">
        <span class="subscribe-block__decor"></span>
        <input type="email" name="subscription_email" class='main-input' placeholder="{{ __('forms.placeholders.Ваш электронный адрес') }}">
        @error('subscription_email')
            <span class="error-text" role="alert">
                {{ $message }}
            </span>
        @enderror
    </label>
    <button class="subscribe-block__button">{{ __('main.Подписаться') }}</button>
</form>