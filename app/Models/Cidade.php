<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cidade extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'estado_id',
        'nome',
        'codigo_ibge',
    ];

    public function estado(){
        return $this->belongsTo(Estado::class);
    }

    public function emitentes(){
        return $this->hasMany(Emitente::class);
    }

}
