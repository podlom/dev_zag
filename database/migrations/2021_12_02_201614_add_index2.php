<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndex2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::table('poll_questions', function(Blueprint $table) {
		    $table->index('language_abbr');
		    $table->index('is_multiple');
		    $table->index('is_active');
		  });
		  
	    Schema::table('promotions', function(Blueprint $table) {
		    $table->index('language_abbr');
		    $table->index('product_id');
		    $table->index('slug');
		    $table->index('start');
		    $table->index('end');
		    $table->index('is_parsed');
		    $table->index('is_active');
		  });
		  
	    Schema::table('regions', function(Blueprint $table) {
		    $table->index('language_abbr');
		    $table->index('region_id');
		    $table->index('article_region_id');
		    $table->index('name');
		    $table->index('slug');
		    $table->index('is_active');
		  });
		  
	    Schema::table('reviews', function(Blueprint $table) {
		    $table->index('language_abbr');
		    $table->index('is_moderated');
		    $table->index('reviewable_id');
		    $table->index('reviewable_type');
		    $table->index('type');
		    $table->index('product_id');
		  });
		  
	    Schema::table('settings', function(Blueprint $table) {
		    $table->index('active');
		  });
		  
	    Schema::table('statistics', function(Blueprint $table) {
		    $table->index('type');
		  });
		  
	    Schema::table('tags', function(Blueprint $table) {
		    $table->index('language_abbr');
		    $table->index('slug');
		    $table->index('name');
		  });
		  
	    Schema::table('terms', function(Blueprint $table) {
		    $table->index('language_abbr');
		    $table->index('name');
		  });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::table('poll_questions', function(Blueprint $table) {
		    $table->dropIndex('language_abbr');
		    $table->dropIndex('is_multiple');
		    $table->dropIndex('is_active');
		  });
		  
	    Schema::table('promotions', function(Blueprint $table) {
		    $table->dropIndex('language_abbr');
		    $table->dropIndex('product_id');
		    $table->dropIndex('slug');
		    $table->dropIndex('start');
		    $table->dropIndex('end');
		    $table->dropIndex('is_parsed');
		    $table->dropIndex('is_active');
		  });
		  
	    Schema::table('regions', function(Blueprint $table) {
		    $table->dropIndex('language_abbr');
		    $table->dropIndex('region_id');
		    $table->dropIndex('article_region_id');
		    $table->dropIndex('name');
		    $table->dropIndex('slug');
		    $table->dropIndex('is_active');
		  });
		  
	    Schema::table('reviews', function(Blueprint $table) {
		    $table->dropIndex('language_abbr');
		    $table->dropIndex('is_moderated');
		    $table->dropIndex('reviewable_id');
		    $table->dropIndex('reviewable_type');
		    $table->dropIndex('type');
		    $table->dropIndex('product_id');
		  });
		  
	    Schema::table('settings', function(Blueprint $table) {
		    $table->dropIndex('active');
		  });
		  
	    Schema::table('statistics', function(Blueprint $table) {
		    $table->dropIndex('type');
		  });
		  
	    Schema::table('tags', function(Blueprint $table) {
		    $table->dropIndex('language_abbr');
		    $table->dropIndex('slug');
		    $table->dropIndex('name');
		  });
		  
	    Schema::table('terms', function(Blueprint $table) {
		    $table->dropIndex('language_abbr');
		    $table->dropIndex('name');
		  });
    }
}
