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
                $table->string('type')->default('');
                $table->string('title')->default('');
                $table->string('h1_title')->default('');
                $table->string('order_title')->default('');
                $table->string('path_title')->default('');
                $table->integer('orderby')->default(0);
                $table->integer('orderby_manual')->default(0);
                $table->integer('orderby_auto')->default(0);
                $table->integer('ignore_orderby_auto')->default(0);
                $table->tinyInteger('hidden')->default(0);
                $table->tinyInteger('stop_sale')->default(0);
                $table->tinyInteger('cancelled')->default(0);
                $table->integer('manid')->default(0);
                $table->string('articul')->default('');
                $table->string('sarticul')->default('');
                $table->double('cost')->default(0);
                $table->double('price')->default(0);
                $table->double('final_price')->default(0);
                $table->double('price_opt1')->default(0);
                $table->double('price_opt2')->default(0);
                $table->double('discount')->default(0);

                for ($i = 1; $i <= Filters::COUNT; $i++) {
                    $table->string('filter_' . $i)->default('');
                    $table->string('filter_' . $i . '_id')->default(0);
                }

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
                $table->string('photos')->default('');
                $table->dateTime('first_inventory')->nullable();
                $table->tinyInteger('not_for_ya_market')->nullable();
                $table->tinyInteger('not_for_site_map')->nullable();
                $table->timestamps();

                $table->index('parent_id');
                $table->index('section_id');
                $table->index('type');
                $table->index('orderby');
                $table->index('hidden');
                $table->index('picture_id');
                $table->index(['manid','sarticul']);
                $table->index(['section_id','hidden']);
            });
        }
        if (!Schema::hasTable('sections')) {
            Schema::create('shop.sections', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('parent_id')->default(0);
                $table->string('title')->default('');
                $table->string('h1_title')->default('');
                $table->string('path_title')->default('');
                $table->integer('orderby')->default(0);
                $table->tinyInteger('hidden')->default(0);

                for ($i = 1; $i <= Filters::COUNT; $i++) {
                    $table->string('filter_' . $i)->default('');
                    $table->string('filter_' . $i . '_id')->default(0);
                }

                $table->integer('picture_id')->default(0);
                $table->string('photos')->default('');
                $table->string('short_description')->default('');
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
                $table->string('title')->default('');
                $table->string('h1_title')->default('');
                $table->string('path_title')->default('');
                $table->integer('orderby')->default(0);
                $table->tinyInteger('hidden')->default(0);

                for ($i = 1; $i <= Filters::COUNT; $i++) {
                    $table->string('filter_' . $i)->default('');
                    $table->string('filter_' . $i . '_id')->default(0);
                }

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
        if (!Schema::hasTable('html_pages')) {
            Schema::create('shop.html_pages', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('parent_id')->default(0);
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
                $table->index('orderby');
                $table->index('hidden');
            });
        }
        if (!Schema::hasTable('urls')) {
            Schema::create('shop.urls', function (Blueprint $table) {
                $table->increments('id');
                $table->string('entity')->default('');
                $table->integer('entity_id');
                $table->string('url')->default('');
                $table->timestamps();

                $table->unique('url');
                $table->index(['entity','entity_id','url']);
            });
        }

        if (!Schema::hasTable('vendors')) {
            Schema::create('shop.vendors', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title')->default('');
                $table->string('vendor_group')->default('');
                $table->integer('orderby')->default(0);
                $table->integer('picture_id')->default(0);
                $table->string('photos')->default('');

                $table->timestamps();

                $table->index('title');
                $table->index('orderby');
            });
        }

        if (!Schema::hasTable('shop_metadata')) {
            Schema::create('shop.shop_metadata', function (Blueprint $table) {
                $table->increments('id');
                $table->string('entity')->default('');
                $table->integer('entity_id');
                $table->string('key')->default('');
                $table->longText('value');
                $table->timestamps();

                //keys
                $table->unique(['entity', 'entity_id','key']);
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
