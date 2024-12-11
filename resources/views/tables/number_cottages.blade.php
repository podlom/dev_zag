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
          Кол-во
        </td>
        <td colspan="6">
          Типы недвижимости
        </td>
        <td colspan="3">
          Готовность
        </td>
        <td>
          Заморожено
        </td>
      </tr>
      <tr class="title">
        <td>
        </td>
        <td>
        </td>
        @php
          $types = [
            "Вилла" => "вилла",
            'Земельный_участок' => 'з/у',
            "Квадрекс" => "квадр.",
            "Дуплекс" =>  "дупл.",
            "Коттедж" => "котт.",
            "Таунхаус" => "таунх.",
          ];
        @endphp
        @foreach(__('attributes.cottage_types') as $key => $item)
          @if($key != 'Эллинг')
          <td>
            {{ $types[$key] }}
          </td>
          @endif
        @endforeach
        <td>
          постр.
        </td>
        <td>
          проект
        </td>
        <td>
          строит.
        </td>
        <td>
        </td>
      </tr>
			<template v-for="(region, regionKey) in table.regions">
				<tr style="background-color: #808080" @click="opened.regions.includes(regionKey)? opened.regions.splice(opened.regions.indexOf(regionKey), 1) : opened.regions.push(regionKey)">
					<td>
						@{{ regionKey }}
					</td>
					<td>
            @{{ region.total }}
					</td>
          <td v-for="(item, key) in region.types">
            @{{ item }}
          </td>
          <td>
            @{{ region.status_done }}
          </td>
          <td>
            @{{ region.status_project }}
          </td>
          <td>
            @{{ region.status_building }}
          </td>
          <td>
            @{{ region.frozen }}
          </td>
				</tr>
				<template v-for="(area, areaKey) in region.areas">
					<tr style="background-color: #D3D3D3" v-show="opened.regions.includes(regionKey)" @click="opened.areas.includes(areaKey)? opened.areas.splice(opened.areas.indexOf(areaKey), 1) : opened.areas.push(areaKey)">
						<td>
							@{{ areaKey }}
						</td>
						<td>
              @{{ area.total }}
						</td>
						<td v-for="(item, key) in area.types">
              @{{ item }}
						</td>
						<td>
              @{{ area.status_done }}
						</td>
						<td>
              @{{ area.status_project }}
						</td>
						<td>
              @{{ area.status_building }}
						</td>
						<td>
              @{{ area.frozen }}
						</td>
					</tr>
					<template v-for="(city, cityKey) in area.cities">
						<tr v-show="opened.areas.includes(areaKey) && opened.regions.includes(regionKey)" class="cities">
							<td>
								@{{ cityKey }}
							</td>
							<td>
                @{{ city.total }}
							</td>
              <td v-for="(item, key) in city.types">
                @{{ item }}
              </td>
              <td>
                @{{ city.status_done }}
              </td>
              <td>
                @{{ city.status_project }}
              </td>
              <td>
                @{{ city.status_building }}
              </td>
              <td>
                @{{ city.frozen }}
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
          @{{ table.total.total }}
        </td>
        <td v-for="(item, key) in table.total.types">
          @{{ item }}
        </td>
        <td>
          @{{ table.total.status_done }}
        </td>
        <td>
          @{{ table.total.status_project }}
        </td>
        <td>
          @{{ table.total.status_building }}
        </td>
        <td>
          @{{ table.total.frozen }}
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
  var area = false;
</script>
<script src="{{ url('js/tables/statistics.js?v=' . $version) }}"></script>