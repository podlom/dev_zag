<?php

use Illuminate\Support\Facades\Route;
use App\City;
use Aimix\Shop\app\Models\Product;
use Aimix\Shop\app\Models\Category;
use Aimix\Shop\app\Models\BrandCategory;
use Aimix\Shop\app\Models\Brand;
// use Aimix\Review\app\Models\Review;
// use Aimix\Gallery\app\Models\Gallery;
// use Aimix\Promotion\app\Models\Promotion;
use Illuminate\Http\Request;
use Backpack\PageManager\app\Models\Page;
use Backpack\NewsCRUD\app\Models\Article;
//use Illuminate\Database\Eloquent\Builder;
use App\Models\Term;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('docs-generate/{id}', '\App\Http\Controllers\CatalogController@generateDocx');

// @ts also check redirects in public/.htaccess
//
// Route::redirect('/ru', '/', 301);
// Route::redirect('ru', '/', 301);
// Route::redirect('rus', '/', 301);
//
// added by @ts 2024-07-10 Task #35 make redirects
// Route::redirect('/ru/stroitelstvo/.html', '/ru/stroitelstvo', 301);
// Route::redirect('/uk/statti/.html', '/uk/statti', 301);
// Route::redirect('/uk/konkursi/konkursi-z-zhitlovoi-ta-zamiskoi-neruhomosti2016/.html', '/uk/konkursi/konkursi-z-zhitlovoi-ta-zamiskoi-neruhomosti2016', 301);
// Route::redirect('/uk/analitika/.html', '/uk/analitika', 301);
// Route::redirect('/uk/novini/.html', '/uk/novini', 301);
//

