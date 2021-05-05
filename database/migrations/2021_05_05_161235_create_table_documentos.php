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
            $table->string('chave', 50);
            $table->string('status', 15);
            $table->integer('numero');
            $table->integer('serie');
            $table->timestamps();
            $table->softDeletes();

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
