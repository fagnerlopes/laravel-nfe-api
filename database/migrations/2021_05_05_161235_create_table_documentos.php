<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDocumentos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('emitente_id');
            $table->string('chave', 50)->unique();
            $table->string('status', 15)->nullable();
            $table->integer('numero');
            $table->integer('serie');
            $table->string('protocolo', 20)->nullable();
            $table->text('conteudo_xml_assinado');
            $table->string('caminho_xml_assinado')->nullable();
            $table->text('conteudo_xml_autorizado')->nullable();
            $table->string('caminho_xml_autorizado')->nullable();
            $table->text('conteudo_pdf')->nullable();
            $table->string('caminho_pdf')->nullable();
            $table->timestamps();

            if (Schema::hasTable('emitentes')) {
                $table->foreign('emitente_id')->references('id')->on('emitentes');
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
        Schema::dropIfExists('documentos');
    }
}
