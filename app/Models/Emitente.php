<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SoftDeletes;

class Emitente extends Model
{
    use HasFactory;

    protected $fillable = [
        'cidade_id',
        'razao_social',
        'fantasia',
        'cnpj',
        'token_ibpt',
        'codigo_csc_id',
        'codigo_csc',
        'inscricao_estadual',
        'inscricao_municipal',
        'conteudo_certificado',
        'caminho_certificado',
        'senha_certificado',
        'codigo_postal',
        'logradouro',
        'numero',
        'bairro',
        'complemento',
        'telefone',
        'email',
        'regime_tributario',
        'aliquota_geral_simples',
        'ambiente_fiscal',
    ];

    protected $dates = ['deleted_at'];

    public function cidade(){
        return $this->belongsTo(Cidade::class);
    }

    public function documentos(){
        return $this->hasMany(Documento::class);
    }

    public function users(){
        return $this->hasMany(User::class);
    }
}
