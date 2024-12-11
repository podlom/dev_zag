<template>
  <div class="form-group col-sm-12">
    <label :for="uniqId">{{ data.name }}</label>

    <select :name="uniqFieldname" :id="uniqId" class="form-control" v-model="thisValue">
      <option v-for="(current_value, index) in data.values" :value="current_value.name" >{{ current_value.name }}</option>
    </select>
  </div>
</template>
<script>
  export default {
    name: 'color',
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
      //console.log(this.data.values[0].name);
    },
    computed: {
      uniqId: function(){
        return 'color' + this.data.id + Math.random();
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