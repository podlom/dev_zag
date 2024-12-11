# Backpack\LangFileManager

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status](https://img.shields.io/travis/Laravel-Backpack/langfilemanager/master.svg?style=flat-square)](https://travis-ci.org/Laravel-Backpack/langfilemanager)
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Style CI](https://styleci.io/repos/53691643/shield)](https://styleci.io/repos/53691643)
[![Total Downloads][ico-downloads]][link-downloads]

A quick interface to edit language files, for Laravel Backpack. 

_**Note:** this interface allows the admin to directly edit the language files stored in ```resources/lang```. Which might not be a great idea in production, if you have those files commited to git and/or use auto-deployment._

> ### Security updates and breaking changes
> Please **[subscribe to the Backpack Newsletter](http://backpackforlaravel.com/newsletter)** so you can find out about any security updates, breaking changes or major features. We send an email every 1-2 months.

## Install in Laravel 6

### Step 1. Install via Composer

``` bash
composer require backpack/langfilemanager
```

### Step 2. Run the migration, seed and file publishing

``` bash
php artisan vendor:publish --provider="Backpack\LangFileManager\LangFileManagerServiceProvider" --tag="migrations" #publish the migration file
php artisan migrate
php artisan db:seed --class="Backpack\LangFileManager\database\seeds\LanguageTableSeeder"
php artisan vendor:publish --provider="Backpack\LangFileManager\LangFileManagerServiceProvider" --tag="config" #publish the config file
php artisan vendor:publish --provider="Backpack\LangFileManager\LangFileManagerServiceProvider" --tag="lang" #publish the lang files
```


## Usage

Tell LangFileManager what langfiles NOT to show, in config/backpack/langfilemanager.php:

``` php
// Language files to NOT show in the LangFileManager
//
'language_ignore' => ['admin', 'pagination', 'reminders', 'validation', 'log', 'crud'],
```

Add a menu item for it in resources/views/vendor/backpack/base/inc/sidebar.blade.php:

```html
<li class="nav-item nav-dropdown">
  <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon fa fa-globe"></i> Translations</a>
  <ul class="nav-dropdown-items">
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('language') }}"><i class="nav-icon fa fa-flag-checkered"></i> Languages</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('language/texts') }}"><i class="nav-icon fa fa-language"></i> Site texts</a></li>
  </ul>
</li>
```

Or just try at **your-project-domain/admin/language/texts**

## Screenshots

See http://laravelbackpack.com

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.


## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## Security

If you discover any security related issues, please email alin@updivision.com or hello@tabacitu.ro instead of using the issue tracker.

Please **[subscribe to the Backpack Newsletter](http://backpackforlaravel.com/newsletter)** so you can find out about any security updates, breaking changes or major features. We send an email every 1-2 months.

## Credits

- [Alin Ghitu][link-author] - author
- [Cristian Tabacitu][link-author-2] - contributor
- [All Contributors][link-contributors]


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Hire us

We've spend more than 10.000 hours creating, polishing and maintaining administration panels on Laravel. We've developed e-Commerce, e-Learning, ERPs, social networks, payment gateways and much more. We've worked on admin panels _so much_, that we've created one of the most popular software in its niche - just from making public what was repetitive in our projects.

If you are looking for a developer/team to help you build an admin panel on Laravel, look no further. You'll have a difficult time finding someone with more experience & enthusiasm for this. This is _what we do_. [Contact us - let's see if we can work together](https://backpackforlaravel.com/need-freelancer-or-development-team).


[ico-version]: https://img.shields.io/packagist/v/backpack/langfilemanager.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/laravel-backpack/langfilemanager/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/laravel-backpack/langfilemanager.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/laravel-backpack/langfilemanager.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/backpack/langfilemanager.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/backpack/langfilemanager
[link-travis]: https://travis-ci.org/laravel-backpack/langfilemanager
[link-scrutinizer]: https://scrutinizer-ci.com/g/laravel-backpack/langfilemanager/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/laravel-backpack/langfilemanager
[link-downloads]: https://packagist.org/packages/backpack/langfilemanager
[link-author]: https://github.com/ghitu
[link-author-2]: http://tabacitu.ro
[link-contributors]: ../../contributors