/*
Route::get('/filldist', function(){

	$dists = array(
		//0 => array('Киевская' => 'Барышевский', 'км' => ''),
		1 => array('Киевская' => 'Барышевка', 'км' => '46'),
		//2 => array('Киевская' => 'Белоцерковский', 'км' => ''),
		3 => array('Киевская' => 'Белая Церковь', 'км' => '71'),
		4 => array('Киевская' => 'Глушки', 'км' => '65'),
		5 => array('Киевская' => 'Фурсы', 'км' => '82'),
		6 => array('Киевская' => 'Шкаровка', 'км' => '87'),
		//7 => array('Киевская' => 'Бориспольский', 'км' => ''),
		8 => array('Киевская' => 'Безугловка', 'км' => '6'),
		9 => array('Киевская' => 'Борисполь', 'км' => '17'),
		10 => array('Киевская' => 'Великая Александровка', 'км' => '10'),
		11 => array('Киевская' => 'Вишенки', 'км' => '20'),
		12 => array('Киевская' => 'Гнедин', 'км' => '13'),
		13 => array('Киевская' => 'Гора', 'км' => '8'),
		14 => array('Киевская' => 'Жеребятин', 'км' => '30'),
		15 => array('Киевская' => 'Займище', 'км' => '23'),
		16 => array('Киевская' => 'Иванков', 'км' => '25'),
		17 => array('Киевская' => 'Кийлов', 'км' => '40'),
		18 => array('Киевская' => 'Малая Александровка', 'км' => '10'),
		19 => array('Киевская' => 'Петропавловское', 'км' => '15'),
		20 => array('Киевская' => 'Процев', 'км' => '25'),
		21 => array('Киевская' => 'Сеньковка', 'км' => '27'),
		22 => array('Киевская' => 'Чубинское', 'км' => '8'),
		23 => array('Киевская' => 'Счастливое', 'км' => '5'),
		24 => array('Киевская' => 'Бородянский', 'км' => ''),
		25 => array('Киевская' => 'Блиставица', 'км' => '14'),
		26 => array('Киевская' => 'Клавдиево-Тарасово', 'км' => '26'),
		27 => array('Киевская' => 'Козинцы', 'км' => '24'),
		28 => array('Киевская' => 'Лубянка', 'км' => '20'),
		29 => array('Киевская' => 'Микуличи', 'км' => '19'),
		30 => array('Киевская' => 'Немешаево', 'км' => '18'),
		31 => array('Киевская' => 'Новая Буда', 'км' => '45'),
		32 => array('Киевская' => 'Озера', 'км' => '14'),
		33 => array('Киевская' => 'Пилиповичи', 'км' => '29'),
		34 => array('Киевская' => 'Броварской', 'км' => ''),
		35 => array('Киевская' => 'Богдановка', 'км' => '23'),
		36 => array('Киевская' => 'Бровары', 'км' => '6'),
		37 => array('Киевская' => 'Великая Дымерка', 'км' => '21'),
		38 => array('Киевская' => 'Зазимье', 'км' => '8'),
		39 => array('Киевская' => 'Княжичи', 'км' => '7'),
		40 => array('Киевская' => 'Красиловка', 'км' => '23'),
		41 => array('Киевская' => 'Летки', 'км' => '35'),
		42 => array('Киевская' => 'Перемога', 'км' => '12'),
		43 => array('Киевская' => 'Погребы', 'км' => '6'),
		44 => array('Киевская' => 'Пуховка', 'км' => '8'),
		45 => array('Киевская' => 'Рожевка', 'км' => '13'),
		46 => array('Киевская' => 'Рожны', 'км' => '16'),
		47 => array('Киевская' => 'Скибин', 'км' => '13'),
		48 => array('Киевская' => 'Требухов', 'км' => '17'),
		//49 => array('Киевская' => 'Васильковский', 'км' => ''),
		50 => array('Киевская' => 'Барахты', 'км' => '33'),
		51 => array('Киевская' => 'Великая Бугаевка', 'км' => '31'),
		52 => array('Киевская' => 'Деревянки', 'км' => '25'),
		53 => array('Киевская' => 'Застугна', 'км' => '29'),
		54 => array('Киевская' => 'Здоровка', 'км' => '25'),
		55 => array('Киевская' => 'Зеленый Бор', 'км' => '18'),
		56 => array('Киевская' => 'Иванковичи', 'км' => '15'),
		57 => array('Киевская' => 'Калиновка', 'км' => '23'),
		58 => array('Киевская' => 'Крушинка', 'км' => '20'),
		59 => array('Киевская' => 'Крячки', 'км' => '23'),
		60 => array('Киевская' => 'Мархалевка', 'км' => '13'),
		61 => array('Киевская' => 'Путровка', 'км' => '25'),
		62 => array('Киевская' => 'Рославичи', 'км' => '18'),
		63 => array('Киевская' => 'Руликов', 'км' => '37'),
		//64 => array('Киевская' => 'Вышгородский', 'км' => ''),
		65 => array('Киевская' => 'Вышгород', 'км' => '3'),
		66 => array('Киевская' => 'Воропаев', 'км' => '32'),
		67 => array('Киевская' => 'Глебовка', 'км' => '31'),
		68 => array('Киевская' => 'Демидов', 'км' => '20'),
		69 => array('Киевская' => 'Дымер', 'км' => '27'),
		70 => array('Киевская' => 'Жукин', 'км' => '38'),
		71 => array('Киевская' => 'Козаровичи', 'км' => '26'),
		72 => array('Киевская' => 'Лебедевка', 'км' => '24'),
		73 => array('Киевская' => 'Литвиновка', 'км' => '28'),
		74 => array('Киевская' => 'Лютеж', 'км' => '13'),
		75 => array('Киевская' => 'Низшая Дубечня', 'км' => '25'),
		76 => array('Киевская' => 'Новые Петровцы', 'км' => '5'),
		77 => array('Киевская' => 'Новоселки', 'км' => '18'),
		78 => array('Киевская' => 'Осещина', 'км' => '10'),
		79 => array('Киевская' => 'Ровжи', 'км' => '43'),
		80 => array('Киевская' => 'Синяк', 'км' => '26'),
		81 => array('Киевская' => 'Старые Петровцы', 'км' => '8'),
		82 => array('Киевская' => 'Хотяновка', 'км' => '13'),
		83 => array('Киевская' => 'Ясногородка', 'км' => '40'),
		//84 => array('Киевская' => 'Киево-Святошинский', 'км' => ''),
		85 => array('Киевская' => 'Белогородка', 'км' => '9'),
		86 => array('Киевская' => 'Бобрица', 'км' => '17'),
		87 => array('Киевская' => 'Боярка', 'км' => '22'),
		88 => array('Киевская' => 'Бузовая', 'км' => '19'),
		89 => array('Киевская' => 'Буча Лесная', 'км' => '10'),
		90 => array('Киевская' => 'Вишневое', 'км' => '4'),
		91 => array('Киевская' => 'Вита-Почтовая', 'км' => '10'),
		92 => array('Киевская' => 'Ворзель', 'км' => '13'),
		93 => array('Киевская' => 'Гатное', 'км' => '4'),
		94 => array('Киевская' => 'Горбовичи', 'км' => '18'),
		95 => array('Киевская' => 'Гореничи', 'км' => '5'),
		96 => array('Киевская' => 'Горенка', 'км' => '3'),
		97 => array('Киевская' => 'Гостомель', 'км' => '10'),
		98 => array('Киевская' => 'Гуровщина', 'км' => '12'),
		99 => array('Киевская' => 'Дмитровка', 'км' => '7.5'),
		100 => array('Киевская' => 'Забучье', 'км' => '11'),
		101 => array('Киевская' => 'Капитановка', 'км' => '6.5'),
		102 => array('Киевская' => 'Кременище', 'км' => '21'),
		103 => array('Киевская' => 'Круглик', 'км' => '9'),
		104 => array('Киевская' => 'Крюковщина', 'км' => '5.5'),
		105 => array('Киевская' => 'Лесное', 'км' => '13'),
		106 => array('Киевская' => 'Лесники', 'км' => '4'),
		107 => array('Киевская' => 'Лука', 'км' => '17'),
		108 => array('Киевская' => 'Малютянка', 'км' => '18'),
		109 => array('Киевская' => 'Милая', 'км' => '5'),
		110 => array('Киевская' => 'Мироцкое', 'км' => '17'),
		111 => array('Киевская' => 'Михайловка-Рубежовка', 'км' => '15'),
		112 => array('Киевская' => 'Мощун', 'км' => '4'),
		113 => array('Киевская' => 'Музычи', 'км' => '23'),
		114 => array('Киевская' => 'Новое', 'км' => '13'),
		115 => array('Киевская' => 'Святопетровское', 'км' => '6'),
		116 => array('Киевская' => 'Петропавловская Борщаговка', 'км' => '2.3'),
		117 => array('Киевская' => 'Петрушки', 'км' => '8'),
		118 => array('Киевская' => 'Софиевская Борщаговка', 'км' => '2'),
		119 => array('Киевская' => 'Стоянка', 'км' => '2.4'),
		120 => array('Киевская' => 'Тарасовка', 'км' => '10'),
		121 => array('Киевская' => 'Хмельная', 'км' => '28'),
		122 => array('Киевская' => 'Ходосовка', 'км' => '7.5'),
		123 => array('Киевская' => 'Хотов', 'км' => '6'),
		124 => array('Киевская' => 'Чабаны', 'км' => '3'),
		125 => array('Киевская' => 'Чайки', 'км' => '7.5'),
		126 => array('Киевская' => 'Шевченково', 'км' => '7'),
		127 => array('Киевская' => 'Шпитьки', 'км' => '11'),
		128 => array('Киевская' => 'Юровка', 'км' => '11'),
		//129 => array('Киевская' => 'Макаровский', 'км' => ''),
		130 => array('Киевская' => 'Березовка', 'км' => '17'),
		131 => array('Киевская' => 'Вышеград', 'км' => '25'),
		132 => array('Киевская' => 'Витривка', 'км' => '45'),
		133 => array('Киевская' => 'Гавронщина', 'км' => '30'),
		134 => array('Киевская' => 'Калиновка', 'км' => '31'),
		135 => array('Киевская' => 'Колонщина', 'км' => '22'),
		136 => array('Киевская' => 'Копылов', 'км' => '27.6'),
		137 => array('Киевская' => 'Лишня', 'км' => '32'),
		138 => array('Киевская' => 'Людвиновка', 'км' => '39'),
		139 => array('Киевская' => 'Маковище', 'км' => '28.5'),
		140 => array('Киевская' => 'Марьяновка', 'км' => '26'),
		141 => array('Киевская' => 'Мотыжин', 'км' => '31'),
		142 => array('Киевская' => 'Осыково', 'км' => '28'),
		143 => array('Киевская' => 'Плахтянка', 'км' => '37.4'),
		144 => array('Киевская' => 'Севериновка', 'км' => '23.2'),
		145 => array('Киевская' => 'Ферма', 'км' => '42.4'),
		146 => array('Киевская' => 'Червоная Слобода', 'км' => '43.5'),
		147 => array('Киевская' => 'Черногородка', 'км' => '39'),
		148 => array('Киевская' => 'Яблоновка', 'км' => '33'),
		149 => array('Киевская' => 'Ясногородка', 'км' => '26'),
		//150 => array('Киевская' => 'Обуховский', 'км' => ''),
		151 => array('Киевская' => 'Великие Дмитровичи', 'км' => '16'),
		152 => array('Киевская' => 'Деревянная', 'км' => '31'),
		153 => array('Киевская' => 'Козин', 'км' => '10'),
		154 => array('Киевская' => 'Креничи', 'км' => '11'),
		155 => array('Киевская' => 'Новые Безрадичи', 'км' => '21'),
		156 => array('Киевская' => 'Обухов', 'км' => '29'),
		157 => array('Киевская' => 'Перше Травня', 'км' => '30'),
		158 => array('Киевская' => 'Подгорцы', 'км' => '10'),
		159 => array('Киевская' => 'Плюты', 'км' => '21'),
		160 => array('Киевская' => 'Романков', 'км' => '14'),
		161 => array('Киевская' => 'Старые Безрадичи', 'км' => '26'),
		162 => array('Киевская' => 'Рудыки', 'км' => '15'),
		163 => array('Киевская' => 'Ирпень', 'км' => ''),
		164 => array('Киевская' => 'Ирпень', 'км' => '5'),
		165 => array('Киевская' => 'Буча', 'км' => ''),
		166 => array('Киевская' => 'Буча', 'км' => '6'),
	);

	foreach($dists as $key => $dist){

		$city = City::where('name', 'like', '%' .$dist['Киевская'] . '%')->first();

		if($city && $city->products) {
			foreach($city->products as $product){
				if((int)$product->extras['distance'] == 0 && $dist['км'] != ''){

					$extras = $product->extras;
					$extras['distance'] = $dist['км'];

					$product->extras = $extras;
					$product->save();
				}
			}
		}
	}

});
*/



