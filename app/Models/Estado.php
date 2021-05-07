<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SoftDeletes;

class Estado extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nome',
        'codigo_ibge',
        'uf',
        'regiao',
        'perc_aliq_icms_interna',
    ];

    protected $dates = ['deleted_at'];

    public function cidades(){
        return $this->hasMany(Cidade::class);
    }
}
