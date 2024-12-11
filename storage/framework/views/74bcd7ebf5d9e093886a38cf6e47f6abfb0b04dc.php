<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('dashboard')); ?>"><i class="la la-home nav-icon"></i> Панель Управления</a></li>
<li class='nav-item'><a class='nav-link' href='<?php echo e(url('elfinder')); ?>'><i class='nav-icon las la-file'></i> Файловый менеджер</a></li>

<li class="nav-title">Основное</li>
<li class="nav-item nav-dropdown">
	<a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon las la-store"></i> Объекты</a>
	<ul class="nav-dropdown-items">
    <li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('product?category_id=1')); ?>'><i class='nav-icon las la-home'></i> Городки</a></li>
	  <li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('product?category_id=2')); ?>'><i class='nav-icon las la-building'></i> Новостройки</a></li>
	  <li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('prod_category')); ?>'><i class='nav-icon las la-stream'></i> Категории</a></li>
	  <li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('attribute')); ?>'><i class='nav-icon la la-tags'></i> Атрибуты</a></li>
    <li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('meta')); ?>'><i class='nav-icon la la-file-alt'></i> СЕО</a></li>
	</ul>
</li>
<li class="nav-item nav-dropdown">
	<a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon las la-industry"></i> Компании</a>
	<ul class="nav-dropdown-items">
    <li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('brand')); ?>'><i class='nav-icon las la-warehouse'></i> Список</a></li>
    <li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('brandcategory')); ?>'><i class='nav-icon las la-stream'></i> Категории</a></li>
	</ul>
</li>
<li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('promotion')); ?>'><i class='nav-icon las la-percent'></i> Акции</a></li>




<li class="nav-title">Контент</li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-newspaper-o"></i>Журнал</a>
    <ul class="nav-dropdown-items">
      <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('newsarticle')); ?>"><i class="nav-icon la la-newspaper-o"></i> Новости</a></li>
      <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('article')); ?>"><i class="nav-icon la la-newspaper-o"></i> Статьи</a></li>
      <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('buildingarticle')); ?>"><i class="nav-icon la la-newspaper-o"></i> Строительство</a></li>
      <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('analiticsarticle')); ?>"><i class="nav-icon la la-newspaper-o"></i> Аналитика</a></li>
      <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('category')); ?>"><i class="nav-icon la la-list"></i> Темы</a></li>
      <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('tag')); ?>"><i class="nav-icon la la-tag"></i> Метки</a></li>
      <li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('review?type=article')); ?>'><i class='nav-icon las la-comment-dots'></i> Комментарии <span>(<?php echo e($new_comments); ?>)</span></a></li>
    </ul>
</li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-pencil-ruler"></i>Аналитика</a>
    <ul class="nav-dropdown-items">
      <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('statisticsarticle')); ?>"><i class="nav-icon la la-table"></i> Статистика</a></li>
      <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('statisticsarticlecategory')); ?>"><i class="nav-icon la la-list"></i> Темы</a></li>
    </ul>
</li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-map"></i>Регионы Украины</a>
    <ul class="nav-dropdown-items">
      <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('regionarticle')); ?>"><i class="nav-icon la la-th-list"></i> Записи</a></li>
      <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('regionarticlecategory')); ?>"><i class="nav-icon la la-list"></i> Темы</a></li>
    </ul>
</li>

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-leaf"></i>Экология</a>
    <ul class="nav-dropdown-items">
      <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('ecologyarticle')); ?>"><i class="nav-icon la la-th-list"></i> Записи</a></li>
      <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('ecologyarticlecategory')); ?>"><i class="nav-icon la la-list"></i> Темы</a></li>
    </ul>
</li>

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-info"></i>Информация</a>
    <ul class="nav-dropdown-items">
      <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('informationarticle')); ?>"><i class="nav-icon la la-th-list"></i> Записи</a></li>
      <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('informationarticlecategory')); ?>"><i class="nav-icon la la-list"></i> Темы</a></li>
    </ul>
</li>

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-calendar"></i>Мероприятия</a>
    <ul class="nav-dropdown-items">
      <li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('exhibitionarticle')); ?>'><i class='nav-icon la la-photo-video'></i> Выставки</a></li>
      <li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('seminararticle')); ?>'><i class='nav-icon la la-chalkboard-teacher'></i> Семинары</a></li>
      <li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('conferencearticle')); ?>'><i class='nav-icon la la-users'></i> Конференции</a></li>
      <li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('contestarticle')); ?>'><i class='nav-icon la la-award'></i> Конкурсы</a></li>
      <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('eventarticlecategory')); ?>"><i class="nav-icon la la-list"></i> Темы</a></li>
    </ul>