Route::get('/statistics.php', '\App\Http\Controllers\TableController@index');
Route::any('/statistics-complex.php', '\App\Http\Controllers\TableController@complex');
Route::any('/statistics-complex-no-links.php', '\App\Http\Controllers\TableController@complex_no_links');
Route::any('/statistics-cottages.php', '\App\Http\Controllers\TableController@cottages');
Route::any('/statistics-cottages-no-links.php', '\App\Http\Controllers\TableController@cottages_no_links');
Route::any('/statistics-cottages-area.php', '\App\Http\Controllers\TableController@cottages_area');
Route::any('/statistics-cottages-area-no-links.php', '\App\Http\Controllers\TableController@cottages_area_no_links');
Route::any('/statistics-complex-number.php', '\App\Http\Controllers\TableController@complex_number');
Route::any('/statistics-cottages-number.php', '\App\Http\Controllers\TableController@cottages_number');

Route::post('/statistics-complex.php/generate', '\App\Http\Controllers\TableController@generate_table');
Route::post('/statistics-complex-no-links.php/generate', '\App\Http\Controllers\TableController@generate_table');
Route::post('/statistics-cottages.php/generate', '\App\Http\Controllers\TableController@generate_table');
Route::post('/statistics-cottages-no-links.php/generate', '\App\Http\Controllers\TableController@generate_table');
Route::post('/statistics-cottages-area.php/generate', '\App\Http\Controllers\TableController@generate_table');
Route::post('/statistics-cottages-area-no-links.php/generate', '\App\Http\Controllers\TableController@generate_table');
Route::post('/statistics-complex-number.php/generate', '\App\Http\Controllers\TableController@generate_table_number');
Route::post('/statistics-cottages-number.php/generate', '\App\Http\Controllers\TableController@generate_table_number');

