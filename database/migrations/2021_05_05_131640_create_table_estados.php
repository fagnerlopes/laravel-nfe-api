<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableEstados extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estados', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('codigo_ibge', 10);
            $table->char('uf', 2);
            $table->integer('regiao');
            $table->double('perc_aliq_icms',3,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estados');
    }
}
