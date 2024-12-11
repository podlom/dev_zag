<template>
  <div class="form-group col-sm-12">
    <label :for="uniqId">{{ data.name }}</label>

    <draggable v-model="thisValue" group="colors" @start="drag=true" @end="drag=false">
    <div class="form-group d-flex flex-wrap justify-content-between" v-for="(value, index) in thisValue">
			<div class="input-group d-flex justify-content-between" style="width: 75%">
				<input type="text" :name="uniqFieldname + '[' + index + '][name]'" v-model="thisValue[index].name" class="form-control" style="width: 75%" placeholder="Название цвета">
				<input type="color" :name="uniqFieldname + '[' + index + '][code]'" v-model="thisValue[index].code" class="form-control" style="width: 25%">
      </div>
	    <div class="input-group" style="width: 20%">
		    <div class="btn btn-danger option-delete w-100" @click="removeItem(index)">Удалить</div>
      </div>
      <div class="form-group" style="width: 100%">
        <label>Изображение</label>
        <div class="input-group">
          <div>
      <!-- Wrap the image or canvas element with a block element (container) -->
      <div class="row">
          <div class="col-sm-6" style="margin-bottom: 20px;">
              <img :src="thisValue[index].preview? thisValue[index].preview : (thisValue[index].image? '/' + thisValue[index].image : '')" style="height: 150px;max-width:none">
          </div>
      </div>
      <div class="btn-group">
          <div class="btn btn-light btn-sm btn-file">
              Выберите файл <input type="file" :name="uniqFieldname + '[' + index + '][image]'" class="hide" @change="fileChange($event, index)">
              <input type="hidden" :name="uniqFieldname + '[' + index + '][image]'" :value="thisValue[index].image" v-if="!thisValue[index].preview">
          </div>
          <!-- <button class="btn btn-light btn-sm" data-handle="remove" type="button"><i class="fa fa-trash"></i></button> -->
        </div>
      </div>
    </div>

      </div>
    </div>
    </draggable>
    <div class="input-group" style="margin-top: 15px">
      <div class="btn btn-primary option-add" @click="addItem()"><i class="fa fa-plus"></i> Добавить значение</div>
    </div>
    

    <!-- <select :name="uniqFieldname" :id="uniqId" class="form-control" v-model="thisValue">
      <option v-for="(current_value, index) in data.values" :value="current_value.name" >{{ current_value.name }}</option>
    </select> -->
  </div>
</template>

<script>
  import draggable from 'vuedraggable'

  export default {
    name: 'colors',
    data: function(){
      return {
        data: this.dataAttribute,
        fieldname: this.dataFieldname,
        basefieldname: this.dataBasefieldname,
        thisValue: this.value? this.value: (this.dataValue? this.dataValue : []),
      }
    },
    components: {
      draggable,
    },
    props: ['value', 'dataAttribute', 'dataFieldname', 'dataBasefieldname', 'dataValue'],
    mounted: function(){
      //console.log(this.thisValue);
    },
    methods: {
      addItem: function(){
        if(this.thisValue.length && !this.thisValue[this.thisValue.length - 1].name) {
          new Noty({
            type: "error",
            text: "Введите название цвета"
          }).show();
          return;
        }
        this.thisValue.push({'name': '', 'code': ''});
      },
      removeItem: function(index){
        this.thisValue.splice(index, 1);
      },
      fileChange: function(event, index){
        Vue.set(this.thisValue[index], 'image', 'uploads/' + event.target.files[0].name);
        Vue.set(this.thisValue[index], 'preview', URL.createObjectURL(event.target.files[0]));
      }
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
    },
  }
</script>

