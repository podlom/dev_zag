<template>
  <div class="form-group col-sm-12">
    <label :for="uniqId">{{ data.name }}</label>

    <select :name="uniqFieldname" :id="uniqId" class="form-control" v-model="thisValue">
      <option v-for="(current_value, index) in data.values" :value="current_value" >{{ current_value }}</option>
    </select>
  </div>
</template>
<script>
  export default {
    name: 'radio',
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
      if(!this.thisValue)
        this.thisValue = this.data.default_value;
    },
    computed: {
      uniqId: function(){
        return 'radio' + this.data.id + Math.random();
      },
      uniqFieldname: function(){
        return this.basefieldname + '[' + this.data.id + '][value]';
      },
      // setValue: {
      //   get: function(){
      //     return this.thisValue? this.thisValue : this.data.default_value;  
      //   },
      //   set: function(value){
      //     this.thisValue = value;
      //   }
      // }
    },
    watch: {
      thisValue: function(value){
        this.$emit('input', value);
      },
      dataValue: function(value){
       this.thisValue = value;
      }
    }
  }
</script>