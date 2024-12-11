var favoritesMix = {
  data: function () {
    return {
      favorites: null,
    }
  },
  methods: {
    addToFavorites: function(item, type) {
      let id = item.original_id? item.original_id : item.id;

      if(this.favorites[type].includes(id)) {
        this.favorites[type].splice(this.favorites[type].indexOf(id), 1);
        noty('success', 'Удалено');
        return;
      }

      this.favorites[type].push(id);
      noty('success', 'Добавлено');
    },
    validateFav: function(event) {
      if(this.totalFavorites === 0) {
        noty('error', 'В избранном пусто');
        return event.preventDefault();
      }
    },
  },
  computed: {
    totalFavorites: function() {
      return this.favorites['products'].length + this.favorites['promotions'].length + this.favorites['companies'].length + this.favorites['articles'].length + this.favorites['projects'].length;
    }
  },
  watch: {
    favorites: {
      handler: function(value) {
        localStorage.zagorodna_fav = JSON.stringify(this.favorites);
      },
      deep: true
    },
  },
  created: function() {
    console.log('favorites mixin');
    let fav = JSON.parse(localStorage.getItem('zagorodna_fav'));
    
    if(!fav)
      fav = {};

    if(!fav['products'])
      fav['products'] = [];

    if(!fav['promotions'])
      fav['promotions'] = [];
      
    if(!fav['companies'])
      fav['companies'] = [];
    
    if(!fav['articles'])
      fav['articles'] = [];
    
    if(!fav['projects'])
      fav['projects'] = [];

    this.favorites = fav;
  }
}

export default favoritesMix;