Route::get('/image/{path}', 'ImageController@imagePath')->where('path', '.*');
Route::get('/glide/{path}', 'ImageController@filePath')->where('path', '.*');
Route::get('/common/{path}', 'ImageController@commonPath')->where('path', '.*');

Route::prefix('/export')->group(function() {
  Route::get('/', '\App\Http\Controllers\ExportController@common');
  Route::get('/cottages', '\App\Http\Controllers\ExportController@cottages');
  Route::get('/newbuilds', '\App\Http\Controllers\ExportController@newbuilds');
  Route::get('/news', '\App\Http\Controllers\ExportController@news');
  Route::get('/themes', '\App\Http\Controllers\ExportController@themes');
  Route::get('/reviews', '\App\Http\Controllers\ExportController@reviews');
  Route::get('/statistics', '\App\Http\Controllers\ExportController@statistics');
  Route::get('/products', '\App\Http\Controllers\ExportController@products');
  Route::get('/tags', '\App\Http\Controllers\ExportController@tags');


/*
  Route::get('/images', function(){
//63, 64, 59, 57, 114, 54
//39, 103, 104, 102, 101, 111, 100, 105
//89, 76, 96, 86, 88, 78, 80, 99, 109, 90, 113, 91, 83, 92, 93, 94, 77, 79, 95, 112, 82, 75, 81
//38, 42, 65, 51, 66, 73, 37, 56, 67, 52, 61, 43, 44, 41, 50, 62, 106, 40, 48, 71, 68, 110, 108, 69, 72, 46, 70, 45, 49, 74

	  $articles = Article::whereHas('category', function($q){
		  $q->where('id', 74);
	  })->update(['image' => '/files/journal/by_categories/acticles/stat_zhilye.jpeg']);
  });
*/

});

Route::post('/getReviews', '\App\Http\Controllers\HomeController@getReviews');
Route::post('/getCities', '\App\Http\Controllers\HomeController@getCities');
Route::post('/getAreas', '\App\Http\Controllers\HomeController@getAreas');
Route::post('/getSelection', '\App\Http\Controllers\HomeController@getSelection');
Route::post('/getNearest', '\App\Http\Controllers\CatalogController@getNearestProducts');
Route::post('/getPrices', '\App\Http\Controllers\CatalogController@getPrices');

Route::post('/addArticleView', '\App\Http\Controllers\NewsController@addArticleView');

Route::post('/pollAnswer', '\App\Http\Controllers\PollController@process');

// Reviews
Route::post('/ru/reviews/{type?}', '\Aimix\Review\app\Http\Controllers\ReviewController@index');
Route::post('/uk/reviews/{type?}', '\Aimix\Review\app\Http\Controllers\ReviewController@index');
Route::prefix('/reviews')->group(function() {
  Route::post('/create/{type?}', '\Aimix\Review\app\Http\Controllers\ReviewController@create');
  Route::post('/requestSearchList/{value}', '\Aimix\Review\app\Http\Controllers\ReviewController@requestSearchList')->name('requestSearchList');
});

Route::post('/ru/otzyvy/{type?}', '\Aimix\Review\app\Http\Controllers\ReviewController@index');
Route::post('/uk/vidguki/{type?}', '\Aimix\Review\app\Http\Controllers\ReviewController@index');

// Researches
Route::post('/research/create', '\App\Http\Controllers\Admin\ResearchCrudController@create');

Route::post('/comparison/getItems', '\App\Http\Controllers\ComparisonController@getItems');
Route::post('/comparison/getRecent', '\App\Http\Controllers\ComparisonController@getRecent');

Route::post('/feedback/create/{type}', '\Aimix\Feedback\app\Http\Controllers\Admin\FeedbackCrudController@create');
Route::post('/applications/create', '\App\Http\Controllers\Admin\ApplicationCrudController@create');

Route::post('/subscribe', '\App\Http\Controllers\Admin\SubscriptionCrudController@subscribe')->name('subscribe');

