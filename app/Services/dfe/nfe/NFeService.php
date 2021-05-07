<?php

namespace App\Services\dfe\nfe;

use App\Services\dfe\DocumentosFiscaisAbstract;
use Illuminate\Http\Request;
use NFePHP\DA\NFe\Danfe;
use Symfony\Component\Console\Input\Input;


class NFeService extends DocumentosFiscaisAbstract
{
    public function buildNFeXml(Request $request):string
    {
        try {

            return json_encode($request->input('emitente.endereco.cep') ?? null);

            $stdInfNfe = new stdClass();
            $stdInfNfe->versao = '4.00'; //versão do layout (string)
            $stdInfNfe->Id = null; //se o Id de 44 digitos não for passado será gerado automaticamente
            $stdInfNfe->pk_nItem = null;//deixe essa variavel sempre como NULL

            $this->nfe->taginfNFe($stdInfNfe);

            $stdIdeNfe = new stdClass();
            $stdIdeNfe->cUF = $this->emitente->cidade->estado->codigo_ibge;
            $stdIdeNfe->cNF = strval(mt_rand(10000000, 99999999));

            $stdIdeNfe->natOp = $request->input('natureza_operacao');
            $stdIdeNfe->indPag = 0; //NÃO EXISTE MAIS NA VERSÃO 4.00
            $stdIdeNfe->mod = $this->modelo;
            $stdIdeNfe->serie = $request->input('serie');
            $stdIdeNfe->nNF = $request->input('numero');
            $stdIdeNfe->dhEmi = $request->input('data_emissao');
            $stdIdeNfe->dhSaiEnt = $request->input('data_entrada_saida');
            $stdIdeNfe->tpNF = $request->input('tipo_operacao');
            $stdIdeNfe->idDest = $request->input('tipo_operacao');
            $stdIdeNfe->cMunFG = $this->emitente->cidade->codigo_ibge;
            $stdIdeNfe->tpImp = 1;
            $stdIdeNfe->tpEmis = $request->input('tipo_operacao');
            $stdIdeNfe->cDV = null;
            $stdIdeNfe->tpAmb = 2;
            $stdIdeNfe->finNFe = $request->input('finalidade_emissao');
            $stdIdeNfe->indFinal = $request->input('consumidor_final');
            $stdIdeNfe->indPres = $request->input('tipo_operacao');
            $stdIdeNfe->indIntermed = null; //usar a partir de 05/04/2021
            $stdIdeNfe->procEmi = 0;
            $stdIdeNfe->verProc = 'DFE_API_V_1.0';
            $stdIdeNfe->dhCont = $request->input('data') ? $request->input('data') : null;
            $stdIdeNfe->xJust = $request->input('motivo') ? $request->input('motivo') : null;

            $this->nfe->tagide($stdIdeNfe);

            $stdEmitente = new stdClass();
            $stdEmitente->xNome = $this->emitente->razao_social;
            $stdEmitente->xFant = $this->emitente->fantasia;
            $stdEmitente->IE = $this->emitente->inscricao_estadual;
            $stdEmitente->IEST =  $request->input('inscricao_estadual_st') ? $request->input('inscricao_estadual_st') : null;
            $stdEmitente->IM = $request->input('inscricao_municipal') ? $request->input('inscricao_municipal') : null;
            $stdEmitente->CNAE = null;
            $stdEmitente->CRT = $this->emitente->regime_tributario;
            $stdEmitente->CNPJ = $this->emitente->cnpj; //indicar apenas um CNPJ ou CPF
            $stdEmitente->CPF = null;

            $this->nfe->tagemit($stdEmitente);

            $stdEnderecoEmitente = new stdClass();
            $stdEnderecoEmitente->xLgr = $request->input('emitente.endereco.logradouro') ?? $this->emitente->logradouro;
            $stdEnderecoEmitente->nro = $request->input('emitente.endereco.numero') ?? $this->emitente->numero;
            $stdEnderecoEmitente->xCpl = $request->input('emitente.endereco.complemento') ?? $this->emitente->complemento;
            $stdEnderecoEmitente->xBairro = $request->input('emitente.endereco.bairro') ?? $this->emitente->bairro;
            $stdEnderecoEmitente->cMun = $request->input('emitente.endereco.codigo_municipio') ?? $this->emitente->cidade->codigo_ibge;
            $stdEnderecoEmitente->xMun = $request->input('emitente.endereco.nome_municipio') ?? $this->emitente->cidade->nome;
            $stdEnderecoEmitente->UF = $request->input('emitente.endereco.uf') ?? $this->emitente->cidade->estado->uf;
            $stdEnderecoEmitente->CEP = $request->input('emitente.endereco.cep') ?? $this->emitente->complemento;
            $stdEnderecoEmitente->cPais = 1058;
            $stdEnderecoEmitente->xPais = "Brasil";
            $stdEnderecoEmitente->fone = $request->input('emitente.endereco.telefone') ?? $this->emitente->telefone;

            $this->nfe->tagenderEmit($stdEnderecoEmitente);

            $stdDestinatário = new stdClass();
            $stdDestinatário->xNome = $request->input('destinatario.nome') ?? null;
            $stdDestinatário->indIEDest = $request->input('destinatario.indicador_inscricao_estadual') ?? 2;
            $stdDestinatário->IE = $request->input('destinatario.inscricao_estadual') ?? null;
            $stdDestinatário->ISUF = $request->input('destinatario.inscricao_suframa') ?? null;
            $stdDestinatário->IM = $request->input('destinatario.inscricao_municipal') ?? null;
            $stdDestinatário->email = $request->input('destinatario.endereco.email') ?? null;
            $stdDestinatário->CNPJ = $request->input('destinatario.cnpj') ?? null;
            $stdDestinatário->CPF = $request->input('destinatario.cpf') ?? null;
            $stdDestinatário->idEstrangeiro = null;

            $this->nfe->tagdest($stdDestinatário);

            $stdEnderecoDestinatario = new stdClass();
            $stdEnderecoDestinatario->xLgr = $request->input('destinatario.endereco.logradouro');
            $stdEnderecoDestinatario->nro = $request->input('destinatario.endereco.numero');
            $stdEnderecoDestinatario->xCpl = $request->input('destinatario.endereco.complemento') ?? null;
            $stdEnderecoDestinatario->xBairro = $request->input('destinatario.endereco.bairro');
            $stdEnderecoDestinatario->cMun = $request->input('destinatario.endereco.codigo_municipio');
            $stdEnderecoDestinatario->xMun = $request->input('destinatario.endereco.nome_municipio');
            $stdEnderecoDestinatario->UF = $request->input('destinatario.endereco.uf');
            $stdEnderecoDestinatario->CEP = $request->input('destinatario.endereco.cep');
            $stdEnderecoDestinatario->cPais = 1058;
            $stdEnderecoDestinatario->xPais = "Brasil";
            $stdEnderecoDestinatario->fone = $request->input('destinatario.endereco.telefone');

            $this->nfe->tagenderDest($stdEnderecoDestinatario);

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
            $stdProdutoItem->vUnTrib = Util::formatNumberForXml(69.00);
            $stdProdutoItem->vProd = (double)$stdProdutoItem->qTrib * $stdProdutoItem->vUnTrib;
            $stdProdutoItem->vFrete = null;
            $stdProdutoItem->vSeg = null;
            $stdProdutoItem->vDesc = null;
            $stdProdutoItem->vOutro = null;
            $stdProdutoItem->indTot = 1;
            $stdProdutoItem->xPed = null;
            $stdProdutoItem->nItemPed = null;
            $stdProdutoItem->nFCI = null;

            $this->nfe->tagprod($stdProdutoItem);


            $stdEspecificacaoST = new stdClass();
            $stdEspecificacaoST->item = 1; //item da NFe
            $stdEspecificacaoST->CEST = '0103500';
            //$stdEspecificacaoST->indEscala = null; //incluido no layout 4.00
            //$stdEspecificacaoST->CNPJFab = null; //incluido no layout 4.00

            $this->nfe->tagCEST($stdEspecificacaoST);

            $stdImpostoItem = new stdClass();
            $stdImpostoItem->item = 1; //item da NFe
            $stdImpostoItem->vTotTrib = 39.16;

            $this->nfe->tagimposto($stdImpostoItem);

        if($this->emitente->regime_tributario !== 1) {

            $stdICMSItem = new stdClass();
            $stdICMSItem->item            = 1; //item da NFe
            $stdICMSItem->orig            = 1;
            $stdICMSItem->CST             = "";
            $stdICMSItem->modBC           = "";
            $stdICMSItem->vBC             = "";
            $stdICMSItem->pICMS           = "";
            $stdICMSItem->vICMS           = "";
            $stdICMSItem->pFCP            = "";
            $stdICMSItem->vFCP            = "";
            $stdICMSItem->vBCFCP          = "";
            $stdICMSItem->modBCST         = "";
            $stdICMSItem->pMVAST          = "";
            $stdICMSItem->pRedBCST        = "";
            $stdICMSItem->vBCST           = "";
            $stdICMSItem->pICMSST         = "";
            $stdICMSItem->vICMSST         = "";
            $stdICMSItem->vBCFCPST        = "";
            $stdICMSItem->pFCPST          = "";
            $stdICMSItem->vFCPST          = "";
            $stdICMSItem->vICMSDeson      = "";
            $stdICMSItem->motDesICMS      = "";
            $stdICMSItem->pRedBC          = "";
            $stdICMSItem->vICMSOp         = "";
            $stdICMSItem->pDif            = "";
            $stdICMSItem->vICMSDif        = "";
            $stdICMSItem->vBCSTRet        = "";
            $stdICMSItem->pST             = "";
            $stdICMSItem->vICMSSTRet      = "";
            $stdICMSItem->vBCFCPSTRet     = "";
            $stdICMSItem->pFCPSTRet       = "";
            $stdICMSItem->vFCPSTRet       = "";
            $stdICMSItem->pRedBCEfet      = "";
            $stdICMSItem->vBCEfet         = "";
            $stdICMSItem->pICMSEfet       = "";
            $stdICMSItem->vICMSEfet       = "";
            $stdICMSItem->vICMSSubstituto = ""; //NT2018.005_1.10_Fevereiro de 2019

             $nfe->tagICMS($stdICMSItem);

        }

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

            if($this->emitente->regime_tributario === 1) {

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

                $this->nfe->tagICMSSN($stdICMSSNItem);
            }


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

            $this->nfe->tagIPI($stdIPIItem);

            $stdPISItem = new stdClass();
            $stdPISItem->item = 1; //item da NFe
            $stdPISItem->CST = '04';
            $stdPISItem->vBC = null;
            $stdPISItem->pPIS = null;
            $stdPISItem->vPIS = null;
            $stdPISItem->qBCProd = null;
            $stdPISItem->vAliqProd = null;

            $this->nfe->tagPIS($stdPISItem);

            $stdCOFINSItem = new stdClass();
            $stdCOFINSItem->item = 1; //item da NFe
            $stdCOFINSItem->CST = '04';
            $stdCOFINSItem->vBC = null;
            $stdCOFINSItem->pCOFINS = null;
            $stdCOFINSItem->vCOFINS = null;
            $stdCOFINSItem->qBCProd = null;
            $stdCOFINSItem->vAliqProd = null;

            $this->nfe->tagCOFINS($stdCOFINSItem);

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

            $this->nfe->tagtransp($stdFrete);

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

            $this->nfe->tagvol($stdVolumes);

            $stdFaturaCobranca = new stdClass();
            $stdFaturaCobranca->nFat = '1233';
            $stdFaturaCobranca->vOrig = 207.00;
            $stdFaturaCobranca->vDesc = 0.00;
            $stdFaturaCobranca->vLiq = 207.00;

            $this->nfe->tagfat($stdFaturaCobranca);

//        $stdDuplicata = new stdClass();
//        $stdDuplicata->nDup = '1233';
//        //$stdDuplicata->dVenc = '2017-08-22';
//        $stdDuplicata->vDup = 207.00;
//
//        $nfe->tagdup($stdDuplicata);

            $stdPagamento = new StdClass();
            $stdPagamento->vTroco = 0.00;

            $this->nfe->tagpag($stdPagamento);

            $stdDetalhePagamento = new stdClass();
            $stdDetalhePagamento->tPag = '01';
            $stdDetalhePagamento->vPag = $stdProdutoItem->vProd; //Obs: deve ser informado o valor pago pelo cliente
            $stdDetalhePagamento->CNPJ = '';
            $stdDetalhePagamento->tBand = '';
            $stdDetalhePagamento->cAut = '';
            $stdDetalhePagamento->tpIntegra = 2; //incluso na NT 2015/002
            $stdDetalhePagamento->indPag = '0'; //0= Pagamento à Vista 1= Pagamento à Prazo

            $this->nfe->tagdetPag($stdDetalhePagamento);

            $stdInfoAdic = new stdClass();
            $stdInfoAdic->infAdFisco = 'informacoes para o fisco';
            $stdInfoAdic->infCpl = 'DOCUMENTO EMITIDO POR ME OU EPP OPTANTE PELO SIMPLES NACIONAL E NAO GERA DIREITO A CREDITO DE ICMS, IPI OU ISS.|Valor Total Aprox. dos Tributos R$ 39,16 ( 18,92%)';

            $this->nfe->taginfAdic($stdInfoAdic);

            $xml = $this->nfe->montaNFe();

            $this->chave = $this->getChave();

            return $xml;

        } catch (Exception $e) {
            exit($e->getMessage());
        }
    }

