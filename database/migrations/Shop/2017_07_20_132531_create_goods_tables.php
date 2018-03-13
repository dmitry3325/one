<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('CREATE DATABASE IF NOT EXISTS shop;');

        Schema::getConnection()->setDatabaseName('shop');

        if (!Schema::hasTable('goods')) {
            Schema::create('shop.goods', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('parent_id')->nullable();
                $table->integer('section_id')->nullable();
                $table->string('type')->nullable();
                $table->string('title')->nullable();
                $table->string('h1_title')->nullable();
                $table->string('order_title')->nullable();
                $table->string('path_title')->nullable();
                $table->integer('orderby')->nullable();
                $table->integer('orderby_manual')->nullable();
                $table->integer('orderby_auto')->nullable();
                $table->integer('ignore_orderby_auto')->nullable();
                $table->tinyInteger('hidden')->nullable();
                $table->tinyInteger('stop_sale')->nullable();
                $table->tinyInteger('cancelled')->nullable();
                $table->integer('manid')->nullable();
                $table->string('articul')->nullable();
                $table->string('sarticul')->nullable();
                $table->double('cost')->nullable();
                $table->double('price')->nullable();
                $table->double('final_price')->nullable();
                $table->double('price_opt1')->nullable();
                $table->double('price_opt2')->nullable();
                $table->double('discount')->nullable();

                $table->double('tarif')->nullable();
                $table->double('tarif_discount')->nullable();
                $table->double('min_qty')->nullable();
                $table->tinyInteger('ignore_min_qty')->nullable();
                $table->double('weight')->nullable();
                $table->string('final_price_round_method', 25)->nullable();
                $table->double('nds')->nullable();
                $table->integer('show_qty')->nullable();
                $table->integer('show_buy')->nullable();
                $table->tinyInteger('img_new')->nullable();
                $table->tinyInteger('img_promo')->nullable();
                $table->integer('comments_avg')->nullable();
                $table->integer('comments_num')->nullable();
                $table->integer('picture_id')->nullable();
                $table->string('photos')->nullable();
                $table->string('short_description')->nullable();
                $table->dateTime('first_inventory')->nullable();
                $table->tinyInteger('not_for_ya_market')->nullable();
                $table->tinyInteger('not_for_site_map')->nullable();
                $table->timestamps();

                $table->index('parent_id');
                $table->index('type');
                $table->index('orderby');
                $table->index('picture_id');
                $table->index(['manid','sarticul']);
                $table->index(['section_id','hidden']);
            });
        }
        if (!Schema::hasTable('sections')) {
            Schema::create('shop.sections', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('parent_id')->nullable();
                $table->string('title')->nullable();
                $table->string('h1_title')->nullable();
                $table->string('path_title')->nullable();
                $table->integer('orderby')->nullable();
                $table->tinyInteger('hidden')->nullable();

                $table->integer('picture_id')->nullable();
                $table->string('photos')->nullable();
                $table->string('short_description')->nullable();
                $table->timestamps();

                $table->index('parent_id');
                $table->index('orderby');
                $table->index('hidden');
            });
        }
        if (!Schema::hasTable('filters')) {
            Schema::create('shop.filters', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('parent_id')->nullable();
                $table->integer('section_id')->nullable();
                $table->string('title')->nullable();
                $table->string('h1_title')->nullable();
                $table->string('path_title')->nullable();
                $table->integer('orderby')->nullable();
                $table->tinyInteger('hidden')->nullable();

                $table->integer('picture_id')->nullable();
                $table->string('photos')->nullable();
                $table->string('short_description')->nullable();
                $table->timestamps();

                $table->index('parent_id');
                $table->index(['section_id','hidden']);
            });
        }
        if (!Schema::hasTable('html_pages')) {
            Schema::create('shop.html_pages', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('parent_id')->nullable();
                $table->string('title')->nullable();
                $table->string('h1_title')->nullable();
                $table->string('path_title')->nullable();
                $table->integer('orderby')->nullable();
                $table->tinyInteger('hidden')->nullable();
                $table->integer('picture_id')->nullable();
                $table->string('photos')->nullable();
                $table->string('short_description')->nullable();
                $table->timestamps();

            });
        }
        if (!Schema::hasTable('urls')) {
            Schema::create('shop.urls', function (Blueprint $table) {
                $table->increments('id');
                $table->string('entity', 20)->nullable();
                $table->integer('entity_id')->nullable();
                $table->string('url')->nullable();
                $table->timestamps();

                $table->unique('url');
                $table->index(['entity','entity_id','url']);
            });
        }

        if (!Schema::hasTable('vendors')) {
            Schema::create('shop.vendors', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title')->nullable();
                $table->string('vendor_group')->nullable();
                $table->integer('orderby')->nullable();
                $table->integer('picture_id')->nullable();
                $table->string('photos')->nullable();

                $table->timestamps();

                $table->index('title');
                $table->index('orderby');
            });
        }

        if(!Schema::hasTable('entity_filters')){
            Schema::create('shop.entity_filters', function (Blueprint $table) {
                $table->increments('id');
                $table->string('entity', 20)->nullable();
                $table->integer('entity_id')->nullable();
                $table->integer('num')->nullable();
                $table->string('value')->nullable();
                $table->unsignedInteger('code')->nullable();
                $table->tinyInteger('auto_create')->nullable();
                $table->tinyInteger('hidden')->nullable();
                $table->integer('order_by')->nullable();

                $table->timestamps();

                $table->index(['entity','entity_id']);
                $table->index(['code','num']);
            });
        }

        if (!Schema::hasTable('shop_metadata')) {
            Schema::create('shop.shop_metadata', function (Blueprint $table) {
                $table->increments('id');
                $table->string('entity', 20)->nullable();
                $table->integer('entity_id')->nullable();
                $table->string('key')->nullable();
                $table->longText('value');
                $table->timestamps();
                $table->softDeletes();

                //keys
                $table->unique(['entity', 'entity_id','key']);
            });
        }

        if (!Schema::hasTable('parse')) {
            Schema::create('shop.parse', function (Blueprint $table) {
                $table->increments('id');
                $table->string('url', 255);
                $table->boolean('success');
                $table->timestamps();
                $table->softDeletes();
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
