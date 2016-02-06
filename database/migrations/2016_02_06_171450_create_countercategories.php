<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountercategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countercategories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('seq');
            $table->string('css_class');
            $table->string('stage_id')->index();
        });
        
        DB::table('countercategories')->insert(array('name' => 'Fitness 16', 'seq' => '50', 'css_class' => 'btn-success', 'stage_id' => 1));
        DB::table('countercategories')->insert(array('name' => 'Bouldern 16', 'seq' => '10', 'css_class' => 'btn-warning', 'stage_id' => 1));
        DB::table('countercategories')->insert(array('name' => 'Ski 15/16', 'seq' => '10', 'css_class' => 'btn-warning', 'stage_id' => 1));
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('countercategories');
    }
}
