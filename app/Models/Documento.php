<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SoftDeletes;

class Documento extends Model
{
    use HasFactory;

    protected $fillable = [
        'emitente_id',
        'chave',
        'status',
        'numero',
        'serie',
        'protocolo',
        'conteudo_xml_assinado',
        'caminho_xml_assinado',
        'conteudo_xml_autorizado',
        'caminho_xml_autorizado',
        'conteudo_pdf',
        'caminho_pdf'
    ];

    protected $hidden = [
        'id',
        'emitente_id'
    ];

    protected $dates = ['deleted_at'];

    public function emitente(){
        return $this->belongsTo(Emitente::class);
    }

    public function eventos(){
        return $this->hasMany(Evento::class);
    }
}
