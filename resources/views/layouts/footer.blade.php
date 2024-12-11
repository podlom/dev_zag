<section class="social-banners">
	<div class="container">
		@include('includes.socialBanner')
	</div>
</section>
<footer class="footer">
    <div class="footer__header">
        <div class="footer__header__wrapper container">
            <div class="footer__write-us">
                <h5 class="footer__caption">{{ __('main.пишите нам') }}:</h5>
                <ul class="footer__socila-list footer__socila-list-write-us">
                    <li class="footer__social-item">
                        <a href="viber://chat?number={{ config('settings.vb') }}" class="footer__social-link social-viber" title="Viber" rel="nofollow">
                            <span class="icon-viber"></span>
                        </a>
                    </li>
                    <li class="footer__social-item">
                        <a href="whatsapp://send?phone={{ config('settings.wa') }}" class="footer__social-link social-whatsapp" title="Whatsapp" rel="nofollow">
                            <span class="icon-whatsapp"></span>
                        </a>
                    </li>
                    <li class="footer__social-item">
                        <a href="tg://resolve?domain={{ config('settings.tg') }}" class="footer__social-link social-telegram" title="Telegram" rel="nofollow">
                            <span class="icon-telegram"></span>
                        </a>
                    </li>
                </ul>
            </div>
            <ul class="footer__socila-list">
                <li class="footer__social-item">
                    <a href="{{ config('settings.fb') }}" target="_blank" class="footer__social-link social-facebook" title="Facebook" rel="nofollow">
                        <span class="icon-facebook"></span>
                    </a>
                </li>
                <li class="footer__social-item">
                    <a href="{{ config('settings.tw') }}" target="_blank" class="footer__social-link social-twitter" title="Twitter" rel="nofollow">
                        <span class="icon-twitter"></span>
                    </a>
                </li>
                <li class="footer__social-item">
                    <a href="{{ config('settings.inst') }}" target="_blank" class="footer__social-link social-instagram" title="Instagram" rel="nofollow">
                        <span class="icon-instagram"></span>
                    </a>
                </li>
                <li class="footer__social-item">
                    <a href="{{ config('settings.yt') }}" target="_blank" class="footer__social-link social-youtube" title="Youtube" rel="nofollow">
                        <span class="icon-youtube"></span>
                    </a>
                </li>
                <li class="footer__social-item">
                    <button class="footer__social-link js-button social-message" data-target="call-back" title="{{ __('main.Форма обратной связи') }}">
                        <span class="icon-message"></span>
                    </button>
                </li>
                <li class="footer__social-item">
                    <a href="{{ config('settings.in') }}" target="_blank" class="footer__social-link js-button linkedin-button" title="Linkedin" rel="nofollow">
                        <span class="icon-linkedin"></span>
                    </a>
                </li>
            </ul>
            <div class="footer__call-back" id="callback">
                <a href="tel:{{ explode(',', config('settings.phone'))[0] }}" class="footer__call-back__link" rel="nofollow">{{ explode(',', config('settings.phone'))[0] }}</a>
                <button class="footer__call-back__button js-button" data-target="footer-callback">{{ __('main.Обратный звонок') }}</button>
                <div class="popup popup-footer @error('callback_phone') active @enderror" data-target="footer-callback">
                    <button class="close-popup js-close">
                        <span class="decor"></span>
                    </button>
                    <form action="{{ url('feedback/create/callback') }}" method="post" class="footer-callback__form">
                        @csrf
                        <label class="input__wrapper @error('callback_name') error @enderror">
                            <span class="input__caption">{{ __('main.Имя') }}</span>
                            <input type="text" class="main-input" name="callback_name" value="{{ old('callback_name') }}" placeholder="{{ __('forms.placeholders.Как к вам обращаться?') }}">
                            @error('callback_name')
                                <span class="error-text" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </label>
                        <label class="input__wrapper @error('callback_phone') error @enderror">
                            <span class="input__caption">{{ __('main.Контактный телефон') }}*</span>
                            <input type="tel" class="main-input" name="callback_phone" value="{{ old('callback_phone') }}" placeholder="{{ __('forms.placeholders.Номер телефона') }}">
                            @error('callback_phone')
                                <span class="error-text" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </label>
                        <button class="main-button main-button-else">{{ __('main.Отправить') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="footer__menu">
        <div class="footer__menu__wrapper container js-drop-item">
            <button class="footer__mobile-button js-drop-button">
                <span>{{ __('main.Полезные ссылки') }}</span>
                <span class="icon-drop"></span>
            </button>
            <ul class="footer__menu__list">
                @foreach($footerMenu->children->sortBy('lft') as $key => $menuItem)
                <li class="footer__menu__item">
                    <a href="{{ $menuItem->url() }}" class="footer__menu__link">{{ $menuItem->name }}</a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="footer__category">
        <div class="footer__category__wrapper container">
            <ul class="footer__category__list">
                @foreach($footerSubMenu as $key => $menu)
                <li class="footer__category__item js-drop-item js-catagory-links-item">
                    <button class="footer__mobile-button js-drop-button">
                        <span>{{ str_replace(['(подвал)', '(підвал)'], '', $menu->name) }}</span>
                        <span class="icon-drop"></span>
                    </button>
                    <h5 class="footer__caption">{{ str_replace(['(подвал)', '(підвал)'], '', $menu->name) }}</h5>
                    <ul class="footer__sub-category__list">
                        @foreach($menu->children->sortBy('lft') as $menuItem)
                        <li class="footer__sub-category__item js-sub-link">
                            <a href="{{ $menuItem->url() }}" class="footer__sub-category__link">{{ $menuItem->name }}</a>
                        </li>
                        @endforeach
                    </ul>
                    <button class="footer__sub-button js-drop-button js-category-button">
                        <span class="icon-drop"></span>
                    </button>
                </li>
                @endforeach
            </ul>
            <div id="SinoptikInformer" style="width:350px;" class="SinoptikInformer type5c1"><div class="siHeader"><div class="siLh"><div class="siMh"><a onmousedown="siClickCount();" class="siLogo" href="https://sinoptik.ua/" target="_blank" rel="nofollow" title="Погода"> </a>Погода <span id="siHeader"></span></div></div></div><div class="siBody"><a onmousedown="siClickCount();" href="https://sinoptik.ua/погода-киев" title="Погода в Киеве" target="_blank"><div class="siCity"><div class="siCityName"><span>Киев</span></div><div id="siCont0" class="siBodyContent"><div class="siLeft"><div class="siTerm"></div><div class="siT" id="siT0"></div><div id="weatherIco0"></div></div><div class="siInf"><p>влажность: <span id="vl0"></span></p><p>давление: <span id="dav0"></span></p><p>ветер: <span id="wind0"></span></p></div></div></div></a><div class="siLinks">Погода на 10 дней от <a href="https://sinoptik.ua/погода-киев/10-дней" title="Погода на 10 дней" target="_blank" onmousedown="siClickCount();" rel="nofollow">sinoptik.ua</a></div></div><div class="siFooter"><div class="siLf"><div class="siMf"></div></div></div></div>
            </div>
    </div>
    <div class="footer__copyright">
        <div class="footer__copyright__wrapper container">
	        <div>
	            <span>© Copyright 2010-{{ now()->format('Y') }} - Zagorodna.com </span> | 
	            <a href="{{ $policyLink }}" class="footer__copyright__link">{{ __('main.Политика конфиденциальности') }}</a> | 
	            <a href="{{ url('sitemap.xml') }}" target="_blank" class="footer__copyright__link">{{ __('main.Карта сайта') }}</a>
	        </div>
	        
<!-- 	        include('includes.bigmir') -->
      

        </div>
    </div>
</footer>