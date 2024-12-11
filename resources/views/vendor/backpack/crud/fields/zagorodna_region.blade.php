@php
  $region = isset($entry)? $entry->region_id : null;
@endphp

<div id="address_zagorodna" class="form-group col-sm-12">
  <div class="form-group">
    <label>Область</label>
    <input type="hidden" name="region_id" value="0">
    <select name="region_id" class="form-control" v-model="address.region">
      <option value="0">Не выбрано</option>
      <option :value="key" v-for="(item, key) in regions">@{{ item }}</option>
    </select>
  </div>
</div>

<script>
  var address = {
    region: '{{ $region }}',
    area: null,
    city: null
  };

  var regions = @json(App\Region::pluck('name', 'region_id'));
</script>
<script src="{{ url('/js/fields/address_zagorodna.js?v=1.0') }}"></script>