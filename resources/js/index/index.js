require('../bootstrap');

import search from '../mixins/search.js'
import favoritesMix from '../mixins/favoritesMix.js'
import notificationsMix from '../mixins/notificationsMix.js'
import comparisonMix from '../mixins/comparisonMix.js'

import productcard from '../components/ProductCard'
// import productcard from '../components/ProductCard'
// import articlecard from '../components/ArticleCard'
import promotioncard from '../components/PromotionCard'

const app = new Vue({
  el: "#app",
  data: {
    cottages: [],
    newbuilds: [],
    articles: [],
    reviews: reviews,
    promotions: [],
    hits: [],
    articleTab: 0,
    cottages_slug : cottages_slug,
    newbuilds_slug: newbuilds_slug,
    newsCategoryLink: '',
    regions: regions,
    areas: {},
    cities: {},
    address: {
      region: null,
      area: null,
      city: null
    },
    iterations_area: 0,
    iterations_city: 0,
    main_search_type: 1,
    lang: lang,
    average_price: null,
    range_options: range_options,
  },
  components: {
    reviewcard: () => import('../components/ReviewCard'),
    productcard: () => import('../components/ProductCard'),
    articlecard: () => import('../components/ArticleCard'),
    promotioncard: promotioncard
  },
  mixins: [search, favoritesMix, notificationsMix, comparisonMix],
  methods: {
    async getProducts(category = null,  is_hit = false) {
      let component = this;
      
      try {
	      axios.post('/' + lang + '/getProducts', {category: category, is_hit: is_hit}).then(response => {
	        if(is_hit)
	          component.hits = response.data;
	        else if(category === component.cottages_slug)
	          component.cottages = response.data;
	        else if(category === component.newbuilds_slug)
	          component.newbuilds = response.data;
	      });
      } catch (ex) {
        console.log(ex);
      }
    },
    getArticles: function() {
      let component = this;
      console.log(lang);
      axios.post('/' + lang + '/getArticles/' + this.articleTab).then(response => {
        component.articles = response.data.articles;
        component.newsCategoryLink = response.data.newsCategoryLink;
      });
    },
    getPromotions: function() {
      let component = this;
      
      axios.post('/' + lang + '/getPromotions').then(response => {
        component.promotions = response.data.promotions;
      });
    },
    getAreas: function() {
      let component = this;
      component.iterations_area++;
      let iterations = component.iterations_area;
      
      setTimeout(() => {
        if(iterations != component.iterations_area)
          return;

        axios.post('/getAreas', {'region': this.address.region}).then(response => {
          console.log(response.data.areas);
          component.areas = response.data.areas;
        });

      }, 250);
    },
    getCities: function() {
      let component = this;
      component.iterations_city++;
      let iterations = component.iterations_city;

      setTimeout(() => {
        if(iterations != component.iterations_city)
          return;

          axios.post('/getCities', {'area': this.address.area}).then(response => {
            component.cities = response.data.cities;
          });
      }, 250);
    },
    checkAveragePrice: function() {
      let range = this.main_search_type? [this.range_options.min_2, this.range_options.max_2] : [this.range_options.min_1, this.range_options.max_1];

      if(!this.average_price)
        return;

      if(this.average_price < range[0])
        this.average_price = +range[0];

      if(this.average_price > range[1])
        this.average_price = +range[1];
    }
  },
  watch: {
    articleTab: function(value) {
      this.getArticles();
    },
    'address.region': function(value) {
      this.address.area = null;
      this.areas = [];
      if(!value)
        return;

      this.getAreas();
    },
    'address.area': function(value) {
      this.address.city = null;
      this.cities = [];
      if(!value)
        return;

      this.getCities();
    },
    main_search_type: function() {
      this.checkAveragePrice();
    }
  },
  computed: {
    searchFormAction: function() {
      let link = '/' + lang + '/';
      link = this.main_search_type? link + this.cottages_slug : link + this.newbuilds_slug;
      link = link + '/catalog';

      return link;
    },
    price: function() {
      return {
        min: this.average_price? +this.average_price - 5000 : 0,
        max: this.average_price? +this.average_price + 5000 : 0
      };
    }
  },
  created: function() {
	  let component = this;
	  
	  window.addEventListener("load", function(event) {
	      component.getArticles();
	      component.getPromotions();
	      component.getProducts(null, true);
	      component.getProducts(this.cottages_slug);
        component.getProducts(this.newbuilds_slug);
        
        setTimeout(() => {
          document.showMoreInfo();
        }, 1000);
    });
      
  }
});



/*
	Vue.component('productcard', function(resolve, reject) {
		window.addEventListener("load", function(event) {
	    	resolve(require('../components/ProductCard'))
	    })
	})
	
	Vue.component('articlecard', function(resolve, reject) {
		window.addEventListener("load", function(event) {
			resolve(require('../components/ArticleCard'))
		})
	})
	
	Vue.component('promotioncard', function(resolve, reject) {
		window.addEventListener("load", function(event) {
	    	resolve(require('../components/PromotionCard'))
	    })
	})
*/
