<?php

namespace Database\Factories;

use App\Models\Emitente;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EmitenteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Emitente::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'cidade_id' => 1,
            'razao_social' => 'Millennium Sistemas de Gestao - API DF-e',
            'fantasia' => 'Millennium Sistemas de Gestao',
            'cnpj' => '06103611000141',
            'token_ibpt' => '',
            'codigo_csc_id' => '',
            'codigo_csc' => '',
            'inscricao_estadual' => '0290419603',
            'inscricao_municipal' => '83067',
            'conteudo_certificado' => '',
            'caminho_certificado' => '',
            'senha_certificado' => '',
            'codigo_postal' => '950960000',
            'logradouro' => 'AVENIDA RIO BRANCO TESTE',
            'numero' => '1512',
            'bairro' => 'RIO BRANCO',
            'complemento' => 'SALA 2',
            'fone' => '5430252422',
            'email' => 'millennium@millgest.com.br',
            'regime_tributario' => 1,
            'aliquota_geral_simples' => ''

        ];
    }
}
