require('../bootstrap');

import search from '../mixins/search.js'
import favoritesMix from '../mixins/favoritesMix.js'
import notificationsMix from '../mixins/notificationsMix.js'
import comparisonMix from '../mixins/comparisonMix.js'

import reviewcard from '../components/ReviewCard'
import articlecard from '../components/ArticleCard'
import productcard from '../components/ProductCard'

import VueSlider from 'vue-slider-component/dist-css/vue-slider-component.umd.min.js'
import 'vue-slider-component/dist-css/vue-slider-component.css'

// import theme
import 'vue-slider-component/theme/default.css'

const app = new Vue({
  el: "#app",
  data: {
	// OBJECTS
    products: {data:[],total:1},
    products_for_map: {data:[],total:1},
    done: {data:[],total:1},
    building: {data:[],total:1},
    project: {data:[],total:1},
    sold: {data:[],total:1},
    frozen: {data:[],total:1},
    other_category: {data:[],total:1},
    // ANOTHER DATA
    reviews: [],
    companies: {data:[],total:1},
    promotions: promotions,
    articles: [],
    articleTab: 0,
    radius: 20,
    regions: regions,
    areas: {},
    cities: {},
    address: address,
    search: {
      region: '',
      area: '',
      city: '',
    },
    iterations_area: 1,
    iterations_city: 1,
    latlng: {
      lat: '',
      lng: '',
    },
    category_slug: category_slug,
    other_category_slug: other_category_slug,
    newsCategoryLink: '',
    type: type,
    status: status,
    objectType: objectType,
    map_filled: false,
    prices: {
      min: null,
      max: null,
      avg: null,
      products: {data:[],total:1}
    }
  },
  components: {
    productcard:  productcard,
    reviewcard: () => import('../components/ReviewCard'),
    companycard: () => import('../components/CompanyCard'),
    promotioncard: () => import('../components/PromotionCard'),
    // articlecard: () => import('../components/ArticleCard'),
    articlecard: articlecard,
    VueSlider
  },
  mixins: [search, favoritesMix, notificationsMix, comparisonMix],
  computed: {
    fullAddress: function() {
      let result = '';

      if(this.address.region && Object.keys(this.regions).length)
        result += this.regions[this.address.region];
        
      if(this.address.kyivdistrict && Object.keys(this.areas).length)
        result += ', ' + this.areas[this.address.kyivdistrict];

      if(this.address.area && Object.keys(this.areas).length)
        result += ', ' + this.areas[this.address.area];

      if(this.address.city && Object.keys(this.cities).length)
        result += ', ' + this.cities[this.address.city];

      return result;
    },
    pricesProducts: function() {
      let items = [];

      this.prices.products.data.forEach(function(product) {
        let include = true;
        
        items.forEach(function(item) {
          if(product.id === item.id)
            include = false;
        });

        if(include && items.length < 10)
          items[items.length] =  product;
      });

      return items;
    }
  },
  methods: {
    getAreas: function() {
      let component = this;
      component.iterations_area++;
      let iterations = component.iterations_area;

      setTimeout(() => {
        if(iterations != component.iterations_area)
          return;

        axios.post('/getAreas', {'region': this.address.region}).then(response => {
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
    getArticles: function() {
      let component = this;
      
      axios.get('/' + lang + '/article/byArea/' + this.articleTab + '/' + this.address.region).then(response => {
        component.articles = response.data.articles;
        component.newsCategoryLink = response.data.newsCategoryLink;
      });
    },
    getReviews: function() {
      let component = this;
      
      axios.post('/getReviews', {region: this.address.region, area: this.address.area, city: this.address.city, kyivdistrict: this.address.kyivdistrict, type: this.type}).then(response => {
        component.reviews = response.data.reviews;
      });
    },
    getProducts(category = null, status = null, type = null, number = null, main = false, map = false) {
      let component = this;
      
      axios.post('/' + lang + '/getProducts', {region: this.address.region, area: this.address.area, city: this.address.city, kyivdistrict: this.address.kyivdistrict, category: category, status: status, type: type, number: number}).then(response => {
	      
        if(main)
          component.products = response.data;
        else if(map)
          component.products_for_map = response.data;
        else if(status)
          component[status] = response.data;
        else if(category == component.other_category_slug)
          component.other_category = response.data;
      });
    },
    getCompanies: function(){
	    let component = this;
	    
	    axios.get('/' + lang + '/company/byArea/' + this.address.region + '/' + this.address.area + '/' + this.address.city).then(response => {
		    component.companies = response.data
	    });
    },
    getPrices: function(){
      let component = this;
      axios.post('/getPrices', {region: this.address.region, area: this.address.area, city: this.address.city, kyivdistrict: this.address.kyivdistrict, category: this.category_slug, status: this.status, type: this.objectType}).then(response => {
        component.prices = response.data;
      });
    }
  },
  watch: {
    // 'prices.products.data': function(value) {
    //   let component = this;
    //   for(let key in value) {
    //     axios.get('/api/product/cardFields/' + value[key].id).then((response) => {
    //       component.prices.products.data[key].price = response.data.price;
    //     });
    //   }
    // },
    'products_for_map.data': {
      handler: function(products) {
        if(this.map_filled)
          return;

        // if response came before map had been loaded
        if(!document.map) {
          setTimeout(() => {
            Object.keys(products).forEach(function(key) {
              if(products[key].lng && products[key].lat) {
                var marker = new mapboxgl.Marker()
                .setLngLat([products[key].lng, products[key].lat])
                .setPopup(new mapboxgl.Popup().setHTML('<a target="_blank" href="' + products[key].link + '">' + products[key].name + '</a>'))
                .addTo(document.map);
              }
            });
          }, 2000);
        } else {
          Object.keys(products).forEach(function(key) {
            if(products[key].lng && products[key].lat) {
              var marker = new mapboxgl.Marker()
              .setLngLat([products[key].lng, products[key].lat])
              .setPopup(new mapboxgl.Popup().setHTML('<a target="_blank" href="' + products[key].link + '">' + products[key].name + '</a>'))
              .addTo(document.map);
            }
          });
        }

        this.map_filled = true;

      },
      deep: true
    },
    'search.region': function(value) {
      if(value)
        this.address.region = null;
    },
    'search.area': function(value) {
      if(value) {
        this.address.area = null;
        this.address.kyivdistrict = null;
      }

      this.getAreas();
    },
    'search.city': function(value) {
      if(value)
        this.address.city = null;

      this.getCities();
    },
    'address.region': function(value) {
      this.address.area = null;
      this.address.kyivdistrict = null;
      this.search.area = '';
      this.search.region = '';
      this.areas = [];
      if(!value)
        return;

      this.getAreas();
    },
    'address.area': function(value) {
      this.address.city = null;
      this.search.area = '';
      this.search.city = '';
      this.cities = [];
      if(!value || this.address.region == 29)
        return;

      this.getCities();
    },
    'address.city': function(value) {
      this.search.city = '';
    },
    articleTab: function() {
      this.getArticles();
    }
  },
  created: function() {
    let component = this;
    
	  window.addEventListener("load", function(event) {
      component.getProducts(component.category_slug, component.status, component.objectType, 6, true);

      setTimeout(() => {
        component.getProducts(component.category_slug, 'done', component.objectType);
        component.getProducts(component.category_slug, 'building', component.objectType);
        component.getProducts(component.category_slug, 'project', component.objectType);
        component.getProducts(component.category_slug, 'sold', component.objectType);
        component.getProducts(component.category_slug, 'frozen', component.objectType);
        component.getProducts(component.other_category_slug);
        component.getProducts(component.category_slug, component.status, component.objectType, 999999, false, true);
        component.getPrices();
        component.getCompanies();
        component.getArticles();
        component.getReviews();

        if(component.address.region)
          component.getAreas();
    
        if(component.address.area && component.address.city != 29)
          component.getCities();
      }, 3000);
     })
     
  }
});