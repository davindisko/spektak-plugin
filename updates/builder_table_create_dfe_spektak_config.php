<?php namespace Davindisko\Spektak\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateDfeSpektakConfig extends Migration
{
    public function up()
    {
        Schema::create('davindisko_spektak_config', function($table)
        {
            $table->engine = 'InnoDB';
            $table->date('date_anniv');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('davindisko_spektak_config');
    }
}
