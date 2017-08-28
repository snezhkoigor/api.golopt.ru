<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProductUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('product_user')) {
            Schema::create('product_user', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('product_id')->unsigned()->index()->foreign()->references("id")->on("products");
                $table->integer('user_id')->unsigned()->index()->foreign()->references("id")->on("users");;
                $table->string('trade_account');
                $table->string('broker');
                $table->date('subscribe_date_until');
                $table->enum('type', ['demo', 'real'])->default('real');
                $table->boolean('active')->default(false);
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
        Schema::drop('product_user');
    }
}
