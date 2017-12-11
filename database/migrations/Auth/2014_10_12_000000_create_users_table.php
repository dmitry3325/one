<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('CREATE DATABASE auth');
        Schema::create('auth.users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('is_confirmed')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('auth.user_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('role', 30);
            $table->integer('user_id')->unsigned();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP DATABASE auth');
        Schema::dropIfExists('users');
        Schema::dropIfExists('user_roles');
    }
}
