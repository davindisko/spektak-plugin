<?php namespace Davindisko\Spektak\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateDfeSpektakPrestations extends Migration
{
    public function up()
    {
        Schema::table('davindisko_spektak_prestations', function($table)
        {
            $table->decimal('salaire_brut', 7, 2)->change();
            $table->decimal('salaire_net', 7, 2)->change();
            $table->decimal('net_imposable', 7, 2)->change();
        });
    }
    
    public function down()
    {
        Schema::table('davindisko_spektak_prestations', function($table)
        {
            $table->decimal('salaire_brut', 5, 2)->change();
            $table->decimal('salaire_net', 5, 2)->change();
            $table->decimal('net_imposable', 5, 2)->change();
        });
    }
}