Route::post('/setRegion', '\App\Http\Controllers\HomeController@setRegion')->name('setRegion');

Route::post('/getNotifications', '\App\Http\Controllers\HomeController@getNotifications')->name('getNotifications');

Route::post('/search', '\App\Http\Controllers\HomeController@search');
Route::post('/placesSearch/{type}', '\App\Http\Controllers\PlacesController@search');

Route::feeds();
Route::redirect('/ru/feed', '/feed', 301);
Route::redirect('/uk/feed', '/feed?lang=uk', 301);
Route::redirect('/ru/servisy/statistika-cen/{theme?}/{slug?}', '/ru/analitics/statistika-cen/{theme?}/{slug?}', 301);
Route::redirect('/uk/servisy/statistika-cin/{theme?}/{slug?}', '/uk/analitics/statistika-cin/{theme?}/{slug?}', 301);
Route::redirect('/ru/servisy/reytingi-po-nedvizhimosti/{theme?}/{slug?}', '/ru/analitics/reytingi-po-nedvizhimosti/{theme?}/{slug?}', 301);
Route::redirect('/uk/servisy/reytingi-po-neruhomosti/{theme?}/{slug?}', '/uk/analitics/reytingi-po-neruhomosti/{theme?}/{slug?}', 301);
Route::redirect('/ru/servisy/voprosyotvety-online/{slug?}', '/ru/faq/{slug?}', 301);
Route::redirect('/uk/servisi/pitannia-vidpovid-online/{slug?}', '/uk/faq/{slug?}', 301);
Route::redirect('/ru/poleznaya-informaciya/slovar-terminov-po-nedvizhimosti-v-ukraine', '/ru/dictionary', 301);
Route::redirect('/uk/korisna-informaciya/slovnik-terminiv-pro-nerukhomist-budivnictvo-ipoteku', '/uk/dictionary', 301);
Route::redirect('/uk/marketingovye-issledovaniya/{type?}', '/uk/marketingovi-doslidzhennya/{type?}', 301);
Route::redirect('/ru/marketingovi-doslidzhennya/{type?}', '/ru/marketingovye-issledovaniya/{type?}', 301);
Route::redirect('/ru/poleznaya-informaciya/ocenka-nedvizhimosti/{slug?}', '/ru/ocenka-nedvizhimosti/{slug?}', 301);
Route::redirect('/uk/korisna-informaciya/ocinka-nerukhomosti/{slug?}', '/uk/ocinka-nerukhomosti/{slug?}', 301);
Route::redirect('/ru/poleznaya-informaciya/professionalnye-i-obshhestvennye-obedineniya/{slug?}', '/ru/professionalnye-i-obshhestvennye-obedineniya/{slug?}', 301);
Route::redirect('/uk/korisna-informaciya/profesijni-ta-gromadski-obyednannya/{slug?}', '/uk/profesijni-ta-gromadski-obyednannya/{slug?}', 301);
Route::redirect('/ru/reklama-na-sajte', '/ru/reklama', 301);
Route::redirect('/uk/reklama-na-sajti', '/uk/reklama-na-sayti-zagorodna-com', 301);
Route::redirect('/ru/servisy/zagorodnarss', '/feed', 301);
Route::redirect('/uk/servisi/zagorodnarss', '/feed?lang=uk', 301);
Route::redirect('/ru/nedvizhimost-za-rubezhom/{path?}', '/', 301)->where('path', '.*');
Route::redirect('/ru/doska-obyavlenij/{path?}', '/', 301)->where('path', '.*');

