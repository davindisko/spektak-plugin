<?php namespace DFE\Spektak\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateDfeSpektakEmployeur extends Migration
{
    public function up()
    {
        Schema::create('dfe_spektak_employeur', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('nom');
            $table->text('adresse')->nullable();
            $table->text('contact')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('dfe_spektak_employeur');
    }
}