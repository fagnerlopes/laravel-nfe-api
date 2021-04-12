<?php


namespace App\Services;

use Exception;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Storage;
use NFePHP\Common\Certificate;
use NFePHP\NFe\Common\Standardize;
use NFePHP\NFe\Complements;
use NFePHP\NFe\Make;
use NFePHP\NFe\Tools;
use stdClass;

/**
 * Class NFeService
 * @package App\Services
 */
class NFeService
{
    protected $configJson;
    protected $result;


    /**
     * NFeService constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if ($config) {
            $this->configJson = json_encode($config);
        } else {
            $configDefaul = [
                "atualizacao" => date('Y-m-d h:i:s'),
                "tpAmb" => 2,
                "razaosocial" => "FORZZA CENTRO MECANICO AUTOMOTIVO LTDA:06103611000141",
                "siglaUF" => "RS",
                "cnpj" => "06103611000141",
                "schemes" => "PL_008i2",
                "versao" => "4.00",
                "tokenIBPT" => "",
                "CSC" => "",
                "CSCid" => "",
                "aProxyConf" => [
                    "proxyIp" => "",
                    "proxyPort" => "",
                    "proxyUser" => "",
                    "proxyPass" => ""
                ]
            ];

            $this->configJson = json_encode($configDefaul);

        }
    }


    public function gerarNFe()
    {
        $nfe = new Make();

        $stdInfNfe = new stdClass();
        $stdInfNfe->versao = '4.00'; //versão do layout (string)
        $stdInfNfe->Id = null; //se o Id de 44 digitos não for passado será gerado automaticamente
        $stdInfNfe->pk_nItem = null;//deixe essa variavel sempre como NULL

        $nfe->taginfNFe($stdInfNfe);

        $stdIdeNfe = new stdClass();
        $stdIdeNfe->cUF = 43;
        $stdIdeNfe->cNF = '01382139';
        $stdIdeNfe->natOp = '5.405 - Venda de Mercadoria TESTE SPED-NFE PHP';
        $stdIdeNfe->indPag = 0; //NÃO EXISTE MAIS NA VERSÃO 4.00
        $stdIdeNfe->mod = 55;
        $stdIdeNfe->serie = 1;
        $stdIdeNfe->nNF = 173;
        $stdIdeNfe->dhEmi = self::getDateIso();
        $stdIdeNfe->dhSaiEnt = self::getDateIso();
        $stdIdeNfe->tpNF = 1;
        $stdIdeNfe->idDest = 1;
        $stdIdeNfe->cMunFG = 4305108;
        $stdIdeNfe->tpImp = 1;
        $stdIdeNfe->tpEmis = 1;
        $stdIdeNfe->cDV = null;
        $stdIdeNfe->tpAmb = 2;
        $stdIdeNfe->finNFe = 1;
        $stdIdeNfe->indFinal = 0;
        $stdIdeNfe->indPres = 1;
        $stdIdeNfe->indIntermed = null; //usar a partir de 05/04/2021
        $stdIdeNfe->procEmi = 0;
        $stdIdeNfe->verProc = 'MILL_4.1000.0.83';
        $stdIdeNfe->dhCont = null;
        $stdIdeNfe->xJust = null;

        $nfe->tagide($stdIdeNfe);

        $stdEmitente = new stdClass();
        $stdEmitente->xNome = 'MILLENNIUM Sistemas de Gestao - Posto Autorizado Premium';
        $stdEmitente->xFant = 'MILLMOTORS';
        $stdEmitente->IE = '0290419603';
        $stdEmitente->IEST = null;
        $stdEmitente->IM = '83067';
        $stdEmitente->CNAE = null;
        $stdEmitente->CRT = 1;
        $stdEmitente->CNPJ = '06103611000141'; //indicar apenas um CNPJ ou CPF
        $stdEmitente->CPF = null;

        $nfe->tagemit($stdEmitente);

        $stdEnderecoEmitente = new stdClass();
        $stdEnderecoEmitente->xLgr = "AVENIDA RIO BRANCO TESTE";
        $stdEnderecoEmitente->nro = "1512";
        $stdEnderecoEmitente->xCpl = "SALA 02";
        $stdEnderecoEmitente->xBairro = "RIO BRANCO";
        $stdEnderecoEmitente->cMun = 4305108;
        $stdEnderecoEmitente->xMun = "CAXIAS DO SUL";
        $stdEnderecoEmitente->UF = "RS";
        $stdEnderecoEmitente->CEP = "95096000";
        $stdEnderecoEmitente->cPais = 1058;
        $stdEnderecoEmitente->xPais = "Brasil";
        $stdEnderecoEmitente->fone = "5430252422";

        $nfe->tagenderEmit($stdEnderecoEmitente);

        $stdDestinatário = new stdClass();
        $stdDestinatário->xNome = "NF-E EMITIDA EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL";
        $stdDestinatário->indIEDest = 2;
        $stdDestinatário->IE = null;
        $stdDestinatário->ISUF = null;
        $stdDestinatário->IM = null;
        $stdDestinatário->email = null;
        $stdDestinatário->CNPJ = null; //indicar apenas um CNPJ ou CPF ou idEstrangeiro
        $stdDestinatário->CPF = "00132986078";
        $stdDestinatário->idEstrangeiro = null;

        $nfe->tagdest($stdDestinatário);

        $stdEnderecoDestinatario = new stdClass();
        $stdEnderecoDestinatario->xLgr = "Garibaldi";
        $stdEnderecoDestinatario->nro = "803";
        $stdEnderecoDestinatario->xCpl = null;
        $stdEnderecoDestinatario->xBairro = "Centro";
        $stdEnderecoDestinatario->cMun = 4305108;
        $stdEnderecoDestinatario->xMun = "CAXIAS DO SUL";
        $stdEnderecoDestinatario->UF = "RS";
        $stdEnderecoDestinatario->CEP = "95012270";
        $stdEnderecoDestinatario->cPais = 1058;
        $stdEnderecoDestinatario->xPais = "Brasil";
        $stdEnderecoDestinatario->fone = "0543027179";

        $nfe->tagenderDest($stdEnderecoDestinatario);

        $stdAutorizadoXml = new stdClass();
        $stdAutorizadoXml->CNPJ = null; //indicar um CNPJ ou CPF
        $stdAutorizadoXml->CPF = '00132986078';

        //$nfe->tagautXML($stdAutorizadoXml);

        $stdProdutoItem = new stdClass();
        $stdProdutoItem->item = 1; //item da NFe
        $stdProdutoItem->cProd = 3114;
        $stdProdutoItem->cEAN = "SEM GTIN";
        $stdProdutoItem->cEANTrib = "SEM GTIN";
        $stdProdutoItem->xProd = "FAGNER - MOTOR COMPRESSOR - MFS - RHF5 - 44,93 / 58mm - 6+6 Palhetas";
        $stdProdutoItem->NCM = "84149039";
        $stdProdutoItem->cBenef = null; //incluido no layout 4.00
        $stdProdutoItem->EXTIPI = null;
        $stdProdutoItem->CFOP = 5405;
        $stdProdutoItem->uCom = "UN";
        $stdProdutoItem->qCom = 3.00;
        $stdProdutoItem->vUnCom = 69.00;
        $stdProdutoItem->uTrib = "UN";
        $stdProdutoItem->qTrib = 3.00;
        $stdProdutoItem->vUnTrib = self::xmlFormatNumber(69.00);
        $stdProdutoItem->vProd = (double)$stdProdutoItem->qTrib * $stdProdutoItem->vUnTrib;
        $stdProdutoItem->vFrete = null;
        $stdProdutoItem->vSeg = null;
        $stdProdutoItem->vDesc = null;
        $stdProdutoItem->vOutro = null;
        $stdProdutoItem->indTot = 1;
        $stdProdutoItem->xPed = null;
        $stdProdutoItem->nItemPed = null;
        $stdProdutoItem->nFCI = null;

        $nfe->tagprod($stdProdutoItem);


        $stdEspecificacaoST = new stdClass();
        $stdEspecificacaoST->item = 1; //item da NFe
        $stdEspecificacaoST->CEST = '0103500';
        //$stdEspecificacaoST->indEscala = null; //incluido no layout 4.00
        //$stdEspecificacaoST->CNPJFab = null; //incluido no layout 4.00

        $nfe->tagCEST($stdEspecificacaoST);

        $stdImpostoItem = new stdClass();
        $stdImpostoItem->item = 1; //item da NFe
        $stdImpostoItem->vTotTrib = 39.16;

        $nfe->tagimposto($stdImpostoItem);

//        $stdICMSItem = new stdClass();
//        $stdICMSItem->item            = 1; //item da NFe
//        $stdICMSItem->orig            = 1;
//        $stdICMSItem->CST             = "";
//        $stdICMSItem->modBC           = "";
//        $stdICMSItem->vBC             = "";
//        $stdICMSItem->pICMS           = "";
//        $stdICMSItem->vICMS           = "";
//        $stdICMSItem->pFCP            = "";
//        $stdICMSItem->vFCP            = "";
//        $stdICMSItem->vBCFCP          = "";
//        $stdICMSItem->modBCST         = "";
//        $stdICMSItem->pMVAST          = "";
//        $stdICMSItem->pRedBCST        = "";
//        $stdICMSItem->vBCST           = "";
//        $stdICMSItem->pICMSST         = "";
//        $stdICMSItem->vICMSST         = "";
//        $stdICMSItem->vBCFCPST        = "";
//        $stdICMSItem->pFCPST          = "";
//        $stdICMSItem->vFCPST          = "";
//        $stdICMSItem->vICMSDeson      = "";
//        $stdICMSItem->motDesICMS      = "";
//        $stdICMSItem->pRedBC          = "";
//        $stdICMSItem->vICMSOp         = "";
//        $stdICMSItem->pDif            = "";
//        $stdICMSItem->vICMSDif        = "";
//        $stdICMSItem->vBCSTRet        = "";
//        $stdICMSItem->pST             = "";
//        $stdICMSItem->vICMSSTRet      = "";
//        $stdICMSItem->vBCFCPSTRet     = "";
//        $stdICMSItem->pFCPSTRet       = "";
//        $stdICMSItem->vFCPSTRet       = "";
//        $stdICMSItem->pRedBCEfet      = "";
//        $stdICMSItem->vBCEfet         = "";
//        $stdICMSItem->pICMSEfet       = "";
//        $stdICMSItem->vICMSEfet       = "";
//        $stdICMSItem->vICMSSubstituto = ""; //NT2018.005_1.10_Fevereiro de 2019
//
//        $nfe->tagICMS($stdICMSItem);

//        $stdICMSSTRet = new stdClass();
//        $stdICMSSTRet->item = 1; //item da NFe
//        $stdICMSSTRet->orig = 0;
//        $stdICMSSTRet->CST = '60';
//        $stdICMSSTRet->vBCSTRet = 2.49;
//        $stdICMSSTRet->vICMSSTRet = 0.42;
//        $stdICMSSTRet->vBCSTDest = null;
//        $stdICMSSTRet->vICMSSTDest = null;
//        $stdICMSSTRet->vBCFCPSTRet = null;
//        $stdICMSSTRet->pFCPSTRet = null;
//        $stdICMSSTRet->vFCPSTRet = null;
//        $stdICMSSTRet->pST = null;
//        $stdICMSSTRet->vICMSSubstituto = null;
//        $stdICMSSTRet->pRedBCEfet = null;
//        $stdICMSSTRet->vBCEfet = null;
//        $stdICMSSTRet->pICMSEfet = null;
//        $stdICMSSTRet->vICMSEfet = null;
//
//        $nfe->tagICMSST($stdICMSSTRet);

        $stdICMSSNItem = new stdClass();
        $stdICMSSNItem->item = 1; //item da NFe
        $stdICMSSNItem->orig = 1;
        $stdICMSSNItem->CSOSN = '500';
        $stdICMSSNItem->pCredSN = 2.00;
        $stdICMSSNItem->vCredICMSSN = 20.00;
        $stdICMSSNItem->modBCST = null;
        $stdICMSSNItem->pMVAST = null;
        $stdICMSSNItem->pRedBCST = null;
        $stdICMSSNItem->vBCST = null;
        $stdICMSSNItem->pICMSST = null;
        $stdICMSSNItem->vICMSST = null;
        $stdICMSSNItem->vBCFCPST = null; //incluso no layout 4.00
        $stdICMSSNItem->pFCPST = null; //incluso no layout 4.00
        $stdICMSSNItem->vFCPST = null; //incluso no layout 4.00
        $stdICMSSNItem->vBCSTRet = null;
        $stdICMSSNItem->pST = null;
        $stdICMSSNItem->vICMSSTRet = null;
        $stdICMSSNItem->vBCFCPSTRet = null; //incluso no layout 4.00
        $stdICMSSNItem->pFCPSTRet = null; //incluso no layout 4.00
        $stdICMSSNItem->vFCPSTRet = null; //incluso no layout 4.00
        $stdICMSSNItem->modBC = null;
        $stdICMSSNItem->vBC = null;
        $stdICMSSNItem->pRedBC = null;
        $stdICMSSNItem->pICMS = null;
        $stdICMSSNItem->vICMS = null;
        $stdICMSSNItem->pRedBCEfet = null;
        $stdICMSSNItem->vBCEfet = null;
        $stdICMSSNItem->pICMSEfet = null;
        $stdICMSSNItem->vICMSEfet = null;
        $stdICMSSNItem->vICMSSubstituto = null;

        $nfe->tagICMSSN($stdICMSSNItem);

        $stdIPIItem = new stdClass();
        $stdIPIItem->item = 1; //item da NFe
        $stdIPIItem->clEnq = null;
        $stdIPIItem->CNPJProd = null;
        $stdIPIItem->cSelo = null;
        $stdIPIItem->qSelo = null;
        $stdIPIItem->cEnq = '999';
        $stdIPIItem->CST = '99';
        $stdIPIItem->vIPI = 0.00;
        $stdIPIItem->vBC = 0.00;
        $stdIPIItem->pIPI = 0.00;
        $stdIPIItem->qUnid = null;
        $stdIPIItem->vUnid = null;

        $nfe->tagIPI($stdIPIItem);

        $stdPISItem = new stdClass();
        $stdPISItem->item = 1; //item da NFe
        $stdPISItem->CST = '04';
        $stdPISItem->vBC = null;
        $stdPISItem->pPIS = null;
        $stdPISItem->vPIS = null;
        $stdPISItem->qBCProd = null;
        $stdPISItem->vAliqProd = null;

        $nfe->tagPIS($stdPISItem);

        $stdCOFINSItem = new stdClass();
        $stdCOFINSItem->item = 1; //item da NFe
        $stdCOFINSItem->CST = '04';
        $stdCOFINSItem->vBC = null;
        $stdCOFINSItem->pCOFINS = null;
        $stdCOFINSItem->vCOFINS = null;
        $stdCOFINSItem->qBCProd = null;
        $stdCOFINSItem->vAliqProd = null;

        $nfe->tagCOFINS($stdCOFINSItem);

        // se não for passado a lib irá calcular com base nos itens
        $stdTotaisICMSItem = new stdClass();
        $stdTotaisICMSItem->vBC = 1000.00;
        $stdTotaisICMSItem->vICMS = 1000.00;
        $stdTotaisICMSItem->vICMSDeson = 1000.00;
        $stdTotaisICMSItem->vFCP = 1000.00; //incluso no layout 4.00
        $stdTotaisICMSItem->vBCST = 1000.00;
        $stdTotaisICMSItem->vST = 1000.00;
        $stdTotaisICMSItem->vFCPST = 1000.00; //incluso no layout 4.00
        $stdTotaisICMSItem->vFCPSTRet = 1000.00; //incluso no layout 4.00
        $stdTotaisICMSItem->vProd = 1000.00;
        $stdTotaisICMSItem->vFrete = 1000.00;
        $stdTotaisICMSItem->vSeg = 1000.00;
        $stdTotaisICMSItem->vDesc = 1000.00;
        $stdTotaisICMSItem->vII = 1000.00;
        $stdTotaisICMSItem->vIPI = 1000.00;
        $stdTotaisICMSItem->vIPIDevol = 1000.00; //incluso no layout 4.00
        $stdTotaisICMSItem->vPIS = 1000.00;
        $stdTotaisICMSItem->vCOFINS = 1000.00;
        $stdTotaisICMSItem->vOutro = 1000.00;
        $stdTotaisICMSItem->vNF = 1000.00;
        $stdTotaisICMSItem->vTotTrib = 1000.00;

        //$nfe->tagICMSTot();

        $stdFrete = new stdClass();
        $stdFrete->modFrete = 9;

        $nfe->tagtransp($stdFrete);

//        $stdTransportadora = new stdClass();
//        $stdTransportadora->xNome = 'Rodo Fulano';
//        $stdTransportadora->IE = '12345678901';
//        $stdTransportadora->xEnder = 'Rua Um, sem numero';
//        $stdTransportadora->xMun = 'Cotia';
//        $stdTransportadora->UF = 'SP';
//        $stdTransportadora->CNPJ = '12345678901234';//só pode haver um ou CNPJ ou CPF, se um deles é especificado o outro deverá ser null
//        $stdTransportadora->CPF = null;
//
//        $nfe->tagtransporta($stdTransportadora);

//        $stdVeiculoTrator = new stdClass();
//        $stdVeiculoTrator->placa = 'ABC1111';
//        $stdVeiculoTrator->UF = 'RJ';
//        $stdVeiculoTrator->RNTC = '999999';
//
//        $nfe->tagveicTransp($stdVeiculoTrator);
//
//        $stdReboque = new stdClass();
//        $stdReboque->placa = 'BCB0897';
//        $stdReboque->UF = 'SP';
//        $stdReboque->RNTC = '123456';
//
//        $nfe->tagreboque($stdReboque);

        $stdVolumes = new stdClass();
        $stdVolumes->item = 1; //indicativo do numero do volume
        $stdVolumes->qVol = 3;
        $stdVolumes->esp = '';
        $stdVolumes->marca = '';
        $stdVolumes->nVol = '';
        $stdVolumes->pesoL = 0.00;
        $stdVolumes->pesoB = 0.00;

        $nfe->tagvol($stdVolumes);

        $stdFaturaCobranca = new stdClass();
        $stdFaturaCobranca->nFat = '1233';
        $stdFaturaCobranca->vOrig = 207.00;
        $stdFaturaCobranca->vDesc = 0.00;
        $stdFaturaCobranca->vLiq = 207.00;

        $nfe->tagfat($stdFaturaCobranca);

//        $stdDuplicata = new stdClass();
//        $stdDuplicata->nDup = '1233';
//        //$stdDuplicata->dVenc = '2017-08-22';
//        $stdDuplicata->vDup = 207.00;
//
//        $nfe->tagdup($stdDuplicata);

        $stdPagamento = new StdClass();
        $stdPagamento->vTroco = 0.00;

        $nfe->tagpag($stdPagamento);

        $stdDetalhePagamento = new stdClass();
        $stdDetalhePagamento->tPag = '01';
        $stdDetalhePagamento->vPag = $stdProdutoItem->vProd; //Obs: deve ser informado o valor pago pelo cliente
        $stdDetalhePagamento->CNPJ = '';
        $stdDetalhePagamento->tBand = '';
        $stdDetalhePagamento->cAut = '';
        $stdDetalhePagamento->tpIntegra = 2; //incluso na NT 2015/002
        $stdDetalhePagamento->indPag = '0'; //0= Pagamento à Vista 1= Pagamento à Prazo

        $nfe->tagdetPag($stdDetalhePagamento);

        $stdInfoAdic = new stdClass();
        $stdInfoAdic->infAdFisco = 'informacoes para o fisco';
        $stdInfoAdic->infCpl = '>DOCUMENTO EMITIDO POR ME OU EPP OPTANTE PELO SIMPLES NACIONAL E NAO GERA DIREITO A CREDITO DE ICMS, IPI OU ISS.|Valor Total Aprox. dos Tributos R$ 39,16 ( 18,92%)';

        $nfe->taginfAdic($stdInfoAdic);

        header('Content-type: text/xml; charset=UTF-8');

        $xml = $nfe->montaNFe();

        $errors = $nfe->getErrors();

        $chave = $nfe->getChave();

        $this->result = [
            'chave_nfe' => $chave,
            'xml' => $xml

        ];

        return $this->result;


    }

    /**
     * @param $xml
     * @return string
     */
    public function assinarXml($xml)
    {
        try {

            $certificadoDigital = Storage::get('certificado.pfx');
            $tools = new Tools($this->configJson, Certificate::readPfx($certificadoDigital, '123456'));

            return $xmlAssinado = $tools->signNFe($xml); // O conteúdo do XML assinado fica armazenado na variável $xmlAssinado
        } catch (\Exception $e) {
            //aqui você trata possíveis exceptions da assinatura
            exit($e->getMessage());
        }
    }

