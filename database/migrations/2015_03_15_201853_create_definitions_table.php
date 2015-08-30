<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefinitionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('definitions', function(Blueprint $table)
		{
            $table->engine = 'InnoDB';

            // Internal ID.
			$table->increments('id')->length(9);

            // Definition details.
            $table->string('title', 400);               	// e.g. word, title of a poem, etc.
            $table->string('alt_titles', 400)->nullable();	// Alternate spellings.
            $table->text('data')->nullable();         		// Definition content, poem, etc.
            $table->tinyInteger('type')->unsigned();		// Data type, e.g. word.
            $table->string('sub_type');	    				// Data sub-type, e.g. noun.
            $table->text('tags')->nullable();           	// Descriptive tags.

            $table->tinyInteger('state');
            $table->text('params');
			$table->timestamps();
            $table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('definitions');
	}
}