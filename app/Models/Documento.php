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
    ];

    protected $dates = ['deleted_at'];

    public function emitente(){
        return $this->belongsTo(Emitente::class);
    }

    public function eventos(){
        return $this->hasMany(Evento::class);
    }
}
