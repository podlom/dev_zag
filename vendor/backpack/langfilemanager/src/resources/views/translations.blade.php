@extends(backpack_view('layouts.top_left'))

@php
  $defaultBreadcrumbs = [
    trans('backpack::crud.admin') => backpack_url('dashboard'),
    $crud->entity_name_plural => url($crud->route),
    trans('backpack::crud.edit').' '.trans('backpack::langfilemanager.texts') => false,
  ];

  // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
  $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
	<section class="container-fluid">
	  <h2>
        <span class="text-capitalize">{{ trans('backpack::langfilemanager.translate') }}</span>
        <small>{{ trans('backpack::langfilemanager.site_texts') }}.</small>

        @if ($crud->hasAccess('list'))
          <small><a href="{{ url($crud->route) }}" class="hidden-print font-sm"><i class="fa fa-angle-double-left"></i> {{ trans('backpack::crud.back_to_all') }} <span>{{ $crud->entity_name_plural }}</span></a></small>
        @endif
	  </h2>
	</section>
@endsection

@section('content')
<!-- Default box -->
  <div class="box">
  	<div class="box-header with-border">
	  <h3 class="box-title float-right pr-1">
		<small>
			 &nbsp; {{ trans('backpack::langfilemanager.switch_to') }}: &nbsp;
			<select name="language_switch" id="language_switch">
				@foreach ($languages as $lang)
				<option value="{{ url(config('backpack.base.route_prefix', 'admin')."/language/texts/{$lang->abbr}") }}" {{ $currentLang == $lang->abbr ? 'selected' : ''}}>{{ $lang->name }}</option>
				@endforeach
			</select>
		</small>
	  </h3>
	</div>
    <div class="box-body">
		<ul class="nav nav-tabs">
			@foreach ($langFiles as $file)
			<li class="nav-item">
				<a class="nav-link {{ $file['active'] ? 'active' : '' }}" href="{{ $file['url'] }}">{{ $file['name'] }}</a>
			</li>
			@endforeach
		</ul>
		<section class="tab-content p-3 lang-inputs">
		@if (!empty($fileArray))
			<form
				method="post"
				id="lang-form"
				class="form-horizontal"
				data-required="{{ trans('admin.language.fields_required') }}"
		  		action="{{ url(config('backpack.base.route_prefix', 'admin')."/language/texts/{$currentLang}/{$currentFile}") }}"
		  		>
				{!! csrf_field() !!}
				<div class="form-group row">
					<div class="col-sm-2">
						<h5>{{ trans('backpack::langfilemanager.key') }}</h5>
					</div>
					<div class="hidden-sm hidden-xs col-md-5">
						<h5>{{ trans('backpack::langfilemanager.language_text', ['language_name' => $browsingLangObj->name]) }}</h5>
					</div>
					<div class="col-sm-10 col-md-5">
						<h5>{{ trans('backpack::langfilemanager.language_translation', ['language_name' => $currentLangObj->name]) }}</h5>
					</div>
				</div>
				{!! $langfile->displayInputs($fileArray) !!}
				<hr>
				<div class="text-center">
					<button type="submit" class="btn btn-success submit">{{ trans('backpack::crud.save') }}</button>
				</div>
				</form>
				@else
					<em>{{ trans('backpack::langfilemanager.empty_file') }}</em>
				@endif
			</section>
    </div><!-- /.card-body -->
    	<p><small>{!! trans('backpack::langfilemanager.rules_text') !!}</small></p>
  </div><!-- /.card -->
@endsection

@section('after_scripts')
	<script>
		jQuery(document).ready(function($) {
			$("#language_switch").change(function() {
				window.location.href = $(this).val();
			})
		});
	</script>
@endsection
