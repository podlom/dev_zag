
<div class="form-group col-sm-12" id="product_images">
  <label for="uniqId">Изображения</label>
  <draggable v-model="images" group="items" @start="drag=true" @end="drag=false">
  <div class="form-group d-flex flex-wrap justify-content-between" v-for="(value, index) in images">
    <!-- <div class="input-group d-flex justify-content-between" style="width: 75%">
      <input type="text" :name="uniqFieldname + '[' + index + '][name]'" v-model="thisValue[index].name" class="form-control" style="width: 75%" placeholder="Название цвета">
      <input type="color" :name="uniqFieldname + '[' + index + '][code]'" v-model="thisValue[index].code" class="form-control" style="width: 25%">
    </div> -->
    <div class="form-group" style="width: 100%">
      <label>Изображение @{{ index + 1 }}</label>
      <div class="input-group">
        <div>
        <!-- Wrap the image or canvas element with a block element (container) -->
        <div class="row">
            <div class="col-sm-6" style="margin-bottom: 20px;">
                <img :src="previews[index]? previews[index] : (value.indexOf('http') === -1 && value.indexOf('/') != 0? '/' + value : value)" style="height: 200px;max-width:none">
            </div>
        </div>
        <div class="btn-group">
            <div class="btn btn-light btn-sm btn-file">
                Выберите файл <input type="file" class="hide" name="images[]" @change="fileChange($event, index)">
                <input type="hidden" name="images[]" :value="value" v-if="!previews[index]">
            </div>
            <div class="btn btn-danger btn-sm option-delete" @click="removeItem(index)">Удалить</div>
          </div>
        </div>
      </div>

    </div>
  </div>
  </draggable>
  <div class="input-group" style="margin-top: 15px">
    <div class="btn btn-primary option-add" @click="addItem()"><i class="fa fa-plus"></i> Добавить изображение</div>
  </div>
</div>

@push('crud_fields_scripts')

<script>
var images = @json(isset($entry)? $entry->images : []);
</script>

<script src="{{ url('/packages/aimix/shop/js/fields/product_images.js') }}"></script>
@endpush