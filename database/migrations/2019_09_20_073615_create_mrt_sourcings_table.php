<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMrtSourcingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mrt_sourcings', function (Blueprint $table) {
            $table->bigIncrements('sourcing_id');
            $table->string('sourcing_no_inv','255');
            $table->string('sourcing_title','255')->nullable();
            $table->date('sourcing_startdate')->nullable();
            $table->date('sourcing_enddate')->nullable();
            $table->integer('sourcing_category');
            $table->integer('sourcing_subcategori');
            $table->integer('sourcing_type');
            $table->integer('sourcing_createdby');
            $table->datetime('sourcing_createddate');
            $table->integer('sourcing_updatedby')->nullable();
            $table->timestamp('sourcing_lastupdate')->nullable();
            $table->string('sourcing_ip','15');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mrt_sourcings');
    }
}
