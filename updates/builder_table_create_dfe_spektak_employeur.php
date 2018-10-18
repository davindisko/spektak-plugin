<?php namespace Davindisko\Spektak\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateDavindiskoSpektakEmployeur extends Migration
{
    public function up()
    {
        Schema::create('davindisko_spektak_employeur', function($table)
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
        Schema::dropIfExists('davindisko_spektak_employeur');
    }
}
