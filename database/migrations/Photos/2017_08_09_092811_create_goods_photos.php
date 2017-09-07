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
        DB::statement('CREATE DATABASE IF NOT EXISTS temp_photos;');

        Schema::getConnection()->setDatabaseName('photos');
        if (!Schema::hasTable('photos')) {
            Schema::create('photos.photos', function (Blueprint $table) {
                $table->increments('id');
                $table->string('entity')->default(null);
                $table->integer('entity_id')->default(0);
                $table->integer('photo_id')->default(1);
                $table->smallInteger('hidden')->default(0);
                $table->smallInteger('width')->nullable();
                $table->smallInteger('height')->nullable();
                $table->string('filetype')->default('');
                $table->integer('watermark_id')->nullable();
                $table->string('path')->nullable();
                $table->unsignedInteger('hash')->nullable();
                $table->timestamps();

                //keys
                $table->index(['entity', 'entity_id']);
                $table->unique(['entity', 'entity_id','photo_id']);
            });
        }

        if (!Schema::hasTable('temp_photos')) {
            Schema::create('photos.temp_photos', function (Blueprint $table) {
                $table->increments('id');
                $table->string('entity')->default(null);
                $table->integer('entity_id')->default(0);
                $table->integer('photo_id')->default(1);
                $table->string('filetype')->default('');
                $table->string('path')->nullable();
                $table->timestamps();

                //keys
                $table->index(['entity', 'entity_id']);
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
