<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {


        DB::table('estados')->insert([
            'nome' => 'Rio Grande do Sul',
            'codigo_ibge' => '43',
            'uf' => 'RS',
            'regiao' => 1,
            'perc_aliq_icms_interna' => 18.00,
            'perc_aliq_icms_interestadual' => 18.00
        ]);

        DB::table('cidades')->insert([
            'estado_id' => 1,
            'nome' => 'Caxias do Sul',
            'codigo_ibge' => '4305108'
        ]);

        DB::table('emitentes')->insert([
            'cidade_id' => 1,
            'razao_social' => 'Millennium Sistemas de Gestao - API DF-e',
            'fantasia' => 'Millennium Sistemas de Gestao',
            'cnpj' => '06103611000141',
            'token_ibpt' => null,
            'codigo_csc_id' => null,
            'codigo_csc' => null,
            'inscricao_estadual' => '0290419603',
            'inscricao_municipal' => '83067',
            'conteudo_certificado' => null,
            'caminho_certificado' => null,
            'senha_certificado' => null,
            'codigo_postal' => '950960000',
            'logradouro' => 'AVENIDA RIO BRANCO TESTE',
            'numero' => '1512',
            'bairro' => 'RIO BRANCO',
            'complemento' => 'SALA 2',
            'fone' => '5430252422',
            'email' => 'millennium@millgest.com.br',
            'regime_tributario' => 1,
            'aliquota_geral_simples' => null
        ]);

        DB::table('users')->insert([
            'emitente_id' => 1,
            'name' => 'Millennium',
            'email' => 'fagner@millgest.com.br',
            'password' => bcrypt('123456'),
        ]);
    }
}
