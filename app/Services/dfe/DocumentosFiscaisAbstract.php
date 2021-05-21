<?php

declare(strict_types=1);

namespace App\Services\dfe;

use App\Models\Documento;
use App\Models\Emitente;
use App\Models\Evento;
use Carbon\Carbon;
use Exception;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use NFePHP\Common\Certificate;
use NFePHP\NFe\Common\Standardize;
use NFePHP\NFe\Complements;
use NFePHP\NFe\Make;
use NFePHP\NFe\Tools;
use stdClass;


/**
 * Class DocumentosFiscaisAbstract
 * Essa classe não pode ser instanciada. Deve ser herdada.
 */
abstract class DocumentosFiscaisAbstract implements DocumentosFiscaisInterface
{
    protected $configJson;
    protected $tools;
    protected $nfe;
    protected $modelo;
    protected $documentoId;
    protected $chave;
    protected $emitente;


    public function __construct(Emitente $emitente, string $modelo)
    {

        try {

            if(!($modelo === '55') && !($modelo === '65')) {
                throw new Exception('Somente os modelos 55 e 65 são permitidos', 9001);
            }

            $this->emitente = $emitente;

            $config = [

                "atualizacao" => date('Y-m-d h:i:s'),
                "tpAmb" => $this->emitente->ambiente_fiscal,
                "razaosocial" => $this->emitente->razao_social,
                "siglaUF" => $this->emitente->cidade->estado->uf,
                "cnpj" => $this->emitente->cnpj,
                "schemes" => "PL_008i2",
                "versao" => "4.00",
                "tokenIBPT" => $this->emitente->token_ibpt,
                "CSC" => $this->emitente->codigo_csc,
                "CSCid" => (string) $this->emitente->codigo_csc_id,
                "aProxyConf" => [
                    "proxyIp" => "",
                    "proxyPort" => "",
                    "proxyUser" => "",
                    "proxyPass" => ""
                ]
            ];

            $this->modelo = $modelo;

            $this->configJson = json_encode($config);

            $this->tools = new Tools($this->configJson, Certificate::readPfx(base64_decode($this->emitente->conteudo_certificado), decrypt($this->emitente->senha_certificado)));
            $this->nfe = new Make();

            return [
                'sucesso' => true,
                'codigo' => 1001,
                'mensagem' => 'Processamento Ok'
            ];


        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'codigo' => $e->getCode(),
                'mensagem' => $e->getMessage()
            ];
        }
    }


    abstract public function buildNFeXml(Request $request);


    public function assignXml(array $data)
    {
        if(!is_null($data)) {
            if(!is_null($data['data'])) {
                $xmlsigned = $this->tools->signNFe($data['data']);

                $documentoData = [
                    'chave' => $data['chave'],
                    'numero' => $data['numero'],
                    'serie' => $data['serie'],
                    'conteudo_xml_assinado' => base64_encode($xmlsigned)
                ];

                $documento = $this->emitente->documentos()->create($documentoData);

                if($documento) {
                    return $documento;
                }
            }
        }
    }


    public function sendBatch(Documento $documento)
    {
        $idBatch = str_pad('100', 15, '0', STR_PAD_LEFT);

        $response = $this->tools->sefazEnviaLote([base64_decode($documento->conteudo_xml_assinado)], $idBatch);

        $std = new Standardize();
        $stdClass = $std->toStd($response);

        $eventoData = [
            'nome_evento' => 'envio_lote',
            'codigo' => $stdClass->cStat,
            'mensagem_retorno' => $stdClass->xMotivo,
            'data_hora_evento' => Carbon::parse($stdClass->dhRecbto),
            //'data_hora_evento' => Carbon::createFromFormat('c', $stdClass->dhRecbto),
            'recibo' => $stdClass->infRec->nRec,
        ];

        if($stdClass->cStat == 103 || $stdClass->cStat == 104 || $stdClass->cStat == 105 ){
            $evento = $documento->eventos()->create($eventoData);
        } else {
            $evento = json_decode(json_encode($eventoData));
        }
        return $evento;
    }


    public function getStatus(Evento $evento)
    {
        $protocolo = $this->tools->sefazConsultaRecibo($evento->recibo);

//        if(!is_null($protocolo) || empty($protocolo)) {
//            return $protocolo;
//        }


        $st = new Standardize();
        $stdClass = $st->toStd($protocolo);

        if($stdClass->protNFe->infProt->cStat != 100) {
            return [
                'sucesso' => false,
                'codigo' => $stdClass->protNFe->infProt->cStat,
                'mensagem' => $stdClass->protNFe->infProt->xMotivo,
                'data' => []
            ];
        }

        if($stdClass->protNFe->infProt->cStat == 100){

            DB::beginTransaction();

            try {
                $documento = Documento::find($evento->documento_id);

                $documento->update([
                    'status' => 'autorizado',
                    'protocolo' => $stdClass->protNFe->infProt->nProt,
                    ''
                ]);

                $documento = Documento::find($documento->id);

                $documento->eventos()->create([
                    'nome_evento' => 'consulta_status_documento',
                    'codigo' => $stdClass->protNFe->infProt->cStat,
                    'mensagem_retorno' => $stdClass->protNFe->infProt->xMotivo,
                    'data_hora_evento' => Carbon::createFromFormat('c', $stdClass->protNFe->infProt->dhRecbto)->format('Y-m-d H:m:s'),
                    'recibo' => null,
                ]);

                DB::commit();

                return [
                    'sucesso' => true,
                    'codigo' => 100,
                    'mensagem' => 'Documento fiscal autorizado com sucesso.',
                    'data' => $protocolo
                ];


            } catch (Exception $e) {
                DB::rollBack();
                return [
                    'sucesso' => false,
                    'codigo' => $e->getCode(),
                    'mensagem' => 'Falha ao consultar o status do documento',
                    'correcao' => 'Entre em contato com o suporte',
                    'data' => null
                ];
            }
        }


    }


    public function addProtocolIntoXml(Documento $documento, $protocolo)
    {
        if(is_null($documento->conteudo_xml_assinado) || empty($documento->conteudo_xml_assinado)){
            return [
                'sucesso' => false,
                'codigo' => 9000,
                'mensagem' => 'Erro ao autorizar o documento. Entre em contato com o suporte.',
                'data' => []
            ];
        }

        $authorizedXml = Complements::toAuthorize(base64_decode($documento->conteudo_xml_assinado), $protocolo);

        DB::beginTransaction();
        try {

            $documento->update([
                'conteudo_xml_autorizado' => base64_encode($authorizedXml),
                'conteudo_xml_assinado' => ''
            ]);
            DB::commit();

            $documento = Documento::find($documento->id);

            return [
                'sucesso' => true,
                'codigo' => 6000,
                'mensagem' => 'Documento fiscal autorizado com sucesso.',
                'data' => $documento
            ];

        } catch(Exception $e) {
            DB::rollBack();

            return [
                'sucesso' => false,
                'codigo' => 9000,
                'mensagem' => 'Erro ao autorizar o documento. Entre em contato com o suporte.',
                'data' => []
            ];
        }

    }



    public function cancelDocument(Documento $documento)
    {
        try {
            $this->tools->model('55');

            $chave = $documento->chave;
            $xJust = 'Erro de digitação nos dados dos produtos';
            $nProt = '143210000197293';
            $response = $this->tools->sefazCancela($chave, $xJust, $nProt);

            //padroniza os dados de retorno atraves da classe
            $stdCl = new Standardize($response);
            $std = $stdCl->toStd();

            //em array do XML
            $arr = $stdCl->toArray();

            //em JSON do XML
            $json = $stdCl->toJson();

            //verifique se houve falha
            if ($std->cStat != 128) {
                throw new Exception($std->xMotivo, (int) $std->cStat);

            } else {
                $cStat = $std->retEvento->infEvento->cStat;
                if ($cStat == '101' || $cStat == '135' || $cStat == '155') {
                    $xml = Complements::toAuthorize($this->tools->lastRequest, $response);

                    return [
                        'sucesso' => true,
                        'codigo' => 1000,
                        'mensagem' => 'Protocolo recebido com sucesso',
                        'data' =>  $xml,
                    ];
                }
            }
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'codigo' => $e->getCode(),
                'motivo' => $e->getMessage(),
                'data' => null,
            ];
        }

    }


    public function getErrors()
    {
        return $this->nfe->getErrors();
    }


    public function getChave()
    {
        return $this->nfe->getChave();
    }


}
