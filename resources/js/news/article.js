require('../bootstrap');

import search from '../mixins/search.js'
import favoritesMix from '../mixins/favoritesMix.js'
import notificationsMix from '../mixins/notificationsMix.js'
import reviewcard from '../components/ReviewCard'
import articlecard from '../components/ArticleCard'
import comparisonMix from '../mixins/comparisonMix.js'

const app = new Vue({
  el: "#app",
  data: {
    article: article,
    reviews: reviews,
    otherArticles: otherArticles,
    query: {
      isJson: true,
      page: 1,
    },
    append: false,
  },
  mixins: [search, favoritesMix, notificationsMix, comparisonMix],
  components: {
    reviewcard,
    articlecard
  },
  methods: {
    loadmore: function() {
      this.append = true;
      this.query.page++;
    },
  },
  watch: {
    query: {
      handler: function(value, oldValue) {
        
        let thisUrl = location.protocol + '//' + location.host + '/' + lang + '/' + location.pathname;
        
        var valueClone = _.cloneDeep(value);

        axios.post(thisUrl, valueClone).then(response => {
          let itterations = 1;
          
          if(this.append){
            let currentData = this.reviews.data;
            this.reviews = response.data.reviews;
                
            currentData.reverse().forEach(item => {
              this.reviews.data.unshift(item);
              
              if(itterations == currentData.length)
                this.append = false;
                
              itterations++;
            });
          }else
            this.reviews = response.data.reviews;
          
        });
      },
      deep: true
    }
  },
created: function(){
  console.log('article vue created');
  axios.post('/addArticleView', {'id': this.article.id});
}
});