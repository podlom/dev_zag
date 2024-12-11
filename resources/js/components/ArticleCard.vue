<template>
  <li class="popular__block__item">
    <a :href="article.category_link" class="popular__name-category">{{ article.category }}</a>
    <div class="popular__info__wrapper">
        <div class="popular__img" v-if="article.showImage" :class="{'popular__img_regions': article.parent_category_id == 202 || article.parent_category_id == 203}">
          <img src="" v-lazy="article.image" :alt="article.title + ' фото'" :title="article.title + ' фото'">
        </div>
        <div class="popular__info" :class="{'popular__info_statistics': !article.showImage}">
            <div class="popular__info__header">
                <h4 class="popular__name"><a :href="article.link">{{ article.title }}</a></h4>
                <button class="popular-button" @click="addToFavorites(article, 'articles')" :class="{active: $parent.favorites['articles'].includes(article.id) || $parent.favorites['articles'].includes(article.original_id)}" v-if="article.showFavoriteButton" :title="favorite_text">
                    <span class="icon-heart-outline"></span>
                </button>
            </div>
            <div class="popular__footer">
                <p class="popular__date" v-if="article.showDate">{{ article.date }}</p>
                <div class="popular__statistics" :class="{'popular__statistics_regions': !article.showDate}">
                    <p class="popular__views">
                        <span class="icon-eyes"></span>
                        <span>{{ article.views }}</span>
                    </p>
                    <p class="popular__comments">
                        <span class="icon-comment-text-outline"></span>
                        <span>{{ article.reviews_count }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
  </li>
</template>

<style scoped>
  .popular__img[lazy="loading"] {
    background-size: auto 1px;
  }
</style>

<script>
export default {
  name: 'articlecard',
  data: function(){
    return {
      article: this.dataArticle,
      favorite_text: favorite_text
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
