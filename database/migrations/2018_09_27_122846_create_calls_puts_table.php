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
			    $table->decimal('open_interest', 15, 5);
			    $table->decimal('volume', 15, 5);
			    $table->decimal('premia', 15, 5);
			    $table->decimal('spros_1', 15, 5);
			    $table->decimal('spros_2', 15, 5);
			    $table->decimal('predlojenie_1', 15, 5);
			    $table->decimal('predlojenie_2', 15, 5);
			    $table->decimal('prirost_tekushiy', 15, 5);
			    $table->decimal('prirost_predydushiy', 15, 5);
			    $table->decimal('money_obshiy', 15, 5);
			    $table->decimal('money_tekushiy', 15, 5);
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