</li>

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-briefcase"></i>Бизнесу</a>
    <ul class="nav-dropdown-items">
      <li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('landsarticle')); ?>'><i class='nav-icon la la-map-marker'></i> Земля Украины</a></li>
      <li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('contractsarticle')); ?>'><i class='nav-icon la la-file-contract'></i> Виды договоров</a></li>
      <li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('classificationarticle')); ?>'><i class='nav-icon la la-folder-open'></i> Классификация</a></li>
      <li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('evaluationarticle')); ?>'><i class='nav-icon la la-comment-dollar'></i> Оценка</a></li>
      <li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('associationsarticle')); ?>'><i class='nav-icon la la-users'></i> Объединения</a></li>
      <li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('businesscategory')); ?>'><i class='nav-icon la la-list'></i> Темы</a></li>
    </ul>
</li>

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-tools"></i>Услуги</a>
    <ul class="nav-dropdown-items">
      <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('servicesarticle')); ?>"><i class="nav-icon la la-th-list"></i> Записи</a></li>
      <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('servicesarticlecategory')); ?>"><i class="nav-icon la la-list"></i> Темы</a></li>
    </ul>
</li>

<li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('statistics')); ?>'><i class='nav-icon la la-table'></i> Статистика цен</a></li>

<li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('servisyarticlecategory')); ?>'><i class='nav-icon la la-pencil-ruler'></i> Сервисы</a></li>

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-poll-h"></i>Опросы</a>
    <ul class="nav-dropdown-items">
      <li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('pollquestion')); ?>'><i class='nav-icon la la-question'></i> Вопросы</a></li>
      <li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('polloption')); ?>'><i class='nav-icon la la-stream'></i> Варианты</a></li>
    </ul>
</li>

<li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('page')); ?>'><i class='nav-icon la la-file-o'></i> <span>Страницы</span></a></li>
<li class="nav-item nav-dropdown">
	<a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-question-circle"></i> FAQ</a>
	<ul class="nav-dropdown-items">
  <li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('faq')); ?>'><i class='nav-icon la la-question'></i> Вопросы</a></li>
  <li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('faqcategory')); ?>'><i class='nav-icon la la-stream'></i> Категории</a></li>
	</ul>
</li>
<li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('term')); ?>'><i class='nav-icon la la-book'></i> Словарь терминов</a></li>
<li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('research')); ?>'><i class='nav-icon la la-flask'></i> Исследования <span>(<?php echo e($new_researches); ?>)</span></a></li>
<li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('banner')); ?>'><i class='nav-icon las la-image'></i> Баннеры</a></li>


<li class="nav-title">Управление</li>
<li class="nav-item nav-dropdown">
  <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-map"></i> Регионы</a>
  <ul class="nav-dropdown-items">
    <li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('region')); ?>'><i class='nav-icon la la-map-marker'></i> Области</a></li>
    <li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('area')); ?>'><i class='nav-icon la la-map-pin'></i> Районы</a></li>
    <li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('city')); ?>'><i class='nav-icon la la-map-signs'></i> Нас. пункты</a></li>
    <li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('kyivdistrict')); ?>'><i class='nav-icon la la-map-pin'></i> Районы Киева</a></li>
  </ul>
</li>

<li class="nav-item nav-dropdown">
  <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-globe"></i> Переводы</a>
  <ul class="nav-dropdown-items">
    <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('language')); ?>"><i class="nav-icon la la-flag-checkered"></i> Языки</a></li>
    <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('language/texts')); ?>"><i class="nav-icon la la-language"></i> Тексты</a></li>
  </ul>
</li>
<!-- Users, Roles, Permissions -->
<li class="nav-item nav-dropdown">
	<a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-group"></i> Пользователи</a>
	<ul class="nav-dropdown-items">
	  <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('user')); ?>"><i class="nav-icon la la-user"></i> <span>Пользователи</span></a></li>
		<!-- <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('usermeta')); ?>"><i class="nav-icon la la-user"></i> <span>Данные</span></a></li> -->
	  <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('role')); ?>"><i class="nav-icon la la-group"></i> <span>Роли</span></a></li>
	  <!-- <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('permission')); ?>"><i class="nav-icon la la-key"></i> <span>Permissions</span></a></li> -->
	</ul>
</li>

<li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('menu-item')); ?>'><i class='nav-icon la la-list'></i> <span>Меню</span></a></li>
<li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('setting')); ?>'><i class='nav-icon la la-cog'></i> Настройки</a></li>
<li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('backup')); ?>'><i class='nav-icon la la-hdd-o'></i> Резервное коп.</a></li>
<li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('log')); ?>'><i class='nav-icon la la-terminal'></i> Логи</a></li>


<li class='nav-item'><a class='nav-link' href='<?php echo e(backpack_url('parser')); ?>'><i class='nav-icon la la-search'></i> Парсер</a></li>
<!-- <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('gallery')); ?>"><i class="nav-icon la la-camera"></i> Галереи</a></li> --><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/vendor/backpack/base/inc/sidebar_content.blade.php ENDPATH**/ ?>