<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Documento;
use App\Services\dfe\nfe\NFeService;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\Request;


class NFeController extends Controller
{
    use ApiResponser;


    public function index(Request $request)
    {
        //return $this->success(['xml' => 'Conteudo do XML'], 'Login bem sucedido.');
        return 'OK';
    }


    public function gerarNFe(Request $request)
    {
        try {
            $emitente = auth()->user()->emitente;

            $nfeService = new NFeService($emitente, '55');

            $data = $nfeService->sendAndAuthorizeNfe($request);

            return response()->json($data);

        } catch (Exception $e) {

            $erros = $nfeService->getErrors();

            $return_erros = [
                'sucesso' => false,
                'codigo' => 9999,
                'mensagem' => 'Consulte o manual de integração',
                'erros_xml' => $erros,
            ];

            if($erros) {
                return response()->json($return_erros);
            } else {
                return response()->json([
                    'sucesso' => false,
                    'codigo' => $e->getCode(),
                    'mensagem' => $e->getMessage(),
                ]);
            }

        }

    }


    public function consultaDfe($chave)
    {
        if(!$chave) {
            throw  new Exception('O parâmetro chave é obrigatório', 9003);
        }

        if(strlen($chave) !== 44) {
            throw  new Exception('A chave informada é inválida. Deve ter 44 caracteres', 9004);
        }

        $documento = Documento::where('chave', $chave)->get();

        if(!$documento) {
            throw  new Exception('O documento solicitado não foi encontrado', 9005);
        }

        return response()->json([
            'sucesso' => true,
            'codigo' => 1000,
            'mensagem' => 'Solicitação processada.',
            'data' => json_decode(json_encode($documento))
        ]);
    }

    public function cancelaDfe($chave)
    {
        //

    }



}