    public function buildDanfe(string $authorizedXml)
    {

            $xml = file_get_contents('app/storage/orbe_do_brasil_ltda/xml_dfe_files/2021/abril/16/xmlDfe-43210406103611000141550010000001831013821390.xml');
            $logo = 'data://text/plain;base64,'. base64_encode(file_get_contents('app/storage/orbe_do_brasil_ltda/logo_nfe.jpg'));


        try {
            //$danfe = new DanfeSimples($xml);
            //$danfe->debugMode(false);
            // Caso queira mudar a configuracao padrao de impressao
            //Informe o numero DPEC
            /*  $danfe->depecNumber('123456789'); */
            //Configura a posicao da logo
            //$danfe->logoParameters($logo, 'C', false);
            //Configura o tamanho do papel. O padrão é A5,
            //$danfe->papel = 'A4';
            //mandar array com o tamanho para o caso etiquetas de tamanhos personalizados.
            // $danfe->papel = [100, 150];
            //Gera o PDF
            //$pdf = $danfe->render($logo);

            $danfe = new Danfe($authorizedXml);
            $danfe->debugMode(false);
            //$danfe->creditsIntegratorFooter('WEBNFe Sistemas - http://www.webenf.com.br');
            //$danfe->obsContShow(false);
            //$danfe->epec('891180004131899', '14/08/2018 11:24:45'); //marca como autorizada por EPEC
            // Caso queira mudar a configuracao padrao de impressao
            /*  $this->printParameters( $orientacao = '', $papel = 'A4', $margSup = 2, $margEsq = 2 ); */
            //Informe o numero DPEC
            /*  $danfe->depecNumber('123456789'); */
            //Configura a posicao da logo
            $danfe->logoParameters($logo, 'C', false);
            //Gera o PDF
            $pdf = $danfe->render($logo);

            Util::savePDF($pdf, "danfe-{$this->chave}");

            return $pdf;

            //header('Content-Type: application/pdf');
        } catch (InvalidArgumentException $e) {
            echo "Ocorreu um erro durante o processamento :" . $e->getMessage();
        }

    }

}
