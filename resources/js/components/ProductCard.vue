<template>
  <li class="product__item" :class="classes">
    <div class="product__item__header">
        <p class="product__rating" v-if="product.rating">{{ product.rating }}</p>
        <p class="product__rating empty" v-else>Н.Д.</p>
        <p class="product__status" :class="{'completed': product.extras['status'] == 'done', 'build': product.extras['status'] == 'building', 'product__status_double' : product.second_status}">{{ product.status_string }} <span>{{ product.second_status }}</span></p>
        <div class="product__buttons">
            <button type="button" class="button-compare" v-if="product.category_id == 2 || product.category_id == 7" @click="addToComparison(product)" :class="{active: $parent.comparison.includes(product.id) || $parent.comparison.includes(product.original_id)}" :title="compare_text">
                <span class="icon-compare"></span>
            </button>
            <button type="button" class="button-favorites" @click="addToFavorites(product, 'products')" :class="{active: $parent.favorites['products'].includes(product.id) || $parent.favorites['products'].includes(product.original_id)}" :title="favorite_text">
                <span class="icon-heart-outline"></span>
            </button>
        </div>
    </div>
    <a :href="product.link" class="product__img">
          <img v-lazy="product.image" :alt="product.image_alt" :title="product.image_title">
    </a>
    <div class="product__item__body">
        <h3 class="product__name"><a :href="product.link">{{ product.name }}</a></h3>
        <div class="product__description">
            <p class="product__name-place">{{ product.type }}</p>
            <p class="product__price" v-if="product.price && product.show_price">{{ from }}<span>{{ product.price }}</span>грн/{{ product.area_unit }}</p>
        </div>
    </div>
    <div class="product__item__footer">
        <p class="product__city">
            <span class="icon-map-marker-outline"></span>
            <span class="name">{{ product.city }}</span>
        </p>
        <p class="procut__distance">
            <span class="icon-city-variant-outline"></span>
            <span>{{ product.extras['distance'] }} <span>км</span></span>
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
    name: 'productcard',
    data: function(){
      return {
	      image: null,
        product: this.dataProduct,
        classes: this.dataClasses,
        firstLoad: false,
        price: null,
        type: null,
        city: null,
        from: from,
        compare_text: compare_text,
        favorite_text: favorite_text
      }
    },
    props: {
      dataProduct: {'required': true}, 
      dataClasses: {}
    },
    methods: {
      addToFavorites: function(item, type) {
        this.$emit('add-to-favorites', item, type);
      },
      addToComparison: function(item, type) {
        this.$emit('add-to-comparison', item);
      },
      // loadCardFields: function(){
	    //   var component = this;
	      
		  // axios.get('/api/product/cardFields/' + component.product.id).then((response) => {
		  //     component.price = response.data.price
		  //     component.type = response.data.type
		  //     component.city = response.data.city
		  //  });
	      
      // },
    },
    watch: {
      dataProduct: {
        handler: function(value) {
          this.product = value;
        },
        deep: true
      },

      product: {
        handler: function(value) {
          var component = this;
          // setTimeout(function(){
          // 	component.loadCardFields();
          // }, 0);
        },
        deep: true
      }

    },
    created: function(){
      	var component = this;
        this.product.fake = 1;

	    // setTimeout(function(){
		  //   component.loadCardFields();
	    // }, 0)

    }
  }
</script>
