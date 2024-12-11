<?php
$lang = session('lang')? session('lang') : 'ru';
// Home
Breadcrumbs::for('home', function ($trail) use ($lang) {
    $trail->push(__('main.Главная'), $lang === 'ru'? url('/') : url($lang));
});

// Home > Page
Breadcrumbs::for('page', function ($trail, $title) {
    $trail->parent('home');
    $trail->push($title);
});

// Home > About > Policy
Breadcrumbs::for('policy', function ($trail, $title) use ($lang) {
  $aboutLink = $lang == 'ru'? url('ru/o-kompanii') : url('uk/pro-kompaniyu');
  $trail->parent('home');
  $trail->push(Backpack\PageManager\app\Models\Page::where('template', 'about')->first()->withFakes()->main_title, $aboutLink);
  $trail->push($title);
});

// Home > Researches
Breadcrumbs::for('researches', function ($trail) use ($lang) {
  $trail->parent('home');
  $trail->push(__('main.Маркетинговые исследования'), route($lang . '_researches'));
});

// Home > Researches > Research
Breadcrumbs::for('research', function ($trail, $title) {
  $trail->parent('researches');
  $trail->push($title);
});

// Home > News > Category > Theme? > Article
Breadcrumbs::for('article', function ($trail, $category, $theme, $title) {
  $trail->parent('home');
  $trail->push($category->name, $category->link);
  $trail->push($theme->name, $theme->link);
  $trail->push($title);
});

// Home > Companies > Category > Company
Breadcrumbs::for('company', function ($trail, $category, $title) use ($lang) {
  $trail->parent('home');
  $trail->push(__('main.Компании'), route($lang . '_companies'));
  $trail->push($category->name, route($lang . '_companies', $category->slug));
  $trail->push($title);
});

// Home > Precatalog
Breadcrumbs::for('precatalog', function ($trail, $category, $region = null, $area = null, $city = null, $kyivdistrict = null) use ($lang) {
  $trail->parent('home');
  $trail->push($category->name, route($lang . '_precatalog', $category->slug));

  if($region)
    $trail->push($region->name, route($lang . '_precatalog', $category->slug . '/region/' . $region->slug));

  if($area)
    $trail->push($area->name, route($lang . '_precatalog', $category->slug . '/area/' . $area->slug));

  if($city)
    $trail->push($city->name, route($lang . '_precatalog', $category->slug . '/city/' . $city->slug));

  if($kyivdistrict)
    $trail->push($kyivdistrict->name, route($lang . '_precatalog', $category->slug . '/kyivdistrict/' . $kyivdistrict->slug));
});

// Home > Map
Breadcrumbs::for('map', function ($trail, $slug, $title) use ($lang) {
  $trail->parent('home');
  $trail->push($title, route($lang . '_map', $slug));
});

// Home > Precatalog > Catalog
Breadcrumbs::for('catalog', function ($trail, $category) {
  $trail->parent('precatalog', $category);
  $trail->push(__('main.Каталог'));
});

// Home > Precatalog > Statistics
Breadcrumbs::for('precatalog_statistics', function ($trail, $category) {
  $trail->parent('precatalog', $category);
  $trail->push(__('main.Статистика'));
});

// Product
Breadcrumbs::for('product', function ($trail, $product) use ($lang) {
  $trail->parent('home');

  $type = $product->category->id === 1 || $product->category->original_id === 1? 'cottage' : 'newbuild';

  $slug = $type === 'cottage'? 'kottedzhnye-gorodki-poselki-ukrainy' : 'novostrojki-prigoroda';
  $trail->push($product->category->name, route($lang . '_precatalog', $product->category->slug));

  if($product->address['region']) {
    $trail->push($product->region, route($lang . '_precatalog', $product->category->slug) . '/region/' . \App\Region::where('region_id', $product->address['region'])->first()->slug);
  }

  if($product->address['area'] && $product->address['region'] != 29) {
    $trail->push($product->area, route($lang . '_precatalog', $product->category->slug) . '/area/' . \App\Area::where('area_id', $product->address['area'])->first()->slug);
  }
  
  if($product->address['city'] && (\App\Area::where('area_id', $product->address['area'])->first() && !\App\Area::where('area_id', $product->address['area'])->first()->is_center)) {
    $trail->push($product->city, route($lang . '_precatalog', $product->category->slug) . '/city/' . \App\City::where('city_id', $product->address['city'])->first()->slug);
  }
   
/*
  if(isset($product->address['kyivdistrict'])) {
	  $kyivdistrict = \App\Kyivdistrict::find($product->address['kyivdistrict']);
	  
	  if($kyivdistrict)
    	$trail->push($kyivdistrict->name, route($lang . '_precatalog', $product->category->slug . '/kyivdistrict/' . $kyivdistrict->slug));
  }
*/

  $trail->push($product->name, $product->link);
});

// Product tab
Breadcrumbs::for('product_tab', function ($trail, $product, $tab_name, $tab_slug, $project = null) use ($lang) {
  $trail->parent('product', $product);
  $trail->push($tab_name, $product->link . '/' . $tab_slug);
  
  if($project)
    $trail->push($project->name, $product->link . '/' . $tab_slug . '/' . $project->slug);
});

// Home > Statistics > Theme > Article
Breadcrumbs::for('statistics', function ($trail, $category, $theme, $title) {
  $trail->parent('home');
  $trail->push($category->name, $category->link);
  $trail->push($theme->name, $theme->link);
  $trail->push($title);
});

// Home > Regions > Theme > Article
Breadcrumbs::for('regions', function ($trail, $theme, $title) {
  $trail->parent('home');
  $trail->push($theme->name, $theme->link);
  $trail->push($title);
});

// Home > Theme > Article
Breadcrumbs::for('services', function ($trail, $theme, $title) {
  $trail->parent('home');
  $trail->push($theme->name, $theme->link);
  $trail->push($title);
});

// Home > Events > Theme > Article
Breadcrumbs::for('events', function ($trail, $category, $theme, $title) {
  $trail->parent('home');
  
  if($category)
    $trail->push($category->name, $category->link);

  $trail->push($theme->name, $theme->link);
  $trail->push($title);
});

// Home > Category > Poll results
Breadcrumbs::for('poll_results', function ($trail, $category, $title) {
  $trail->parent('home');
  $trail->push($category->name, $category->link);
  $trail->push($title);
});