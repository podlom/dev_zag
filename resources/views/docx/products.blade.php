<!DOCTYPE html>
<html>
	<head>
	    <meta charset="utf-8">
	    <meta charset="UTF-8">
	    <style>
		  body { font-family: DejaVu Sans, sans-serif; }
		</style>

	</head>
	
	<body>
	
		<h1 style="text-transform: uppercase; text-align: center;font-size:36px;font-weight:600;margin-bottom:10px;">{{ $area->name }} район</h1>

		@foreach($products as $product)
		<div style="margin-bottom:30px;">
			<h2 style="text-transform: uppercase; text-align: center;font-size:30px;font-weight:600;">{{ $product->name }}</h2>
			
			<table style="margin: 0; height: 0; padding: 0px;">
				<tr>
					<th style="width:40%;font-weight:600;padding: 5px 10px;">Характеристики</th>
					<th style="width:60%;font-weight:600;padding: 5px 10px;">Описание</th>
				</tr>

				@if($product->city)
				<tr>
					<td style="padding: 5px 10px;font-weight:500;">Населенный пункт</td>
					<td style="padding: 5px 10px;">{{ $product->city }}</td>
				</tr>
				@endif

				@if(isset($product->extras['distance']))
				<tr>
					<td style="padding: 5px 10px;font-weight:500;">Расстояние от Киева, км</td>
					<td style="padding: 5px 10px;">{{ $product->extras['distance'] }}</td>
				</tr>
				@endif

				@if($product->status_string)
				<!-- <tr>
					<td style="padding: 5px 10px;font-weight:500;">Состояние строительства</td>
					<td style="padding: 5px 10px;">{{ $product->status_string }}</td>
				</tr> -->
				@endif

				
				@php
					$projects = $product->modifications;
					
					$statuses_array = ['project' => null, 'building' => null, 'done' => null];
					$string = '';
					
					foreach($projects as $project) {
              foreach($project->amount as $key => $amount) {
                if($amount !== null)
                  $statuses_array[$key] += $amount;
              }
            }

					if($statuses_array['project'])
						$string .= $statuses_array['project'] . ' - проект; ';
						
					if($statuses_array['building'])
						$string .= $statuses_array['building'] . ' - строится; ';
						
					if($statuses_array['done'])
						$string .= $statuses_array['done'] . ' - построено; ';

					$string = $string? mb_substr($string, 0, mb_strlen($string) - 2) : '';
				@endphp

				@if($string)
				<tr>
					<td style="padding: 5px 10px;font-weight:500;">Состояние строительства</td>
					<td style="padding: 5px 10px;">{{ $string }}</td>
				</tr>
				@endif

				@if($product->brand)
				<tr>
					<td style="padding: 5px 10px;font-weight:500;">Застройщик</td>
					<td style="padding: 5px 10px;">{{ $product->brand? $product->brand->name : '' }}</td>
				</tr>
				@endif

				@if($product->description)
				<tr>
					<td style="padding: 5px 10px;font-weight:500;">Описание городка</td>
					<td style="padding: 5px 10px;">{!! $product->description !!}</td>
				</tr>
				@endif
				
				@if($product->area_m2 && $product->area_m2 != 0)
				<tr>
					<td style="padding: 5px 10px;font-weight:500;">Площадь застройки, га</td>
					<td style="padding: 5px 10px;">{{ $product->area_m2 }}</td>
				</tr>
				@endif

				@php
					$types = $product->modifications->where('is_default', 0)->groupBy('type_key');
          $string = '';

          foreach($types as $type => $projects) {
            $final_amount = 0;

            foreach($projects as $project) {
              foreach($project->amount as $key => $amount) {
                if($amount !== null)
                  $final_amount += $amount;
              }
            }

            $string .= __('plural.nominative.' . $type);

            if($final_amount)
              $string .= ' (' . $final_amount . ')';
              
            $string .= ', ';
          }

          $string = mb_substr($string, 0, mb_strlen($string) - 2);
				@endphp
				
				@if($string)
				<tr>
					<td style="padding: 5px 10px;font-weight:500;">Тип недвижимости</td>
					<td style="padding: 5px 10px;">{{ $string }}</td>
				</tr>
				@endif

				@if($product->total_items)
				<tr>
					<td style="padding: 5px 10px;font-weight:500;">Количество домовладений</td>
					<td style="padding: 5px 10px;">{{ $product->total_items }}</td>
				</tr>
				@endif

				@php
					$types = $product->modifications->where('is_default', 0)->groupBy('type_key');
					$string = '';

					foreach($types as $type => $projects) {
						$string .= __('plural.nominative.' . $type);

						if($projects->where('area', '!=', 0)->min('area') !== $projects->where('area', '!=', 0)->max('area'))
							$area = $projects->where('area', '!=', 0)->min('area') . '-' . $projects->where('area', '!=', 0)->max('area');
						else
							$area = $projects->where('area', '!=', 0)->min('area');

						if($area)
							$string .= ' (' . $area . ')';

						$string .= ', ';
					}

					$string = mb_substr($string, 0, mb_strlen($string) - 2);
				@endphp

				@if($string)
				<tr>
					<td style="padding: 5px 10px;font-weight:500;">Площадь домовладения, кв.м</td>
					<td style="padding: 5px 10px;">{{ $string }}</td>
				</tr>
				@endif

				@php
					$string = '';

					if(isset($product->extras['area_cottage']))
						$string .= 'Коттеджи (' . $product->extras['area_cottage'] . '), ';
						
					if(isset($product->extras['area_townhouse']))
						$string .= 'Таунхаусы (' . $product->extras['area_townhouse'] . '), ';
						
					if(isset($product->extras['area_duplex']))
						$string .= 'Дуплексы (' . $product->extras['area_duplex'] . '), ';
						
					if(isset($product->extras['area_quadrex']))
						$string .= 'Квадрексы (' . $product->extras['area_quadrex'] . '), ';

					$string = $string? mb_substr($string, 0, mb_strlen($string) - 2) : '';
				@endphp
				
				@if($string)
				<tr>
					<td style="padding: 5px 10px;font-weight:500;">Размер земельного участка, сот.</td>
					<td style="padding: 5px 10px;">{{ $string }}</td>
				</tr>
				@endif

				@if($product->price)
				<tr>
					<td style="padding: 5px 10px;font-weight:500;">Стоимость, грн / 1 кв.м</td>
					<td style="padding: 5px 10px;">{{ $product->price !== $product->max_price? $product->price . ' - ' . $product->max_price : $product->price }}</td>
				</tr>
				@endif

				@if($product->statistics_price_plot)
				<tr>
					<td style="padding: 5px 10px;font-weight:500;">Стоимость 1 сотки земли, грн</td>
					<td style="padding: 5px 10px;">{{ $product->statistics_price_plot !== $product->statistics_price_plot_max? $product->statistics_price_plot . ' - ' . $product->statistics_price_plot_max : $product->statistics_price_plot }}</td>
				</tr>
				@endif

				@if(isset($product->extras['wall_material']) && $product->extras['wall_material'])
				<tr>
					<td style="padding: 5px 10px;font-weight:500;">Материалы строительства</td>
					<td style="padding: 5px 10px;">{{ __('attributes.wall_materials')[$product->extras['wall_material']] }}</td>
				</tr>
				@endif

				@if($product->image)
				<tr>
					<td style="padding: 5px 10px;font-weight:500;">Изображение</td>
					<td style="padding: 5px 10px;">
						<a href="{{ url($product->image) }}" target="_blank">Ссылка</a>
					</td>
				</tr>
						@endif
			</table>
		</div>
		@endforeach
	</body>
</html>