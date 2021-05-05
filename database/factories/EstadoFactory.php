<?php

namespace Database\Factories;

use App\Models\Estado;
use Illuminate\Database\Eloquent\Factories\Factory;

class EstadoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Estado::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nome' => 'Rio Grande do Sul',
            'codigo_ibge' => '43',
            'uf' => 'RS',
            'regiao' => 1,
            'perc_aliq_interna' => 18.00,
            'perc_aliq_interestadual' => 18.00
        ];
    }
}
