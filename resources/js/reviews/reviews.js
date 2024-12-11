require('../bootstrap');

import search from '../mixins/search.js'
import favoritesMix from '../mixins/favoritesMix.js'
import notificationsMix from '../mixins/notificationsMix.js'
import reviewcard from '../components/ReviewCard'
import comparisonMix from '../mixins/comparisonMix.js'

const app = new Vue({
  el: "#app",
  data: {
    reviews: reviews,
    sorts: {
      'created_at_desc': 'Сначала новые',
      'created_at_asc': 'Сначала старые',
    },
    query: {
      isJson: true,
      page: page,
      type: type,
      sort: 'created_at_desc'
    },
    append: false,
    h1: h1,
    seo_title: seo_title,
    seo_text: seo_text,
    meta_title: meta_title,
    loading: false,
    slug: slug,
    types: types
  },
  mixins: [search, favoritesMix, notificationsMix, comparisonMix],
  components: {
    reviewcard
  },
  methods: {
    loadmore: function() {
      this.append = true;
      this.query.page++;
    }
  },
  watch: {
    'query.type': function() {
      this.query.page = 1;
      this.loading = true;

      let newUrl = this.query.type != 'zagorodna'? location.protocol + '//' + location.host + '/' + lang + '/' + this.slug + '/' + this.types[this.query.type] : location.protocol + '//' + location.host + '/' + lang + '/' + this.slug;
      
      history.pushState({
          id: 'news'
      }, 'News', newUrl);
    },
    'query.page': function() {
      if(!this.append)
        this.loading = true;

      let newUrl = this.query.type != 'zagorodna'? location.protocol + '//' + location.host + '/' + lang + '/' + this.slug + '/' + this.types[this.query.type] : location.protocol + '//' + location.host + '/' + lang + '/' + this.slug;
      newUrl += '?page=' + this.query.page;
    
      history.pushState({
          id: 'news'
      }, 'News', newUrl);
    },
    query: {
      handler: function(value, oldValue) {
        let thisUrl = this.query.type != 'zagorodna'? location.protocol + '//' + location.host + '/' + lang + '/reviews/' + this.types[this.query.type] : location.protocol + '//' + location.host + '/' + lang + '/reviews';
        
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
          
          this.h1 = response.data.h1;
          this.seo_title = response.data.seo_title;
          this.seo_text = response.data.seo_text;
          this.loading = false;
          document.title = response.data.meta_title + ' – Zagorodna.com';
          setTimeout(() => {
            document.showMoreInfo();
          }, 1);
        });
      },
      deep: true
    }
  },
//   mixins: [cartMix, search, favoritesMix],
created: function(){
  console.log('article vue created');
}
});