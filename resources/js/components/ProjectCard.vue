<template>
  <li class="product__item" :class="{'product__item-sales': project.status == 'Продано' }">
    <div class="product__item__header">
        <p class="product__status build" v-if="project.status == 'Строится' || project.status == 'Проект'">{{ project.status_string }}</p>
        <p class="product__status sales" v-if="project.status == 'Продано'">{{ project.status_string }}</p>
        <p class="product-page__availability" v-if="project.status == 'Построено'">{{ project.status_string }}</p>
        <div class="product__buttons">
            <button class="button-favorites" @click="addToFavorites(project, 'projects')" :class="{active: $parent.favorites['projects'].includes(project.id) || $parent.favorites['projects'].includes(project.original_id)}">
                <span class="icon-heart-outline"></span>
            </button>
        </div>
    </div>
    <a :href="project.link" class="product__img">
        <img v-lazy="project.images && project.images.length? project.images[0] : project.product_image" :alt="'Фото: ' + project.product_name" :title="'Картинка: ' + project.product_name" :style="{objectFit: project.product_category_id == 2 || project.product_category_id == 7? 'contain' : 'cover' }">
    </a>
    <div class="product__item__body">
        <h3 class="product__name"><a :href="project.link">{{ project.name }}</a></h3>
        <div class="product__description">
            <p class="product__name-place">{{ __('main.' + project.type) }}</p>
            <p class="product__price">{{ from }}<span>{{ project.price }}</span>грн/кв.м</p>
        </div>
    </div>
    <div class="product__item__footer">
        <div class="product-page__info-about-house">
            <div class="area about-house-wrapper" v-if="project.area">
                <div class="area-img img" style="background-image:url('/img/area-icon.png')"></div>
                <p>{{ project.area }} м<sup>2</sup></p>
            </div>
            <div class="floor about-house-wrapper" v-if="project.floors" style="margin-left:auto">
                <div class="floor-img img" style="background-image:url('/img/floor-icon.png')"></div>
                <p>{{ project.floors }}</p>
            </div>
            <div class="rooms about-house-wrapper" v-if="project.bedrooms" style="margin-left:auto">
                <div class="rooms-img img" style="background-image:url('/img/rooms-icon.png')"></div>
                <p>{{ project.bedrooms }}</p>
            </div>
            <div class="rooms about-house-wrapper" v-if="project.rooms" style="margin-left:auto">
                <div class="rooms-img img" style="background-image:url('/img/rooms-icon.png')"></div>
                <p>{{ project.rooms }}</p>
            </div>
        </div>
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
    name: 'projectcard',
    data: function(){
      return {
        project: this.dataProject,
        classes: this.dataClasses,
        from: from,
      }
    },
    props: {
      dataProject: {'required': true},
      dataClasses: {}
    },
    methods: {
      addToFavorites: function(item, type) {
        this.$emit('add-to-favorites', item, type);
      }
    },
    watch: {
      dataProject: {
        handler: function(value) {
          this.project = value;
        },
        deep: true
      },
    },
  }
</script>
