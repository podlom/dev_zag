<template>
  <div class="form-group col-sm-12">
    <label :for="uniqId">{{ data.name }}</label>
    <textarea :name="uniqFieldname" v-model="thisValue" style="display: none"></textarea>
    <ckeditor :editor="editor" :config="editorConfig"  class="form-control" v-model="thisValue"></ckeditor>
  </div>
</template>

<style>
  .ck-editor__editable {
    min-height: 500px;
   }
</style>

<script>
  
  import CKEditor from '@ckeditor/ckeditor5-vue';
  import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

  export default {
    name: 'longtext',
    data: function(){
      return {
        data: this.dataAttribute, 
        fieldname: this.dataFieldname,
        basefieldname: this.dataBasefieldname,
        thisValue: this.value? this.value: this.dataValue,
        editor: ClassicEditor,
        editorConfig: {
            name: 'ddd'
        }
      }
    },
    components: {
        // Use the <ckeditor> component in this view.
        ckeditor: CKEditor.component
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