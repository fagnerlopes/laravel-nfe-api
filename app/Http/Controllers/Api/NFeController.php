<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Documento;
use App\Models\Evento;
use App\Services\dfe\nfe\NFeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NFeController extends Controller
{


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

            $arrayBuiltXml = $nfeService->buildNFeXml($request);

            $documento = $nfeService->assignXml($arrayBuiltXml);

            $evento = $nfeService->sendBatch($documento);

            if(get_class($evento) !== Evento::class){
                return response()->json($evento);
            }

            $protocoloXml = $nfeService->getStatus($evento);

            $documentoAutorizado = $nfeService->addProtocolIntoXml($documento, $protocoloXml);

            $data = [
                'sucesso' => true,
                'mensagem' => 'Autorizado o uso do NF-e',
                'chave' => $documentoAutorizado->chave,
                'protocolo' => $documentoAutorizado->protocolo,
                'xml' => base64_encode($documentoAutorizado->conteudo_xml_autorizado),
                'status' => $documentoAutorizado->status,
                'numero' => $documentoAutorizado->numero,
                'serie' => $documentoAutorizado->serie
            ];

            //$data = $nfeService->sendAndAuthorizeNfe($data);

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


    public function show($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
