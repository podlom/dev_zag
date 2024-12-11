

<template>
  <div class="form-group col-sm-12">
    <label>{{ data.name }}</label>
    
    <div class="checkbox" v-for="(value, index) in data.values">
      <input type="checkbox" :name="uniqFieldname" :id="uniqId + '_' + index" :value="value" v-model="thisValue[index]" >
      <label :for="uniqId + '_' + index" class="font-weight-normal form-check-label">{{ value }}</label>
    </div>
    
  </div>
  
</template>


<script>
  export default {
    name: 'checkbox',
    data: function(){
      return {
        data: this.dataAttribute, 
        fieldname: this.dataFieldname,
        basefieldname: this.dataBasefieldname,
        thisValue: this.value? this.value: (this.dataValue? this.dataValue : []),
      }
    },
    props: ['value', 'dataAttribute', 'dataFieldname', 'dataBasefieldname', 'dataValue'],
    mounted: function(){
      //console.log(this.data);
    },
    computed: {
      uniqId: function(){
        return 'checkbox' + this.data.id + Math.random();
      },
      uniqFieldname: function(){
        return this.basefieldname + '[' + this.data.id + '][value][]';
      },
      // setValue: {
      //   get: function(){
      //     return this.values? this.values : this.data.default_value;  
      //   },
      //   set: function(value){
      //     this.values = value;
      //   }
      // }
    },
    watch: {
      thisValue: function(value){
        this.$emit('input', value);
      }
    },
  }
</script>