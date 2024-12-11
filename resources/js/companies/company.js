require('../bootstrap');

import search from '../mixins/search.js'
import favoritesMix from '../mixins/favoritesMix.js'
import notificationsMix from '../mixins/notificationsMix.js'
import reviewcard from '../components/ReviewCard'
import productcard from '../components/ProductCard'
import companycard from '../components/CompanyCard'
import promotioncard from '../components/PromotionCard'
import comparisonMix from '../mixins/comparisonMix.js'

const app = new Vue({
  el: "#app",
  data: {
    reviews: reviews,
    products: products,
    companies: companies,
    promotions: promotions,
    query: {
      isJson: true,
      page: 1,
    },
	  append: false,
  },
  mixins: [search, favoritesMix, notificationsMix, comparisonMix],
  components: {
    reviewcard,
    productcard,
    companycard,
    promotioncard
  },
  methods: {
    loadmore: function() {
      this.append = true;
      this.query.page++;
    }
  },
  watch: {
    query: {
      handler: function(value, oldValue) {
        
        let thisUrl = location.protocol + '//' + location.host + location.pathname;

        var valueClone = _.cloneDeep(value);

        axios.post(thisUrl, valueClone).then(response => {
          let itterations = 1;
          
          if(this.append){
            let currentData = this.reviews.data;
            this.reviews = response.data.reviews;
                
            currentData.reverse().forEach(item => {
              this.reviews.data.unshift(item);
              
              if(itterations == currentData.length)
                this.append = false;
                
              itterations++;
            });
          }else
            this.reviews = response.data.reviews;
          
        });
      },
      deep: true
    }
  },
created: function(){
  console.log('reviews vue created');
}
});