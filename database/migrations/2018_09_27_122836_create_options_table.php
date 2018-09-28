<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('options')) {
		    Schema::create('options', function (Blueprint $table) {
			    $table->increments('id');
			    $table->string('symbol', 10);
			    $table->date('expw');
			    $table->dateTime('parse_date');
			    $table->string('type', 5);
			    $table->string('strike', 50);
			    $table->string('open_interest_call', 50);
			    $table->string('volume_call', 50);
			    $table->string('premia_call', 50);
			    $table->string('spros_1_call', 50);
			    $table->string('spros_2_call', 50);
			    $table->string('predlojenie_1_call', 50);
			    $table->string('predlojenie_2_call', 50);
			    $table->string('prirost_tekushiy_call', 50);
			    $table->string('prirost_predydushiy_call', 50);
			    $table->string('money_obshiy_call', 50);
			    $table->string('money_tekushiy_call', 50);
			    $table->string('balance_of_day_call', 50);

			    $table->string('open_interest_puts', 50);
			    $table->string('volume_puts', 50);
			    $table->string('premia_puts', 50);
			    $table->string('spros_1_puts', 50);
			    $table->string('spros_2_puts', 50);
			    $table->string('predlojenie_1_puts', 50);
			    $table->string('predlojenie_2_puts', 50);
			    $table->string('prirost_tekushiy_puts', 50);
			    $table->string('prirost_predydushiy_puts', 50);
			    $table->string('money_obshiy_puts', 50);
			    $table->string('money_tekushiy_puts', 50);
			    $table->string('balance_of_day_puts', 50);
			    
			    $table->decimal('fp', 15, 4)->default(0);
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
        Schema::dropIfExists('options');
    }
}
