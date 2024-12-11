require('../bootstrap');

import search from '../mixins/search.js'
import favoritesMix from '../mixins/favoritesMix.js'
import notificationsMix from '../mixins/notificationsMix.js'
import articlecard from '../components/ArticleCard'
import comparisonMix from '../mixins/comparisonMix.js'

const faq = new Vue({
  el: '#app',
  data: {
    questions: questions,
    categories: categories,
    articles: articles,
    query: {
      isJson: true,
      category: currentCategorySlug,
      page: 1,
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
    'query.category': function() {
      this.query.page = 1;
      this.loading = true;

      let newUrl = location.protocol + '//' + location.host + '/' + lang + '/faq/' + this.query.category;
      
      history.pushState({
          id: 'faq'
      }, 'Faq', newUrl);
    },
    query: {
      handler: function(value, oldValue) {
        
        let thisUrl = location.protocol + '//' + location.host + '/' + lang + '/faq/' + this.query.category;
        
        var valueClone = _.cloneDeep(value);

        axios.post(thisUrl, valueClone).then(response => {
          let itterations = 1;
          
          if(this.append){
            let currentData = this.questions.data;
            this.questions = response.data.questions;
                
            currentData.reverse().forEach(item => {
              this.questions.data.unshift(item);
              
              if(itterations == currentData.length)
                this.append = false;
                
              itterations++;
            });
          }else
            this.questions = response.data.questions;
            
          document.title = response.data.meta_title + ' – Zagorodna.com';
          this.seo_text = response.data.seo_text;
          this.articles = response.data.articles;
          this.loading = false;
          
        });
      },
      deep: true
    }
  },
  created: function(){
    console.log('faq vue created');
  }
});