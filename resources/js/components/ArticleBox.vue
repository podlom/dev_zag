<template>
  <div class="popular__wrapper container">
	    <div class="general-heading more">
	        <h2 class="main-caption-l main-caption-l--transform">Статьи о коттеджных городках и поселках {{ $region_name_genitive }}</h2>
	        <a :href="newsCategoryLink" class="read-more">
	            <span>Читать все статьи</span>
	            <span class="icon-arrow-more"></span>
	        </a>
	    </div>
	    <div class="popular__block">
	        <div class="popular__block__header">
	            <div class="wrapper">
	                <p class="popular__category-name">{{ __('main.Новости') }}</p>
	                <ul class="popular-sub-name__list">
	                    <li class="popular-sub-name__item" :class="{active: articleTab == 0}" @click="articleTab = 0">{{ __('main.Недвижимость') }}</li>
	                </ul>
	            </div>
	            <div class="wrapper">
	                <p class="popular__category-name">Статьи</p>
	                <ul class="popular-sub-name__list">
	                    <li class="popular-sub-name__item" :class="{active: articleTab == 1}" @click="articleTab = 1">{{ __('main.Строительство') }}</li>
	                    <li class="popular-sub-name__item" :class="{active: articleTab == 2}" @click="articleTab = 2">{{ __('main.Недвижимость') }}</li>
	                    <li class="popular-sub-name__item" :class="{active: articleTab == 3}" @click="articleTab = 3">{{ __('main.Аналитика') }}</li>
	                </ul>
	            </div>
	        </div>
	        <div class="popular__block__body">
	            <ul class="popular__block__list popular__block__list-main">
	                <articlecard v-for="(article, key) in articles" :key="key" :data-article="article" @add-to-favorites="addToFavorites"></articlecard>
	            </ul>
	        </div>
	    </div>
	    <div class="subscribe-block">
	        <h5 class="subscribe-block__text">{{ __('main.Нашли полезную информацию?') }}<br> {{ __('main.Подписывайтесь на актуальные публикации') }}:</h5>
	        @include('modules.subscription')
	    </div>
	</div>
</template>

<script>
export default {
  name: 'articlecard',
  data: function(){
    return {
      article: this.dataArticle
    }
  },
  props: {
    'dataArticle': {
      'required': true
    }
  },
  methods: {
    addToFavorites: function(item, type) {
      this.$emit('add-to-favorites', item, type);
    }
  },
  watch: {
    dataArticle: {
      handler: function(value) {
        this.article = value;
      },
      deep: true
    }
  },
}
</script>
