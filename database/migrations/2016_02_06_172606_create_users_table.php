<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->char('email', 50)->unique();
                $table->char('password');
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('country')->nullable();
                $table->integer('calling_code')->nullable();
                $table->string('phone')->nullable();
                $table->string('skype')->nullable();
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
        Schema::drop('users');
    }
}
