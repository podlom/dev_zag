<div id="gallery_field" class="form-group col-sm-12">
  <div class="form-group col-sm-12">
    <label>{{ $field['label'] }}</label>
    <div class="images">
    <draggable v-model="images" group="items" @start="drag=true" @end="drag=false">
      <div class="images-item" v-for="(image, key) in images">
        <div class="d-flex justify-content-between">
          <h3>Изображение @{{ key + 1 }}</h3>
          <div class="btn-group col-2">
            <button class="btn btn-block btn-danger" type="button" @click="removeImage(key)">Удалить</button>
          </div>
          
        </div>
        <hr>
        <!-- <div class="form-group">
          <label>Заголовок</label>
          <input type="text" :name="'images[' + key + '][name]'" v-model="image.name" class="form-control">
        </div> -->
        <div class="form-group">
          <label>Изображение</label>
					<div class="input-group">
						<div>
						<!-- Wrap the image or canvas element with a block element (container) -->
						<div class="row">
								<div class="col-sm-6" style="margin-bottom: 20px;">
										<img :src="image.preview? image.preview : (image.image? '/' + image.image : '')" style="height: 200px;max-width:none">
								</div>
						</div>
						<div class="btn-group">
								<div class="btn btn-light btn-sm btn-file btn-file">
										Выберите файл <input type="file" class="hide" :name="'images[' + key + '][image]'" @change="fileChange($event, key)">
										<input type="hidden" :name="'images[' + key + '][image]'" :value="image.image" v-if="!image.preview">
								</div>
							</div>
						</div>
					</div>
        </div>
        <!-- <div class="form-group">
          <label>Описание</label>
          <textarea name="" rows="6" class="form-control" :name="'images[' + key + '][desc]'" v-model="image.desc"></textarea>
        </div> -->
      </div>
    </draggable>
    </div>
    <div class="btn-group m-t-10">
      <button class="btn btn-block btn-primary" type="button" @click="addImage()">Добавить изображение</button>
    </div>
  </div>
</div>

@push('crud_fields_styles')
<style>
.hide {
	position: absolute;
	top: 0;
	right: 0;
	min-width: 100%;
	min-height: 100%;
	font-size: 100px;
	text-align: right;
	filter: alpha(opacity=0);
	opacity: 0;
	outline: none;
	background: white;
	cursor: inherit;
	display: block;
}
.btn-file {
  overflow: hidden;
}
</style>
@endpush
<script>
  var images = @json(isset($entry)? $entry->images : []);
</script>
<script src="{{ url('/packages/aimix/gallery/js/fields/gallery_images.js') }}"></script>