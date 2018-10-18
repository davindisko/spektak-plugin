<?php namespace Davindisko\Spektak\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateDfeSpektakEmployeurs extends Migration
{
    public function up()
    {
        Schema::rename('davindisko_spektak_employeur', 'davindisko_spektak_employeurs');
    }
    
    public function down()
    {
        Schema::rename('davindisko_spektak_employeurs', 'davindisko_spektak_employeur');
    }
}
