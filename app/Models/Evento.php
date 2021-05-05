<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SoftDeletes;

class Evento extends Model
{
    use HasFactory;

    protected $fillable = [
        'emitente_id',
        'chave',
        'status',
        'numero',
        'serie'
    ];

    protected $dates = ['deleted_at'];

    public function documento(){
        return $this->belongsTo(Documento::class);
    }
}