Route::redirect('/en/cottage-towns-and-settlements-in-ukraine/{path?}', '/ru/kottedzhnye-gorodki-poselki-ukrainy', 301)->where('path', '.*');
Route::redirect('/en/new-buildings-in-suburb/{path?}', '/ru/novostrojki-prigoroda', 301)->where('path', '.*');
Route::redirect('/en/articles/{path?}', '/ru/stati', 301)->where('path', '.*');
Route::redirect('/en/construction/{path?}', '/ru/stroitelstvo', 301)->where('path', '.*');
Route::redirect('/en/news/{path?}', '/ru/novosti', 301)->where('path', '.*');
Route::redirect('/en/analytics/{path?}', '/ru/analitika', 301)->where('path', '.*');
Route::redirect('/en/contact', '/ru/kontakty', 301);
Route::redirect('/en/exhibitions-by-real-estate-suburban-residential-commercial-property/{path?}', '/ru/vystavki-po-nedvizhimosti-i-stroitelstvu-v-kieve', 301)->where('path', '.*');
Route::redirect('/en/conferences/{path?}', '/ru/konferencii', 301)->where('path', '.*');
Route::redirect('/en/seminars-workshops/{path?}', '/ru/seminary', 301)->where('path', '.*');
Route::redirect('/en/competitions/{path?}', '/ru/konkursy', 301)->where('path', '.*');
Route::redirect('/en/about-us/{path?}', '/ru/o-kompanii', 301)->where('path', '.*');
Route::redirect('/en/marketing-research/{path?}', '/ru/marketingovye-issledovaniya', 301)->where('path', '.*');
Route::redirect('/en/useful-information/{path?}', '/ru/poleznaya-informaciya', 301)->where('path', '.*');
Route::redirect('/en/ecology/{path?}', '/ru/jekologiya', 301)->where('path', '.*');
Route::redirect('/en/land-ukraine/{path?}', '/ru/zemlya-ukrainy', 301)->where('path', '.*');
Route::redirect('/en/regions-of-ukraine/{path?}', '/ru/regiony-ukrainy/oblasti-ukrainy', 301)->where('path', '.*');
Route::redirect('/en/services/zagorodnarss', '/feed', 301)->where('path', '.*');
Route::redirect('/en/services/{path?}', '/ru/servisy/oprosy-i-golosovaniya', 301)->where('path', '.*');
Route::redirect('/en/classification-of-real-estate/{path?}', '/ru/klassifikaciia-nedvizhimosti', 301)->where('path', '.*');
Route::redirect('/en/real-estate-expertise/{path?}', '/ru/ocenka-nedvizhimosti', 301)->where('path', '.*');
Route::redirect('/en/firms/{path?}', '/ru/firms', 301)->where('path', '.*');
Route::redirect('/en/special-proposals/{path?}', '/ru/akcii', 301)->where('path', '.*');
Route::redirect('/en/ratings-on-real-estate/{path?}', '/ru/analitics/reytingi-po-nedvizhimosti', 301)->where('path', '.*');
Route::redirect('/en/advertising-on-site', '/ru/reklama', 301)->where('path', '.*');
Route::redirect('ru/meropriyatiya/seminary/{path?}', '/')->where('path', '.*');
Route::redirect('uk/meropriyatiya/seminary/{path?}', '/')->where('path', '.*');

Route::redirect('novini', '/uk/novini', 301);
Route::redirect('novosti', '/ru/novosti', 301);

Route::redirect('/en/{path?}', '/', 301)->where('path', '.*');

$analitics = Backpack\NewsCRUD\app\Models\Category::withoutGlobalScopes()->whereIn('id', [194,195,375,376])->get();
foreach($analitics as $category) {
	Route::redirect('/' . $category->language_abbr . '/' . $category->slug . '/{theme?}/{slug?}', '/' . $category->language_abbr . '/analitics/' . $category->slug . '/{theme?}/{slug?}', 301);
}

// Route::redirect('/uk/reklama-na-sajti', '/uk/reklama-na-sayti-zagorodna-com');

// Researches
Route::get('/ru/marketingovye-issledovaniya/{type?}', '\App\Http\Controllers\PageController@research')->middleware('lcl')->name('ru_researches');
Route::get('/uk/marketingovi-doslidzhennya/{type?}', '\App\Http\Controllers\PageController@research')->middleware('lcl')->name('uk_researches');

//->where(['locale' => 'ru|uk'])
//Route::group(['prefix' => '{locale?}', 'where' => '', 'middleware' => ['lcl']], function() {

// Reviews
Route::get('ru/otzyvy/{type?}', '\Aimix\Review\app\Http\Controllers\ReviewController@index')->middleware('lcl')->name('ru_reviews');
Route::get('uk/vidguki/{type?}', '\Aimix\Review\app\Http\Controllers\ReviewController@index')->middleware('lcl')->name('uk_reviews');

Route::get('/ru/reviews/{type?}', function($type = "") {
	return redirect()->to('ru/otzyvy/'.$type);
});

Route::get('/uk/reviews/{type?}', function($type = "") {
	return redirect()->to('uk/vidguki/'.$type);
});

Route::get('/uk/otzyvy/{type?}', function($type = "") {
	return redirect()->to('uk/vidguki/'.$type);
});


Route::get('/ru/vidguki/{type?}', function($type = "") {
	return redirect()->to('ru/otzyvy/'.$type);
});
/*
Route::redirect('/ru/reviews/{type?}', '/ru/otzyvy/{type?}', 301);
Route::redirect('/uk/reviews/{type?}', '/uk/vidguki/{type?}', 301);
Route::redirect('/ru/vidguki/{type?}', '/ru/otzyvy/{type?}', 301);
Route::redirect('/uk/otzyvy/{type?}', '/uk/vidguki/{type?}', 301);
*/

Route::redirect('/ru/pro-kompaniyu/{policy?}', '/ru/o-kompanii/{policy?}', 301);
Route::redirect('/uk/o-kompanii/{policy?}', '/uk/pro-kompaniyu/{policy?}', 301);
Route::redirect('/ru/karta-nedvizhimosti', '/ru/kottedzhnye-gorodki-poselki-ukrainy/map', 301);
Route::redirect('/ru/interaktivnaia-karta-prigorodnyh-novostroek-ukrainy', '/ru/novostrojki-prigoroda/map', 301);





