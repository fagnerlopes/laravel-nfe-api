<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SoftDeletes;

class Evento extends Model
{
    use HasFactory;

    protected $fillable = [
        'documento_id',
        'nome_evento',
        'codigo',
        'data_hora_evento',
        'mensagem_retorno',
        'justificativa',
        'recibo'
    ];

    protected $dates = ['deleted_at'];

    public function documento(){
        return $this->belongsTo(Documento::class);
    }
}
