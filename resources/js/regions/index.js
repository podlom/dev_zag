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
    categories: categories,
    query: {
      isJson: true,
      category: currentThemeSlug,
      page: page,
    },
    append: false,
    parentCategorySlug: parentCategorySlug,
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
    'query.page': function(value) {
      if(!this.append)
        this.loading = true;

      let newUrl = this.query.category? 
      location.protocol + '//' + location.host + '/' + lang + '/' + this.parentCategorySlug + '/' + this.query.category 
      : location.protocol + '//' + location.host + '/' + lang + '/' + this.parentCategorySlug

      newUrl = this.query.page > 1? '?page=' + this.query.page : newUrl;
      
      history.pushState({
          id: 'regions'
      }, 'Regions', newUrl);
    },
    'query.category': function(value) {
      this.query.page = 1;
      this.loading = true;

      let newUrl = this.query.category? 
      location.protocol + '//' + location.host + '/' + lang + '/' + this.parentCategorySlug + '/' + this.query.category 
      : location.protocol + '//' + location.host + '/' + lang + '/' + this.parentCategorySlug
      
      
      history.pushState({
          id: 'regions'
      }, 'Regions', newUrl);
    },
    query: {
      handler: function(value, oldValue) {
        
        let thisUrl = this.query.category? 
        location.protocol + '//' + location.host + '/' + lang + '/' + this.parentCategorySlug + '/' + this.query.category 
        : location.protocol + '//' + location.host + '/' + lang + '/' + this.parentCategorySlug;
        
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
            
          document.title = response.data.meta_title + ' – Zagorodna.com';
          this.seo_text = response.data.seo_text;
          this.loading = false;
        });
      },
      deep: true
    }
  },
  created: function(){
    console.log('regions vue created');
  }
});