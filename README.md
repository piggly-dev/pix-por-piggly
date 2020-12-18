# Bem-vindo ao Pix por Piggly #

O **Pix** é o mais novo método de pagamento eletrônico criado pelo Banco Central do Brasil.
Você encontra todos os detalhes na [página oficial](https://www.bcb.gov.br/estabilidadefinanceira/pix) do Pix.

O propósito deste plugin é permitir que você utilize o método de pagamento Pix em sua loja **Woocommerce** sem qualquer custo.

Se você apreciar a função deste plugin e quiser apoiar este trabalho, sinta-se livre para fazer qualquer doação para a chave aleatória Pix `aae2196f-5f93-46e4-89e6-73bf4138427b` ❤.

## Como funciona? ##

Assim como é feito atualmente com uma transferência eletrônica no Woocommerce, o **Pix por Piggly** permite aos consumidores escolherem o método de pagamento Pix, então eles recebem as instruções de pagamento e enviam o comprovante, bem simples né?

Para permitir isso, nosso plugin segue todas as propostas do padrão EMV®1, implementado pelo Banco Central do Brasil, você pode ler mais sobre isso em [Especificações Técnicas](https://www.bcb.gov.br/content/estabilidadefinanceira/forumpireunioes/Anexo%20I%20-%20QRCodes%20-%20Especifica%C3%A7%C3%A3o%20-%20vers%C3%A3o%201-1.pdf).

Dessa forma, nosso plugin gera os três principais métodos de pagamento:

1. Um **QR Code** com o código Pix, utilizando a biblioteca `chillerlan/php-code` ([veja aqui](https://github.com/chillerlan/php-qrcode));
2. O código Pix em formato de texto para utilização da função **Pix Copia & Cola**; 
3. Pagamento manual com os dados Pix fornecidos.

Nosso plugin gera de forma automática o código Pix com base nas informações do Pedido e nas informações preenchidas na configuração do plugin. Todos os dados são válidados e preparados para respeitarem o padrão EMV®1.

## Funcionalidades ##

Nas configurações do plugin você é capaz de manipular as seguintes funções:

1. Habilitar/Desabilitar o método de pagamento;
2. Gerar um código Pix único para ser pago apenas uma vez;
3. Exibir os métodos QR Code, Pix Copia & Cola e Manual;
4. Alterar o título e descrição do pagamento;
5. Informar o nome da loja para a descrição do Pix;
6. Inserir o nome e a cidade do titular da conta Pix;
7. Escolher o tipo de chave e informar o valor da chave Pix;
8. Escrever as instruções para envio do comprovante de pagamento.

### Testes realizados ###

O código Pix gerado por esse plugin, incluindo a função QR Code e Pix Copia & Cola, foi testado nos seguintes aplicativos de banco:

* Banco do Brasil;
* Banco Inter;
* BMG;
* Bradesco;
* C6;
* Itaú;
* Mercado Pago;
* Nubank;
* PagPank;
* Santander.

Como o código utiliza o padrão do Pix é possível que funcione em todos os bancos. Mas, caso encontre problemas ou dificuldades hesite em abrir uma [Issue](https://github.com/piggly-dev/wc-piggly-pix/issues) ou enviar um e-mail para **dev@piggly.com.br**.

### Futuras implementações ###

Assim como uma transferência bancária tradicional, ainda não é possível validar se um Pix foi pago ou não (pelo menos, por enquanto). Então, é necessário identificar o pagamento Pix na conta titular e solicitar ao cliente que envie o comprovante de pagamento para cruzamento de dados.

Embora, o código Pix gerado por esse plugin inclua o número do pedido e o nome da Loja, alguns bancos ainda não possibilitam visualizar esses dados do Pix de origem. O próximo passo deste plugin por tanto é:

* Implementar o upload do comprovante no pedido.

## Como instalar? ##

### No diretório oficial do Wordpress ###

A página oficial do plugin pode ser encontrada em: [wordpress@wc-piggly-pix](https://wordpress.org/plugins/pix-por-piggly/).

### Neste repositório ###

Vá para (Releases)[https://github.com/piggly-dev/piggly-views-wordpress/releases] neste repositório e faça o download em `.zip` da versão mais recente.

Então, no **Painel Administrativo** do Wordpress, vá em `Plugins > Adicionar novo` e clique em `Upload plugin` no topo da página para enviar o arquivo `.zip`.

### Da origem ###

Você precisará do Git instalado para contruir da origem. Para completar os passos a seguir, você precisará abrir um terminal de comando. Clone o repositório:

`git clone https://github.com/piggly-dev/wc-piggly-pix.git`

## Como utilizar? ##

Após a instalação do plugin, vá até `Plugins > Plugins instalados`, ative o plugin **Pix por Piggly para Woocommerce**. Assim que você ativar, o plugin já estará disponível em `Woocommerce > Configurações > Pagamentos` e você poderá inserir todas as configurações pertinentes.

## Changelog ##

### 1.0.0 ### 

* Versão inicial do plugin.

## Contribuições ##

Todas as contribuições são bem-vindas e serão bem recebidas ❤. 
Não esqueça de ler nossas políticas de contribuição em [Contribuindo](https://github.com/piggly-dev/wc-piggly-pix/blob/master/CONTRIBUTING.md)