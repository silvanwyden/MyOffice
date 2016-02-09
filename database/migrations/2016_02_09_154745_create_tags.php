<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTags extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('seq');
            $table->string('css_class');
            $table->integer('category_id')->index();
        });
        
        DB::table('tags')->insert(array('name' => 'Nina', 'seq' => '50', 'css_class' => 'btn-success', 'category_id' => 1));
        DB::table('tags')->insert(array('name' => 'Silvan', 'seq' => '50', 'css_class' => 'btn-primary', 'category_id' => 1));
        DB::table('tags')->insert(array('name' => 'Karten Ferien', 'seq' => '50', 'css_class' => 'btn-warning', 'category_id' => 1));
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tags');
    }
}
