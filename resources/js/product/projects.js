require('../bootstrap');

import search from '../mixins/search.js'
import favoritesMix from '../mixins/favoritesMix.js'
import notificationsMix from '../mixins/notificationsMix.js'
import projectcard from '../components/ProjectCard'
import comparisonMix from '../mixins/comparisonMix.js'
import recentMix from '../mixins/recentMix.js'
import pollMix from '../mixins/pollMix.js'

const app = new Vue({
  el: "#app",
  data: {
    projects: projects,
  },
  mixins: [search, favoritesMix, notificationsMix, comparisonMix, recentMix, pollMix],
  components: {
    projectcard
  },
  created: function() {
    console.log('Product projects vue created')
  },
});