    /**
     * @param $recibo
     * @return string
     */
    public function consultarStatus($recibo)
    {
        try {
            $certificadoDigital = Storage::get('certificado.pfx');
            $tools = new Tools($this->configJson, Certificate::readPfx($certificadoDigital, '123456'));
            $protocolo = $tools->sefazConsultaRecibo($recibo);
            return $protocolo;
        } catch (Exception $e) {
            //aqui você trata possíveis exceptions da consulta
            exit($e->getMessage());
        }
    }

    /**
     * @param $xmlAssinado
     * @return mixed
     */
    public function enviarLote($xmlAssinado)
    {
        try {
            $certificadoDigital = Storage::get('certificado.pfx');
            $tools = new Tools($this->configJson, Certificate::readPfx($certificadoDigital, '123456'));
            $idLote = str_pad(100, 15, '0', STR_PAD_LEFT); // Identificador do lote
            $resp = $tools->sefazEnviaLote([$xmlAssinado], $idLote);

            $st = new Standardize();
            $std = $st->toStd($resp);
            if ($std->cStat != 103) {
                //erro registrar e voltar
                exit("[$std->cStat] $std->xMotivo");
            }
            $recibo = $std->infRec->nRec; // Vamos usar a variável $recibo para consultar o status da nota

            return $recibo;
        } catch (Exception $e) {
            //aqui você trata possiveis exceptions do envio
            exit($e->getMessage());
        }
    }

