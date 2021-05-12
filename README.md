# API NFe

### Configurações Gerais NFe
[Documentação Config](https://github.com/nfephp-org/sped-nfe/blob/master/docs/Config.md)

### Instanciando a Classe Make
[Documentação Montagem NFe](https://github.com/fagnerlopes/sped-nfe/blob/master/docs/Make.md)

### Exemplo de JSON POST /nfe
```
{
    "natureza_operacao": "VENDA DENTRO DO ESTADO",
    "serie": "1",
    "numero": "1035",
    "data_emissao": "2020-10-15T03:00:00-03:00",
    "data_entrada_saida": "2020-10-15T03:00:00-03:00",
    "tipo_operacao": "1",
    "local_destino": "1",
    "finalidade_emissao": "1",
    "consumidor_final": "0",
    "presenca_comprador": "9",
    "notas_referenciadas": [],
    "destinatario": {
        "cnpj": "15493535500128",
        "nome": "EMPRESA MODELO",
        "indicador_inscricao_estadual": "1",
        "inscricao_estadual": "212055510",
        "endereco": {
            "logradouro": "AVENIDA TESTE",
            "numero": "444",
            "bairro": "CENTRO",
            "codigo_municipio": "2408003",
            "nome_municipio": "Mossoro",
            "uf": "RN",
            "cep": "59653120",
            "codigo_pais": "1058",
            "nome_pais": "BRASIL",
            "telefone": "8499995555"
        }
    },
    "itens": [
        {
            "numero_item": "1",
            "codigo_produto": "000297",
            "descricao": "SAL GROSSO 50KGS",
            "codigo_ncm": "55110011",
            "cfop": "5102",
            "unidade_comercial": "SC",
            "quantidade_comercial": 10,
            "valor_unitario_comercial": "22.45",
            "valor_bruto": "224.50",
            "unidade_tributavel": "SC",
            "quantidade_tributavel": "10.00",
            "valor_unitario_tributavel": "22.45",
            "origem": "0",
            "inclui_no_total": "1",
            "imposto": {
                "valor_aproximado_tributos": 9.43,
                "icms": {
                    "situacao_tributaria": "101",
                    "modalidade_base_calculo": "3",
                    "valor_base_calculo": "0.00",
                    "modalidade_base_calculo_st": "4",
                    "aliquota_reducao_base_calculo": "0.00",
                    "aliquota": "0.00",
                    "aliquota_final": "0.00",
                    "valor": "0.00",
                    "aliquota_margem_valor_adicionado_st": "0.00",
                    "aliquota_reducao_base_calculo_st": "0.00",
                    "valor_base_calculo_st": "0.00",
                    "aliquota_st": "0.00",
                    "valor_st": "0.00"
                },
                "pis": {
                    "situacao_tributaria": "01",
                    "valor_base_calculo": 224.5,
                    "aliquota": "1.65",
                    "valor": "3.70"
                },
                "cofins": {
                    "situacao_tributaria": "01",
                    "valor_base_calculo": 224.5,
                    "aliquota": "7.60",
                    "valor": "17.06"
                }
            },
            "valor_desconto": 0,
            "valor_frete": 0,
            "valor_seguro": 0,
            "valor_outras_despesas": 0,
            "informacoes_adicionais_item": "Valor aproximado tributos R$: 9,43 (4,20%) Fonte: IBPT"
        }
    ],
    "icms_base_calculo": 0,
    "icms_valor_total": 0,
    "valor_produtos": 224.5,
    "valor_frete": 0,
    "valor_seguro": 0,
    "valor_desconto": 0,
    "valor_pis": 3.7,
    "valor_cofins": 17.06,
    "valor_outras_despesas": 0,
    "valor_total": 224.5,
    "frete": {
        "modalidade_frete": "0",
        "volumes": [
            {
                "quantidade": "10",
                "especie": null,
                "marca": "TESTE",
                "numero": null,
                "peso_liquido": 500,
                "peso_bruto": 500
            }
        ]
    },
    "cobranca": {
        "fatura": {
            "numero": "001",
            "valor_original": "224.50",
            "valor_desconto": "0.00",
            "valor_liquido": "224.50"
        }
    },
    "pagamento": {
        "formas_pagamento": [
            {
                "meio_pagamento": "01",
                "valor": "224.50",
                "tipo_integracao": "2"
            }
        ]
    },
    "informacoes_adicionais_contribuinte": "PV: 3325 * Rep: DIRETO * Motorista:  * Forma Pagto: 04 DIAS * teste de observação para a nota fiscal * Valor aproximado tributos R$9,43 (4,20%) Fonte: IBPT",
    "pessoas_autorizadas": [
        {
            "cnpj": "96256273000170"
        }, {
            "cnpj": "80681257000195"
        }
    ]
}
```
# Exemplo de retorno POST /nfe
```
{
    "sucesso": true,
    "codigo": 100,
    "mensagem": "Autorizado o uso do NF-e",
    "chave": "50190813188739000110550010000012001581978549",
    "xml": "PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZ...VByb2M+", // BASE 64
    "pdf": "JVBERi0xLjMKMyAwIG9iago8PC9UeXBlIC9QYA+...GCg==", // BASE 64
    "data_hora_evento": "2019-08-30T11:25:22-04:00",
    "protocolo": "150190003925457",
    "status": "autorizado", // autorizado ou cancelado
    "numero": "1200",
    "serie": "1"
}
```

### Exemplo de JSON POST /nfe/cancela
```
{
     "chave": "50191213188739000110550010000012151581978542",
     "justificativa": "Teste de cancelamento de nfe"
}
```

### Exemplo de retorno POST /nfe/cancela
```
{
    "sucesso": true,
    "codigo": "101",
    "mensagem": "Homologado o cancelamento da NF-e",
    "data_hora_evento": "2019-09-16T17:18:48-03:00",
    "protocolo": "141190000844226",
    "xml": "PGVudjpFbnZlbG9wZSB4bWxuczplbnY9J2h...cGU+", // BASE 64
    "pdf": "JVBERi0xLjMKMyAwIG9iago8PC9UeXBlIC9QYA+...GCg==" // BASE 64
}
```

### Exemplo de busca por NFe JSON POST /nfe/busca
```
{
    "numero_inicial": 1210,
    "numero_final": 1210,
    "serie": 1,
    "data_inicial": "2019-12-01", // Autorização
    "data_final": "2019-12-31",
    "cancel_inicial": "2019-12-01", // Cancelamento
    "cancel_final": "2019-12-31"
}
```
### Exemplo de CC-e JSON POTS /nfe/correcao
```
{
    "chave": "50191213188739000110550010000012151581978542",
    "justificativa": "Teste de carta de correcao"
}
```

# Exemplo de Rejeição
### Consulta o status - Rejeição por NCM

```
{
  "attributes": {
    "versao": "4.00"
  },
  "tpAmb": "2",
  "verAplic": "RS202104301543",
  "nRec": "431022029094179",
  "cStat": "104",
  "xMotivo": "Lote processado",
  "cUF": "43",
  "dhRecbto": "2021-05-12T14:58:35-03:00",
  "protNFe": {
    "attributes": {
      "versao": "4.00"
    },
    "infProt": {
      "tpAmb": "2",
      "verAplic": "RS202104301543",
      "chNFe": "43210506103611000141550010000001911887566629",
      "dhRecbto": "2021-05-12T14:58:35-03:00",
      "digVal": "Db2QK+2Tw7YrDzBEsxVPmeLNMB8=",
      "cStat": "778",
      "xMotivo": "Rejeicao: Informado NCM inexistente [nItem:1]"
    }
  }
}
```

### Rejeição por Duplicidade

```
{
  "attributes": {
    "versao": "4.00"
  },
  "tpAmb": "2",
  "verAplic": "RS202104301543",
  "nRec": "431022029094524",
  "cStat": "104",
  "xMotivo": "Lote processado",
  "cUF": "43",
  "dhRecbto": "2021-05-12T15:42:59-03:00",
  "protNFe": {
    "attributes": {
      "versao": "4.00"
    },
    "infProt": {
      "tpAmb": "2",
      "verAplic": "RS202104301543",
      "chNFe": "43210506103611000141550010000001911256500420",
      "dhRecbto": "2021-05-12T15:42:59-03:00",
      "digVal": "WsvFoJmtPoOn7W8EXy7Tlt68U\/c=",
      "cStat": "539",
      "xMotivo": "Rejeicao: Duplicidade de NF-e, com diferenca na Chave de Acesso [chNFe:43210506103611000141550010000001911593545241][nRec:431022029092399]"
    }
  }
}
```



