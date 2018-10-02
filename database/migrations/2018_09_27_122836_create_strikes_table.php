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
        if (!Schema::hasTable('option_strikes')) {
		    Schema::create('option_strikes', function (Blueprint $table) {
			    $table->increments('id');
			    $table->string('symbol', 10);
			    $table->date('expire');
			    $table->integer('parse_date_id');
			    $table->string('type', 3);
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
        Schema::dropIfExists('option_strikes');
    }
}
