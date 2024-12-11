require('../bootstrap');

import search from '../mixins/search.js'
import favoritesMix from '../mixins/favoritesMix.js'
import notificationsMix from '../mixins/notificationsMix.js'
import productcard from '../components/ProductCard'
import comparisonMix from '../mixins/comparisonMix.js'

const app = new Vue({
  el: "#app",
  data: {
    items: {data:[],total:1},
    enabledParameters: [1,2],
    recent: {data:[],total:1},
    formText: formText,
  },
  components: {
    productcard,
  },
  mixins: [search, favoritesMix, notificationsMix, comparisonMix],
  methods: {
    getItems: function() {
      let component = this;

      axios.post('/comparison/getItems', {ids: this.comparison}).then(response => {
        component.items = response.data.items;
        Object.keys(component.items.data).forEach(function(key) {
          axios.get('/api/product/cardFields/' + component.items.data[key].id).then((response) => {
            component.items.data[key].price = response.data.price
            component.items.data[key].type = response.data.type
            component.items.data[key].city = response.data.city
         });
        });
        
        setTimeout(() => {
          scrollBloks();
        }, 0);
      });
    },
    getRecent: function() {
      let component = this;
      let ids = JSON.parse(localStorage.getItem('zagorodna_recent'));
      
      if(!ids)
        return;

      axios.post('/comparison/getRecent', {ids: ids}).then(response => {
        component.recent = response.data.items;
      });
    },
    removeItem: function(key) {
      console.log(this.items, key);
      let id = this.items.data[key].original_id? this.items.data[key].original_id : this.items.data[key].id;
      this.comparison.splice(this.comparison.indexOf(id), 1);
      Vue.delete(this.items.data, key);
    }
  },
  watch: {
    enabledParameters: function(value) {
      let component = this;
      this.enabledParameters.forEach(function(value, key) {
        component.enabledParameters[key] = +value;
      });
    },
    comparison: {
      handler: function(value) {
        this.getItems();
      },
      deep: true
    },
  },
  created: function() {
    console.log('comparison vue created');
    this.getItems();
    this.getRecent();
  }
});