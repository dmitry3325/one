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
                $table->index(['entity', 'entity_id']);
                $table->unique(['entity', 'entity_id','photo_id']);
            });
        }

        if (!Schema::hasTable('temp_photos')) {
            Schema::create('photos.temp_photos', function (Blueprint $table) {
                $table->increments('id');
                $table->string('entity')->default(null);
                $table->integer('entity_id');
                $table->integer('photo_id');
                $table->string('filetype')->default('');
                $table->string('path');
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
