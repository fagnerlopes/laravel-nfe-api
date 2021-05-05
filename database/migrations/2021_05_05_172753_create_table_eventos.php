<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableEventos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('documento_id');
            $table->string('nome_evento', 30);
            $table->text('conteudo_xml');
            $table->string('caminho_xml');
            $table->text('conteudo_pdf');
            $table->string('caminho_pdf');
            $table->integer('codigo');
            $table->timestamp('data_hora_evento')->nullable();
            $table->string('protocolo', 20);
            $table->text('mensagem_retorno');
            $table->text('justificativa');
            $table->timestamps();
            $table->softDeletes();

            if (Schema::hasTable('documentos')) {
                $table->foreign('documento_id')->references('id')->on('documentos');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eventos');
    }
}
