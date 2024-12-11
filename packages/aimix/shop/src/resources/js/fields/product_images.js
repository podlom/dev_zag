require('./bootstrap');

window.Vue = require('vue');

import draggable from 'vuedraggable'

const product_images = new Vue({
  el: "#product_images",
  data: {
    images: images,
    previews: {},
  },
  components: {
    draggable,
  },
  methods: {
    addItem: function(){
      this.images.push('');
    },
    removeItem: function(index){
      this.images.splice(index, 1);
    },
    fileChange: function(event, index){
      this.images[index] = 'uploads/' + event.target.files[0].name;
      Vue.set(this.previews, index, URL.createObjectURL(event.target.files[0]));
    }
  },
});