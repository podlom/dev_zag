<div id="achievements" class="form-group col-sm-12">
  <div class="form-group col-sm-12">
    <label><?php echo e($field['label']); ?></label>
    <div class="images">
      <div class="images-item" v-for="(image, key) in images">
        <div class="d-flex justify-content-between">
          <h3>Достижение {{ key + 1 }}</h3>
          <div class="btn-group col-2">
            <button class="btn btn-block btn-danger" type="button" @click="removeImage(key)">Удалить</button>
          </div>
          
        </div>
        <hr>
        <div class="form-group">
          <label>Заголовок</label>
          <input type="text" :name="'achievements[' + key + '][name]'" v-model="image.name" class="form-control">
        </div>
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
								<div class="btn btn-light btn-sm btn-file">
										Выберите файл <input type="file" class="hide" :name="'achievements[' + key + '][image]'" @change="fileChange($event, key)">
										<input type="hidden" :name="'achievements[' + key + '][image]'" :value="image.image" v-if="!image.preview">
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
    </div>
    <div class="btn-group m-t-10">
      <button class="btn btn-block btn-primary" type="button" @click="addImage()">Добавить изображение</button>
    </div>
  </div>
</div>

<?php $__env->startPush('crud_fields_styles'); ?>
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
<?php $__env->stopPush(); ?>

<script>
  var images = <?php echo json_encode(isset($entry) && $entry->achievements? $entry->achievements : [], 15, 512) ?>;
</script>
<script src="<?php echo e(url('/js/fields/achievements.js')); ?>"></script><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/vendor/backpack/crud/fields/achievements.blade.php ENDPATH**/ ?>