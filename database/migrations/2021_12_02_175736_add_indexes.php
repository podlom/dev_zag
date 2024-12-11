<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::table('product_categories', function(Blueprint $table) {
		    $table->index('language_abbr');
		    $table->index('name');
		    $table->index('slug');
		  });
		  
	    Schema::table('areas', function(Blueprint $table) {
		    $table->index('language_abbr');
		    $table->index('name');
		    $table->index('slug');
		    $table->index('region_id');
		    $table->index('area_id');
		  });
		  
	    Schema::table('attributes', function(Blueprint $table) {
		    $table->index('language_abbr');
		    $table->index('name');
		    $table->index('slug');
		    $table->index('is_important');
		    $table->index('is_active');
		    $table->index('in_filters');
		    $table->index('in_properties');
		  });
		  
	    Schema::table('attribute_category', function(Blueprint $table) {
		    $table->index('attribute_id');
		    $table->index('category_id');
		  });
		  
	    Schema::table('attribute_modification', function(Blueprint $table) {
		    $table->index('value');
		  });
		  
	    Schema::table('brands', function(Blueprint $table) {
		    $table->index('language_abbr');
		    $table->index('name');
		    $table->index('slug');
		    $table->index('category_id');
		    $table->index('country_id');
		    $table->index('is_popular');
		    $table->index('is_active');
		    $table->index('is_parsed');
		  });
		  
	    Schema::table('brand_categories', function(Blueprint $table) {
		    $table->index('language_abbr');
		    $table->index('name');
		    $table->index('slug');
		    $table->index('is_popular');
		    $table->index('is_active');
		  });
		  
	    Schema::table('categories', function(Blueprint $table) {
		    $table->index('language_abbr');
		    $table->index('name');
		    $table->index('slug');
		    $table->index('faq_category_id');
		    $table->index('is_active');
		  });
		  
	    Schema::table('cities', function(Blueprint $table) {
		    $table->index('language_abbr');
		    $table->index('area_id');
		    $table->index('city_id');
		    $table->index('name');
		    $table->index('slug');
		  });
		  
	    Schema::table('faqs', function(Blueprint $table) {
		    $table->index('language_abbr');
		    $table->index('category_id');
		  });
		  
	    Schema::table('faq_categories', function(Blueprint $table) {
		    $table->index('language_abbr');
		    $table->index('name');
		    $table->index('slug');
		    $table->index('is_active');
		  });
		  
	    Schema::table('kyivdistricts', function(Blueprint $table) {
		    $table->index('language_abbr');
		    $table->index('kyivdistrict_id');
		    $table->index('slug');
		    $table->index('name');
		  });
		  
	    Schema::table('languages', function(Blueprint $table) {
		    $table->index('name');
		    $table->index('abbr');
		    $table->index('native');
		    $table->index('active');
		    $table->index('default');
		  });
		  
	    Schema::table('menu_items', function(Blueprint $table) {
		    $table->index('name');
		    $table->index('language_abbr');
		    $table->index('parent_id');
		  });
		  
	    Schema::table('metas', function(Blueprint $table) {
		    $table->index('language_abbr');
		    $table->index('type');
		    $table->index('status');
		    $table->index('is_map');
		  });
		  
	    Schema::table('modifications', function(Blueprint $table) {
		    $table->index('price');
		    $table->index('is_default');
		    $table->index('is_active');
		    $table->index('in_stock');
		    $table->index('trans_name');
		  });
		  
	    Schema::table('pages', function(Blueprint $table) {
		    $table->index('language_abbr');
		    $table->index('template');
		    $table->index('slug');
		  });
		  
	    Schema::table('poll_answers', function(Blueprint $table) {
		    $table->index('question_id');
		    $table->index('product_id');
		    $table->index('option_id');
		  });
		  
	    Schema::table('poll_options', function(Blueprint $table) {
		    $table->index('language_abbr');
		    $table->index('question_id');
		    $table->index('is_active');
		  });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::table('product_categories', function(Blueprint $table) {
		    $table->dropIndex('language_abbr');
		    $table->dropIndex('name');
		    $table->dropIndex('slug');
		  });  
		  
	    Schema::table('areas', function(Blueprint $table) {
		    $table->dropIndex('language_abbr');
		    $table->dropIndex('name');
		    $table->dropIndex('slug');
		    $table->dropIndex('region_id');
		    $table->dropIndex('area_id');
		  });
		  
	    Schema::table('attributes', function(Blueprint $table) {
		    $table->dropIndex('language_abbr');
		    $table->dropIndex('name');
		    $table->dropIndex('slug');
		    $table->dropIndex('is_important');
		    $table->dropIndex('is_active');
		    $table->dropIndex('in_filters');
		    $table->dropIndex('in_properties');
		  });
		  
	    Schema::table('attribute_category', function(Blueprint $table) {
		    $table->dropIndex('attribute_id');
		    $table->dropIndex('category_id');
		  });
		  
	    Schema::table('attribute_modification', function(Blueprint $table) {
		    $table->dropIndex('value');
		  });
		  
	    Schema::table('brands', function(Blueprint $table) {
		    $table->dropIndex('language_abbr');
		    $table->dropIndex('name');
		    $table->dropIndex('slug');
		    $table->dropIndex('category_id');
		    $table->dropIndex('country_id');
		    $table->dropIndex('is_popular');
		    $table->dropIndex('is_active');
		    $table->dropIndex('is_parsed');
		  });
		  
	    Schema::table('brand_categories', function(Blueprint $table) {
		    $table->dropIndex('language_abbr');
		    $table->dropIndex('name');
		    $table->dropIndex('slug');
		    $table->dropIndex('is_popular');
		    $table->dropIndex('is_active');
		  });
		  
	    Schema::table('categories', function(Blueprint $table) {
		    $table->dropIndex('language_abbr');
		    $table->dropIndex('name');
		    $table->dropIndex('slug');
		    $table->dropIndex('faq_category_id');
		    $table->dropIndex('is_active');
		  });
		  
	    Schema::table('cities', function(Blueprint $table) {
		    $table->dropIndex('language_abbr');
		    $table->dropIndex('area_id');
		    $table->dropIndex('city_id');
		    $table->dropIndex('name');
		    $table->dropIndex('slug');
		  });
		  
	    Schema::table('faqs', function(Blueprint $table) {
		    $table->dropIndex('language_abbr');
		    $table->dropIndex('category_id');
		  });
		  
	    Schema::table('faq_categories', function(Blueprint $table) {
		    $table->dropIndex('language_abbr');
		    $table->dropIndex('name');
		    $table->dropIndex('slug');
		    $table->dropIndex('is_active');
		  });
		  
	    Schema::table('kyivdistricts', function(Blueprint $table) {
		    $table->dropIndex('language_abbr');
		    $table->dropIndex('kyivdistrict_id');
		    $table->dropIndex('slug');
		    $table->dropIndex('name');
		  });
		  
	    Schema::table('languages', function(Blueprint $table) {
		    $table->dropIndex('name');
		    $table->dropIndex('abbr');
		    $table->dropIndex('native');
		    $table->dropIndex('active');
		    $table->dropIndex('default');
		  });
		  
	    Schema::table('menu_items', function(Blueprint $table) {
		    $table->dropIndex('name');
		    $table->dropIndex('language_abbr');
		    $table->dropIndex('slug');
		    $table->dropIndex('parent_id');
		  });
		  
	    Schema::table('metas', function(Blueprint $table) {
		    $table->dropIndex('language_abbr');
		    $table->dropIndex('type');
		    $table->dropIndex('status');
		    $table->dropIndex('is_map');
		  });
		  
	    Schema::table('modifications', function(Blueprint $table) {
		    $table->dropIndex('language_abbr');
		    $table->dropIndex('name');
		    $table->dropIndex('price');
		    $table->dropIndex('is_default');
		    $table->dropIndex('is_active');
		    $table->dropIndex('in_stock');
		    $table->dropIndex('trans_name');
		  });
		  
	    Schema::table('pages', function(Blueprint $table) {
		    $table->dropIndex('language_abbr');
		    $table->dropIndex('template');
		    $table->dropIndex('slug');
		  });
		  
	    Schema::table('poll_answers', function(Blueprint $table) {
		    $table->dropIndex('question_id');
		    $table->dropIndex('product_id');
		    $table->dropIndex('option_id');
		  });
		  
	    Schema::table('poll_options', function(Blueprint $table) {
		    $table->dropIndex('language_abbr');
		    $table->dropIndex('question_id');
		    $table->dropIndex('is_active');
		  });
		  
    }
}
