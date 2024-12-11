require('../bootstrap');

import search from '../mixins/search.js'
import favoritesMix from '../mixins/favoritesMix.js'
import notificationsMix from '../mixins/notificationsMix.js'
import articlecard from '../components/ArticleCard'
import comparisonMix from '../mixins/comparisonMix.js'

const news = new Vue({
  el: '#app',
  data: {
    articles: articles,
    parent_categories: parent_categories,
    categories: categories,
    regions: regions,
    years: years,
    sorts: sorts,
    query: {
      isJson: true,
      category: currentThemeSlug,
      parent_category: currentCategorySlug,
      region: region,
      year: year,
      page: page,
      sort: sort,
    },
    append: false,
    seo_text: seo_text,
    loading: false
  },
  mixins: [search, favoritesMix, notificationsMix, comparisonMix],
  components: {
    articlecard
  },
  methods: {
    loadmore: function() {
      this.append = true;
      this.query.page++;
    }
  },
  watch: {
    'query.category': function(value) {
      this.query.page = 1;
      this.query.region = null;
      this.query.year = null;
      this.loading = true;
    },
    'query.parent_category': function(value) {
      this.query.page = 1;
      this.query.category = null;
      this.query.region = null;
      this.query.year = null;
      this.loading = true;
    },
    'query.region': function() {
      this.query.page = 1;
      this.query.year = null;
      this.loading = true;
    },
    'query.year': function() {
      this.query.page = 1;
      this.loading = true;
    },
    'query.sort': function() {
      this.query.page = 1;
      this.loading = true;
    },
    'query.page': function() {
      if(!this.append)
        this.loading = true;
    },
    query: {
      handler: function(value, oldValue) {
        
        let thisUrl = this.query.category? 
        location.protocol + '//' + location.host + '/' + lang + '/' + this.query.parent_category + '/bookmarks/' + this.query.category 
        : location.protocol + '//' + location.host + '/' + lang + '/' + this.query.parent_category;
        
        var valueClone = _.cloneDeep(value);

        axios.post(thisUrl, valueClone).then(response => {
          let itterations = 1;
          
          if(this.append){
            let currentData = this.articles.data;
            this.articles = response.data.articles;
                
            currentData.reverse().forEach(item => {
              this.articles.data.unshift(item);
              
              if(itterations == currentData.length)
                this.append = false;
                
              itterations++;
            });
          }else
            this.articles = response.data.articles;
          
          this.categories = response.data.categories;
          this.regions = response.data.regions;
          this.years = response.data.years;
          document.title = response.data.meta_title + ' – Zagorodna.com';
          this.seo_text = response.data.seo_text;
          this.loading = false;
        });

        if(this.query.page > 1 || this.query.year > 1 || this.query.region || this.query.sort !== 'date_desc')
          thisUrl += '?';

        thisUrl = this.query.sort !== 'date_desc'? thisUrl + '&sort=' + this.query.sort : thisUrl;
        thisUrl = this.query.page > 1? thisUrl + '&page=' + this.query.page : thisUrl;
        thisUrl = this.query.year > 1? thisUrl + '&year=' + this.query.year : thisUrl;
        thisUrl = this.query.region? thisUrl + '&region=' + this.query.region : thisUrl;
  
        history.pushState({
            id: 'news'
        }, 'News', thisUrl);
      },
      deep: true
    }
  },
  created: function(){
    console.log('news vue created');
  }
});