foreach(['ru', 'uk',''] as $lang){
Route::prefix($lang)->middleware('lcl')->group(function() use ($lang) {


	// Main Page
	Route::get('/', '\App\Http\Controllers\HomeController@index')->name($lang . '_home');

	Route::prefix('company')->group(function() {
		Route::get('byArea/{region}/{area}/{city}', '\App\Http\Controllers\API\CompanyController@companiesByArea');
	});

	Route::prefix('article')->group(function() {
		Route::get('byArea/{articleTab}/{region}', '\App\Http\Controllers\API\ArticleController@byArea');
	});

	Route::post('/getArticles/{category}', '\App\Http\Controllers\HomeController@getArticles');
	Route::post('/getPromotions', '\App\Http\Controllers\HomeController@getPromotions');
	Route::post('/getProducts', '\App\Http\Controllers\CatalogController@getProducts');

	// Pages
	foreach(Page::withoutGlobalScopes()->where('template', 'common')->get() as $page) {
		Route::get('/' . $page->slug, '\App\Http\Controllers\PageController@index');
	}

	foreach(['kontakty', 'kontakti'] as $cont) {
		Route::get('/' . $cont, '\App\Http\Controllers\PageController@contacts');
	}

	// Companies
	Route::prefix('/firms')->group(function() use ($lang) {
	  Route::any('/{slug?}', '\App\Http\Controllers\CompanyController@index')->name($lang . '_companies');
	  Route::any('/{category}/{slug}/{tab?}', '\App\Http\Controllers\CompanyController@show')->name($lang . '_company');
	});

	// Promotions
	Route::any('/akcii/{slug?}', '\App\Http\Controllers\PromotionController@index')->name($lang . '_promotions');
	Route::any('/akcij/{slug?}', '\App\Http\Controllers\PromotionController@index');

	// Faq
	Route::any('/faq/{slug?}', '\App\Http\Controllers\FaqController@index')->name($lang . '_faq');

	// Dictionary
	Route::any('/dictionary', '\App\Http\Controllers\DictionaryController@index')->name($lang . '_dictionary');

	// Favorite
	Route::get('/favorite', '\App\Http\Controllers\FavoriteController@index')->name($lang . '_favorite');
	Route::post('/favorite', '\App\Http\Controllers\FavoriteController@response');

	// Comparison
	Route::get('/comparison', '\App\Http\Controllers\ComparisonController@index')->name($lang . '_comparison');

	// About, Policy
		foreach(['o-kompanii', 'pro-kompaniyu'] as $item) {
			Route::prefix($item)->group(function() use ($lang) {
				Route::get('/', '\App\Http\Controllers\PageController@about');
				foreach(['politika-konfidencialnosti.html', 'politika-konfidenciynosti.html'] as $pol) {
					Route::get($pol, '\App\Http\Controllers\PageController@policy');
				}
			});
		}

	// Cookies
	Route::get('/cookies', '\App\Http\Controllers\PageController@cookies')->name($lang . '_cookies');
	Route::post('/cookies/allow', '\App\Http\Controllers\PageController@allow_cookies');

	Route::any('tags', '\App\Http\Controllers\NewsController@tags')->name($lang . '_tags');

	$categories = Backpack\NewsCRUD\app\Models\Category::withoutGlobalScopes()->find([202,203,281,282,293,294,297,324,302,329,349,350,300,327,347,348,1,14,5,26,12,27,13,28,215,216,218,219,220,222,217,221,351,352,371,372]); // 194,195,369,370,371,372,373,374

	// Servisy
	Route::prefix('/servisy')->group(function() use ($categories) {

			// Polls results
			foreach($categories->find([371,372]) as $category) {
				Route::any('/' . $category->slug . '/{id}', '\App\Http\Controllers\NewsController@poll_results');
			}

			Route::prefix('/{category?}')->group(function() {
				Route::any('/{theme?}', '\App\Http\Controllers\NewsController@servisy');
				Route::any('/{theme}/{slug}', '\App\Http\Controllers\NewsController@servisy_show');
			});
	});

	// `Analitics`
	Route::prefix('/analitics')->group(function() {
		Route::prefix('/{category?}')->group(function() {
			Route::any('/{theme?}', '\App\Http\Controllers\NewsController@analitics');
			Route::any('/{theme}/{slug}', '\App\Http\Controllers\NewsController@analitics_show');
		});
	});

	foreach($categories->find([351,352]) as $category) {
	  Route::prefix($category->slug)->group(function() {
	    Route::any('/{theme?}', '\App\Http\Controllers\NewsController@services');
	    Route::any('/{theme}/{slug}', '\App\Http\Controllers\NewsController@services_show');
	  });
	}

	foreach($categories->find([202,203]) as $category) {
	  Route::prefix($category->slug)->group(function() {
	    Route::any('/{theme?}', '\App\Http\Controllers\NewsController@regions');
	    Route::any('/{theme}/{slug}', '\App\Http\Controllers\NewsController@regions_show');
	  });
	}

	foreach($categories->find([281,282]) as $category) {
	  Route::prefix($category->slug)->group(function() {
	    Route::any('/{theme?}', '\App\Http\Controllers\NewsController@ecology');
	    Route::any('/{theme}/{slug}', '\App\Http\Controllers\NewsController@ecology_show');
	  });
	}

	foreach($categories->find([293,294]) as $category) {
	  Route::prefix($category->slug)->group(function() {
	    Route::any('/{theme?}', '\App\Http\Controllers\NewsController@information');
	    Route::any('/{theme}/{slug}', '\App\Http\Controllers\NewsController@information_show');
	  });
	}

	foreach($categories->find([349,350]) as $category) {
	  Route::prefix($category->slug)->group(function() {
	    Route::any('/{theme?}', '\App\Http\Controllers\NewsController@business');
	    Route::any('/{theme?}/{slug}', '\App\Http\Controllers\NewsController@business_show');
	  });
	}

	foreach($categories->find([297,324,302,329,300,327,347,348]) as $category) {
	  Route::prefix($category->slug)->group(function() {
	    Route::any('/', '\App\Http\Controllers\NewsController@business');
	    Route::any('/{slug}', '\App\Http\Controllers\NewsController@business_show');
	  });
	}

	foreach($categories->find([1,14,5,26,12,27,13,28]) as $category) {
	  Route::prefix($category->slug)->group(function() {
	    Route::any('/{slug}', '\App\Http\Controllers\NewsController@show')->where('slug', '.*html$');
	    Route::any('/', '\App\Http\Controllers\NewsController@index');
	    Route::any('/bookmarks/{theme?}', '\App\Http\Controllers\NewsController@index');
	  });
	}

	foreach($categories->find([215,216,218,219,220,222,217,221]) as $category) {
	  Route::prefix($category->slug)->group(function() use ($category) {
			if($category->id == 217 || $category->original_id == 217) {
				foreach(Article::withoutGlobalScopes()->where('category_id', $category->id)->get() as $article) {
					Route::any('/' . $article->slug, '\App\Http\Controllers\NewsController@seminars_show');
				}
			}
	    Route::any('/{theme?}', '\App\Http\Controllers\NewsController@events');
	    Route::any('/{theme}/{slug}', '\App\Http\Controllers\NewsController@events_show');
	  });
	}

	// foreach(Category::find([1,2,6,7]) as $category) {

		Route::prefix('{category}')->group(function() use ($lang) {
			Route::prefix('/map')->group(function() use ($lang) {
				Route::any('/', '\App\Http\Controllers\CatalogController@map')->name($lang . '_map');

				foreach(['region','area','city','kyivdistrict'] as $item) {
					Route::any('/' . $item . '/{arg2}/{arg3?}/{arg4?}', '\App\Http\Controllers\CatalogController@map');
				}

				foreach(__('main.product_statuses') as $key => $item) {
					Route::any('/' . $key . '/{arg_2?}', '\App\Http\Controllers\CatalogController@map');
				}

				Route::any('/frozen/{arg_2?}', '\App\Http\Controllers\CatalogController@map'); // "frozen" not in statuses array

				foreach(__('attributes.cottage_types') as $key => $item) {
					Route::any('/' . \Str::slug($key), '\App\Http\Controllers\CatalogController@map');
				}
				foreach(__('attributes.newbuild_types') as $key => $item) {
					Route::any('/' . \Str::slug($key), '\App\Http\Controllers\CatalogController@map');
				}
			});

			Route::any('/catalog', '\App\Http\Controllers\CatalogController@index')->name($lang . '_catalog');
			Route::any('/', '\App\Http\Controllers\CatalogController@precatalog')->name($lang . '_precatalog');
			Route::get('/statistics', '\App\Http\Controllers\CatalogController@statistics')->name($lang . '_precatalog_statistics');

			foreach(['region','area','city','kyivdistrict'] as $item) {
				Route::any('/' . $item . '/{arg2}/{arg3?}/{arg4?}', '\App\Http\Controllers\CatalogController@precatalog');
			}

			foreach(__('main.product_statuses') as $key => $item) {
				Route::any('/' . $key . '/{arg_2?}', '\App\Http\Controllers\CatalogController@precatalog');
			}

			Route::any('/frozen/{arg_2?}', '\App\Http\Controllers\CatalogController@precatalog'); // "frozen" not in statuses array

			foreach(__('attributes.cottage_types') as $key => $item) {
				Route::any('/' . \Str::slug($key), '\App\Http\Controllers\CatalogController@precatalog');
			}
			foreach(__('attributes.newbuild_types') as $key => $item) {
				Route::any('/' . \Str::slug($key), '\App\Http\Controllers\CatalogController@precatalog');
			}

			Route::any('/{product}/rating', '\App\Http\Controllers\CatalogController@product_rating')->name($lang . '_product_rating');
			Route::any('/{product}/{tab?}/{project_slug?}', '\App\Http\Controllers\CatalogController@show')->name($lang . '_product_page');
		});
	// }

	// Route::get('{page}/{subs?}', ['uses' => '\App\Http\Controllers\PageController@index'])
	//     ->where(['page' => '^(((?=(?!admin))(?=(?!\/)).))*$', 'subs' => '.*']);

});


// TAGS REDIRECT
Route::get('/', function(Request $request) {
	$id = $request->query('id');

	if($id) {
		$tag = \Backpack\NewsCRUD\app\Models\Tag::where('id', $id)->firstOrTranslation();
		return redirect()->to('/' . $tag->language_abbr . '/tags?id=' . $id);
	}else{
		$homeController = new \App\Http\Controllers\HomeController();
		return $homeController->index($request);
	}

})->middleware('lcl');

}
