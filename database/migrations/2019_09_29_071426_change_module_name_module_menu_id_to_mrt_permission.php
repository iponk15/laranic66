<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeModuleNameModuleMenuIdToMrtPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mrt_modules', function (Blueprint $table) {
            $table->integer('module_menu_id')->length(10)->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mrt_modules', function (Blueprint $table) {
            $table->dropColumn('module_name');
        });
    }
}
