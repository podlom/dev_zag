<?php
  $region = isset($entry)? $entry->address['region'] : null;
  $area = isset($entry)? $entry->address['area'] : null;
  $city = isset($entry)? $entry->address['city'] : null;
  $kyivdistrict = isset($entry) && isset($entry->address['kyivdistrict'])? $entry->address['kyivdistrict'] : null;
?>

<div id="address_zagorodna" class="form-group col-sm-12">
  <div class="form-group">
    <label>Область</label>
    <input type="hidden" name="address[region]" value="0">
    <select name="address[region]" class="form-control" v-model="address.region">
      <option value="0">Не выбрано</option>
      <option :value="key" v-for="(item, key) in regions">{{ item }}</option>
    </select>
  </div>

  <div class="form-group" v-if="address.region != 29">
    <label>Район</label>
    <input type="hidden" name="address[area]" value="0">
    <select name="address[area]" class="form-control" v-model="address.area" :disabled="!Object.keys(areas).length">
      <option value="0">Не выбрано</option>
      <option :value="key" v-for="(item, key) in areas">{{ item }}</option>
    </select>
  </div>

  <div class="form-group" v-else>
    <label>Район</label>
    <input type="hidden" name="address[kyivdistrict]" value="0">
    <select name="address[kyivdistrict]" class="form-control" v-model="address.kyivdistrict" :disabled="!Object.keys(areas).length">
      <option value="0">Не выбрано</option>
      <option :value="key" v-for="(item, key) in areas">{{ item }}</option>
    </select>
  </div>

  <div v-if="address.region != 29">
    <label>Город</label>
    <input type="hidden" name="address[city]" value="0">
    <select name="address[city]" class="form-control" v-model="address.city" :disabled="!Object.keys(cities).length">
      <option value="0">Не выбрано</option>
      <option :value="key" v-for="(item, key) in cities">{{ item }}</option>
    </select>
  </div>
  <div v-else>
    <input type="hidden" name="address[area]" value="2732">
    <input type="hidden" name="address[city]" value="112500">
  </div>
</div>

<script>
  var address = {
    region: '<?php echo e($region); ?>',
    area: '<?php echo e($area); ?>',
    city: '<?php echo e($city); ?>',
    kyivdistrict: '<?php echo e($kyivdistrict); ?>',
  };

  var regions = <?php echo json_encode(App\Region::pluck('name', 'region_id'), 512) ?>;
</script>
<script src="<?php echo e(url('/js/fields/address_zagorodna.js?v=1.1')); ?>"></script><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/vendor/backpack/crud/fields/address_zagorodna.blade.php ENDPATH**/ ?>