require('../bootstrap');

import search from '../mixins/search.js'
import favoritesMix from '../mixins/favoritesMix.js'
import notificationsMix from '../mixins/notificationsMix.js'
import comparisonMix from '../mixins/comparisonMix.js'
import productcard from '../components/ProductCard'
import VueSlider from 'vue-slider-component/dist-css/vue-slider-component.umd.min.js'
import 'vue-slider-component/dist-css/vue-slider-component.css'

// import theme
import 'vue-slider-component/theme/default.css'

const app = new Vue({
  el: "#app",
  data: {
    products: {
      data: []
    },
    filters: filters,
    sorts: sorts,
    per_page: [15, 30, 60],
    query: selecterFilters,
    append: false,
    rangeOptions: rangeOptions,
    regions: regions,
    areas: {},
    cities: {},
    iterations_area: 1,
    iterations_city: 1,
    search: {
      region: '',
      area: '',
      city: '',
    },
    loading: true
  },
  components: {
    productcard,
    VueSlider
  },
  mixins: [search, favoritesMix, notificationsMix, comparisonMix],
  computed: {
    fullAddress: function() {
      let result = '';

      if(this.query.address.region && Object.keys(this.regions).length)
        result += this.regions[this.query.address.region];

      if(this.query.address.kyivdistrict && Object.keys(this.areas).length)
        result += ', ' + this.areas[this.query.address.kyivdistrict];

      if(this.query.address.area && Object.keys(this.areas).length)
        result += ', ' + this.areas[this.query.address.area];

        
      if(this.query.address.city && Object.keys(this.cities).length)
        result += ', ' + this.cities[this.query.address.city];

      return result;
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

        axios.post('/getAreas', {'region': this.query.address.region}).then(response => {
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

        axios.post('/getCities', {'area': this.query.address.area}).then(response => {
          component.cities = response.data.cities;
        });
      }, 250);
    },
    makeRequest: function() {
      let component = this;
      let url = location.protocol + '//' + location.host + location.pathname;
      this.setUrl();
      
      axios.post(url, this.query).then(response => {
        let itterations = 1;
          
        if(component.append){
          let currentData = component.products.data;
          component.products = response.data.products;
              
          currentData.reverse().forEach(item => {
            component.products.data.unshift(item);
            
            if(itterations == currentData.length)
            component.append = false;
              
            itterations++;
          });
        }else
        component.products = response.data.products;
        
        component.loading = false;
      });
    },
    launchSearch: function() {
      this.loading = true;

      if(this.query.page === 1)
        this.makeRequest();
      else
        this.query.page = 1;
    },
    loadmore: function() {
      this.append = true;
      this.query.page++;
    },
    clearFilters: function() {
      for(let key in this.query.filters.attributes) {
        if(this.filters.attributes[key].type === 'number')
          this.query.filters.attributes[key] = [this.filters.attributes[key].values.min, this.filters.attributes[key].values.max];
        else
          this.query.filters.attributes[key] = [];
      }
      this.query.filters.product_attributes = {
        wall_material: [],
        roof_material: [],
        status: [],
      };
      
      this.query.price = [this.rangeOptions.min, this.rangeOptions.max];

      this.launchSearch();
    },
    setUrl: function() {
      let query = this.query;
      let newUrl = location.protocol + '//' + location.host + location.pathname;
      
      newUrl += '?page=' + query.page;
      newUrl += '&per_page=' + query.per_page;
      newUrl += '&sort=' + query.sort;
      newUrl += '&price[0]=' + query.price[0];
      newUrl += '&price[1]=' + query.price[1];

      for(let key in query.filters.product_attributes) {
        let item = query.filters.product_attributes[key];
        if(!item.length)
          continue;

        for(let i = 0; i < item.length; i++) {
          newUrl += '&filters[product_attributes][' + key + '][]=' + item[i];
        }
      }

      for(let key in query.filters.attributes) {
        let item = query.filters.attributes[key];
        if(!item.length)
          continue;

        for(let i = 0; i < item.length; i++) {
          newUrl += '&filters[attributes][' + key + '][]=' + item[i];
        }
      }

      newUrl = query.filters.search_value? newUrl + '&filters[search_value]=' + query.filters.search_value : newUrl;
      newUrl = query.address.region? newUrl + '&address[region]=' + query.address.region : newUrl;
      newUrl = query.address.kyivdistrict? newUrl + '&address[kyivdistrict]=' + query.address.kyivdistrict : newUrl;
      newUrl = query.address.area? newUrl + '&address[area]=' + query.address.area : newUrl;
      newUrl = query.address.city? newUrl + '&address[city]=' + query.address.city : newUrl;
      newUrl = query.radius? newUrl + '&radius=' + query.radius : newUrl;
      
      history.pushState({
          id: 'catalog'
      }, 'Catalog', newUrl);
    }
  },
  watch: {
    'query.page': function() {
      if(!this.append)
        this.loading = true;

      this.makeRequest();
    },
    'search.region': function(value) {
      if(value)
        this.query.address.region = null;
    },
    'search.area': function(value) {
      if(value) {
        this.query.address.area = null;
        this.query.address.kyivdistrict = null;
      }

      this.getAreas();
    },
    'search.city': function(value) {
      if(value)
        this.query.address.city = null;

      this.getCities();
    },
    'query.address.region': function(value) {
      this.query.address.area = null;
      this.query.address.kyivdistrict = null;
      this.search.area = '';
      this.search.region = '';
      this.areas = [];
      if(!value)
        return;

      this.getAreas();
    },
    'query.address.area': function(value) {
      this.query.address.city = null;
      this.search.area = '';
      this.search.city = '';
      this.cities = [];
      if(!value)
        return;

      this.getCities();
    },
    'query.address.city': function(value) {
      this.search.city = '';
    },
    'products.data' : {
      handler: function(products) {
        if (currentMarkers !== null) {
            for (var i = currentMarkers.length - 1; i >= 0; i--) {
              currentMarkers[i].remove();
            }
        }

        currentMarkers = [];

        Object.keys(products).forEach(function(key) {
          if(products[key].lng && products[key].lat) {
            var marker = new mapboxgl.Marker()
            .setLngLat([products[key].lng, products[key].lat])
            .setPopup(new mapboxgl.Popup().setHTML('<a target="_blank" href="' + products[key].link + '">' + products[key].name + '</a>'))
            .addTo(document.map);

            currentMarkers.push(marker);
          }
        });
      },
      deep: true
    }
  },
  created: function() {
    console.log('catalog vue created');
    
    this.makeRequest();

    if(this.query.address.region)
      this.getAreas();

    if(this.query.address.area)
      this.getCities();
  }
});