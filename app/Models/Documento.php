<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;

    protected $fillable = [
        'emitente_id',
        'chave',
        'status',
        'numero',
        'serie'
    ];

    public function emitente(){
        return $this->belongsTo(Emitente::class);
    }
}
