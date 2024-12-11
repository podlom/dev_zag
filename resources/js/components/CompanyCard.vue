<template>
  <li class="best-company-info__item">
    <div class="best-company-info__header">
        <div class="info">
            <h4> 
                <a :href="company.link">{{ company.name }}</a>
            </h4>
            <a :href="company.link">{{ company.category_name }}</a>
        </div>
        <div class="best-company-info__button-list">
            <button class="best-company-info__button" @click="addToFavorites(company, 'companies')" :class="{active: $parent.favorites['companies'].includes(company.id) || $parent.favorites['companies'].includes(company.original_id)}" :title="favorite_text">
                <span class="icon-heart-outline"></span>
            </button>
            <button class="best-company-info__button" v-if="company.category_id == 1 || company.category_id == 18" @click="addToNotifications(company, 'companies')" :class="{active: $parent.notifications['companies'].includes(company.id) || $parent.notifications['companies'].includes(company.original_id)}">
                <span class="icon-bell-outline"></span>
            </button>
        </div>
    </div>
    <div class="best-company-info__footer">
        <ul class="best-company-info__contact-list">
            <li class="best-company-info__contact-item">
                <span class="icon-web"></span>
                <span class="text">{{ company.contacts['site']? company.contacts['site'] : 'н.д.' }}</span>
            </li>
            <li class="best-company-info__contact-item">
                <span class="icon-phone-outline"></span>
                <span class="text">{{ company.contacts['phone']? company.contacts['phone'] : 'н.д.' }}</span>
            </li>
            <li class="best-company-info__contact-item">
                <span class="icon-map-marker-outline"></span>
                <span class="text">{{ company.city? company.city : 'н.д.' }}</span>
            </li>
        </ul>
        <div class="wrapper">
            <ul class="best-company-info__social-list">
                <li class="best-company-info__social-item" v-if="company.contacts['fb']">
                    <a :href="company.contacts['fb']" class="best-company-info___social-link">
                        <span class="icon-facebook"></span>
                    </a>
                </li>
                <li class="best-company-info__social-item" v-if="company.contacts['inst']">
                    <a :href="company.contacts['inst']" class="best-company-info___social-link">
                        <span class="icon-instagram"></span>
                    </a>
                </li>
            </ul>
            <div class="best-company-info__img">
                <a :href="company.link">
                    <img v-lazy="company.business_card" :alt="'Фото: ' + company.name" :title="'Картинка: ' + company.name">
                </a>
            </div>
        </div>
    </div>
  </li>
</template>

<script>
export default {
  name: 'companycard',
  data: function(){
    return {
      company: this.dataCompany,
      favorite_text: favorite_text
    }
  },
  props: {
    'dataCompany': {
      'required': true
    }
  },
  methods: {
    addToFavorites: function(item, type) {
      this.$emit('add-to-favorites', item, type);
    },
    addToNotifications: function(item, type) {
      this.$emit('add-to-notifications', item, type);
    }
  },
  watch: {
    dataCompany: {
      handler: function(value) {
        this.company = value;
      },
      deep: true
    }
  },
}
</script>
