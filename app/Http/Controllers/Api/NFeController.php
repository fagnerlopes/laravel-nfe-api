<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\dfe\DocumentosFiscaisAbstract;
use App\Services\dfe\nfe\NFeService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class NFeController extends Controller
{
    use ApiResponser;


    public function index(Request $request)
    {
        //return $this->success(['xml' => 'Conteudo do XML'], 'Login bem sucedido.');
        return 'OK';
    }


    public function store(Request $request)
    {
        $emitente = auth()->user()->emitente;

        $nfeService = new NFeService($emitente, '55');


        return $nfeService->buildNFeXml($request);

        //return $this->success(['configurações' => 'Natureza preenchida com ' . $request->input('natureza_operacao')], 'Login bem sucedido.');

//        $request->whenFilled('natureza_operacao', function ($input) {
//
//        });
//
//        if($request->filled('natureza_operacao')) {
//            return $this->success(['configurações' => 'Natureza preenchida'], 'Login bem sucedido.');
//        }

//        $nfe = $request->all();
//
//        dd($request->natureza_operacao);




        //return $this->success(['configurações' => 'Ok'], 'Login bem sucedido.');
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
