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
			    $table->string('open_interest', 50);
			    $table->string('volume', 50);
			    $table->string('premia', 50);
			    $table->string('spros_1', 50);
			    $table->string('spros_2', 50);
			    $table->string('predlojenie_1', 50);
			    $table->string('predlojenie_2', 50);
			    $table->string('prirost_tekushiy', 50);
			    $table->string('prirost_predydushiy', 50);
			    $table->string('money_obshiy', 50);
			    $table->string('money_tekushiy', 50);
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
