const mix = require('laravel-mix');
require('laravel-mix-brotli');
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
   .js('resources/js/news/news.js', 'public/js/news')
   .js('resources/js/news/article.js', 'public/js/news')
   .js('resources/js/news/tags.js', 'public/js/news')
   .js('resources/js/analitics/index.js', 'public/js/analitics')
   .js('resources/js/servisy/index.js', 'public/js/servisy')
   .js('resources/js/regions/index.js', 'public/js/regions')
   .js('resources/js/services/index.js', 'public/js/services')
   .js('resources/js/ecology/index.js', 'public/js/ecology')
   .js('resources/js/information/index.js', 'public/js/information')
   .js('resources/js/events/index.js', 'public/js/events')
   .js('resources/js/business/index.js', 'public/js/business')
   .js('resources/js/faq/faq.js', 'public/js/faq')
   .js('resources/js/companies/companies.js', 'public/js/companies')
   .js('resources/js/companies/company.js', 'public/js/companies')
   .js('resources/js/reviews/reviews.js', 'public/js/reviews')
   .js('resources/js/fields/achievements.js', 'public/js/fields')
   .js('resources/js/fields/address_zagorodna.js', 'public/js/fields')
   .js('resources/js/product/reviews.js', 'public/js/product')
   .js('resources/js/product/projects.js', 'public/js/product')
   .js('resources/js/product/promotions.js', 'public/js/product')
   .js('resources/js/product/show.js', 'public/js/product')
   .js('resources/js/product/rating.js', 'public/js/product')
   .js('resources/js/product/default.js', 'public/js/product')
   .js('resources/js/catalog/precatalog.js', 'public/js/catalog')
   .js('resources/js/catalog/map.js', 'public/js/catalog')
   .js('resources/js/catalog/catalog.js', 'public/js/catalog')
   .js('resources/js/researches/researches.js', 'public/js/researches')
   .js('resources/js/dictionary/dictionary.js', 'public/js/dictionary')
   .js('resources/js/index/index.js', 'public/js/index')
   .js('resources/js/promotions/promotions.js', 'public/js/promotions')
   .js('resources/js/favorite/favorite.js', 'public/js/favorite')
   .js('resources/js/comparison/comparison.js', 'public/js/comparison')
   .js('resources/js/tables/statistics.js', 'public/js/tables')
   .js('resources/js/parser/parser.js', 'public/js/parser')
   .sass('resources/sass/app.scss', 'public/css')
   .styles(['resources/css/main.css'], 'public/css/main.css')
   .js('packages/aimix/shop/src/resources/js/fields/modification.js', 'packages/aimix/shop/js/fields')
   .js('packages/aimix/shop/src/resources/js/fields/product_images.js', 'packages/aimix/shop/js/fields')
   .js('packages/aimix/gallery/src/resources/js/fields/gallery_images.js', 'packages/aimix/gallery/js/fields')
   .js('node_modules/popper.js/dist/popper.js', 'public/js').sourceMaps().brotli();
