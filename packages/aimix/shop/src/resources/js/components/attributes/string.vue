<template>
  <div class="form-group col-sm-12">
    <label :for="uniqId">{{ data.name }}</label>
    <input type="text" :name="uniqFieldname" class="form-control" v-model="thisValue">
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
    },
    watch: {
      thisValue: function(value){
        this.$emit('input', value);
      }
    },
  }
</script>