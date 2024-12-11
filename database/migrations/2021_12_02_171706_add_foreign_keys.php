<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::table('products', function(Blueprint $table) {
		    $table->index('language_abbr');
		    $table->index('category_id');
		    $table->index('brand_id');
		    $table->index('name');
		    $table->index('slug');
		    $table->index('rating');
		    $table->index('top_rating');
		    $table->index('is_hit');
		    $table->index('is_new');
		    $table->index('is_recommended');
		    $table->index('is_active');
		    $table->index('is_sold');
		    $table->index('is_parsed');
		    $table->index('created_at');
		    
			});
/*
			Schema::table('applications', function(Blueprint $table) {
				$table->foreign('article_id')->references('id')->on('articles');
			});
			
			
			Schema::table('areas', function(Blueprint $table) {
				$table->foreign('region_id')->references('id')->on('regions');
				$table->foreign('area_id')->references('id')->on('areas');
			});
*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::table('products', function(Blueprint $table) {
				
				$table->dropIndex('language_abbr_index');
		    $table->dropIndex('category_id_index');
		    $table->dropIndex('brand_id_index');
		    $table->dropIndex('name_index');
		    $table->dropIndex('slug_index');
		    $table->dropIndex('rating_index');
		    $table->dropIndex('top_rating_index');
		    $table->dropIndex('is_hit_index');
		    $table->dropIndex('is_new_index');
		    $table->dropIndex('is_recommended_index');
		    $table->dropIndex('is_active_index');
		    $table->dropIndex('is_sold_index');
		    $table->dropIndex('is_parsed_index');
		    $table->dropIndex('created_at_index');
				
			});
/*
			Schema::table('applications', function(Blueprint $table) {
				$table->dropForeign('applications_article_id_foreign');
			});
			
			Schema::table('areas', function(Blueprint $table) {
				$table->dropForeign('areas_region_id_foreign');
				$table->dropForeign('areas_area_id_foreign');
			});
*/
    }
}
