<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMrtRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mrt_roles', function (Blueprint $table) {
            $table->bigIncrements('role_id');
            $table->string('role_name')->nullable();
            $table->text('role_description')->nullable();
            $table->integer('role_status', false, true)->nullable();
            $table->integer('role_createdby', false, true)->nullable();
            $table->datetime('role_createddate')->nullable();
            $table->integer('role_updatedby', false, true)->nullable();
            $table->timestamp('role_lastupdate')->default(\DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->string('role_ip','15')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mrt_roles');
    }
}
