/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import radio from '../components/attributes/radio'
import checkbox from '../components/attributes/checkbox'
import number from '../components/attributes/number'
import string from '../components/attributes/string'
import longtext from '../components/attributes/longtext'
import color from '../components/attributes/color'
import colors from '../components/attributes/colors'

import draggable from 'vuedraggable'

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const modification = new Vue({
  el: "#mod_field",
  components: {
    radio,
    checkbox,
    number,
    color,
    colors,
    longtext,
    string,
    draggable,
  },
  data: {
    newModificationName: null,
    items: items,
    baseItem: baseItem,
    attributes: attributes,
    openedTabs: [],
    fieldname: fieldname,
  },
  methods: {
    addItem: function(){
      if(!this.newModificationName){
        new Noty({
          type: "error",
          text: "Введите название модификации"
        }).show();
        return;
      }
      
      if(enableComplectations && !this.baseItem.extras.complectations.length){
        new Noty({
          type: "error",
          text: "Добавьте хотя бы одну комплектацию"
        }).show();
        return;
      }
      
      // this.items.push({
      //   'id' : null,
      //   'name' : Object.assign(this.newModificationName),
      //   'slug' : null,
      //   'code' : null,
      //   'price' : null,
      //   'is_active' : true,
      //   'is_pricehidden' : false,
      //   'extras' : [
      //     'complectation' : 0
      //   ],
      // });
      let base = Object.assign({}, this.baseItem);
      base.name = this.newModificationName;
      base.slug = null;
      base.id = null;
      base.images = [];
      base.layouts = [];

      if(enableComplectations)
        base.extras.complectation = this.baseItem.extras.complectations[0]['value'];

      console.log('baseitem', base);
      this.items.push(base);
      
      new Noty({
        type: "success",
        text: "Модификация добавлена"
      }).show();
      
      this.openedTabs.push(this.items.length - 1);
      this.newModificationName = null;
    },
    addComplectation: function(){
      let complectations = this.baseItem.extras.complectations;
      
      if(complectations.length && !complectations[complectations.length - 1].value){
          new Noty({
          type: "error",
          text: "Введите название комплектации"
        }).show();
        return;
      }
      
      complectations.push({'value': ''});
      
      new Noty({
        type: "success",
        text: "Комплектация добавлена"
      }).show();
    },
    deleteItem: function(index, id){
      if(confirm('Удалить модификацию?')){
        this.items.splice(index, 1);
        axios.post('/admin/modification/remove/' + id).then(response => (
          new Noty({
            type: "success",
            text: "Модификация удалена."
          }).show()
        ));
      }
        
    },
    toggleTab: function(index){
      let openedTab = this.openedTabs.indexOf(index);
      
      if(openedTab != -1)
        this.openedTabs.splice(openedTab, 1);
      else
        this.openedTabs.push(index);
    },
    getBaseAttrName: function(index){
      return this.fieldname + '[' + index + '][attrs]';
    },
    getAttributeValue: function(attributeId, itemIndex){
      return this.items[itemIndex] && this.items[itemIndex].attrs? this.items[itemIndex].attrs[attributeId] : this.getBaseAttributeValue(attributeId);
    },
    getBaseAttributeValue: function(attributeId){
      return this.baseItem && this.baseItem.attrs? this.baseItem.attrs[attributeId] : 0;
    },
    addImage: function(index){
      if(!this.items[index].images)
        Vue.set(this.items[index], 'images', []);

      this.items[index].images.push('');
    },
    removeImage: function(index, key){
      this.items[index].images.splice(key, 1);
    },
    fileChange: function(event, index, key){
      if(!this.items[index].previews)
        Vue.set(this.items[index], 'previews', []);

      this.items[index].images[key] = 'uploads/' + event.target.files[0].name;
      Vue.set(this.items[index].previews, key, URL.createObjectURL(event.target.files[0]));
    },
    addLayout: function(index){
      if(!this.items[index].layouts)
        Vue.set(this.items[index], 'layouts', []);

      this.items[index].layouts.push({name: '', image: ''});
    },
    removeLayout: function(index, key){
      this.items[index].layouts.splice(key, 1);
    },
    fileChangeLayouts: function(event, index, key){
      if(!this.items[index].layout_previews)
        Vue.set(this.items[index], 'layout_previews', []);

      this.items[index].layouts[key] = 'uploads/' + event.target.files[0].name;
      Vue.set(this.items[index].layout_previews, key, URL.createObjectURL(event.target.files[0]));
    }
  },
  computed: {
    trimedComplectations: function() {
      return this.baseItem.extras.complectations.filter(function(item){
        return item.value? true: false;
      });
    }
  },
  watch:{
      baseItem: {
        handler: function(value) {
         // console.log(value);
        },
        deep: true
      },
      // items: {
      //   handler: function(value) {
      //     value.forEach(element => {
      //       let price = element.price;
      //       let mileage = element.attrs[8];
      //       let mileageAttrId = 28;
          // console.log(this.attributes[28]);
          // console.log(this.attributes[29]);
            
            // if(!this.attributes[28] && this.attributes[29])
            //   mileageAttrId = 29;
            
            // this.attributes[mileageAttrId].values.forEach(function(item){
            //   let range = item.split('-');
              
              
            //   if((parseInt(mileage) != 0 && item.indexOf('до') != -1 && mileage < parseInt(item.split('до')[1])) ||
            //     (item.indexOf('от') != -1 && mileage > parseInt(item.split('от')[1])) ||
            //     (parseInt(range[0]) < parseInt(mileage)  && parseInt(range[1]) > parseInt(mileage)) ||
            //     (parseInt(mileage) == 0 && item == 'Новые'))
            //     element.attrs[mileageAttrId] = item;
            
            // });
            
            // this.attributes[18].values.forEach(function(item){
            //   let range = item.split('-');
            //   if(parseInt(range[0]) < parseInt(price)  && parseInt(range[1]) > parseInt(price))
            //     element.attrs[18] = item;
            // });
            
            //console.log(element);
          // });
        // },
        // deep: true
      // }
  },
  created: function(){
    if(Object.keys(baseItem.attrs).length === 0){
      for(var element in this.attributes) {
        this.baseItem.attrs[element] = this.attributes[element].default_value;
      }   
    }
  }
})