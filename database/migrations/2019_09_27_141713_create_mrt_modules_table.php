<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMrtModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mrt_modules', function (Blueprint $table) {
            $table->bigIncrements('module_id');
            $table->string('module_name','100');
            $table->boolean('module_status')->default(1)->change();
            $table->integer('module_createdby')->nullable();
            $table->integer('module_updatedby')->nullable();
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
        Schema::dropIfExists('mrt_modules');
    }
}
