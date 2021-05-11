<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\dfe\nfe\NFeService;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NFeController extends Controller
{
    use ApiResponser;


    public function index(Request $request):JsonResponse
    {
        //return $this->success(['xml' => 'Conteudo do XML'], 'Login bem sucedido.');
        return 'OK';
    }


    public function store(Request $request):JsonResponse
    {
//        try {
            $emitente = auth()->user()->emitente;

            $nfeService = new NFeService($emitente, '55');
//
//            $xml = $nfeService->buildNFeXml($request);
//
//            $chave = $nfeService->getChave();
//
//            $signedXml = $nfeService->assignXml($xml);
//
//            if (isset($signedXml) && !empty($signedXml)) {
//                $result = $nfeService->sendBatch($signedXml);
//            }
//
//            if(!$result['sucesso']) {
//                throw new Exception($result['motivo'], $result['codigo']);
//            }
//
//            if (!is_null($result['recibo'])) {
//                $protocol = $nfeService->getStatus($result['recibo']);
//            }
//
//            if (isset($protocol) && !empty($protocol)) {
//                $authorizedXml = $nfeService->addProtocolIntoXml($signedXml, $protocol);
//
//                $data = [
//                    'sucesso' => $result['sucesso'],
//                    'codigo' => $result['codigo'],
//                    'mensagem' => $result['mensagem'],
//                    'chave' => $chave,
//                    'protocolo' => $protocol,
//                    'xml' => base64_encode($authorizedXml),
//                ];
//            }

            $data = $nfeService->sendAndAuthorizeNfe($request);

            //return response()->json($this->successResponse($data));

        return json_encode($data);


//        } catch (Exception $e) {
//            $erros = [];
//            $errors = $nfeService->getErrors();
//            foreach ($errors as $err) {
//                array_push($erros, $err['desc']);
//            }
//
//            $data = [
//                'sucesso' => false,
//                'codigo' => $e->getCode(),
//                'mensagem' => $e->getMessage(),
//                'erros' => $erros,
//            ];
//            return response()->json($this->errorResponse($data));
//        }

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
