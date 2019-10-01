<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnModuleMenuToMrtModules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mrt_modules', function (Blueprint $table) {
            $table->dropColumn('module_name');
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
            $table->string('module_name','100');
        });
    }
}
