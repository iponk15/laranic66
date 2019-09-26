<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMrtGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mrt_groups', function (Blueprint $table) {
            $table->bigIncrements('group_id');
            $table->integer('group_role_id')->unsigned();
            $table->integer('group_menu_id')->unsigned();
            $table->integer('group_status', false, true)->nullable();
            $table->integer('group_createdby', false, true)->nullable();
            $table->integer('group_updatedby', false, true)->nullable();
            $table->string('group_ip','15')->nullable();
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
        Schema::dropIfExists('mrt_groups');
    }
}
