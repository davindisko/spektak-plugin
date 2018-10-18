<?php namespace Davindisko\Spektak\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateDavindiskoSpektakPrestations extends Migration
{
    public function up()
    {
        Schema::create('davindisko_spektak_prestations', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->date('date_start');
            $table->date('date_end')->nullable();
            $table->string('title');
            $table->string('location')->nullable();
            $table->string('link')->nullable();
            $table->boolean('is_published')->default(0);
            $table->boolean('important')->default(0);
            $table->integer('employeur_id')->nullable();
            $table->integer('nb_cachets')->nullable();
            $table->integer('nb_heures')->nullable();
            $table->decimal('salaire_brut', 7, 2)->nullable();
            $table->decimal('salaire_net', 7, 2)->nullable();
            $table->decimal('net_imposable', 7, 2)->nullable();
            $table->boolean('aem')->nullable();
            $table->boolean('contrat')->nullable();
            $table->boolean('fiche_paie')->nullable();
            $table->boolean('conges_spectacle')->nullable();
            $table->boolean('paiement')->nullable();
            $table->date('date_paiement')->nullable();
            $table->string('type_paiement')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('davindisko_spektak_prestations');
    }
}
