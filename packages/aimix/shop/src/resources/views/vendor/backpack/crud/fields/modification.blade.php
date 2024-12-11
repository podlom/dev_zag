@php
  $base = isset($entry)? $entry->modifications()->base() : null;
  $base_default = array('is_active' => 1, 'in_stock' => 1, 'attrs' => [], 'extras' => ['complectations' => []]);
@endphp
<div id="mod_field" class="form-group" style="width: 100%">

<!-- START COMPLECTATIONS -->
@if(config('aimix.shop.enable_complectations'))
<div class="form-group col-sm-12">
  <label>Комплектации</label>
  <div class="array-container form-group">
  <div class="form-group col-sm-12" v-for="(item, index) in baseItem.extras.complectations">
    <input type="text" :name="'{{ $field['name'] }}[0][extras][complectations][' + index + '][value]'" v-model="item.value" class="form-control">
  </div>
    <div class="array-controls btn-group m-t-10">
        <button class="btn btn-sm btn-light" type="button" @click="addComplectation()"><i class="la la-plus"></i> Добавить комплектацию</button>
    </div>
  </div>
</div>
@endif
<!-- END COMPLECTATIONS -->
<!-- START BASE MODIFICATION -->
<div class="@if(config('aimix.shop.enable_modifications')) default-modifications @endif" style="display:none">
  @if(config('aimix.shop.enable_modifications'))
  <h4>Значения по умолчанию</h4>
  @endif
  <input type="hidden" name="{{ $field['name'] }}[0][name]" value="base">
  <input type="hidden" name="{{ $field['name'] }}[0][is_default]" value="1">
  <input type="hidden" name="{{ $field['name'] }}[0][id]" value="{{ $base->id ?? null }}">
  <div class="form-group col-sm-12">
      <label>Код/артикул</label>
      <input type="text" name="{{ $field['name'] }}[0][code]" value="{{ $base->code ?? null }}" class="form-control">
  </div>

  <div class="form-group col-sm-12">
    <label for="price">Цена</label>
    <input type="number" name="{{ $field['name'] }}[0][price]" id="price" value="{{ $base->price ?? null }}" class="form-control">
  </div>
  
  @if(config('aimix.shop.enable_old_price'))
  <div class="form-group col-sm-12">
    <label for="old_price">Старая цена</label>
    <input type="number" name="{{ $field['name'] }}[0][old_price]" id="old_price" value="{{ $base->old_price ?? null }}" class="form-control">
  </div>
  @endif

  <input type="hidden" name="{{ $field['name'] }}[0][is_active]" v-model="baseItem.is_active" value="1">


  <div class="form-group col-sm-12">
    <div class="checkbox">
      <input type="hidden" name="{{ $field['name'] }}[0][is_pricehidden]" value="0">
      <input type="checkbox" name="{{ $field['name'] }}[0][is_pricehidden]" v-model="baseItem.is_pricehidden" value="1"  id="base_pricehidden_checkbox">
      <label class="form-check-label font-weight-normal" for="base_pricehidden_checkbox">Скрыть цену</label>
    </div>
  </div>
  
<!-- START IN_STOCK -->
@if(config('aimix.shop.enable_in_stock'))
  @if(config('aimix.shop.enable_in_stock_count'))
  <!-- START IN_STOCK NUMERIC -->
  <div class="form-group col-sm-12">
    <div class="checkbox">
      <input type="number" min="0" name="{{ $field['name'] }}[0][in_stock]" v-model="baseItem.in_stock" id="base_in_stock_input" class="form-control"> 
      <label class="form-check-label font-weight-normal" for="base_in_stock_input">Товаров в наличии</label>
    </div>
  </div>
  <!-- END IN_STOCK NUMERIC -->
  @else
  <!-- START IN_STOCK BOOLEAN -->
  <div class="form-group col-sm-12">
    <div class="checkbox">
      <input type="hidden" name="{{ $field['name'] }}[0][in_stock]" value="0">
      <input type="checkbox" name="{{ $field['name'] }}[0][in_stock]" v-model="baseItem.in_stock" value="1"  id="base_in_stock_checkbox">
      <label class="form-check-label font-weight-normal" for="base_in_stock_checkbox">Есть в наличии</label>
    </div>
  </div>
  <!-- END IN_STOCK BOOLEAN -->
  @endif
