require('./bootstrap');

import search from './mixins/search.js'
import favoritesMix from './mixins/favoritesMix.js'
import comparisonMix from './mixins/comparisonMix.js'
import notificationsMix from './mixins/notificationsMix.js'

const app = new Vue({
  el: "#app",
  mixins: [search, favoritesMix, comparisonMix, notificationsMix],
});