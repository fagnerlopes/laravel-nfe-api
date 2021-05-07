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
                throw new Exception('Somente os modelos 55 e 65 são permitidos');
            }

            if(is_null($emitente)) {

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

        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param array $param
     * @return string
     */
    abstract public function buildNFeXml(Request $request):string;

    /**
     * @param string $xml
     * @return string
     */
    public function assignXml(string $xml):string
    {
        try {
            if(!isset($this->tools) && empty($this->tools)) {
                throw new Exception('Erro ao assinar o xml');
            }

            return $this->tools->signNFe((string) $xml);

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param string $signedXml
     * @return string
     * Encapsula o XML assinado em um arquivo de lote
     */
    public function sendBatch(string $signedXml):string
    {
        try {
            $idBatch = str_pad('100', 15, '0', STR_PAD_LEFT);

            $response = $this->tools->sefazEnviaLote([$signedXml], $idBatch);

            $st = new Standardize();
            $std = $st->toStd($response);

            if ($std->cStat != 103) {
                throw new Exception("[$std->cStat] $std->xMotivo");
            }

            return $std->infRec->nRec;

        } catch (Exception $e) {
            //aqui você trata possiveis exceptions do envio
            return($e->getMessage());
        }
    }

    /**
     * @param string $receipt
     * @return string
     * Consulta a situação do documento fiscal na SEFAZ através do recibo
     * retornado pelo método sendBatch()  [envio do lote]
     */
    public function getStatus(string $receipt):string
    {
        try {
            return $this->tools->sefazConsultaRecibo($receipt);
        } catch (Exception $e) {
            return($e->getMessage());
        }
    }

    /**
     * @param string $signedXml
     * @param string $protocol
     * @return string
     * Recebe o XML assinado e o XML do retorno da consulta de status,
     * retornando um XML com o dados da autorização pela SEFAZ
     */
    public function addProtocolIntoXml(string $signedXml, string $protocol):string
    {
        try {
            $request = $signedXml;
            $response = $protocol;

            //header('Content-type: text/xml; charset=UTF-8');
            return Complements::toAuthorize($request, $response);

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


    /**
     * @param array $nfe
     * Array contendo as chaves chave, justificativa e protocolo
     * @return mixed
     */
    public function cancelNFe(array $nfe):string
    {
        try {
            $this->tools->model('55');

            $chave = '43210406103611000141550010000001731013821394';
            $xJust = 'Erro de digitação nos dados dos produtos';
            $nProt = '143210000197293';
            $response = $this->tools->sefazCancela($chave, $xJust, $nProt);

            //padroniza os dados de retorno atraves da classe
            $stdCl = new Standardize($response);

            //em stdClass do XML
            $std = $stdCl->toStd();

            //em array do XML
            $arr = $stdCl->toArray();

            //em JSON do XML
            $json = $stdCl->toJson();

            //verifique se houve falha
            if ($std->cStat != 128) {

            } else {
                $cStat = $std->retEvento->infEvento->cStat;
                if ($cStat == '101' || $cStat == '135' || $cStat == '155') {
                    $xml = Complements::toAuthorize($this->tools->lastRequest, $response);

                    return $xml;
                } else {

                }
            }
        } catch (Exception $e) {
            return($e->getMessage());
        }

    }

    /**
     * @param array $param
     * @return string
     * Método que executa todos os métodos para autorização do documento fiscal
     */
    public function sendAndAuthorizeNfe(array $param): string
    {

        $xml = $this->buildNFeXml($param);

        $signedXml = $this->assignXml($xml);

        var_dump($this->getErrors());

        if (isset($signedXml) && !empty($signedXml)) {
            $receipt = $this->sendBatch($signedXml);
        }

        if (isset($receipt) && !empty($receipt)) {
            $protocol = $this->getStatus($receipt);
        }

        if (isset($protocol) && !empty($protocol)) {
            $authorizedXml = $this->addProtocolIntoXml($signedXml, $protocol);
        }

        return $authorizedXml;

    }

    /**
     * @return array
     * Retorna, se houver, erros de validação no XML
     */
    public function getErrors()
    {
        return $this->nfe->getErrors();
    }

    /**
     * @return string
     * Retorna a chave do documento fiscal composto de 44 caracteres
     */
    public function getChave()
    {
        return $this->nfe->getChave();
    }


}
