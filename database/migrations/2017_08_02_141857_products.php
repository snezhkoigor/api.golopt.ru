<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Products extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('path');
                $table->string('image')->nullable();
                $table->text('description')->nullable();
                $table->smallInteger('price');
                $table->enum('price_by', ['month', 'year', 'full'])->default('month');
                $table->char('currency', 3)->default(\App\Dictionary::CURRENCY_USD);
                $table->tinyInteger('demo_access_days')->default(14);
                $table->boolean('active')->default(true);
                $table->boolean('has_demo')->default(false);
                $table->enum('group', ['full', 'lite', 'advisor'])->default('lite');
                $table->text('functional')->nullable();
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
        Schema::dropIfExists('products');
    }
}
