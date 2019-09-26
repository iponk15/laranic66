<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMrtVendorPolicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mrt_vendor_polics', function (Blueprint $table) {
            $table->bigIncrements('venpol_id');
            $table->integer('venpol_type');
            $table->string('venpol_title','255');
            $table->text('venpol_content')->nullable();
            $table->integer('venpol_status')->nullable();
            $table->integer('venpol_createdby');
            $table->datetime('venpol_createddate');
            $table->integer('venpol_updatedby')->nullable();
            $table->timestamp('venpol_lastupdate')->default(\DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->string('venpol_ip','15');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mrt_vendor_polics');
    }
}
