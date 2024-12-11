<style>
	td {
		padding: 10px;
		cursor: pointer;
		border: 1px solid #ffffff;
	}
	tr.title {
		background-color: #808080;
		background-color: #000000;
		color: #ffffff;
	}
	tr.title td {
		font-weight: bold;
	}
	tr.regions {
		background-color: #808080;
	}
	tr.areas {
		background-color: #D3D3D3;
	}
	tr.cities td {
		border: 1px solid #D3D3D3;
	}
	td a {
		color: black;
	}
	.form {
		width: 150px;
   		height: 75px;
		z-index: 999;
		position: fixed;
		top: 0; 
		right: 0;
		text-align: right;
		padding: 20px;
		margin: 10px;
		border: 1px solid black;
	}
	
</style>	
<div id="app">
	<div class="form">
		<select name="st-id" v-model="selectedTable">
			<option :value="key" v-for="(item, key) in tables">@{{ item }}</option>
		</select><br/><br/>
		@if(backpack_user() && backpack_user()->hasRole('admin'))
		<input name="st-save" type="button" :value="loading? 'Подождите...' : 'Сгенерировать'" @click="generateTable()" :readonly="loading" :style="{pointerEvents: loading? 'none' : 'initial'}"/>
		@endif
	</div>
	<div v-if="table">
		<h1 style="text-align:center;margin: 20px auto;max-width:1000px">{{ $title }} @{{ table.date }}</h1>
		<table style="margin: 20px auto 20px auto; border-collapse: collapse" >
			<tr class="title">
				<td>
					Местоположение
				</td>
				<td>
					Мин. цена
				</td>
				<td>
					Макс. цена
				</td>
				<td>
					Средняя мин. цена
				</td>
				<td>
					Средняя макс. цена
				</td>
				@if($category_id != 1 && !$area)
				<td>
					Квартиры
				</td>
				<td>
					Апартаменты
				</td>
				@endif
				<td>
					Кол-во всего
				</td>
			</tr>
			<template v-for="(region, regionKey) in table.regions">
				<tr style="background-color: #808080" @click="opened.regions.includes(regionKey)? opened.regions.splice(opened.regions.indexOf(regionKey), 1) : opened.regions.push(regionKey)">
					<td>
						@{{ regionKey }}
					</td>
					<td>
						@if($show_links)
							<a target="_blank" :href="region.min.link">@{{ region.min.price }}</a>
						@else
							@{{ region.min.price }}
						@endif
					</td>
					<td>
						@if($show_links)
							<a target="_blank" :href="region.max.link">@{{ region.max.price }}</a>
						@else
							@{{ region.max.price }}
						@endif
					</td>
					<td>
						@{{ region.avg_min }}
					</td>
					<td>
						@{{ region.avg_max }}
					</td>
					@if($category_id != 1 && !$area)
					<td v-for="(item, key) in region.types">
						@{{ item }}
					</td>
					@endif
					<td>
						@{{ region.total }}
					</td>
				</tr>
				<template v-for="(area, areaKey) in region.areas">
					<tr style="background-color: #D3D3D3" v-show="opened.regions.includes(regionKey)" @click="opened.areas.includes(areaKey)? opened.areas.splice(opened.areas.indexOf(areaKey), 1) : opened.areas.push(areaKey)">
						<td>
							@{{ areaKey }}
						</td>
						<td>
							@if($show_links)
								<a target="_blank" :href="area.min.link">@{{ area.min.price }}</a>
							@else
								@{{ area.min.price }}
							@endif
						</td>
						<td>
							@if($show_links)
							<a target="_blank" :href="area.max.link">@{{ area.max.price }}</a>
							@else
								@{{ area.max.price }}
							@endif
						</td>
						<td>
							@{{ area.avg_min }}
						</td>
						<td>
							@{{ area.avg_max }}
						</td>
						@if($category_id != 1 && !$area)
						<td v-for="(item, key) in area.types">
							@{{ item }}
						</td>
						@endif
						<td>
							@{{ area.total }}
						</td>
					</tr>
					<template v-for="(city, cityKey) in area.cities">
						<tr v-show="opened.areas.includes(areaKey) && opened.regions.includes(regionKey)" class="cities">
							<td>
								@{{ cityKey }}
							</td>
							<td>
							@if($show_links)
								<a target="_blank" :href="city.min.link">@{{ city.min.price }}</a>
							@else
								@{{ city.min.price }}
							@endif
							</td>
							<td>
							@if($show_links)
								<a target="_blank" :href="city.max.link">@{{ city.max.price }}</a>
							@else
								@{{ city.max.price }}
							@endif
							</td>
							<td>
								@{{ city.avg_min }}
							</td>
							<td>
								@{{ city.avg_max }}
							</td>
							@if($category_id != 1 && !$area)
							<td v-for="(item, key) in city.types">
								@{{ item }}
							</td>
							@endif
							<td>
								@{{ city.total }}
							</td>
						</tr>
					</template>
				</template>
			</template>
			<tr class="title">
				<td>
					По Украине
				</td>
				<td>
					@{{ table.total.min }}
				</td>
				<td>
					@{{ table.total.max }}
				</td>
				<td>
					@{{ table.total.avg_min }}
				</td>
				<td>
					@{{ table.total.avg_max }}
				</td>
				@if($category_id != 1 && !$area)
				<td v-for="(item, key) in table.total.types">
					@{{ item }}
				</td>
				@endif
				<td>
					@{{ table.total.total }}
				</td>
			</tr>
		</table>
	</div>
	<div v-else style="text-align:center;margin: 20px 0 20px 0;">
		Нет данных.
	</div>
</div>

<script>
	var table = @json($table);
	var tables = @json($tables);
	var selectedTable = @json($selectedTable);
	var category_id = @json($category_id);
	var area = @json($area);
</script>
<script src="{{ url('js/tables/statistics.js?v=' . $version) }}"></script>