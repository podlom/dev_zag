require('../bootstrap');

import search from '../mixins/search.js'
import favoritesMix from '../mixins/favoritesMix.js'
import notificationsMix from '../mixins/notificationsMix.js'
import comparisonMix from '../mixins/comparisonMix.js'

// import productcard from '../components/ProductCard'

const app = new Vue({
  el: "#app",
  data: {
	// OBJECTS
    products_for_map: {data:[],total:1},
    // ANOTHER DATA
    address: address,
    category_slug: category_slug,
    other_category_slug: other_category_slug,
    type: type,
    status: status,
    objectType: objectType,
    map_filled: false,
  },
  components: {
    // productcard:  productcard
  },
  mixins: [search, favoritesMix, notificationsMix, comparisonMix],
  computed: {

  },
  methods: {
    getProducts() {
      let component = this;

      /* added by @ts 2024-08-23 19:03 */
      axios.post('/getProducts', {lang: tsLang, region: this.address.region, area: this.address.area, city: this.address.city, kyivdistrict: this.address.kyivdistrict, category: this.category_slug, status: this.status, type: this.objectType, number: 999999}).then(response => {
          console.log(response);
          component.products_for_map = response.data;
      });
    },
  },
  watch: {
    'products_for_map.data': {
      handler: function(products) {
        if(this.map_filled)
          return;

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

        this.map_filled = true;

      },
      deep: true
    },
  },
  created: function() {
    let component = this;

	  window.addEventListener("load", function(event) {
      component.getProducts();
	   })
  }
});
