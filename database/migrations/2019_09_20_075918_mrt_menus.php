<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MrtMenus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mrt_menus', function (Blueprint $table) {
            $table->bigIncrements('menu_id');
            $table->string('menu_parent')->nullable();
            $table->string('menu_nama')->nullable();
            $table->string('menu_link')->nullable();
            $table->string('menu_icon')->nullable();
            $table->string('menu_ip','15')->nullable();
            $table->integer('menu_order', false, true)->nullable();
            $table->boolean('menu_status', false, true)->nullable();
            $table->integer('created_by', false, true)->nullable();
            $table->integer('updated_by', false, true)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mrt_menus');
    }
}
