<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBasketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('CREATE DATABASE IF NOT EXISTS baskets;');

        foreach(['','_history'] as $sub_name){
            if (!Schema::hasTable('baskets.heads'.$sub_name)) {
                Schema::create('baskets.heads'.$sub_name, function (Blueprint $table) {
                    $table->increments('head_id'); /** Номер заказа */
                    $table->string('sid', 32)->nullable(); /** Уник юзвера дается при первом посещении */

                    $table->string('firstname')->nullable();
                    $table->string('lastname')->nullable();
                    $table->string('email', 32)->nullable();
                    $table->string('mobile_phone', 20)->nullable();

                    $table->string('method')->nullable(); /** Метод доставки */

                    $table->integer('city')->nullable();
                    $table->string('city_text')->nullable();
                    $table->string('region')->nullable();
                    $table->string('index')->nullable();
                    $table->string('metro', 50)->nullable();
                    $table->string('street')->nullable();
                    $table->string('house')->nullable();
                    $table->string('corpus')->nullable();
                    $table->string('building')->nullable();
                    $table->string('entrance')->nullable();
                    $table->string('floor')->nullable();
                    $table->string('room')->nullable();
                    $table->string('doorcode')->nullable();
                    $table->smallInteger('no_lift')->nullable();

                    /** Для случая доставки транспортной компанией */
                    $table->string('transport_style')->nullable();
                    $table->string('transport_reciever')->nullable();
                    $table->string('transport_company')->nullable();
                    $table->string('transport_company_phone')->nullable();
                    $table->string('transport_company_address')->nullable();

                    /** Метод оплаты */
                    $table->integer('paytype_style')->nullable();
                    /** Юр лицо или физ */
                    $table->integer('client_type')->nullable();

                    /** Для юриков */
                    $table->string('company', 50)->nullable();
                    $table->string('company_inn', 50)->nullable();
                    $table->string('company_kpp', 50)->nullable();
                    $table->string('company_rs', 50)->nullable();
                    $table->string('company_bik', 50)->nullable();
                    $table->string('company_bank')->nullable();
                    $table->string('company_ks', 50)->nullable();
                    $table->string('company_address')->nullable();

                    /** Коммент к заказу */
                    $table->string('comments')->nullable();

                    $table->smallInteger('need_call')->nullable();

                    $table->smallInteger('last_step')->nullable();

                    $table->string('ip', 50)->nullable();

                    $table->integer('manager')->nullable();

                    /** Статус заказа */
                    $table->integer('status')->nullable();

                    $table->timestamps();

                    $table->index('sid');
                    $table->index('status');
                });
            }

            if (!Schema::hasTable('baskets.rows'.$sub_name)) {
                Schema::create('baskets.rows'.$sub_name, function (Blueprint $table) {
                    $table->increments('row_id');
                    $table->integer('head_id');
                    $table->integer('good_id');
                    $table->integer('manid');
                    $table->string('sarticul', 150);
                    $table->string('vendor', 50);
                    $table->string('articul', 50);
                    $table->string('title');
                    $table->integer('section_id');
                    $table->integer('picture_id');

                    $table->integer('qty');
                    $table->integer('min_qty');
                    $table->integer('unit');

                    $table->double('discount');
                    $table->double('cost');
                    $table->double('price');
                    $table->double('final_price');

                    $table->integer('gift')->nullable();
                    $table->integer('manager')->nullable();
                    $table->integer('status')->default(0);

                    $table->timestamps();

                    $table->index('head_id');
                    $table->index('good_id');
                    $table->index(['manid','sarticul']);
                });
            }

        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP DATABASE baskets');
    }
}