@endif
<!-- END IN_STOCK -->
  
  <template v-for="(attribute, index) in attributes">
    <radio :data-attribute="attribute" :data-fieldname="fieldname"  :data-value="getBaseAttributeValue(attribute.id)" :data-basefieldname="getBaseAttrName(0)" v-if="attribute.type == 'radio'" :key="'key' + index" v-model="baseItem.attrs[attribute.id]"></radio>
    <checkbox :data-attribute="attribute" :data-fieldname="fieldname" :data-value="getBaseAttributeValue(attribute.id)" :data-basefieldname="getBaseAttrName(0)" v-if="attribute.type == 'checkbox'" :key="'key' + index" v-model="baseItem.attrs[attribute.id]"></checkbox>
    <number :data-attribute="attribute" :data-fieldname="fieldname" :data-value="getBaseAttributeValue(attribute.id)" :data-basefieldname="getBaseAttrName(0)" v-if="attribute.type == 'number'" :key="'key' + index" v-model="baseItem.attrs[attribute.id]"></number>
    <string :data-attribute="attribute" :data-fieldname="fieldname" :data-value="getBaseAttributeValue(attribute.id)" :data-basefieldname="getBaseAttrName(0)" v-if="attribute.type == 'string'" :key="'key' + index" v-model="baseItem.attrs[attribute.id]"></string>
    <longtext :data-attribute="attribute" :data-fieldname="fieldname"  :data-value="getBaseAttributeValue(attribute.id)" :data-basefieldname="getBaseAttrName(0)" v-if="attribute.type == 'longtext'" :key="'key' + index" v-model="baseItem.attrs[attribute.id]"></longtext>
    <color :data-attribute="attribute" :data-fieldname="fieldname" :data-value="getBaseAttributeValue(attribute.id)" :data-basefieldname="getBaseAttrName(0)" v-if="attribute.type == 'color'" :key="'key' + index" v-model="baseItem.attrs[attribute.id]"></color>
    <colors :data-attribute="attribute" :data-fieldname="fieldname" :data-value="getBaseAttributeValue(attribute.id)" :data-basefieldname="getBaseAttrName(0)" v-if="attribute.type == 'colors'" :key="'key' + index" v-model="baseItem.attrs[attribute.id]"></colors>
  </template>
  
  @if(config('aimix.shop.enable_modifications'))
  <hr>
  @endif
