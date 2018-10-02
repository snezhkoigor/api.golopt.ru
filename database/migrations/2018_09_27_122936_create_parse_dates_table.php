<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStrikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('option_parse_dates')) {
		    Schema::create('option_parse_dates', function (Blueprint $table) {
			    $table->increments('id');
			    $table->dateTime('parse_date');
			    $table->string('strike', 50);
			    $table->boolean('odr')->default(false);

			    $table->timestamps();
		    });
	    }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('option_parse_dates');
    }
}
