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
        if (!Schema::hasTable('strikes')) {
		    Schema::create('strikes', function (Blueprint $table) {
			    $table->increments('id');
			    $table->string('symbol', 10);
			    $table->date('expw');
			    $table->timestamp('parse_date');
			    $table->tinyInteger('type');
			    $table->string('strike', 50);

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
        Schema::dropIfExists('strikes');
    }
}
