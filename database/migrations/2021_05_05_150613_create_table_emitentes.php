<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableEmitentes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emitentes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger ('cidade_id');
            $table->string('razao_social', 180);
            $table->string('fantasia', 130);
            $table->string('cnpj', 20);
            $table->string('token_ibpt');
            $table->integer('codigo_csc_id');
            $table->string('codigo_csc');
            $table->string('inscricao_estadual', 20);
            $table->string('inscricao_municipal', 20);
            $table->text('conteudo_certificado');
            $table->text('caminho_certificado');
            $table->text('senha_certificado');
            $table->string('codigo_postal', 20 );
            $table->string('logradouro', 150);
            $table->string('numero', 20);
            $table->string('bairro', 100);
            $table->string('complemento', 50);
            $table->string('fone', 15);
            $table->string('email', 150);
            $table->integer('regime_tributario');
            $table->double('aliquota_geral_simples', 3, 2);
            $table->timestamps();
            $table->softDeletes();


            if (Schema::hasTable('cidades')) {
                $table->foreign('cidade_id')->references('id')->on('cidades');
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
        Schema::dropIfExists('emitentes');
    }
}
