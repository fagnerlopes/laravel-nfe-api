<?php

declare(strict_types=1);

namespace App\Services\dfe;

use App\Models\Emitente;
use Exception;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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


    public function assignXml(string $xml)
    {
        try {
            if(!isset($this->tools) && empty($this->tools)) {
                throw new Exception('Erro ao assinar o xml', 9002);
            }

            return [
                'sucesso' => true,
                'codigo' => 1000,
                'mensagem' => 'XML assinado com sucesso',
                'data' => $this->tools->signNFe((string) $xml)
            ];


        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'codigo' => $e->getCode(),
                'mensagem' => $e->getMessage(),
                'data' => null
            ];
        }
    }


    public function sendBatch(string $signedXml)
    {
        try {
            $idBatch = str_pad('100', 15, '0', STR_PAD_LEFT);

            $response = $this->tools->sefazEnviaLote([$signedXml], $idBatch);

            $st = new Standardize();
            $std = $st->toStd($response);

            if ($std->cStat != 103) {
                throw new Exception($std->xMotivo, $std->cStat);
            }

            return [
                'sucesso' => true,
                'codigo' => $std->cStat,
                'mensagem' => $std->xMotivo,
                'data' => $std->infRec->nRec,
            ];

        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'codigo' => $e->getCode(),
                'motivo' => $e->getMessage(),
                'data' => null,
            ];
        }
    }


    public function getStatus(string $receipt)
    {
        try {

            $xmlProtocolo = $this->tools->sefazConsultaRecibo($receipt);

            return [
                'sucesso' => true,
                'codigo' => 1000,
                'mensagem' => 'Protocolo recebido com sucesso',
                'data' =>  $xmlProtocolo,
            ];

        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'codigo' => $e->getCode(),
                'motivo' => $e->getMessage(),
                'data' => null,
            ];
        }
    }


    public function addProtocolIntoXml(string $signedXml, string $protocol)
    {
        try {
            $request = $signedXml;
            $response = $protocol;


            $xmlAuthorized = Complements::toAuthorize($request, $response);

            return [
                'sucesso' => true,
                'codigo' => 1000,
                'mensagem' => 'Protocolo recebido com sucesso',
                'data' =>  $xmlAuthorized,
            ];

        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'codigo' => $e->getCode(),
                'motivo' => $e->getMessage(),
                'data' => null,
            ];
        }
    }



    public function cancelNFe(array $nfe)
    {
        try {
            $this->tools->model('55');

            $chave = '43210406103611000141550010000001731013821394';
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


    public function sendAndAuthorizeNfe(Request $request)
    {

        $resultXml = $this->buildNFeXml($request);

        if($resultXml['sucesso']){
            $resultXmlSigned = $this->assignXml($resultXml['data']);
        } else {
            return $resultXml;
        }

        if ($resultXmlSigned['sucesso']) {
            $signedXml = $resultXmlSigned['data'];
            $resultSendBatch = $this->sendBatch($signedXml);
        } else {
            return $resultXmlSigned;
        }

        if ($resultSendBatch['sucesso']) {
            $resultStatus = $this->getStatus($resultSendBatch['data']);
        } else {
            return $resultSendBatch;
        }

        if ($resultStatus['sucesso']) {
            $authorizedXml = $this->addProtocolIntoXml($signedXml, $resultStatus['data']);
        } else {
            return $resultStatus;
        }

        return [
            'sucesso' => true,
            'codigo' => 1000,
            'mensagem' => 'Solicitação processada com sucesso',
            'data' =>  $authorizedXml,
        ];

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
