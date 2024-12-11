require('../bootstrap');

import search from '../mixins/search.js'
import favoritesMix from '../mixins/favoritesMix.js'
import notificationsMix from '../mixins/notificationsMix.js'
import promotioncard from '../components/PromotionCard'
import comparisonMix from '../mixins/comparisonMix.js'
import recentMix from '../mixins/recentMix.js'
import pollMix from '../mixins/pollMix.js'

const app = new Vue({
  el: "#app",
  data: {
    promotions: promotions,
  },
  mixins: [search, favoritesMix, notificationsMix, comparisonMix, recentMix, pollMix],
  components: {
    promotioncard
  },
  methods: {

  },
  watch: {

  },
  created: function(){
    console.log('Product promotions vue created');
  }
});