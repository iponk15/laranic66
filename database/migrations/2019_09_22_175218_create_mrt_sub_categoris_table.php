<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMrtSubCategorisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mrt_sub_categoris', function (Blueprint $table) {
            $table->bigIncrements('subcat_id');
            $table->integer('subcat_category_id');
            $table->string('subcat_code','5');
            $table->string('subcat_name','100');
            $table->integer('subcat_status')->nullable();
            $table->integer('subcat_createdby')->nullable();
            $table->datetime('subcat_createddate')->nullable();
            $table->integer('subcat_updatedby')->nullable();
            $table->timestamp('subcat_lastupdate')->default(\DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->string('subcat_ip','15')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mrt_sub_categoris');
    }
}
