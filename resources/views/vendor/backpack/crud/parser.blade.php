@extends(backpack_view('blank'))

@php
  $breadcrumbs = [
    trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
    $title => false,
  ];
@endphp

@section('header')
  <div class="container-fluid">
    <h2>
      <span class="text-capitalize">{{ $title }}</span>
      <small id="datatable_info_stack"></small>
    </h2>
  </div>
@endsection

@section('content')
<div id="parser">
  <div class="row">
    <div class="form-group col-3">
      <select name="logs" class="form-control " v-model="log_id">
        <option :value="id" v-for="(date, id) in logs">@{{ date }}</option>
      </select>
    </div>
    <div class="form-group col-3"  v-if="!jobs_left">
      <button class="btn btn-primary" @click="startParsing()">Запустить</button>
    </div>
    <div class="form-group col-3 d-flex align-items-center" v-else>
      В обработке: @{{ jobs_left }}
    </div>

  </div>
  <div class="row">
    <div class="form-group btn-group col-4">
      <button class="btn btn-default" @click="type = 'product'" :class="{active: type === 'product'}">Городки</button>
      <button class="btn btn-default" @click="type = 'newbuild'" :class="{active: type === 'newbuild'}">Новостройки</button>
      <button class="btn btn-default" @click="type = 'promotion'" :class="{active: type === 'promotion'}">Акции</button>
      <button class="btn btn-default" @click="type = 'brand'" :class="{active: type === 'brand'}">Компании</button>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <table id="crudTable" class="bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs" cellspacing="0">
        <thead>
          <tr>
            <th style="cursor:pointer;user-select:none" @click="only_new = !only_new" :style="{color: only_new? '#399400' : 'initial'}">Новый</th>
            <th>Название</th>
            <th>Ссылки</th>
            <th>Обработано в</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in filtered_table">
            <td>
              <span>
                <i class="la" :class="{'la-circle': !item.is_new, 'la-check-circle': item.is_new}"></i>
              </span>
            </td>
            <td>@{{ item.name }}</td>
            <td><a :href="item.admin_link" target="_blank">В админке</a> | <a :href="item.link" target="_blank">На сайте</a></td>
            <td>@{{ item.time }}</td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>
  <div class="row mt-2">

    <div class="col-sm-6 col-md-4">
      <div class="dataTables_length" id="crudTable_length">
        <label style="display:flex">
          <select name="crudTable_length" aria-controls="crudTable" class="custom-select custom-select-sm form-control" v-model="per_page" style="width:55px">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="-1">Все </option>
          </select>
         <span style="margin-left:5px;display:flex;align-items:center">записей на странице</span></label>
      </div>
    </div>

    <div class="col-sm-2 col-md-4 text-center"></div>

    <div class="col-sm-6 col-md-4 hidden-print">
      <div class="dataTables_paginate paging_simple_numbers" id="crudTable_paginate">

        <ul class="pagination" v-if="last_page > 1">
            <li class="paginate_button page-item previous" v-bind:class="{disabled: current_page == 1}" @click="current_page--" id="crudTable_previous"><a href="#" aria-controls="crudTable" data-dt-idx="0" tabindex="0" class="page-link">&lt;</a></li>
            <li class="paginate_button page-item" @click="current_page = 1" v-bind:class="{active: current_page == 1}"><a href="#" class="page-link">1</a></li>

            <li class="paginate_button page-item disabled" v-if="last_page > 7 && current_page - 1 > 3" id="crudTable_ellipsis"><a href="#" class="page-link">…</a></li>

            <li class="paginate_button page-item " v-for="page in (last_page - 1)" @click="current_page = page" v-bind:class="{active: page == current_page}" v-show="page != 1 && ((current_page == 1 && page <= 6) || (current_page == last_page && page >= last_page - 5) || (Math.abs(current_page - page) < 3) || (current_page <= 3 && page <= 6) || (current_page >= last_page - 3 && page >= last_page - 6))"><a href="#" class="page-link">@{{ page }}</a></li>

            <li class="paginate_button page-item disabled" v-if="last_page > 7 && last_page - current_page > 3" id="crudTable_ellipsis"><a href="#" class="page-link">…</a></li>


            <li class="paginate_button page-item " @click="current_page = last_page" v-if="last_page != 1" v-bind:class="{active: last_page == current_page}"><a href="#" class="page-link">@{{ last_page }}</a></li>

          <li class="paginate_button page-item next" v-bind:class="{disabled: current_page == last_page}" @click="current_page++" id="crudTable_next"><a href="#" class="page-link">&gt;</a></li>
        </ul>

      </div>
    </div>
  </div>
</div>
@endsection

@section('after_styles')
<style>
.paginate_button.disabled {
  pointer-events: none;
}
</style>
@endsection

@section('after_scripts')
<script>
  var logs = @json($logs);
  var log = @json($log);
  var jobs_left = @json($jobs_left);
</script>
<script src="{{ url('js/parser/parser.js?v=' . $version) }}"></script>
@endsection
