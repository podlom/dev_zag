require('../bootstrap');

import search from '../mixins/search.js'
import favoritesMix from '../mixins/favoritesMix.js'
import notificationsMix from '../mixins/notificationsMix.js'
import articlecard from '../components/ArticleCard'
import comparisonMix from '../mixins/comparisonMix.js'

const app = new Vue({
  el: "#app",
  data: {
    articles: articles,
  },
  components: {
    articlecard
  },
  mixins: [search, favoritesMix, notificationsMix, comparisonMix],
  watch: {
    
  }
});