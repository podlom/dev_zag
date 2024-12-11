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
    top_rating: top_rating,
    reviews_rating: reviews_rating,
    parent_categories: parent_categories,
    categories: categories,
    sorts: sorts,
    query: {
      isJson: true,
      category: currentThemeSlug,
      parent_category: currentCategorySlug,
      page: page,
      sort: 'date_desc',
    },
	  append: false,
    seo_text: seo_text,
    content: content,
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
      location.protocol + '//' + location.host + '/' + lang + '/analitics/' + this.query.parent_category + '/' + this.query.category 
      : this.query.parent_category? 
      location.protocol + '//' + location.host + '/' + lang + '/analitics/' + this.query.parent_category
      : location.protocol + '//' + location.host + '/' + lang;

      newUrl = this.query.page > 1? '?page=' + this.query.page : newUrl;

      history.pushState({
          id: 'analitics'
      }, 'analitics', newUrl);
    },
    'query.category': function(value) {
      this.query.page = 1;
      this.loading = true;

      let newUrl = this.query.category? 
      location.protocol + '//' + location.host + '/' + lang + '/analitics/' + this.query.parent_category + '/' + this.query.category 
      : this.query.parent_category? 
      location.protocol + '//' + location.host + '/' + lang + '/analitics/' + this.query.parent_category
      : location.protocol + '//' + location.host + '/' + lang;
      
      history.pushState({
          id: 'analitics'
      }, 'analitics', newUrl);
    },
    'query.parent_category': function(value) {
      this.query.page = 1;
      this.query.category = null;
      this.loading = true;

      let newUrl = this.query.parent_category? location.protocol + '//' + location.host + '/' + lang + '/analitics/' + this.query.parent_category : location.protocol + '//' + location.host + '/' + lang;
      
      history.pushState({
          id: 'analitics'
      }, 'analitics', newUrl);
    },
    query: {
      handler: function(value, oldValue) {
        
        let thisUrl = this.query.category? 
        location.protocol + '//' + location.host + '/' + lang + '/analitics/' + this.query.parent_category + '/' + this.query.category 
        : this.query.parent_category? 
        location.protocol + '//' + location.host + '/' + lang + '/analitics/' + this.query.parent_category
        : location.protocol + '//' + location.host + '/' + lang;
        
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
          document.title = response.data.meta_title + ' – Zagorodna.com';
          this.seo_text = response.data.seo_text;
          this.content = response.data.content;
          this.top_rating = response.data.top_rating;
          this.reviews_rating = response.data.reviews_rating;
          this.loading = false;
        });
      },
      deep: true
    }
  },
  created: function(){
    console.log('analitics vue created');
  }
});