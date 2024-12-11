<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => [
        config('backpack.base.web_middleware', 'web'),
        config('backpack.base.middleware_key', 'admin'),
    ],
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('faq', 'FaqCrudController');
    Route::crud('faqcategory', 'FaqCategoryCrudController');
    Route::crud('term', 'TermCrudController');
    Route::crud('research', 'ResearchCrudController');
    Route::crud('product', 'ProductCrudController');
    Route::crud('subscription', 'SubscriptionCrudController');
    Route::crud('meta', 'MetaCrudController');
    Route::crud('region', 'RegionCrudController');
    Route::crud('area', 'AreaCrudController');
    Route::crud('city', 'CityCrudController');
    Route::crud('regionarticle', 'RegionArticleCrudController');
    Route::crud('regionarticlecategory', 'RegionArticleCategoryCrudController');
    Route::crud('statisticsarticle', 'StatisticsArticleCrudController');
    Route::crud('statisticsarticlecategory', 'StatisticsArticleCategoryCrudController');
    Route::crud('newsarticle', 'NewsArticleCrudController');
    Route::crud('buildingarticle', 'BuildingArticleCrudController');
    Route::crud('analiticsarticle', 'AnaliticsArticleCrudController');
    Route::crud('eventarticlecategory', 'EventArticleCategoryCrudController');
    Route::crud('exhibitionarticle', 'ExhibitionArticleCrudController');
    Route::crud('seminararticle', 'SeminarArticleCrudController');
    Route::crud('conferencearticle', 'ConferenceArticleCrudController');
    Route::crud('contestarticle', 'ContestArticleCrudController');
    Route::crud('ecologyarticlecategory', 'EcologyArticleCategoryCrudController');
    Route::crud('ecologyarticle', 'EcologyArticleCrudController');
    Route::crud('informationarticlecategory', 'InformationArticleCategoryCrudController');
    Route::crud('informationarticle', 'InformationArticleCrudController');
    Route::crud('landsarticle', 'LandsArticleCrudController');
    Route::crud('contractsarticle', 'ContractsArticleCrudController');
    Route::crud('classificationarticle', 'ClassificationArticleCrudController');
    Route::crud('evaluationarticle', 'EvaluationArticleCrudController');
    Route::crud('associationsarticle', 'AssociationsArticleCrudController');
    Route::crud('servicesarticle', 'ServicesArticleCrudController');
    Route::crud('servicesarticlecategory', 'ServicesArticleCategoryCrudController');
    Route::crud('businesscategory', 'BusinessCategoryCrudController');
    Route::crud('statistics', 'StatisticsCrudController');
    Route::crud('kyivdistrict', 'KyivdistrictCrudController');
    Route::crud('servisyarticlecategory', 'ServisyArticleCategoryCrudController');
    Route::crud('pollquestion', 'PollQuestionCrudController');
    Route::crud('polloption', 'PollOptionCrudController');
    Route::any('parser', 'ParserController@index');
    Route::post('parser/parse', 'ParserController@parse');
    Route::post('parser/getLog', 'ParserController@getLog');
    Route::any('compareMeta', 'MetaController@compareMeta');
    
    Route::crud('application', 'ApplicationCrudController');
}); // this should be the absolute last line of this file