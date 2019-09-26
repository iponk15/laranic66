<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMrtCategorisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mrt_categoris', function (Blueprint $table) {
            $table->bigIncrements('category_id');
            $table->string('category_code','5');
            $table->string('category_name','100');
            $table->integer('category_status')->nullable();
            $table->integer('category_createdby')->nullable();
            $table->datetime('category_createddate')->nullable();
            $table->integer('category_updatedby')->nullable();
            $table->timestamp('category_lastupdate')->default(\DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->string('category_ip','15')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mrt_categoris');
    }
}
