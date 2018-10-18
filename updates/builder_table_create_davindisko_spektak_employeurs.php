<?php namespace Davindisko\Spektak\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateDavindiskoSpektakEmployeurs extends Migration
{
    public function up()
    {
        Schema::create('davindisko_spektak_employeurs', function($table)
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
        Schema::dropIfExists('davindisko_spektak_employeurs');
    }
}