</div>
<!-- END BASE MODIFICATION -->
<!-- START NOT BASE MODIFICATIONS -->
@if(config('aimix.shop.enable_modifications'))
<div class="form-group col-sm-12">
  <h4>Проекты</h4>
  <!-- START TOP MODIFICATION ADD CONTROL -->
  <div class="row">
    <div class="col-12 col-sm-6 col-xl-8 mb-3">
      <input type="text" class="form-control" placeholder="Название модификации" v-model="newModificationName">
    </div>
    <div class="col-8 col-sm-6 col-xl-4 mb-3">
      <button class="btn btn-block btn-primary" type="button" @click="addItem()">Добавить проект</button>
    </div>
  </div>
  <!-- END TOP MODIFICATION ADD CONTROL -->
    <!-- MODIFICATION START -->
  <template v-if="items.length">
  <div class="card" v-for="(item, index) in items" >
    <div class="card-header d-flex">
        <input type="hidden" :name="'{{ $field['name'] }}[' + (index + 1) + '][id]'" v-model="item.id" class="form-control">
        <input type="text" :name="'{{ $field['name'] }}[' + (index + 1) + '][name]'" v-model="item.name" class="form-control col-12 col-sm-6 col-xl-8 mb-3">
        @if(!$base || !$base->original)
        <div class="col-6 col-sm-4 col-xl-3 mb-3">
          <a class="h-100 w-100 btn btn-danger" href="#"  @click.prevent="deleteItem(index, item.id)">Удалить</a>
        </div>
        <div class="col-4 col-sm-2 col-xl-1 mb-3">
          <a href="#" class="card-header-action btn-minimize btn btn-light h-100 w-100 d-flex align-items-center justify-content-center" @click.prevent="toggleTab(index)"><i class="la la-angle-down"></i></a>
        </div>
        @endif
    </div>
    
    <div class="card-content" :class="{hide: openedTabs.indexOf(index) == -1}">
    
    @if(!$base || !$base->original)
    <!-- START COMPLECTATION SELECT -->
    @if(config('aimix.shop.enable_complectations'))
      <div class="form-group col-sm-12">
        <label for="">Комплектация</label>

        <select :name="'{{ $field['name'] }}[' + (index + 1) + '][extras][complectation]'" class="form-control" v-model="item.extras.complectation">
          <option v-for="complectation in trimedComplectations" :value="complectation.value">@{{ complectation.value }}</option>
        </select>
      </div>
    @endif
    <!-- END COMPLECTATION SELECT -->
    
      <div class="form-group col-sm-12">
        <label>URL</label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text">http://car.parabee8.beget.tech/catalog/{slug товара}/</span>
            </div>
            <input type="text" :name="'{{ $field['name'] }}[' + (index + 1) + '][slug]'" v-model="item.slug" value="" class="form-control">
        </div> 
        <p class="help-block">По умолчанию будет сгенерирован из названия.</p>
      </div>

      <div class="form-group col-sm-12">
        <label>Код/артикул</label>
        <input type="text" :name="'{{ $field['name'] }}[' + (index + 1) + '][code]'" value="" v-model="item.code" class="form-control">
      </div>

      <div class="form-group col-sm-12">
        <label>Цена</label>
        <input type="number" :name="'{{ $field['name'] }}[' + (index + 1) + '][price]'" value="" v-model="item.price" class="form-control">
      </div>
      
      @if(config('aimix.shop.enable_old_price'))
      <div class="form-group col-sm-12">
        <label>Старая цена</label>
        <input type="number" :name="'{{ $field['name'] }}[' + (index + 1) + '][old_price]'" value="" v-model="item.old_price" class="form-control">
      </div>
      @endif

      <div class="form-group col-sm-12">
        <div class="checkbox">
          <input type="hidden" :name="'{{ $field['name'] }}[' + (index + 1) + '][is_active]'" value="0">
          <input type="checkbox" :name="'{{ $field['name'] }}[' + (index + 1) + '][is_active]'" value="1" v-model="item.is_active" :id="'{{ $field['name'] }}[' + (index + 1) + '][is_active]'">
          <label class="form-check-label font-weight-normal" :for="'{{ $field['name'] }}[' + (index + 1) + '][is_active]'">Активно</label>
        </div>
      </div>

      <div class="form-group col-sm-12">
        <div class="checkbox">
          <input type="hidden" :name="'{{ $field['name'] }}[' + (index + 1) + '][is_pricehidden]'" value="0">
          <input type="checkbox" :name="'{{ $field['name'] }}[' + (index + 1) + '][is_pricehidden]'" value="1" v-model="item.is_pricehidden" :id="'{{ $field['name'] }}[' + (index + 1) + '][is_pricehidden]'">
          <label class="form-check-label font-weight-normal" :for="'{{ $field['name'] }}[' + (index + 1) + '][is_pricehidden]'">Скрыть цену</label>
        </div>
      </div>
      
      <template v-for="(attribute, attrIndex) in attributes">
        <radio :data-attribute="attribute" :data-fieldname="fieldname" :data-basefieldname="getBaseAttrName(index + 1)" v-if="attribute.type == 'radio'" :key="'key' + attrIndex" :data-value="item.attrs[attribute.id]" v-model="item.attrs[attribute.id]"></radio>
        <checkbox :data-attribute="attribute" :data-fieldname="fieldname" :data-basefieldname="getBaseAttrName(index + 1)" v-if="attribute.type == 'checkbox'" :key="'key' + attrIndex" :data-value="item.attrs[attribute.id]" v-model="item.attrs[attribute.id]"></checkbox>
        <number :data-attribute="attribute" :data-fieldname="fieldname" :data-basefieldname="getBaseAttrName(index + 1)" v-if="attribute.type == 'number'" :key="'key' + attrIndex" :data-value="item.attrs[attribute.id]" v-model="item.attrs[attribute.id]"></number>
      </template>
      
      <div class="form-group col-sm-12" id="product_images">
        <label for="uniqId">Изображения</label>
        <!-- <draggable v-model="item.images" group="items" @start="drag=true" @end="drag=false"> -->
        <div class="form-group d-flex flex-wrap justify-content-between" v-for="(value, key) in item.images">
          <div class="form-group" style="width: 100%">
            <label>Изображение @{{ key + 1 }}</label>
            <div class="input-group">
              <div>
              <!-- Wrap the image or canvas element with a block element (container) -->
              <div class="row">
                  <div class="col-sm-6" style="margin-bottom: 20px;">
                      <img :src="item.previews && item.previews[key]? item.previews[key] : (value? '/' + value : '')" style="height: 200px;max-width:none">
                  </div>
              </div>
              <div class="btn-group">
                  <div class="btn btn-light btn-sm btn-file">
                      Выберите файл <input type="file" class="hide" :name="'{{ $field['name'] }}[' + (index + 1) + '][images][' + key + ']'" @change="fileChange($event, index, key)">
                      <input type="hidden" :name="'{{ $field['name'] }}[' + (index + 1) + '][images][' + key + ']'" :value="value" v-if="!item.previews || (item.previews && !item.previews[key])">
                  </div>
                  <div class="btn btn-danger btn-sm option-delete" @click="removeImage(index, key)">Удалить</div>
                </div>
              </div>
            </div>

          </div>
        </div>
        <!-- </draggable> -->
        <div class="input-group" style="margin-top: 15px">
          <div class="btn btn-primary option-add" @click="addImage(index)"><i class="fa fa-plus"></i> Добавить изображение</div>
        </div>
      </div>
      @endif
      
    </div>
    
  </div>
  </template>
    <!-- MODIFICATION END -->
  <template v-else>
    <div class="col-12 text-center">Проекты отсутствуют</div>
  </template>
  <!-- START BOTTOM MODIFICATION ADD CONTROL -->
  <template v-if="items.length > 2">
    <div class="row">
      <div class="col-12 col-sm-6 col-xl-8 mb-3">
        <input type="text" class="form-control" placeholder="Название проекта" v-model="newModificationName">
      </div>
      <div class="col-8 col-sm-6 col-xl-4 mb-3">
        <button class="btn btn-block btn-primary" type="button" @click="addItem()">Добавить проект</button>
      </div>
    </div>
  </template>
  <!-- END BOTTOM MODIFICATION ADD CONTROL -->
</div>
@endif
<!-- END NOT BASE MODIFICATIONS -->
</div>
<script></script>
@if ($crud->checkIfFieldIsFirstOfItsType($field, $fields))

@push('crud_fields_styles')
<style>
.default-modifications {
  padding-top: 10px;
  background-color: #f1f1f1;
}
.card-content {
  transition: max-height .3s;
  overflow: hidden;
  max-height: 4000px;
}
.card-content.hide {
  max-height: 0px;
}
.btn {
  cursor: pointer
}
</style>
@endpush

@push('crud_fields_scripts')

<script>
  
var baseItem = @json(isset($entry) ? $entry->modifications()->Base() : $base_default);
var items = @json(isset($entry) ? $entry->modifications()->NotBase()->get() : []);
var attributes = @json(isset($crud->attributes) ? $crud->attributes->where('in_properties', 1)->keyBy('id') : []);
var complectations = @json(isset($entry) && isset($entry->complectations) ? $entry->complectations : []);
var fieldname = @json($field['name']);
var enableComplectations = @json(config('aimix.shop.enable_complectations'));

</script>

<script src="{{ url('/packages/aimix/shop/js/fields/modification.js') }}"></script>

@endpush
@endif