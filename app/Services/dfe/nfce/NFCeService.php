<?php

use NFePHP\NFe\Make;
use NFePHP\DA\NFe\Danfce;


class NFCeService extends DocumentosFiscaisAbstract
{
    public function buildNFeXml(array $param): string
    {
        //$this->tools->disableCertValidation(true); //tem que desabilitar
        $this->tools->model('65');

        try {

            $this->nfe = new Make();

            //infNFe OBRIGATÓRIA
            $std = new \stdClass();
            $std->Id = '';
            $std->versao = '4.00';
            $this->nfe->taginfNFe($std);

            //ide OBRIGATÓRIA
            $std = new \stdClass();
            $std->cUF = 43;
            $std->cNF = null;
            $std->natOp = 'VENDA CONSUMIDOR';
            $std->mod = $this->modelo;
            $std->serie = 1;
            $std->nNF = 107;
            $std->dhEmi = (new \DateTime())->format('Y-m-d\TH:i:sP');
            $std->dhSaiEnt = null;
            $std->tpNF = 1;
            $std->idDest = 1;
            $std->cMunFG = 4305108;
            $std->tpImp = 4;
            $std->tpEmis = 1;
            $std->cDV = null;
            $std->tpAmb = 2;
            $std->finNFe = 1;
            $std->indFinal = 1;
            $std->indPres = 1;
            $std->procEmi = 0;
            $std->verProc = 'NFCe V 1.0';
            $std->dhCont = null;
            $std->xJust = null;
            $this->nfe->tagIde($std);

            //emit OBRIGATÓRIA
            $std = new \stdClass();
            $std->xNome = 'MILLENNIUM Sistemas de Gestão - Posto Autorizado Premium';
            $std->xFant = 'MILLWEB';
            $std->IE = '0290419603';
            $std->IEST = null;
            //$std->IM = '95095870';
            $std->CNAE = null;
            $std->CRT = 1;
            $std->CNPJ = '06103611000141';
            //$std->CPF = '12345678901'; //NÃO PASSE TAGS QUE NÃO EXISTEM NO CASO
            $this->nfe->tagemit($std);

            //enderEmit OBRIGATÓRIA
            $std = new \stdClass();
            $std->xLgr = 'AVENIDA RIO BRANCO TESTE';
            $std->nro = '1512';
            $std->xCpl = 'LOJA 42';
            $std->xBairro = 'RIO BRANCO';
            $std->cMun = 4305108;
            $std->xMun = 'CAXIAS DO SUL';
            $std->UF = 'RS';
            $std->CEP = '95096000';
            $std->cPais = 1058;
            $std->xPais = 'Brasil';
            $std->fone = '5430252422';
            $this->nfe->tagenderemit($std);

            //dest OPCIONAL
            $std = new \stdClass();
            $std->xNome = 'Eu Ltda';
            $std->CNPJ = '01234123456789';
            //$std->CPF = '12345678901';
            //$std->idEstrangeiro = 'AB1234';
            $std->indIEDest = 9;
            //$std->IE = '';
            //$std->ISUF = '12345679';
            //$std->IM = 'XYZ6543212';
            $std->email = 'seila@seila.com.br';
            //$nfce->tagdest($std);

            //enderDest OPCIONAL
            $std = new \stdClass();
            $std->xLgr = 'Avenida Sebastião Diniz';
            $std->nro = '458';
            $std->xCpl = null;
            $std->xBairro = 'CENTRO';
            $std->cMun = 1400100;
            $std->xMun = 'Boa Vista';
            $std->UF = 'RR';
            $std->CEP = '69301088';
            $std->cPais = 1058;
            $std->xPais = 'Brasil';
            $std->fone = '1111111111';
            //$nfce->tagenderdest($std);


            //prod OBRIGATÓRIA
            $std = new \stdClass();
            $std->item = 1;
            $std->cProd = '1111';
            $std->cEAN = "SEM GTIN";
            $std->xProd = 'QUEROSENE MTZAAA20150109101';
            $std->NCM = 27101919;
            //$std->cBenef = 'ab222222';
            $std->EXTIPI = '';
            $std->CFOP = 5405;
            $std->uCom = 'UN';
            $std->qCom = 1;
            $std->vUnCom = 700.00;
            $std->vProd = 700.00;
            $std->cEANTrib = "SEM GTIN"; //'6361425485451';
            $std->uTrib = 'UN';
            $std->qTrib = 1;
            $std->vUnTrib = 700.00;
            //$std->vFrete = 0.00;
            //$std->vSeg = 0;
            //$std->vDesc = 0;
            //$std->vOutro = 0;
            $std->indTot = 1;
            //$std->xPed = '12345';
            //$std->nItemPed = 1;
            //$std->nFCI = '12345678-1234-1234-1234-123456789012';
            $this->nfe->tagprod($std);

            $tag = new \stdClass();
            $tag->item = 1;
            $tag->infAdProd = null;
            //$nfce->taginfAdProd($tag);

            $stdEspecificacaoST = new stdClass();
            $stdEspecificacaoST->item = 1; //item da NFe
            $stdEspecificacaoST->CEST = '0600400';
            //$stdEspecificacaoST->indEscala = null; //incluido no layout 4.00
            //$stdEspecificacaoST->CNPJFab = null; //incluido no layout 4.00

            $this->nfe->tagCEST($stdEspecificacaoST);

            //Imposto
            $std = new stdClass();
            $std->item = 1; //item da NFe
            $std->vTotTrib = 25.00;
            $this->nfe->tagimposto($std);

            $std = new stdClass();
            $std->item = 1; //item da NFe
            $std->orig = 0;
            $std->CSOSN = '500';
            $std->pCredSN = 0.00;
            $std->vCredICMSSN = 0.00;
            $std->modBCST = null;
            $std->pMVAST = null;
            $std->pRedBCST = null;
            $std->vBCST = null;
            $std->pICMSST = null;
            $std->vICMSST = null;
            $std->vBCFCPST = null; //incluso no layout 4.00
            $std->pFCPST = null; //incluso no layout 4.00
            $std->vFCPST = null; //incluso no layout 4.00
            $std->vBCSTRet = null;
            $std->pST = null;
            $std->vICMSSTRet = null;
            $std->vBCFCPSTRet = null; //incluso no layout 4.00
            $std->pFCPSTRet = null; //incluso no layout 4.00
            $std->vFCPSTRet = null; //incluso no layout 4.00
            $std->modBC = 5;
            $std->vBC = null;
            $std->pRedBC = null;
            $std->pICMS = null;
            $std->vICMS = null;
            $std->pRedBCEfet = null;
            $std->vBCEfet = null;
            $std->pICMSEfet = null;
            $std->vICMSEfet = null;
            $std->vICMSSubstituto = null;
            $this->nfe->tagICMSSN($std);

            //PIS
            $std = new stdClass();
            $std->item = 1; //item da NFe
            $std->CST = '49';
            //$std->vBC = 1200;
            //$std->pPIS = 0;
            $std->vPIS = 0.00;
            $std->qBCProd = 0;
            $std->vAliqProd = 0;
            $this->nfe->tagPIS($std);

            //COFINS
            $std = new stdClass();
            $std->item = 1; //item da NFe
            $std->CST = '49';
            $std->vBC = null;
            $std->pCOFINS = null;
            $std->vCOFINS = 0.00;
            $std->qBCProd = 0;
            $std->vAliqProd = 0;
            $this->nfe->tagCOFINS($std);

            //icmstot OBRIGATÓRIA
            $std = new \stdClass();
            $std->vBC = 0;
            $std->vICMS = 0;
            $std->vICMSDeson = 0;
            $std->vFCPUFDest = 0;
            $std->vICMSUFDest = 0;
            $std->vICMSUFRemet = 0;
            $std->vFCP = 0;
            $std->vBCST = 0;
            $std->vST = 0;
            $std->vFCPST = 0;
            $std->vFCPSTRet = 0;
            $std->vProd = 700.00;
            $std->vFrete = 0;
            $std->vSeg = null;
            $std->vDesc = null;
            $std->vII = 0;
            $std->vIPI = 0;
            $std->vIPIDevol = 0;
            $std->vPIS = 0;
            $std->vCOFINS = 0;
            $std->vOutro = null;
            $std->vNF = 700.00;
            $std->vTotTrib = 0;
            $this->nfe->tagicmstot($std);

            //transp OBRIGATÓRIA
            $std = new \stdClass();
            $std->modFrete = 9;
            $this->nfe->tagtransp($std);


            //pag OBRIGATÓRIA
            $std = new \stdClass();
            $std->vTroco = 0;
            $this->nfe->tagpag($std);

            //detPag OBRIGATÓRIA
            $std = new \stdClass();
            $std->indPag = 1;
            $std->tPag = '03';
            $std->vPag = 700.00;
            $this->nfe->tagdetpag($std);

            //infadic
            $std = new \stdClass();
            $std->infAdFisco = '';
            $std->infCpl = '';
            $this->nfe->taginfadic($std);

            $std = new stdClass();
            $std->CNPJ = '99999999999999'; //CNPJ da pessoa jurídica responsável pelo sistema utilizado na emissão do documento fiscal eletrônico
            $std->xContato = 'Fulano de Tal'; //Nome da pessoa a ser contatada
            $std->email = 'fulano@soft.com.br'; //E-mail da pessoa jurídica a ser contatada
            $std->fone = '1155551122'; //Telefone da pessoa jurídica/física a ser contatada
            //$std->CSRT = 'G8063VRTNDMO886SFNK5LDUDEI24XJ22YIPO'; //Código de Segurança do Responsável Técnico
            //$std->idCSRT = '01'; //Identificador do CSRT
            //$nfce->taginfRespTec($std);

            $this->nfe->monta();

            return $this->nfe->getXML();

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function buildDanfce(string $authorizedXml)
    {
        try {
            // imagem do logotipo
            $logo = 'data://text/plain;base64,'. base64_encode(file_get_contents('app/storage/06103611000141/logo/logo_nfe.jpg'));

            //$logo = 'app/storage/06103611000141/logo/logo_nfe.jpg';

            $danfce = new Danfce($authorizedXml);
            $danfce->debugMode(true);//seta modo debug, deve ser false em produção
            $danfce->setPaperWidth(80); //seta a largura do papel em mm max=80 e min=58
            $danfce->setMargins(2);//seta as margens
            $danfce->setDefaultFont('arial');//altera o font pode ser 'times' ou 'arial'
            //$danfce->setOffLineDoublePrint(true); //ativa ou desativa a impressão conjunta das via do consumidor e da via do estabelecimento qnado a nfce for emitida em contingência OFFLINE
            //$danfce->setPrintResume(true); //ativa ou desativa a impressao apenas do resumo
            //$danfce->setViaEstabelecimento(); //altera a via do consumidor para a via do estabelecimento, quando a NFCe for emitida em contingência OFFLINE
            //$danfce->setAsCanceled(); //força marcar nfce como cancelada
            //$danfce->creditsIntegratorFooter('WEBNFe Sistemas - http://www.webnfe.com.br');
            $pdf = $danfce->render($logo);
            //header('Content-Type: application/pdf');

            if($this->nfe->getChave()) {
                Util::savePDF($pdf, "danfce-{$this->nfe->getChave()}");
            }

            return $pdf;

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }


}