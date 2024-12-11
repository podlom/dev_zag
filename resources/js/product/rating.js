require('../bootstrap');

import search from '../mixins/search.js'
import favoritesMix from '../mixins/favoritesMix.js'
import notificationsMix from '../mixins/notificationsMix.js'
import comparisonMix from '../mixins/comparisonMix.js'
import productcard from '../components/ProductCard'

const app = new Vue({
  el: "#app",
  data: {
    products: {data:[],total:1},
  },
  mixins: [search, favoritesMix, notificationsMix, comparisonMix],
  components: {
    productcard,
  },
  methods: {
    getProducts: function() {
      let component = this;
      let thisUrl = location.protocol + '//' + location.host + location.pathname + location.search;
      
      axios.post(thisUrl, {isJson: true}).then(response => {
          component.products = response.data;
      });
    }
  },
  watch: {

  },
  created: function(){
    console.log('Rating vue created');
    this.getProducts();
  }
});