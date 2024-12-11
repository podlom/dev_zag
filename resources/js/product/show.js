require('../bootstrap');

import search from '../mixins/search.js'
import favoritesMix from '../mixins/favoritesMix.js'
import notificationsMix from '../mixins/notificationsMix.js'
import promotioncard from '../components/PromotionCard'
import companycard from '../components/CompanyCard'
import productcard from '../components/ProductCard'
import comparisonMix from '../mixins/comparisonMix.js'
import recentMix from '../mixins/recentMix.js'
import pollMix from '../mixins/pollMix.js'

const app = new Vue({
  el: "#app",
  data: {
    promotions: promotions,
    companies: companies,
    product_id: product_id,
    other_products: {data:[],total:1}
  },
  mixins: [search, favoritesMix, notificationsMix, comparisonMix, recentMix, pollMix],
  components: {
    promotioncard,
    companycard,
    productcard
  },
  methods: {
    getOtherProducts: function() {
      let component = this;

      axios.post('/getNearest', {product_id: this.product_id}).then(function(response) {
        component.other_products = response.data.products;
      });
    }
  },
  watch: {

  },
  created: function(){
    console.log('Product vue created');
    this.getOtherProducts();
  }
});