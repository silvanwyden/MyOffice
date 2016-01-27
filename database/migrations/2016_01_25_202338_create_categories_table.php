<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('seq');
            $table->string('css_class');
        });
        
        DB::table('categories')->insert(array('name' => 'Private', 'seq' => '50', 'css_class' => 'btn-success'));
        DB::table('categories')->insert(array('name' => 'Company', 'seq' => '10', 'css_class' => 'btn-warning'));
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('categories');
    }
}
