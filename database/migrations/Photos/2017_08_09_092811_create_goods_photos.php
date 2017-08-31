<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsPhotos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('CREATE DATABASE IF NOT EXISTS photos;');

        Schema::getConnection()->setDatabaseName('photos');

        if (!Schema::hasTable('photos.photos')) {
            Schema::create('photos.photos', function (Blueprint $table) {
                $table->increments('id');
                $table->string('entity')->default(null);
                $table->integer('entity_id');
                $table->integer('photo_id');
                $table->smallInteger('hidden')->default(0);
                $table->smallInteger('width')->nullable();
                $table->smallInteger('height')->nullable();
                $table->string('filetype')->default('');
                $table->integer('watermark_id')->nullable();
                $table->string('path');
                $table->unsignedInteger('hash');
                $table->timestamps();

                //keys
                $table->unique(['entity', 'entity_id','photo_id']);
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
        //
    }
}
