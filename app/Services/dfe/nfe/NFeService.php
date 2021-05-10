<?php

namespace App\Services\dfe\nfe;

use App\Services\dfe\DocumentosFiscaisAbstract;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use NFePHP\DA\NFe\Danfe;
use stdClass;
use Symfony\Component\Console\Input\Input;


class NFeService extends DocumentosFiscaisAbstract
{
    public function buildNFeXml(Request $request):string
    {
        try {

            $csts_icms_st = new Collection(['10', '60', 'otheridshere']);

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
            $stdDestinatário->xNome = $request->input('destinatario.nome');
            $stdDestinatário->indIEDest = $request->input('destinatario.indicador_inscricao_estadual') ?? 2;
            $stdDestinatário->IE = $request->input('destinatario.inscricao_estadual') ?? null;
            $stdDestinatário->ISUF = $request->input('destinatario.inscricao_suframa') ?? null;
            $stdDestinatário->IM = $request->input('destinatario.inscricao_municipal') ?? null;
            $stdDestinatário->email = $request->input('destinatario.endereco.email') ?? null;
            $stdDestinatário->CNPJ = $request->input('destinatario.cnpj') ?? null;
            $stdDestinatário->CPF = $request->input('destinatario.cpf') ?? null;
            $stdDestinatário->idEstrangeiro = $request->input('destinatario.id_destinatario') ?? null;
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


            foreach ($request->input('itens') as $key => $item) {

                $stdProdutoItem = new stdClass();
                $stdProdutoItem->item = $request->input("itens.{$key}.numero_item"); //item da NFe
                $stdProdutoItem->cProd = $request->input("itens.{$key}.codigo_produto");
                $stdProdutoItem->cEAN = $request->input("itens.{$key}.codigo_barras_comercial") ?? "SEM GTIN";
                $stdProdutoItem->cEANTrib = $request->input("itens.{$key}.codigo_barras_tributavel") ?? "SEM GTIN";
                $stdProdutoItem->xProd = $request->input("itens.{$key}.descricao");
                $stdProdutoItem->NCM = $request->input("itens.{$key}.codigo_ncm");
                $stdProdutoItem->cBenef = $request->input("itens.{$key}.codigo_beneficio_fiscal") ?? null; //incluido no layout 4.00
                $stdProdutoItem->EXTIPI = $request->input("itens.{$key}.codigo_ex_tipi") ?? null;
                $stdProdutoItem->CFOP = $request->input("itens.{$key}.cfop");
                $stdProdutoItem->uCom = $request->input("itens.{$key}.unidade_comercial");
                $stdProdutoItem->qCom = $request->input("itens.{$key}.quantidade_comercial");
                $stdProdutoItem->vUnCom = $request->input("itens.{$key}.valor_unitario_comercial");
                $stdProdutoItem->uTrib = $request->input("itens.{$key}.unidade_tributavel");
                $stdProdutoItem->qTrib = $request->input("itens.{$key}.quantidade_tributavel");
                $stdProdutoItem->vUnTrib = $request->input("itens.{$key}.valor_unitario_tributavel");
                $stdProdutoItem->vProd = $request->input("itens.{$key}.valor_bruto");
                $stdProdutoItem->vFrete = $request->input("itens.{$key}.valor_frete") ?? null;
                $stdProdutoItem->vSeg = $request->input("itens.{$key}.valor_seguro") ?? null;
                $stdProdutoItem->vDesc = $request->input("itens.{$key}.valor_desconto") ?? null;
                $stdProdutoItem->vOutro = $request->input("itens.{$key}.valor_outras_despesas") ?? null;
                $stdProdutoItem->indTot = $request->input("itens.{$key}.inclui_no_total");
                $stdProdutoItem->xPed = $request->input("itens.{$key}.pedido_compra.numero") ?? null;
                $stdProdutoItem->nItemPed = $request->input("itens.{$key}.pedido_compra..item") ?? null;
                $stdProdutoItem->nFCI = $request->input("itens.{$key}.numero_fci") ?? null;
                $this->nfe->tagprod($stdProdutoItem);

                $stdEspecificacaoST = new stdClass();
                $stdEspecificacaoST->item = $request->input("itens.{$key}.numero_item"); //item da NFe
                $stdEspecificacaoST->CEST = $request->input("itens.{$key}.cest") ?? null;
                $stdEspecificacaoST->indEscala = $request->input("itens.{$key}.escala_relevante") ?? null; //incluido no layout 4.00
                $stdEspecificacaoST->CNPJFab = $request->input("itens.{$key}.cnpj_fabricante") ?? null; //incluido no layout 4.00
                $this->nfe->tagCEST($stdEspecificacaoST);


                $stdImpostoItem = new stdClass();
                $stdImpostoItem->item = $request->input("itens.{$key}.numero_item"); //item da NFe
                $stdImpostoItem->vTotTrib = $request->input("itens.{$key}.imposto.valor_aproximado_tributos");
                $this->nfe->tagimposto($stdImpostoItem);

                if ($this->emitente->regime_tributario !== 1) {

                    $stdICMSItem = new stdClass();
                    $stdICMSItem->item = $request->input("itens.{$key}.numero_item"); //item da NFe
                    $stdICMSItem->orig = $request->input("itens.{$key}.origem");
                    $stdICMSItem->CST = $request->input("itens.{$key}.imposto.icms.situacao_tributaria");
                    $stdICMSItem->modBC = $request->input("itens.{$key}.imposto.icms.modalidade_base_calculo");
                    $stdICMSItem->vBC = $request->input("itens.{$key}.imposto.icms.valor_base_calculo") ?? null;
                    $stdICMSItem->pICMS = $request->input("itens.{$key}.imposto.icms.aliquota") ?? '';
                    $stdICMSItem->vICMS = $request->input("itens.{$key}.imposto.icms.valor") ?? '';
                    $stdICMSItem->pFCP = $request->input("itens.{$key}.imposto.icms.fcp.aliquota ") ?? '';
                    $stdICMSItem->vFCP = $request->input("itens.{$key}.imposto.icms.fcp.valor ") ?? '';
                    $stdICMSItem->vBCFCP = $request->input("itens.{$key}.imposto.icms.fcp.valor_base_calculo ") ?? '';
                    $stdICMSItem->modBCST = $request->input("itens.{$key}.imposto.icms.modalidade_base_calculo_st ") ?? '';
                    $stdICMSItem->pMVAST = $request->input("itens.{$key}.imposto.icms.aliquota_margem_valor_adicionado_st ") ?? '';
                    $stdICMSItem->pRedBCST = $request->input("itens.{$key}.imposto.icms.aliquota_reducao_base_calculo_st ") ?? '';
                    $stdICMSItem->vBCST = $request->input("itens.{$key}.imposto.icms.valor_base_calculo_st") ?? '';
                    $stdICMSItem->pICMSST = $request->input("itens.{$key}.imposto.icms.aliquota_st") ?? '';
                    $stdICMSItem->vICMSST = $request->input("itens.{$key}.imposto.icms.valor_st") ?? '';
                    $stdICMSItem->vBCFCPST = $request->input("itens.{$key}.imposto.icms.fcp.valor_base_calculo_st") ?? '';
                    $stdICMSItem->pFCPST = $request->input("itens.{$key}.imposto.icms.fcp.aliquota_st") ?? '';
                    $stdICMSItem->vFCPST = $request->input("itens.{$key}.imposto.icms.fcp.valor_st") ?? '';
                    $stdICMSItem->vICMSDeson = $request->input("itens.{$key}.imposto.icms.valor_desonerado") ?? '';
                    $stdICMSItem->motDesICMS = $request->input("itens.{$key}.imposto.icms.motivo_desoneracao") ?? '';
                    $stdICMSItem->pRedBC = $request->input("itens.{$key}.imposto.icms.aliquota_reducao_base_calculo") ?? '';
                    $stdICMSItem->vICMSOp = $request->input("itens.{$key}.imposto.icms.valor_operacao") ?? '';
                    $stdICMSItem->pDif = $request->input("itens.{$key}.imposto.icms.aliquota_diferimento") ?? '';
                    $stdICMSItem->vICMSDif = $request->input("itens.{$key}.imposto.icms.valor_diferido") ?? '';

                    $stdICMSItem->vBCSTRet = $request->input("itens.{$key}.imposto.icms.valor_base_calculo_retido_st") ?? '';
                    $stdICMSItem->pST = $request->input("itens.{$key}.imposto.icms.aliquota_final ") ?? '';
                    $stdICMSItem->vICMSSTRet = $request->input("itens.{$key}.imposto.icms.valor_retido_st") ?? '';

                    // icms st - fundo de combate a pobreza
                    $stdICMSItem->vBCFCPSTRet = $request->input("itens.{$key}.imposto.icms.fcp.valor_base_calculo_retido_st") ?? '';
                    $stdICMSItem->pFCPSTRet = $request->input("itens.{$key}.imposto.icms.fcp.aliquota_retido_st") ?? '';
                    $stdICMSItem->vFCPSTRet = $request->input("itens.{$key}.imposto.icms.fcp.valor_retido_st") ?? '';

                    $stdICMSItem->pRedBCEfet = $request->input("itens.{$key}.imposto.icms.aliquota_reducao_base_calculo_efetiva") ?? '';
                    $stdICMSItem->vBCEfet = $request->input("itens.{$key}.imposto.icms.valor_base_calculo_efetiva") ?? '';
                    $stdICMSItem->pICMSEfet = $request->input("itens.{$key}.imposto.icms.aliquota_efetiva") ?? '';
                    $stdICMSItem->vICMSEfet = $request->input("itens.{$key}.imposto.icms.valor_efetivo") ?? '';
                    $stdICMSItem->vICMSSubstituto = $request->input("itens.{$key}.imposto.icms.valor_substituto") ?? ''; //NT2018.005_1.10_Fevereiro de 2019
                    $this->nfe->tagICMS($stdICMSItem);

                }

                if($csts_icms_st->contains($request->input("itens.{$key}.imposto.icms.situacao_tributaria"))){
                    $stdICMSSTRet = new stdClass();
                    $stdICMSSTRet->item = $request->input("itens.{$key}.numero_item");; //item da NFe
                    $stdICMSSTRet->orig = $request->input("itens.{$key}.origem");
                    $stdICMSSTRet->CST = $request->input("itens.{$key}.imposto.icms.situacao_tributaria");

                    $stdICMSSTRet->vBCSTRet = $request->input("itens.{$key}.imposto.icms.valor_base_calculo_retido_st") ?? '';
                    $stdICMSSTRet->vICMSSTRet = $request->input("itens.{$key}.imposto.icms.valor_retido_st") ?? '';

                    //$stdICMSSTRet->vBCSTDest = $request->input("itens.{$key}.imposto.icms.aliquota_final ") ?? '';
                    //$stdICMSSTRet->vICMSSTDest = null;

                    $stdICMSSTRet->vBCFCPSTRet = $request->input("itens.{$key}.imposto.icms.fcp.valor_base_calculo_retido_st") ?? null;
                    $stdICMSSTRet->pFCPSTRet = $request->input("itens.{$key}.imposto.icms.fcp.valor_base_calculo_retido_st") ?? null;
                    $stdICMSSTRet->vFCPSTRet = $request->input("itens.{$key}.imposto.icms.fcp.valor_base_calculo_retido_st") ?? null;
                    $stdICMSSTRet->pST = $request->input("itens.{$key}.imposto.icms.fcp.valor_base_calculo_retido_st") ?? null;
                    $stdICMSSTRet->vICMSSubstituto = $request->input("itens.{$key}.imposto.icms.fcp.valor_base_calculo_retido_st") ?? null;
                    $stdICMSSTRet->pRedBCEfet = $request->input("itens.{$key}.imposto.icms.fcp.valor_base_calculo_retido_st") ?? null;
                    $stdICMSSTRet->vBCEfet = $request->input("itens.{$key}.imposto.icms.fcp.valor_base_calculo_retido_st") ?? null;
                    $stdICMSSTRet->pICMSEfet = $request->input("itens.{$key}.imposto.icms.fcp.valor_base_calculo_retido_st") ?? null;
                    $stdICMSSTRet->vICMSEfet = $request->input("itens.{$key}.imposto.icms.fcp.valor_base_calculo_retido_st") ?? null;
                    $this->nfe->tagICMSST($stdICMSSTRet);
                }

                if ($this->emitente->regime_tributario === 1) {

                    $stdICMSSNItem = new stdClass();
                    $stdICMSSNItem->item = $request->input("itens.{$key}.numero_item"); //item da NFe
                    $stdICMSSNItem->orig = $request->input("itens.{$key}.origem");
                    $stdICMSSNItem->CSOSN = $request->input("itens.{$key}.imposto.icms.situacao_tributaria");
                    $stdICMSSNItem->pCredSN = $request->input("itens.{$key}.imposto.icms.aliquota_credito_simples");
                    $stdICMSSNItem->vCredICMSSN = $request->input("itens.{$key}.imposto.icms.valor_credito_simples");
                    $stdICMSSNItem->modBCST = $request->input("itens.{$key}.imposto.icms.modalidade_base_calculo_st") ?? null;
                    $stdICMSSNItem->pMVAST = $request->input("itens.{$key}.imposto.icms.aliquota_margem_valor_adicionado_st") ?? null;
                    $stdICMSSNItem->pRedBCST = $request->input("itens.{$key}.imposto.icms.aliquota_reducao_base_calculo_st") ?? null;
                    $stdICMSSNItem->vBCST = $request->input("itens.{$key}.imposto.icms.valor_base_calculo_st") ?? null;
                    $stdICMSSNItem->pICMSST = $request->input("itens.{$key}.imposto.icms.aliquota_st") ?? null;
                    $stdICMSSNItem->vICMSST = $request->input("itens.{$key}.imposto.icms.valor_st") ?? null;
                    $stdICMSSNItem->vBCFCPST = $request->input("itens.{$key}.imposto.icms.fcp.valor_base_calculo_st") ?? null; //incluso no layout 4.00
                    $stdICMSSNItem->pFCPST = $request->input("itens.{$key}.imposto.icms.fcp.aliquota_st") ?? null; //incluso no layout 4.00
                    $stdICMSSNItem->vFCPST = $request->input("itens.{$key}.imposto.icms.fcp.valor_st") ?? null; //incluso no layout 4.00
                    $stdICMSSNItem->vBCSTRet = $request->input("itens.{$key}.imposto.icms.valor_base_calculo_retido_st") ?? null;
                    $stdICMSSNItem->pST = $request->input("itens.{$key}.imposto.icms.aliquota_final") ?? null;
                    $stdICMSSNItem->vICMSSTRet = $request->input("itens.{$key}.imposto.icms.valor_retido_st") ?? null;
                    $stdICMSSNItem->vBCFCPSTRet = $request->input("itens.{$key}.imposto.icms.valor_base_calculo_retido_st") ?? null; //incluso no layout 4.00
                    $stdICMSSNItem->pFCPSTRet = $request->input("itens.{$key}.imposto.icms.aliquota_retido_st") ?? null; //incluso no layout 4.00
                    $stdICMSSNItem->vFCPSTRet = $request->input("itens.{$key}.imposto.icms.valor_retido_st") ?? null; //incluso no layout 4.00
                    $stdICMSSNItem->modBC = $request->input("itens.{$key}.imposto.icms.modalidade_base_calculo") ?? null;
                    $stdICMSSNItem->vBC = $request->input("itens.{$key}.imposto.icms.valor_base_calculo") ?? null;
                    $stdICMSSNItem->pRedBC = $request->input("itens.{$key}.imposto.icms.aliquota_reducao_base_calculo") ?? null;
                    $stdICMSSNItem->pICMS = $request->input("itens.{$key}.imposto.icms.aliquota") ?? null;
                    $stdICMSSNItem->vICMS = $request->input("itens.{$key}.imposto.icms.valor") ?? null;

                    $stdICMSSNItem->pRedBCEfet = $request->input("itens.{$key}.imposto.icms.aliquota_reducao_base_calculo_efetiva") ?? null;
                    $stdICMSSNItem->vBCEfet = $request->input("itens.{$key}.imposto.icms.valor_base_calculo_efetiva") ?? null;
                    $stdICMSSNItem->pICMSEfet = $request->input("itens.{$key}.imposto.icms.aliquota_efetiva") ?? null;
                    $stdICMSSNItem->vICMSEfet = $request->input("itens.{$key}.imposto.icms.valor_efetivo") ?? null;
                    $stdICMSSNItem->vICMSSubstituto = $request->input("itens.{$key}.imposto.icms.valor_substituto") ?? null;
                    $this->nfe->tagICMSSN($stdICMSSNItem);
                }

                $stdIPIItem = new stdClass();
                $stdIPIItem->item = $request->input("itens.{$key}.numero_item"); //item da NFe
                //$stdIPIItem->clEnq = $request->input("itens.{$key}.imposto.ipi.") ?? null;
                $stdIPIItem->CNPJProd = $request->input("itens.{$key}.imposto.ipi.cnpj_produtor") ?? null;
                $stdIPIItem->cSelo = $request->input("itens.{$key}.imposto.ipi.codigo_selo_controle") ?? null;
                $stdIPIItem->qSelo = $request->input("itens.{$key}.imposto.ipi.quantidade_selo_controle") ?? null;
                $stdIPIItem->cEnq = $request->input("itens.{$key}.imposto.ipi.codigo_enquadramento_legal") ?? '999';
                $stdIPIItem->CST = $request->input("itens.{$key}.imposto.ipi.situacao_tributaria") ?? '99';
                $stdIPIItem->vIPI = $request->input("itens.{$key}.imposto.ipi.valor") ?? 0.00;
                $stdIPIItem->vBC = $request->input("itens.{$key}.imposto.ipi.valor_base_calculo") ?? 0.00;
                $stdIPIItem->pIPI = $request->input("itens.{$key}.imposto.ipi.aliquota") ?? 0.00;
                $stdIPIItem->qUnid = $request->input("itens.{$key}.imposto.ipi.quantidade_total") ?? null;
                $stdIPIItem->vUnid = $request->input("itens.{$key}.imposto.ipi.valor_unidade_tributavel") ?? null;
                $this->nfe->tagIPI($stdIPIItem);

                $stdPISItem = new stdClass();
                $stdPISItem->item = $request->input("itens.{$key}.numero_item"); //item da NFe
                $stdPISItem->CST = $request->input("itens.{$key}.imposto.pis.situacao_tributaria");
                $stdPISItem->vBC = $request->input("itens.{$key}.imposto.pis.valor_base_calculo") ?? null;
                $stdPISItem->pPIS = $request->input("itens.{$key}.imposto.pis.aliquota") ?? null;
                $stdPISItem->vPIS = $request->input("itens.{$key}.imposto.pis.valor") ?? null;
                $stdPISItem->qBCProd = $request->input("itens.{$key}.imposto.pis.quantidade_vendida") ?? null;
                $stdPISItem->vAliqProd = $request->input("itens.{$key}.imposto.pis.aliquota_valor") ?? null;
                $this->nfe->tagPIS($stdPISItem);

                $stdCOFINSItem = new stdClass();
                $stdCOFINSItem->item = $request->input("itens.{$key}.numero_item"); //item da NFe
                $stdCOFINSItem->CST = $request->input("itens.{$key}.imposto.cofins.situacao_tributaria");
                $stdCOFINSItem->vBC = $request->input("itens.{$key}.imposto.cofins.valor_base_calculo") ?? null;
                $stdCOFINSItem->pCOFINS = $request->input("itens.{$key}.imposto.cofins.aliquota") ?? null;
                $stdCOFINSItem->vCOFINS = $request->input("itens.{$key}.imposto.cofins.valor") ?? null;
                $stdCOFINSItem->qBCProd = $request->input("itens.{$key}.imposto.cofins.quantidade_vendida") ?? null;
                $stdCOFINSItem->vAliqProd = $request->input("itens.{$key}.imposto.cofins.aliquota_valor") ?? null;
                $this->nfe->tagCOFINS($stdCOFINSItem);
            }

            // se não for passado a lib irá calcular com base nos itens
            $stdTotaisICMSItem = new stdClass();
            $stdTotaisICMSItem->vBC = $request->input("totais.icms_base_calculo") ?? null;
            $stdTotaisICMSItem->vICMS = $request->input("totais.icms_valor_total") ?? null;
            $stdTotaisICMSItem->vICMSDeson = $request->input("totais.icms_valor_total_desonerado") ?? null;
            $stdTotaisICMSItem->vFCP = $request->input("totais.fcp_valor_total") ?? null; //incluso no layout 4.00
            $stdTotaisICMSItem->vBCST = $request->input("totais.icms_st_base_calculo") ?? null;
            $stdTotaisICMSItem->vST = $request->input("totais.icms_st_valor_total") ?? null;
            $stdTotaisICMSItem->vFCPST = $request->input("totais.fcp_st_valor_total") ?? null; //incluso no layout 4.00
            $stdTotaisICMSItem->vFCPSTRet = $request->input("totais.fcp_st_valor_total_retido") ?? null; //incluso no layout 4.00
            $stdTotaisICMSItem->vProd = $request->input("totais.valor_produtos_total") ?? null;
            $stdTotaisICMSItem->vFrete = $request->input("totais.valor_frete_total") ?? null;
            $stdTotaisICMSItem->vSeg = $request->input("totais.valor_seguro_total") ?? null;
            $stdTotaisICMSItem->vDesc = $request->input("totais.valor_desconto_total") ?? null;
            $stdTotaisICMSItem->vII = $request->input("totais.ii_valor_total") ?? null;
            $stdTotaisICMSItem->vIPI = $request->input("totais.ipi_valor_total") ?? null;
            $stdTotaisICMSItem->vIPIDevol = $request->input("totais.ipi_valor_devolvido_total") ?? null; //incluso no layout 4.00
            $stdTotaisICMSItem->vPIS = $request->input("totais.pis_valor_total") ?? null;
            $stdTotaisICMSItem->vCOFINS = $request->input("totais.cofins_valor_total") ?? null;
            $stdTotaisICMSItem->vOutro = $request->input("totais.outras_despesas_valor_total") ?? null;
            $stdTotaisICMSItem->vNF = $request->input("totais.valor_total") ?? null;
            $stdTotaisICMSItem->vTotTrib = $request->input("totais.tributos_valor_total") ?? null;
            //$nfe->tagICMSTot();

            $stdFrete = new stdClass();
            $stdFrete->modFrete = $request->input("frete.modalidade_frete") ?? 9;
            $this->nfe->tagtransp($stdFrete);

            if($request->input("frete.modalidade_frete") != '9') {

                $stdTransportadora = new stdClass();
                $stdTransportadora->xNome = $request->input("frete.transportador.nome");
                $stdTransportadora->IE = $request->input("frete.transportador.inscricao_estadual") ?? null;
                $stdTransportadora->xEnder = $request->input("frete.transportador.endereco");
                $stdTransportadora->xMun = $request->input("frete.transportador.nome_municipio");
                $stdTransportadora->UF = $request->input("frete.transportador.uf");
                $stdTransportadora->CNPJ = $request->input("frete.transportador.cnpj") ?? null;//só pode haver um ou CNPJ ou CPF, se um deles é especificado o outro deverá ser null
                $stdTransportadora->CPF = $request->input("frete.transportador.cpf") ?? null;
                $this->nfe->tagtransporta($stdTransportadora);
            }

            $stdVeiculoTrator = new stdClass();
            $stdVeiculoTrator->placa = $request->input("frete.veiculo.placa");
            $stdVeiculoTrator->UF = $request->input("frete.veiculo.uf");
            $stdVeiculoTrator->RNTC = $request->input("frete.veiculo.rntc");
            $this->nfe->tagveicTransp($stdVeiculoTrator);

            foreach ($request->input('frete.veiculo.reboques') as $key => $item) {

                $stdReboque = new stdClass();
                $stdReboque->placa =  $request->input("frete.veiculo.reboques.{$key}.placa");
                $stdReboque->UF = $request->input("frete.veiculo.reboques.{$key}.uf");
                $stdReboque->RNTC = $request->input("frete.veiculo.reboques.{$key}.rntc");
                $this->nfe->tagreboque($stdReboque);
            }

            foreach ($request->input('frete.veiculo.reboques') as $key => $item) {
                $stdVolumes = new stdClass();
                $stdVolumes->item = $key++; //indicativo do numero do volume
                $stdVolumes->qVol = $request->input("frete.volumes.{$key}.quantidade");
                $stdVolumes->esp = $request->input("frete.volumes.{$key}.especie") ?? '';
                $stdVolumes->marca = $request->input("frete.volumes.{$key}.marca") ?? '';
                $stdVolumes->nVol = $request->input("frete.volumes.{$key}.numero") ?? '';
                $stdVolumes->pesoL = $request->input("frete.volumes.{$key}.peso_liquido") ?? 0.00;
                $stdVolumes->pesoB = $request->input("frete.volumes.{$key}.peso_bruto") ?? 0.00;
                $this->nfe->tagvol($stdVolumes);
            }

            $stdFaturaCobranca = new stdClass();
            $stdFaturaCobranca->nFat = $request->input("cobranca.fatura.numero");
            $stdFaturaCobranca->vOrig = $request->input("cobranca.fatura.valor_original");
            $stdFaturaCobranca->vDesc = $request->input("cobranca.fatura.valor_desconto");
            $stdFaturaCobranca->vLiq = $request->input("cobranca.fatura.valor_liquido");
            $this->nfe->tagfat($stdFaturaCobranca);


            foreach ($request->input('cobranca.duplicatas') as $key => $item) {
                $stdDuplicata = new stdClass();
                $stdDuplicata->nDup = $key++;
                $stdDuplicata->dVenc = $request->input("cobranca.duplicatas.{$key}.data_vencimento");
                $stdDuplicata->vDup = $request->input("cobranca.duplicatas.{$key}.valor");
                $this->nfe->tagdup($stdDuplicata);
            }

            $stdPagamento = new StdClass();
            $stdPagamento->vTroco = $request->input('pagamento.valor_troco');
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

            $stdAutorizadoXml = new stdClass();
            $stdAutorizadoXml->CNPJ = $request->input('pessoas_autorizadas.cnpj') ?? null; //indicar um CNPJ ou CPF
            $stdAutorizadoXml->CPF = $request->input('pessoas_autorizadas.cpf') ?? null;

            $this->nfe->tagautXML($stdAutorizadoXml);

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
