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
                $table->integer('parent_id')->default(0);
                $table->integer('section_id')->default(0);
                $table->string('type')->nullable();
                $table->string('title')->nullable();
                $table->string('h1_title')->nullable();
                $table->string('order_title')->nullable();
                $table->string('path_title')->nullable();
                $table->integer('orderby')->default(0);
                $table->integer('orderby_manual')->default(0);
                $table->integer('orderby_auto')->default(0);
                $table->integer('ignore_orderby_auto')->default(0);
                $table->tinyInteger('hidden')->default(0);
                $table->tinyInteger('stop_sale')->default(0);
                $table->tinyInteger('cancelled')->default(0);
                $table->integer('manid')->default(0);
                $table->string('articul')->nullable();
                $table->string('sarticul')->nullable();
                $table->double('cost')->default(0);
                $table->double('price')->default(0);
                $table->double('final_price')->default(0);
                $table->double('price_opt1')->default(0);
                $table->double('price_opt2')->default(0);
                $table->double('discount')->default(0);

                $table->double('tarif')->default(0);
                $table->double('tarif_discount')->default(0);
                $table->double('min_qty')->nullable();
                $table->tinyInteger('ignore_min_qty')->default(0);
                $table->double('weight')->default(0);
                $table->string('final_price_round_method', 25)->default(0);
                $table->double('nds')->default(0);
                $table->integer('show_qty')->nullable();
                $table->integer('show_buy')->nullable();
                $table->tinyInteger('img_new')->nullable();
                $table->tinyInteger('img_promo')->nullable();
                $table->integer('comments_avg')->nullable();
                $table->integer('comments_num')->nullable();
                $table->integer('picture_id')->default(0);
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
                $table->integer('parent_id')->default(0);
                $table->string('title')->nullable();
                $table->string('h1_title')->nullable();
                $table->string('path_title')->nullable();
                $table->integer('orderby')->default(0);
                $table->tinyInteger('hidden')->default(0);

                $table->integer('picture_id')->default(0);
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
                $table->integer('parent_id')->default(0);
                $table->integer('section_id')->default(0);
                $table->string('title')->nullable();
                $table->string('h1_title')->nullable();
                $table->string('path_title')->nullable();
                $table->integer('orderby')->default(0);
                $table->tinyInteger('hidden')->default(0);

                $table->integer('picture_id')->default(0);
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
                $table->integer('parent_id')->default(0);
                $table->string('title')->nullable();
                $table->string('h1_title')->nullable();
                $table->string('path_title')->nullable();
                $table->integer('orderby')->default(0);
                $table->tinyInteger('hidden')->default(0);
                $table->integer('picture_id')->default(0);
                $table->string('photos')->nullable();
                $table->string('short_description')->nullable();
                $table->timestamps();

            });
        }
        if (!Schema::hasTable('urls')) {
            Schema::create('shop.urls', function (Blueprint $table) {
                $table->increments('id');
                $table->string('entity', 20)->nullable();
                $table->integer('entity_id')->default(0);
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
                $table->integer('orderby')->default(0);
                $table->integer('picture_id')->default(0);
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
                $table->integer('entity_id')->default(0);
                $table->integer('num')->default(0);
                $table->string('value')->nullable();
                $table->unsignedInteger('code')->default(0);
                $table->tinyInteger('auto_create')->default(0);
                $table->tinyInteger('hidden')->default(0);
                $table->integer('order_by')->default(0);

                $table->timestamps();

                $table->index(['entity','entity_id']);
                $table->index(['code','num']);
            });
        }

        if (!Schema::hasTable('shop_metadata')) {
            Schema::create('shop.shop_metadata', function (Blueprint $table) {
                $table->increments('id');
                $table->string('entity', 20)->nullable();
                $table->integer('entity_id')->default(0);
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
