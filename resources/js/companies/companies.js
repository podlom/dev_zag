require('../bootstrap');

import search from '../mixins/search.js'
import favoritesMix from '../mixins/favoritesMix.js'
import notificationsMix from '../mixins/notificationsMix.js'
import companycard from '../components/CompanyCard'
import promotioncard from '../components/PromotionCard'
import comparisonMix from '../mixins/comparisonMix.js'

const faq = new Vue({
  el: '#app',
  data: {
    promotions: promotions,
    companies: {data:[],total:1},
    categories: categories,
    popularCategories: popularCategories,
    regions: regions,
    areas: null,
    cities: null,
    query: {
      isJson: true,
      category: currentCategorySlug,
      page: page,
      per_page: 10,
      search: '',
      region: null,
      city: null,
      area: null,
    },
    append: false,
    searchValue: '',
    region: null,
    city: null,
    area: null,
    iterations_region: 1,
    iterations_area: 1,
    search: {
      region: '',
      area: '',
      city: '',
    },
    seo_title: seo_title,
    seo_text: seo_text,
    loading: false
  },
  mixins: [search, favoritesMix, notificationsMix, comparisonMix],
  components: {
    companycard,
    promotioncard
  },
  methods: {
    loadmore: function() {
      this.append = true;
      this.query.page++;
    },
    launchSearch: function() {
      this.query.page = 1;
      this.query.region = this.region;
      this.query.city = this.city;
      this.query.area = this.area;
      this.query.search = this.searchValue;
      this.loading = true;
    },
    getCompanies: function() {
      let thisUrl = this.query.category? location.protocol + '//' + location.host + '/' + lang + '/firms/' + this.query.category : location.protocol + '//' + location.host + '/' + lang + '/firms';
        
      var valueClone = _.cloneDeep(this.query);

      axios.post(thisUrl, valueClone).then(response => {
        let itterations = 1;
        
        if(this.append){
          let currentData = this.companies.data;
          this.companies = response.data.companies;
              
          currentData.reverse().forEach(item => {
            this.companies.data.unshift(item);
            
            if(itterations == currentData.length)
              this.append = false;
              
            itterations++;
          });
        }else
          this.companies = response.data.companies;
        
        document.title = response.data.meta_title + ' – Zagorodna.com';
        this.seo_title = response.data.seo_title;
        this.seo_text = response.data.seo_text;
        this.loading = false;
      });
    }
  },
  computed: {
    selectedAddress: function() {
      let address = '';

      if(this.region)
        address = this.regions[this.region];

      if(this.area)
        address += ', ' + this.areas[this.area];

      if(this.city)
        address += ', ' + this.cities[this.city];

      return address;
    },
  },
  watch: {
    'region': function(value) {
      let component = this;
      component.iterations_region++;
      let iterations = component.iterations_region;
      component.city = null;
      component.area = null;
      component.areas = null;
      component.search.area = '';
      component.search.city = '';

      setTimeout(() => {
        if(iterations != component.iterations_region)
          return;

        axios.post('/getAreas', {'region': value}).then(response => {
          component.areas = response.data.areas;
          component.city = null;
          component.area = null;
          component.city = null;
        });
      }, 250);
    },
    'area': function(value) {
      let component = this;
      component.iterations_area++;
      let iterations = component.iterations_area;
      component.city = null;
      component.search.city = '';

      setTimeout(() => {
        if(iterations != component.iterations_area)
          return;

        axios.post('/getCities', {'area': value}).then(response => {
          component.cities = response.data.cities;
          component.city = null;
        });
      }, 250);
    },
    'query.category': function() {
      this.query.page = 1;
      this.loading = true;

      let newUrl = this.query.category? location.protocol + '//' + location.host + '/' + lang + '/firms/' + this.query.category : location.protocol + '//' + location.host + '/' + lang + '/firms';
      
      history.pushState({
          id: 'companies'
      }, 'Companies', newUrl);
    },
    'query.page': function(value) {
      if(!this.append)
        this.loading = true;

      let newUrl = this.query.category? location.protocol + '//' + location.host + '/' + lang + '/firms/' + this.query.category + '?page=' + value : location.protocol + '//' + location.host + '/' + lang + '/firms?page=' + value;
    
      history.pushState({
          id: 'companies'
      }, 'Companies', newUrl);
    },
    query: {
      handler: function(value, oldValue) {
        this.getCompanies();
      },
      deep: true
    }
  },
  created: function(){
    console.log('companies vue created');
    let component = this;
    
	  window.addEventListener("load", function(event) {
      component.getCompanies();
    });
  }
});