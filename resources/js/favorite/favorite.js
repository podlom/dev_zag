require('../bootstrap');

import search from '../mixins/search.js'
import favoritesMix from '../mixins/favoritesMix.js'
import notificationsMix from '../mixins/notificationsMix.js'
import productcard from '../components/ProductCard'
import articlecard from '../components/ArticleCard'
import promotioncard from '../components/PromotionCard'
import companycard from '../components/CompanyCard'
import projectcard from '../components/ProjectCard'
import comparisonMix from '../mixins/comparisonMix.js'

const app = new Vue({
  el: "#app",
  data: {
    items: null,
    sorts: {},
    filters_1: {},
    filters_2: {},
    filters_3: {},
    filter_name_1: null,
    filter_name_2: null,
    filter_name_3: null,
    query: {
      isJson: true,
      page: 1,
      tab: 'cottages',
      sort: 'created_at_desc',
      filter_1: null,
      filter_2: null,
      filter_3: 0,
    },
    append: false,
    tab: null,
    preload: true
  },
  components: {
    productcard,
    articlecard,
    promotioncard,
    companycard,
    projectcard
  },
  mixins: [search, favoritesMix, notificationsMix, comparisonMix],
  methods: {
    makeRequest: function(value) {
      this.preload = true;
      let thisUrl = location.protocol + '//' + location.host + location.pathname;
      
      var valueClone = _.cloneDeep(value);
      let type;

      if(this.query.filter_3 === 1 && (this.query.tab == 'cottages' || this.query.tab == 'newbuilds'))
        type = 'projects';
      else if(this.query.filter_3 === 0 && (this.query.tab == 'cottages' || this.query.tab == 'newbuilds'))
        type = 'products';
      else
        type = this.query.tab

      valueClone.ids = this.favorites[type];
      
      axios.post(thisUrl, valueClone).then(response => {
        let itterations = 1;
        this.sorts = response.data.sorts;
        this.filters_1 = response.data.filters_1;
        this.filters_2 = response.data.filters_2;
        this.filters_3 = response.data.filters_3;
        this.filter_name_1 = response.data.filter_name_1;
        this.filter_name_2 = response.data.filter_name_2;
        this.filter_name_3 = response.data.filter_name_3;
        
        if(this.append){
          let currentData = this.items.data;
          this.items = response.data.items;
              
          currentData.reverse().forEach(item => {
            this.items.data.unshift(item);
            
            if(itterations == currentData.length)
              this.append = false;
              
            itterations++;
          });
        }else
          this.items = response.data.items;
        
          this.tab = this.query.tab;
          this.preload = false;
      });
    },
    loadmore: function() {
      this.append = true;
      this.query.page++;
    },
  },
  watch: {
    'query.tab': function() {
      this.query.page = 1;
      this.query.sort = 'created_at_desc';
      this.query.filter_1 = null;
      this.query.filter_2 = null;
      this.query.filter_3 = 0;
    },
    'query.filter_1': function() {
      if(this.query.tab == 'articles')
        this.query.filter_2 = null;
    },
    query: {
      handler: function(value, oldValue) {
        this.makeRequest(value);
      },
      deep: true
    }
  },
  created: function() {
    console.log('favorite vue created');
    this.makeRequest(this.query);
  }
});