# Módulo Gerencianet Pix para WHMCS

## Instalação

1. Faça o download da última versão do módulo;
2. Descompacte o arquivo baixado;
3. Copie o arquivo **gerencianetpix.php** e a pasta **gerencianetpix**, para o diretório **/modules/gateways** da instalação do WHMCS;
4. Altere as permissões do arquivo copiado utilizando o comando: `chmod 777 modules/gateways/gerencianetpix.php`
5. Altere as permissões da pasta copiada utilizando o comando: `chmod 777 modules/gateways/gerencianetpix/ -R`
6. Copie o arquivo **gerencianetpix.php**, disponível no diretório **callback**, para o diretório **modules/gateways/callback**. Ele deve estar no caminho: *modules/gateways/callback/gerencianetpix.php*
7. Altere as permissões do arquivo copiado utilizando o comando: `chmod 777 modules/gateways/callback/gerencianetpix.php`
8. Copie o arquivo **gerencianetpix.php**, disponível no diretório **hooks**, para o diretório **includes/hooks**. Ele deve estar no caminho *includes/hooks/gerencianetpix.php*
9. Altere as permissões do arquivo copiado utilizando o comando: `chmod 777 includes/hooks/gerencianetpix.php`
10. Crie uma pasta na raiz do seu servidor e insira seu certificado na pasta. Vale lembrar que **seu certificado deve estar no formato *.pem***. Você encontra o passo a passo de conversão na sessão: [**Converter certificado .p12 para .pem**](#conversao)

Ao final da instalação, os arquivos do módulo Gerencianet devem estar na seguinte estrutura no WHMCS:

```
includes/hooks/
  |- gerencianetpix.php
 modules/gateways/
  |- callback/gerencianetpix.php
  |- gerencianetpix/
  |- gerencianetpix.php
```

### <a id="conversao"></a>Converter certificado .p12 para .pem
Todas as requisições devem conter um certificado de segurança que será fornecido pela Gerencianet dentro da sua conta, no formato PFX(.p12). Essa exigência está descrita na integra no [manual de segurança do PIX](https://www.bcb.gov.br/estabilidadefinanceira/comunicacaodados).

Caso ainda não tenha seu certificado, basta seguir o passo a passo do link a seguir para gerar um novo: [Clique Aqui](https://gerencianet.com.br/artigo/como-gerar-o-certificado-para-usar-a-api-pix/)

Para converter seu certificado de .p12 para .pem, basta utilizar o conversor de certificados disponibilizado pela Gerencianet no link: [Clique aqui](https://gnetbr.com/HylSpVZzLu)
## Configuração do Módulo

![Tela de Configuração](https://gnetbr.com/B1glJBqjBO)
1. **Client_Id Produção:** Deve ser preenchido com o client_id de produção de sua conta Gerencianet. Este campo é obrigatório e pode ser encontrado no menu "API" -> "Minhas Aplicações". Em seguida, selecione sua aplicação criada, conforme é mostrado no [link](https://gnetbr.com/Ske9THqjrO);
2. **Client_Secret Produção:** Deve ser preenchido com o client_secret de produção de sua conta Gerencianet. Este campo é obrigatório e pode ser encontrado no menu "API" ->  "Minhas Aplicações". Em seguida, selecione sua aplicação criada, conforme é mostrado no [link](https://gnetbr.com/Ske9THqjrO);
3. **Client_Id Desenvolvimento:** Deve ser preenchido com o client_id de desenvolvimento de sua conta Gerencianet. Este campo é obrigatório e pode ser encontrado no menu "API" -> "Minhas Aplicações". Em seguida, selecione sua aplicação criada, conforme é mostrado no [link](https://gnetbr.com/BJe-vIciHd);
4. **Client_Secret Desenvolvimento:** Deve ser preenchido com o client_secret de desenvolvimento de sua conta Gerencianet. Este campo é obrigatório e pode ser encontrado no menu "API" -> "Minhas Aplicações". Em seguida, selecione sua aplicação criada, conforme é mostrado no [link](https://gnetbr.com/BJe-vIciHd);
5. **Sandbox:** Caso seja de seu interesse, habilite o ambiente de testes da API Gerencianet;
6. **Debug:** Neste campo é possível habilitar os logs de transação e de erros da Gerencianet no painel WHMCS;
7. **Certificado Pix** Deve ser preenchido com o caminho do certificado salvo em seu servidor no passo 10 da instalação;
8. **Desconto:** Informe o valor de desconto que deverá ser aplicado ao pix gerado exclusivamente pela Gerencianet;
9. **Validade da Cobrança** Deve ser informado o período de validade em dias da cobrança PIX;
10. **Mtls** Entenda os riscos de não configurar o mTLS acessando o link https://gnetbr.com/rke4baDVyd.
