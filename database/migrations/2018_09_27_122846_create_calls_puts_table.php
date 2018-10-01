<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCallsPutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('calls_puts')) {
		    Schema::create('calls_puts', function (Blueprint $table) {
			    $table->increments('id');
			    $table->integer('strike_id');
			    $table->string('type', 5);
			    $table->integer('open_interest');
			    $table->integer('volume');
			    $table->integer('premia');
			    $table->integer('spros_1');
			    $table->integer('spros_2');
			    $table->integer('predlojenie_1');
			    $table->integer('predlojenie_2');
			    $table->integer('prirost_tekushiy');
			    $table->integer('prirost_predydushiy');
			    $table->integer('money_obshiy');
			    $table->integer('money_tekushiy');
			    $table->decimal('balance_of_day', 15, 5);
			    $table->boolean('is_balance', 5)->default(false);

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
        Schema::dropIfExists('calls_puts');
    }
}
