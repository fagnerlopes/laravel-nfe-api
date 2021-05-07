<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
            'regiao' => 5,
            'perc_aliq_icms_interna' => 18.00,
        ]);

        DB::table('cidades')->insert([
            'estado_id' => 1,
            'nome' => 'Caxias do Sul',
            'codigo_ibge' => '4305108'
        ]);

        $conteudo_certificado = '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';

        DB::table('emitentes')->insert([
            'cidade_id' => 1,
            'razao_social' => 'Millennium Sistemas de Gestao - Teste Emissao',
            'fantasia' => 'Millennium Sistemas de Gestao',
            'cnpj' => '06103611000141',
            'token_ibpt' => null,
            'codigo_csc_id' => 1,
            'codigo_csc' => '7A9F9680-5ECB-49D0-BBB0-2E4D16D25448',
            'inscricao_estadual' => '0290419603',
            'inscricao_municipal' => '83067',
            'conteudo_certificado' => $conteudo_certificado,
            'caminho_certificado' => null,
            'senha_certificado' => encrypt('123456'),
            'codigo_postal' => '950960000',
            'logradouro' => 'AVENIDA RIO BRANCO TESTE',
            'numero' => '1512',
            'bairro' => 'RIO BRANCO',
            'complemento' => 'SALA 2',
            'fone' => '5430252422',
            'email' => 'fagner@millgest.com.br',
            'regime_tributario' => 1,
            'aliquota_geral_simples' => null,
            'ambiente_fiscal' => 2,
        ]);

        DB::table('emitentes')->insert([
            'cidade_id' => 1,
            'razao_social' => 'Millennium Sistemas de Gestao - Teste Login',
            'fantasia' => 'Millennium Sistemas de Gestao',
            'cnpj' => '04446528000140',
            'token_ibpt' => null,
            'codigo_csc_id' => null,
            'codigo_csc' => null,
            'inscricao_estadual' => '0290419603',
            'inscricao_municipal' => '83067',
            'conteudo_certificado' => $conteudo_certificado,
            'caminho_certificado' => null,
            'senha_certificado' => encrypt('123456'),
            'codigo_postal' => '950960000',
            'logradouro' => 'AVENIDA RIO BRANCO TESTE',
            'numero' => '1512',
            'bairro' => 'RIO BRANCO',
            'complemento' => 'SALA 2',
            'fone' => '5430252422',
            'email' => 'millennium@millgest.com.br',
            'regime_tributario' => 1,
            'aliquota_geral_simples' => null,
            'ambiente_fiscal' => 2,
        ]);

        DB::table('users')->insert([
            'emitente_id' => 1,
            'name' => 'Millennium 1',
            'email' => 'fagner@millgest.com.br',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'password' => bcrypt('123456'),
        ]);

        DB::table('users')->insert([
            'emitente_id' => 2,
            'name' => 'Millennium 2',
            'email' => 'millennium@millgest.com.br',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'password' => bcrypt('123456'),
        ]);
    }
}
