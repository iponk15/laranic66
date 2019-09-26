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
            $table->integer('menu_order', false, true)->nullable();
            $table->integer('menu_status', false, true)->nullable();
            $table->integer('menu_createdby', false, true)->nullable();
            $table->datetime('menu_createddate')->nullable();
            $table->integer('menu_updatedby', false, true)->nullable();
            $table->timestamp('menu_lastupdate')->default(\DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->string('menu_ip','15')->nullable();
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
