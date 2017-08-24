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
        if (!Schema::hasTable('goods_photos')) {
            Schema::create('shop.goods_photos', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('parent_id')->default(0);
                $table->integer('section_id')->default(0);
                $table->string('title')->default('');
                $table->string('h1_title')->default('');
                $table->string('path_title')->default('');
                $table->integer('orderby')->default(0);
                $table->tinyInteger('hidden')->default(0);

                $table->integer('picture_id')->default(0);
                $table->string('photos')->default('');
                $table->string('short_description')->default('');
                $table->timestamps();

                $table->index('parent_id');
                $table->index('section_id');
                $table->index('hidden');
                $table->index('orderby');
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
