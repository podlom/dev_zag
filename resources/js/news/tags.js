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
    query: {
      isJson: true,
      page: page,
      id: tag_id
    },
    append: false,
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
    query: {
      handler: function(value, oldValue) {
        
        let thisUrl = location.protocol + '//' + location.host + location.pathname;
        
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
          
        });

        thisUrl = thisUrl + '?id=' + this.query.id;
        thisUrl = this.query.page > 1? thisUrl + '&page=' + this.query.page : thisUrl;
  
        history.pushState({
            id: 'tags'
        }, 'tags', thisUrl);
      },
      deep: true
    }
  },
  created: function(){
    console.log('tags vue created');
  }
});