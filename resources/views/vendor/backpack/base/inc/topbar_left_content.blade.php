<!-- This file is used to store topbar (left) items -->

<li class="nav-item px-3"><a class="nav-link" href="{{ backpack_url('feedback') }}"><i class='nav-icon las la-phone-volume'></i> Обратная связь 
		<span class="badge badge-{{ $new_feedbacks? 'warning': 'light' }}" style="position:initial">{{ $new_feedbacks }}</span></a>
</li>
<li class="nav-item px-3">
	<a class="nav-link" href="{{ backpack_url('review') }}">
		<i class='nav-icon las la-comment-dots'></i> Отзывы 
		<span class="badge badge-{{ $new_reviews? 'warning': 'light' }}" style="position:initial">{{ $new_reviews }}</span>
	</a>
</li>
<li class="nav-item px-3"><a class="nav-link" href="{{ backpack_url('subscription') }}"><i class='nav-icon la la-mail-bulk'></i> Подписки</a></li>

<li class='nav-item px-3'><a class='nav-link' href='{{ backpack_url('application') }}'><i class='nav-icon la la-file-alt'></i> Заявки</a></li>