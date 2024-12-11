var search = {
  data: function () {
    return {
      search_list: [],
      search_filter: '',
      search_type: 'cottages',
      search_type_changed: 'cottages',
      search_iterations: 0,
      search_preload: false,
      show_cookies: true,
      selection: {
        type: 'cottage',
        region: null,
        area: null,
        city: null,
        kyivdistrict: null,
        size: null,
        status: [],
        cottage_type: [],
      },
      selection_regions: regions,
      selection_areas: [],
      selection_cities: [],
      selection_search: {
        region: '',
        area: '',
        city: '',
      },
      selection_products: [],
      selection_done: false,
      selection_started: false,
    }
  },
  computed: {
    fullSelectionAddress: function() {
      let result = '';

      if(this.selection.region)
        result += this.selection_regions[this.selection.region];

      if(this.selection.kyivdistrict)
        result += ', ' + this.selection_areas[this.selection.kyivdistrict];

      if(this.selection.area)
        result += ', ' + this.selection_areas[this.selection.area];

        
      if(this.selection.city)
        result += ', ' + this.selection_cities[this.selection.city];

      return result;
    }
  },
  methods: {
    requestSearchList: function(){
      let component = this;
      this.search_iterations++;
      let iterations = this.search_iterations;
      this.search_preload = true;

      // if(!this.search_filter) {
      //   this.search_list = [];
      //   return;
      // }
      setTimeout(() => {
        if(iterations != component.search_iterations)
          return;

        console.log('start search');
        axios.post('/search', {filter: this.search_filter, type: this.search_type}).then(function(response) {
          console.log('end search');
          
          if(iterations == component.search_iterations) {
            component.search_list = response.data.data;
            component.search_type_changed = component.search_type;
            component.search_preload = false;
          }
        });
      }, 500);
    },
    allowCookies: function() {
      let component = this;
      axios.post('/cookies/allow').then(function() {
        component.show_cookies = false;
      });
    },
    getSelectionAreas: function() {
      let component = this;
      component.iterations_area++;
      let iterations = component.iterations_area;
      
      setTimeout(() => {
        if(iterations != component.iterations_area)
          return;

        axios.post('/getAreas', {'region': this.selection.region}).then(response => {
          component.selection_areas = response.data.areas;
        });
      }, 250);
    },
    getSelectionCities: function() {
      let component = this;
      component.iterations_city++;
      let iterations = component.iterations_city;

      setTimeout(() => {
        if(iterations != component.iterations_city)
          return;

        axios.post('/getCities', {'area': this.selection.area}).then(response => {
          component.selection_cities = response.data.cities;
        });
      }, 250);
    },
    getSelection: function() {
      this.selection_started = true;
      axios.post('/getSelection', this.selection).then(response => {
        this.selection_products = response.data.data;
        this.selection_done = true;
        this.selection_started = false;
      })
    },
    restartSelection: function() {
      this.selection_done = false;
      this.selection_products = [];
    }
  },
  watch: {
    search_filter: function(value) {
      this.search_list = [];

      if(value.length >= 3)
        this.requestSearchList();
      else
        this.search_preload = false;
    },
    search_type: function(value) {
      this.search_list = [];

      if(this.search_filter.length >= 3)
        this.requestSearchList();
      else
        this.search_preload = false;
    },
    'selection_search.region': function(value) {
      if(value)
        this.selection.region = null;
    },
    'selection_search.area': function(value) {
      if(value) {
        this.selection.area = null;
        this.selection.kyivdistrict = null;
      }

      this.getSelectionAreas();
    },
    'selection_search.city': function(value) {
      if(value)
        this.selection.city = null;

      this.getSelectionCities();
    },
    'selection.region': function(value) {
      this.selection.area = null;
      this.selection.kyivdistrict = null;
      this.selection_search.area = '';
      this.selection_search.region = '';
      this.selection_areas = [];
      if(!value)
        return;

      this.getSelectionAreas();
    },
    'selection.area': function(value) {
      this.selection.city = null;
      this.selection_search.area = '';
      this.selection_search.city = '';
      this.selection_cities = [];
      if(!value)
        return;

      this.getSelectionCities();
    },
    'selection.city': function(value) {
      this.selection_search.city = '';
    },
    'selection.size': function(value) {
      if(+value > +max_area)
        this.selection.size = max_area;
    },
  },
  created: function() {
    
  }
}

export default search;