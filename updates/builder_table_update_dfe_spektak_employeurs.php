<?php namespace DFE\Spektak\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateDfeSpektakEmployeurs extends Migration
{
    public function up()
    {
        Schema::rename('dfe_spektak_employeur', 'dfe_spektak_employeurs');
    }
    
    public function down()
    {
        Schema::rename('dfe_spektak_employeurs', 'dfe_spektak_employeur');
    }
}
