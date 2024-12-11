@php
  $region = isset($entry)? $entry->region->region_id : null;
  $area = isset($entry)? $entry->area_id : null;
@endphp

<div id="address_zagorodna" class="form-group col-sm-12">
  <div class="form-group">
    <label>Область</label>
    <select class="form-control" v-model="address.region">
      <option value="0">Не выбрано</option>
      <option :value="key" v-for="(item, key) in regions">@{{ item }}</option>
    </select>
  </div>

  <div class="form-group">
    <label>Район</label>
    <input type="hidden" name="area_id" value="0">
    <select name="area_id" class="form-control" v-model="address.area" :disabled="!Object.keys(areas).length">
      <option value="0">Не выбрано</option>
      <option :value="key" v-for="(item, key) in areas">@{{ item }}</option>
    </select>
  </div>
</div>

<script>
  var address = {
    region: '{{ $region }}',
    area: '{{ $area }}',
    city: null
  };

  var regions = @json(App\Region::where('language_abbr', 'ru')->pluck('name', 'region_id'));
</script>
<script src="{{ url('/js/fields/address_zagorodna.js?v=1.0') }}"></script>