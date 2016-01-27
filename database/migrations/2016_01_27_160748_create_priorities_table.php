<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrioritiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('priorities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('seq');
        });
        
        DB::table('priorities')->insert(array('name' => 'Highest', 'seq' => '50'));
        DB::table('priorities')->insert(array('name' => 'High', 'seq' => '40'));
        DB::table('priorities')->insert(array('name' => 'Normal', 'seq' => '30'));
        DB::table('priorities')->insert(array('name' => 'Low', 'seq' => '20'));
        DB::table('priorities')->insert(array('name' => 'Lowest', 'seq' => '10'));
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('priorities');
    }
}
