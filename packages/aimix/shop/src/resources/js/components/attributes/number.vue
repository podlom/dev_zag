<template>
  <div class="form-group col-sm-12">
    <label :for="uniqId">{{ data.name }}</label>
    <input type="number" :name="uniqFieldname" :min="data.values.min" :step="data.values.step" :max="data.values.max" class="form-control" v-model="thisValue">
  </div>
</template>



<script>
  export default {
    name: 'number',
    data: function(){
      return {
        data: this.dataAttribute, 
        fieldname: this.dataFieldname,
        basefieldname: this.dataBasefieldname,
        thisValue: this.value? this.value: this.dataValue,
      }
    },
    props: ['value', 'dataAttribute', 'dataFieldname', 'dataBasefieldname', 'dataValue'],
    mounted: function(){
      //console.log(this.data);
    },
    computed: {
      uniqId: function(){
        return 'number' + this.data.id + Math.random();
      },
      uniqFieldname: function(){
        return this.basefieldname + '[' + this.data.id + '][value]';
      },
      // setValue: {
      //   get: function(){
      //     return this.value? this.value : this.data.default_value;  
      //   },
      //   set: function(value){
      //     this.value = value;
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