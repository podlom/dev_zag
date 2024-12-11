require('../bootstrap');

import search from '../mixins/search.js'
import favoritesMix from '../mixins/favoritesMix.js'
import notificationsMix from '../mixins/notificationsMix.js'
import promotioncard from '../components/PromotionCard'
import comparisonMix from '../mixins/comparisonMix.js'

const app = new Vue({
  el: '#app',
  data: {
    promotions: promotions,
    regions: regions,
    cities: cities,
    types: types,
    companies: companies,
    query: {
      isJson: true,
      region: null,
      city: null,
      type: type,
      company: null,
      page: page,
      sort: 'created_at_desc',
    },
	  append: false,
    loading: false,
    title: title,
    seo_title: seo_title,
    seo_text: seo_text,
    types_slugs: types_slugs,
    slug: slug,
    lang: lang
  },
  mixins: [search, favoritesMix, notificationsMix, comparisonMix],
  components: {
    promotioncard
  },
  methods: {
    loadmore: function() {
      this.append = true;
      this.query.page++;
    }
  },
  watch: {
    'query.company': function() {
      this.query.page = 1;
    },
    'query.region': function() {
      this.query.city = null;
      this.query.company = null;
      this.query.type = null;
      this.loading = true;
      this.query.page = 1;
    },
    'query.city': function() {
      this.query.company = null;
      this.query.type = null;
      this.query.page = 1;
      this.loading = true;
    },
    'query.type': function(value) {
      this.loading = true;
      this.query.page = 1;

      let thisUrl = !value? location.protocol + '//' + location.host + '/' + this.lang + '/' + this.slug : location.protocol + '//' + location.host + '/' + this.lang + '/' + this.slug + '/' + this.types_slugs[value];

      history.pushState({
          id: 'promotions'
      }, 'Promotions', thisUrl);
    },
    'query.page': function(value) {
      if(!this.append)
        this.loading = true;
        
      let thisUrl = !this.query.type? location.protocol + '//' + location.host + '/' + this.lang + '/' + this.slug : location.protocol + '//' + location.host + '/' + this.lang + '/' + this.slug + '/' + this.types_slugs[this.query.type];

      if(value > 1)
        thisUrl += '?page=' + value;

        history.pushState({
          id: 'promotions'
      }, 'Promotions', thisUrl);
    },
    query: {
      handler: function(value, oldValue) {
        
        let thisUrl = location.protocol + '//' + location.host + location.pathname;
        
        var valueClone = _.cloneDeep(value);

        axios.post(thisUrl, valueClone).then(response => {
          let itterations = 1;
          
          if(this.append){
            let currentData = this.promotions.data;
            this.promotions = response.data.promotions;
                
            currentData.reverse().forEach(item => {
              this.promotions.data.unshift(item);
              
              if(itterations == currentData.length)
                this.append = false;
                
              itterations++;
            });
          }else
            this.promotions = response.data.promotions;
          
          this.cities = response.data.cities;
          this.companies = response.data.companies;
          // this.years = response.data.years;
          this.title = response.data.title;
          document.title = response.data.meta_title + ' – Zagorodna.com';
          this.seo_title = response.data.seo_title;
          this.seo_text = response.data.seo_text;
          this.loading = false;
        });
      },
      deep: true
    }
  },
  created: function(){
    console.log('promotions vue created');
  }
});