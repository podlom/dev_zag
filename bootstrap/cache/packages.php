<?php return array (
  'aimix/account' => 
  array (
    'providers' => 
    array (
      0 => 'Aimix\\Account\\ServiceProvider',
    ),
  ),
  'aimix/aimix' => 
  array (
    'branch-alias' => 
    array (
      'dev-master' => '1.0',
    ),
    'providers' => 
    array (
      0 => 'Aimix\\aimix\\ServiceProvider',
    ),
  ),
  'aimix/banner' => 
  array (
    'providers' => 
    array (
      0 => 'Aimix\\Banner\\ServiceProvider',
    ),
  ),
  'aimix/currency' => 
  array (
    'providers' => 
    array (
      0 => 'Aimix\\Currency\\ServiceProvider',
    ),
  ),
  'aimix/feedback' => 
  array (
    'providers' => 
    array (
      0 => 'Aimix\\Feedback\\ServiceProvider',
    ),
    'aliases' => 
    array (
      'Feedback' => 'Aimix\\Feedback\\Facades\\Feedback',
    ),
  ),
  'aimix/gallery' => 
  array (
    'providers' => 
    array (
      0 => 'Aimix\\Gallery\\ServiceProvider',
    ),
  ),
  'aimix/promotion' => 
  array (
    'providers' => 
    array (
      0 => 'Aimix\\Promotion\\ServiceProvider',
    ),
  ),
  'aimix/review' => 
  array (
    'providers' => 
    array (
      0 => 'Aimix\\Review\\ServiceProvider',
    ),
  ),
  'aimix/shop' => 
  array (
    'providers' => 
    array (
      0 => 'Aimix\\Shop\\ServiceProvider',
    ),
  ),
  'alexpechkarev/google-maps' => 
  array (
    'providers' => 
    array (
      0 => 'GoogleMaps\\ServiceProvider\\GoogleMapsServiceProvider',
    ),
    'aliases' => 
    array (
      'GoogleMaps' => 'GoogleMaps\\Facade\\GoogleMapsFacade',
    ),
  ),
  'backpack/backupmanager' => 
  array (
    'providers' => 
    array (
      0 => 'Backpack\\BackupManager\\BackupManagerServiceProvider',
    ),
  ),
  'backpack/crud' => 
  array (
    'providers' => 
    array (
      0 => 'Backpack\\CRUD\\BackpackServiceProvider',
    ),
    'aliases' => 
    array (
      'CRUD' => 'Backpack\\CRUD\\app\\Library\\CrudPanel\\CrudPanelFacade',
    ),
  ),
  'backpack/generators' => 
  array (
    'providers' => 
    array (
      0 => 'Backpack\\Generators\\GeneratorsServiceProvider',
    ),
  ),
  'backpack/langfilemanager' => 
  array (
    'providers' => 
    array (
      0 => 'Backpack\\LangFileManager\\LangFileManagerServiceProvider',
    ),
  ),
  'backpack/logmanager' => 
  array (
    'providers' => 
    array (
      0 => 'Backpack\\LogManager\\LogManagerServiceProvider',
    ),
  ),
  'backpack/menucrud' => 
  array (
    'providers' => 
    array (
      0 => 'Backpack\\MenuCRUD\\MenuCRUDServiceProvider',
    ),
  ),
  'backpack/newscrud' => 
  array (
    'providers' => 
    array (
      0 => 'Backpack\\NewsCRUD\\NewsCRUDServiceProvider',
    ),
  ),
  'backpack/pagemanager' => 
  array (
    'providers' => 
    array (
      0 => 'Backpack\\PageManager\\PageManagerServiceProvider',
    ),
  ),
  'backpack/permissionmanager' => 
  array (
    'providers' => 
    array (
      0 => 'Backpack\\PermissionManager\\PermissionManagerServiceProvider',
    ),
  ),
  'backpack/settings' => 
  array (
    'providers' => 
    array (
      0 => 'Backpack\\Settings\\SettingsServiceProvider',
    ),
  ),
  'barryvdh/laravel-elfinder' => 
  array (
    'providers' => 
    array (
      0 => 'Barryvdh\\Elfinder\\ElfinderServiceProvider',
    ),
  ),
  'creativeorange/gravatar' => 
  array (
    'providers' => 
    array (
      0 => 'Creativeorange\\Gravatar\\GravatarServiceProvider',
    ),
    'aliases' => 
    array (
      'Gravatar' => 'Creativeorange\\Gravatar\\Facades\\Gravatar',
    ),
  ),
  'cviebrock/eloquent-sluggable' => 
  array (
    'providers' => 
    array (
      0 => 'Cviebrock\\EloquentSluggable\\ServiceProvider',
    ),
  ),
  'davejamesmiller/laravel-breadcrumbs' => 
  array (
    'providers' => 
    array (
      0 => 'DaveJamesMiller\\Breadcrumbs\\BreadcrumbsServiceProvider',
    ),
    'aliases' => 
    array (
      'Breadcrumbs' => 'DaveJamesMiller\\Breadcrumbs\\Facades\\Breadcrumbs',
    ),
  ),
  'facade/ignition' => 
  array (
    'providers' => 
    array (
      0 => 'Facade\\Ignition\\IgnitionServiceProvider',
    ),
    'aliases' => 
    array (
      'Flare' => 'Facade\\Ignition\\Facades\\Flare',
    ),
  ),
  'fideloper/proxy' => 
  array (
    'providers' => 
    array (
      0 => 'Fideloper\\Proxy\\TrustedProxyServiceProvider',
    ),
  ),
  'fruitcake/laravel-cors' => 
  array (
    'providers' => 
    array (
      0 => 'Fruitcake\\Cors\\CorsServiceProvider',
    ),
  ),
  'hisorange/browser-detect' => 
  array (
    'providers' => 
    array (
      0 => 'hisorange\\BrowserDetect\\ServiceProvider',
    ),
    'aliases' => 
    array (
      'Browser' => 'hisorange\\BrowserDetect\\Facade',
    ),
  ),
  'intervention/image' => 
  array (
    'providers' => 
    array (
      0 => 'Intervention\\Image\\ImageServiceProvider',
    ),
    'aliases' => 
    array (
      'Image' => 'Intervention\\Image\\Facades\\Image',
    ),
  ),
  'laracasts/generators' => 
  array (
    'providers' => 
    array (
      0 => 'Laracasts\\Generators\\GeneratorsServiceProvider',
    ),
  ),
  'laravel-notification-channels/telegram' => 
  array (
    'providers' => 
    array (
      0 => 'NotificationChannels\\Telegram\\TelegramServiceProvider',
    ),
  ),
  'laravel/scout' => 
  array (
    'providers' => 
    array (
      0 => 'Laravel\\Scout\\ScoutServiceProvider',
    ),
  ),
  'laravel/tinker' => 
  array (
    'providers' => 
    array (
      0 => 'Laravel\\Tinker\\TinkerServiceProvider',
    ),
  ),
  'laravel/ui' => 
  array (
    'providers' => 
    array (
      0 => 'Laravel\\Ui\\UiServiceProvider',
    ),
  ),
  'maatwebsite/excel' => 
  array (
    'providers' => 
    array (
      0 => 'Maatwebsite\\Excel\\ExcelServiceProvider',
    ),
    'aliases' => 
    array (
      'Excel' => 'Maatwebsite\\Excel\\Facades\\Excel',
    ),
  ),
  'nesbot/carbon' => 
  array (
    'providers' => 
    array (
      0 => 'Carbon\\Laravel\\ServiceProvider',
    ),
  ),
  'nunomaduro/collision' => 
  array (
    'providers' => 
    array (
      0 => 'NunoMaduro\\Collision\\Adapters\\Laravel\\CollisionServiceProvider',
    ),
  ),
  'prologue/alerts' => 
  array (
    'providers' => 
    array (
      0 => 'Prologue\\Alerts\\AlertsServiceProvider',
    ),
    'aliases' => 
    array (
      'Alert' => 'Prologue\\Alerts\\Facades\\Alert',
    ),
  ),
  'spatie/geocoder' => 
  array (
    'providers' => 
    array (
      0 => 'Spatie\\Geocoder\\GeocoderServiceProvider',
    ),
    'aliases' => 
    array (
      'Geocoder' => 'Spatie\\Geocoder\\Facades\\Geocoder',
    ),
  ),
  'spatie/laravel-backup' => 
  array (
    'providers' => 
    array (
      0 => 'Spatie\\Backup\\BackupServiceProvider',
    ),
  ),
  'spatie/laravel-feed' => 
  array (
    'providers' => 
    array (
      0 => 'Spatie\\Feed\\FeedServiceProvider',
    ),
  ),
  'spatie/laravel-glide' => 
  array (
    'providers' => 
    array (
      0 => 'Spatie\\Glide\\GlideServiceProvider',
    ),
    'aliases' => 
    array (
      'GlideImage' => 'Spatie\\Glide\\GlideImageFacade',
    ),
  ),
  'spatie/laravel-permission' => 
  array (
    'providers' => 
    array (
      0 => 'Spatie\\Permission\\PermissionServiceProvider',
    ),
  ),
  'spatie/laravel-sitemap' => 
  array (
    'providers' => 
    array (
      0 => 'Spatie\\Sitemap\\SitemapServiceProvider',
    ),
  ),
);