    /**
     * @param $xmlAssinado
     * @param $protocolo
     * @return string
     */
    public function inserirProtocolo($xmlAssinado, $protocolo)
    {
        $request = $xmlAssinado;
        $response = $protocolo;

        try {
            $xml = Complements::toAuthorize($request, $response);
            header('Content-type: text/xml; charset=UTF-8');
            return $xml;
        } catch (Exception $e) {
            return "Erro: " . $e->getMessage();
        }
    }

    /**
     * @param $xml
     * @return string
     */
    public function salvarXML($xml, $chave)
    {
        try {
            //Storage::put('xmlDfe-' + $chave, $xml);
            //Storage::put('xmlDfe-' + $chave, $xml);
            Storage::disk('local')->put('arquivos_protocolados/xmlDfe-' . $chave . '.xml', (string) $xml);

        } catch( Exception $e) {
            return "Erro: " . $e->getMessage();
        }
    }

    public function cancelarNFe()
    {
        try {
            $certificadoDigital = Storage::get('certificado.pfx');
            $tools = new Tools($this->configJson, Certificate::readPfx($certificadoDigital, '123456'));
            $tools->model('55');

            $chave = '43210406103611000141550010000001731013821394';
            $xJust = 'Erro de digitação nos dados dos produtos';
            $nProt = '143210000197293';
            $response = $tools->sefazCancela($chave, $xJust, $nProt);

            //você pode padronizar os dados de retorno atraves da classe abaixo
            //de forma a facilitar a extração dos dados do XML
            //NOTA: mas lembre-se que esse XML muitas vezes será necessário,
            //      quando houver a necessidade de protocolos
            $stdCl = new Standardize($response);
            //nesse caso $std irá conter uma representação em stdClass do XML
            $std = $stdCl->toStd();
            //nesse caso o $arr irá conter uma representação em array do XML
            $arr = $stdCl->toArray();
            //nesse caso o $json irá conter uma representação em JSON do XML
            $json = $stdCl->toJson();

            //verifique se o evento foi processado
            if ($std->cStat != 128) {
                //houve alguma falha e o evento não foi processado
                //TRATAR
            } else {
                $cStat = $std->retEvento->infEvento->cStat;
                if ($cStat == '101' || $cStat == '135' || $cStat == '155') {
                    //SUCESSO PROTOCOLAR A SOLICITAÇÂO ANTES DE GUARDAR
                    $xml = Complements::toAuthorize($tools->lastRequest, $response);
                    //grave o XML protocolado
                    return $xml;
                } else {
                    //houve alguma falha no evento
                    //TRATAR
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

    }

    /**
     * @param float $number
     * @param int $decimals
     * @return string
     */
    public static function xmlFormatNumber(float $number, int $decimals = 2)
    {
        return number_format((float)$number, $decimals, ".", "");
    }

    /**
     * @return string
     */
    public static function getDateIso(): string
    {
        $fuso = new DateTimeZone('America/Sao_Paulo');
        $date = new DateTime();
        $date->setTimezone($fuso);

        return $date->format('Y-m-d\TH:i:sP');
    }

}
