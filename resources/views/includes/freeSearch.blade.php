<section class="free-search container">
    <div class="free-search__wrapper" v-lazy:background-image="'{{ url('img/free-search-bg.png') }}'">
        <h3 class="free-search__caption">{{ __('main.Бесплатный подбор недвижимости') }}</h3>
        <div class="free-search__body">
            <p><span>{{ __('main.подберем') }}</span>за</p>
            <h4 class="free-search__number">5</h4>
            <p><span>{{ __('main.вариантов') }}</span>{{ __('main.шагов') }}</p>
        </div>
        <button class="call-back__button js-button" data-target="free_selection">{{ __('main.Подобрать бесплатно') }}</button>
    </div>
</section>