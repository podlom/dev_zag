require('../bootstrap');

window.Vue = require('vue');

import Vue from 'vue';

const address_zagorodna = new Vue({
  el: "#address_zagorodna",
  data: {
    address: address,
    regions: regions,
    areas: {},
    cities: {},
    iterations_region: 0,
    iterations_area: 0,
  },
  methods: {
    getAreas: function() {
      let component = this;
      component.iterations_region++;
      let iterations = component.iterations_region;

      setTimeout(() => {
        if(iterations != component.iterations_region)
          return;

        axios.post('/getAreas', {'region': this.address.region, empty: true}).then(response => {
          component.areas = response.data.areas;
        });
      }, 250);
    },
    getCities: function() {
      let component = this;
      component.iterations_area++;
      let iterations = component.iterations_area;

      setTimeout(() => {
        if(iterations != component.iterations_area)
          return;

        axios.post('/getCities', {'area': this.address.area, empty: true}).then(response => {
          component.cities = response.data.cities;
        });
      }, 250);
    },
  },
  watch: {
    'address.region': function(value) {
      this.address.area = null;
      this.address.city = null;
      this.getAreas();
    },
    'address.area': function(value) {
      this.address.city = null;
      this.getCities();
    }
  },
  created: function() {
    console.log('Address_zagorodna vue created');

    if(this.address.region)
      this.getAreas();

    if(this.address.area && this.address.region != 29)
      this.getCities();
  }
});