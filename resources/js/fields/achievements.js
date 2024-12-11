require('../bootstrap');

window.Vue = require('vue');

import Vue from 'vue';
import draggable from 'vuedraggable'

const achievements = new Vue({
  el: "#achievements",
  data: {
    images: images,
  },
  components: {
    draggable,
  },
  methods: {
    addImage: function() {
      this.images.push({});
    },
    removeImage: function(index) {
      Vue.delete(this.images, index);
    },
    fileChange: function(event, index){
      this.images[index].image = 'uploads/' + event.target.files[0].name;
      Vue.set(this.images[index], 'preview', URL.createObjectURL(event.target.files[0]));
    }
  },
  created: function() {
    console.log('Achievements vue created');
  }
});