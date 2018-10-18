<?php namespace Davindisko\Spektak\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableDeleteDavindiskoSpektakConfig extends Migration
{
    public function up()
    {
        Schema::dropIfExists('davindisko_spektak_config');
    }
    
    public function down()
    {
        Schema::create('davindisko_spektak_config', function($table)
        {
            $table->engine = 'InnoDB';
            $table->date('date_anniv');
        });
    }
}
