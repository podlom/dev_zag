<template>
  <li class="product__item" :class="classes">
    <div class="product__item__header">
        <p class="product__sale">{{ promotion.title }}</p>
        <p class="product__time-sale">{{ promotion.start }} - {{ promotion.end }}</p>
        <div class="product__buttons">
            <button class="button-favorites" @click="addToFavorites(promotion, 'promotions')" :class="{active: $parent.favorites['promotions'].includes(promotion.id) || $parent.favorites['promotions'].includes(promotion.original_id)}" :title="favorite_text">
                <span class="icon-heart-outline"></span>
            </button>
        </div>
    </div>
    <a :href="promotion.product_link" class="product__img">
        <img v-lazy="promotion.image" :alt="'Фото: ' + promotion.product_name" :title="'Картинка: ' + promotion.product_name">
        <span class="product__name">{{ promotion.product_name }}</span>
    </a>
    <div class="product__item__body">
        <div class="product__description">
            <p class="product__text"><a rel="nofollow" :href="promotion.link" v-html="promotion.desc" target="_blank"></a></p>
            <p class="product__name-company">
                <span class="company-logo" :style="{backgroundImage:'url(' + promotion.brand_logo + ')'}" v-if="promotion.brand_logo"></span>
                <a :href="promotion.brand_link">{{ promotion.brand_name }}</a>
            </p>
        </div>
    </div>
    <div class="product__item__footer">
        <p class="product__city">
            <span class="icon-map-marker-outline"></span>
            <span>{{ promotion.product_city }}</span>
        </p>
    </div>
  </li>
</template>

<style scoped>
  img[lazy="loading"] {
    margin: auto;
    width: initial;
    height: initial;
    object-fit: initial;
  }
</style>

<script>
export default {
  name: 'promotioncard',
  data: function(){
    return {
      promotion: this.dataPromotion,
      classes: this.dataClasses,
      favorite_text: favorite_text
    }
  },
  props: {
    'dataPromotion': {
      'required': true
    },
    'dataClasses' : {},
  },
  methods: {
    addToFavorites: function(item, type) {
      this.$emit('add-to-favorites', item, type);
    }
  },
  watch: {
    dataPromotion: {
      handler: function(value) {
        this.promotion = value;
      },
      deep: true
    }
  }
}
</script>
