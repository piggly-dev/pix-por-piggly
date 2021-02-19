=== Pix por Piggly ===

Contributors: pigglydev, caiquearaujo
Tags: woocommerce, payment, pix, e-commerce, shop, ecommerce, pagamento
Requires at least: 4.0
Requires PHP: 7.2
Tested up to: 5.6
Stable tag: 1.1.10
License: GPLv2 or later
Language: pt_BR 
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Pix por Piggly adiciona suporte para pagamentos via Pix sem taxas. Precisa do Woocommerce para funcionar. **Compatível com Woocommerce 3.x**.

== Bem-vindo ao Pix por Piggly ==

> Nós iremos continuamente atualizar esse plugin baseado nas experiências de vocês e nos bugs que forem encontrados, por isso, mantenham o plugin sempre atualizado e abram um chamado no suporte ou enviem um e-mail para [dev@piggly.com.br](mailto:dev@piggly.com.br) para que possamos continuamente melhorar esse plugin juntos.

O **Pix** é o mais novo método de pagamento eletrônico criado pelo Banco Central do Brasil.
Você encontra todos os detalhes na [página oficial](https://www.bcb.gov.br/estabilidadefinanceira/pix) do Pix.

O propósito deste plugin é permitir que você utilize o método de pagamento Pix em sua loja **Woocommerce** sem qualquer custo de forma simples, rápida e direta. Acesse as configurações do plugin em `Woocommerce > Configurações > Pagamentos > Pix`.

* Compatível com o Pix versão 2.2.1, veja mais detalhes [clicando aqui](https://www.bcb.gov.br/content/estabilidadefinanceira/pix/Regulamento_Pix/II-ManualdePadroesparaIniciacaodoPix.pdf). Se você tiver problemas de incompatibilidade, sugirimos o uso da Chave Aleatória da sua conta Pix.

**Quer saber mais?** Assista ao vídeo tutorial de configuração no [Youtube](https://www.youtube.com/watch?v=PqRqXFgWOsg&t=200s).

> Se você apreciar a função deste plugin e quiser apoiar este trabalho, sinta-se livre para fazer qualquer doação para a chave aleatória Pix `aae2196f-5f93-46e4-89e6-73bf4138427b` ❤.

> Não esqueça de deixar a sua avaliação sobre o plugin! Isso nos incentivará a lançar mais atualizações e continuar prestando um suporte de qualidade.

== Vantagens do Pix por Piggly para Woocommerce ==

**Sempre atualize para continuar aproveitando**

* Importe os dados Pix de uma chave Pix válida e não precise digitar manualmente os dados da sua conta Pix;
* Direcione o usuário para qualquer página carregando o código do Pedido, seja uma página para enviar comprovantes ou um link para enviar via Whatsapp e/ou Telegram;
* Utilize Merge Tags em campos disponíveis para substituir variáveis e customizar ainda mais as funções do plugin;
* Não se preocupe com a formatação, nós formatamos corretamente todos os dados preenchidos no plugin para máxima compatibilidade e facilidade;
* Teste antes de habilitar! Insira um valor e um número do pedido fictício para simular o código Pix e verificar se está tudo como você espera. Você ainda pode usar essa função para gerar pagamentos Pix e enviar para clientes via Whatsapp e/ou Telegram;
* Selecione o modelo de e-mail onde o Pix será enviado e o status do pedido enquanto aguarda a conferência do pagamento Pix;

== Sobre a versão 1.1.2 e acima ==

Estamos contentes com os feedbacks que recebemos. Por essa razão, atualizamos as páginas de configurações do plugin. Deixamos muito mais explicativas e detalhadas. Além de implementarmos a "Importação Pix" que irá ler um código Pix válido que você tenha e importar os dados para as configurações do plugin. Também acrescentamos botões específicos para enviar comprovantes via Whatsapp e/ou Telegram personalizando a mensagem. Bem legal, né? Continuem dando o feedback de vocês para que possamos sempre melhorar.

= 1.1.5 e acima =

O comportamento do **identificador** do pedido no Pix foi atualizado. Nas versões mais recentes, o Pix aceita apenas caracteres entre A a Z e dígitos entre 0 a 9.

= 1.1.6 e acima =

Controle do e-mail com código Pix e do status do pedido.

== Como funciona? ==

Assim como é feito atualmente com uma transferência eletrônica no Woocommerce, o **Pix por Piggly** permite aos consumidores escolherem o método de pagamento Pix, então eles recebem as instruções de pagamento e enviam o comprovante, bem simples né?

Para permitir isso, nosso plugin segue todas as propostas do padrão EMV®1, implementado pelo Banco Central do Brasil, você pode ler mais sobre isso em [Especificações Técnicas](https://www.bcb.gov.br/content/estabilidadefinanceira/forumpireunioes/Anexo%20I%20-%20QRCodes%20-%20Especifica%C3%A7%C3%A3o%20-%20vers%C3%A3o%201-1.pdf). Utilizamos a nossa bibliteca [piggly/php-pix](https://github.com/piggly-dev/php-pix) para manipular e gerar os códigos pix.

Dessa forma, nosso plugin gera os três principais métodos de pagamento:

1. Um **QR Code** com o código Pix;
2. O código Pix em formato de texto para utilização da função **Pix Copia & Cola**; 
3. Pagamento manual com os dados Pix fornecidos.

Nosso plugin gera de forma automática o código Pix com base nas informações do Pedido e nas informações preenchidas na configuração do plugin. 

> Não importa como você digita a chave Pix, ela será automaticamente convertida para os formatos apropriados, okay? Caso ela esteja inválida, de acordo com o formato escolhido, você será notificado.

== Funcionalidades ==

Nas configurações do plugin você é capaz de manipular as seguintes funções:

1. Importar configurações de um código Pix pré-existente;
2. Habilitar/Desabilitar o método de pagamento;
3. Alterar o título e descrição do pagamento;
4. Exibir os métodos QR Code, Pix Copia & Cola e Pagamento Manual;
5. Informar o nome da loja para a descrição do Pix;
6. Inserir o nome e a cidade do titular da conta Pix;
7. Escolher o tipo de chave e informar o valor da chave Pix;
8. Escrever as instruções para envio do comprovante de pagamento;
9. Alterar o formato do identificador do Pix;
10. Incluir uma página para enviar o comprovante;
11. Testar o pix com qualquer valor.

= Quer receber comprovantes? O que sugerimos: =

> **Cuidado com o envio de comprovantes**. O upload de arquivos no Wordpress é delicado. É necessário tomar certas precauções e por isso utilize alguns plugins que já fazem isso muito bem como o **Gravity Forms** ou **WP Forms**.

Crie uma nova página no Wordpress com um formulário para envio de arquivos, permitindo apenas as extensões jpg, png e pdf. Isso pode ser feito utilizando os plugins citados acima. Posteriormente, nas configurações do Pix em **Página para Comprovante** insira a URL da página para enviar o comprovante. 

Você pode utilizar `{{pedido}}` na URL pois esse termo será substituído pelo número do pedido. Assim, caso seu formulário permita o auto preenchimento via URL conseguirá preencher automaticamente o número do pedido para o cliente.

Por exemplo, com o número do pedido `1234` defina a URL em **Página para Comprovante** como, por exemplo, `https://minhaloja.com.br/comprovante-pix/?order_id={{pedido}}`. Nosso plugin traduzirá essa URL para `https://minhaloja.com.br/comprovante-pix/?order_id=1234`, basta então ler o campo `order_id` da URL com o seu formulário no campo apropriado.

Você também pode inserir seu número do Whatsapp e/ou usuário do Telegram para que seu cliente envie o comprovante de pagamento Pix por esses canais.

= Testes realizados =

O código Pix gerado por esse plugin, incluindo a função **QR Code** e **Pix Copia & Cola**, foi testado nos seguintes aplicativos de banco:

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

Como o código utiliza o padrão do Pix é possível que funcione em todos os bancos. Mas, caso encontre problemas ou dificuldades não hesite em abrir uma [thread](https://wordpress.org/support/plugin/pix-por-piggly/) no Suporte do Plugin ou enviar um e-mail para **[dev@piggly.com.br](mailto:dev@piggly.com.br)**.

= Futuras implementações =

Assim como uma transferência bancária tradicional, ainda não é possível validar se um Pix foi pago ou não (pelo menos, por enquanto). Então, é necessário identificar o pagamento Pix na conta titular e solicitar ao cliente que envie o comprovante de pagamento para cruzamento de dados.

Embora, o código Pix gerado por esse plugin inclua o número do pedido e o nome da Loja, alguns bancos ainda não possibilitam visualizar esses dados do Pix de origem.

Nosso próximo passo é possibilitar o suporte para Pix Dinâmicos, esperamos em breve colocar um suporte as APIs para fazer esses tipos de transações. Por enquanto, a comunicação entre bancos e provedores de pagamento está muito complicada e precisará ser feita de forma manual.

Algumas melhorias no layout da página de pagamento Pix estão sendo planejadas.

== Frequently Asked Questions ==

= **Qual é a licença do plugin?** =

Este plugin esta licenciado como GPL.

= **O que eu preciso para utilizar este plugin?** =

* Ter instalado o Wordpress 4.0 ou superior;
* Ter instalado o plugin WooCommerce 3.0 ou superior;
* Utilizar a versão 7.2 do php;
* Possuir uma conta bancária com Chave Pix.

= **Posso utilizar com outros gateways de pagamento?** =

Sim, esse plugin funciona apenas como um método de pagamento adicional, assim como acontece com o método de transferência eletrônica.

= **Como conferir o pagamento Pix?** =

A conferência do Pix ainda é manual, assim como acontece em uma transferência eletrônica. Para facilitar, o plugin gera os Pix com um código identificador. Esse código possuí o número do pedido e você pode personalizá-lo na página de configurações. Ao abrir o aplicativo do seu banco, você poderá ver detalhes sobre o recebimento Pix e lá estará o código identificador do Pix.

= **O código Pix ou QR Code está inválido! O que fazer?** =

O Pix ainda é muito recente e, apenas das padronizações do Banco Central do Brasil, muitos bancos criaram algumas variações e definiram como aceitam determinadas chaves. A nossa recomendação principal é: **utilize as chaves aleatórias**. Assim, você não expõe seus dados e ao mesmo tempo tem compatibilidade total de pagamentos.

Há alguns relatos que alguns bancos leem o QR Code, mas não leem o Pix Copia & Cola. Este não é um problema do plugin, o código Pix de ambos são o mesmo! Caso esteja curioso, abra um leitor de QR Code e leia o código é examente o mesmo que o Pix Copia & Cola.

Neste caso, precisamos verificar cada caso. E você pode contribuir com isso enviando um e-mail para [dev@piggly.com.br](mailto:dev@piggly.com.br). Ao enviar um e-mail, certifique-se de informar:

* Versão do Wordpress;
* Versão do WooCommerce;
* Banco Emitente (Conta Pix);
* Banco Pagador (que está utilizando o Código Pix);
* Tipo de Erro;
* Chave Pix gerada;

== Como instalar? ==

= No diretório oficial do Wordpress =

A página oficial do plugin pode ser encontrada em: [wordpress@pix-por-piggly](https://wordpress.org/plugins/pix-por-piggly/).

= No repositório do Github =

Vá para [Releases](https://github.com/piggly-dev/piggly-views-wordpress/releases) neste repositório e faça o download em `.zip` da versão mais recente.

Então, no **Painel Administrativo** do Wordpress, vá em `Plugins > Adicionar novo` e clique em `Upload plugin` no topo da página para enviar o arquivo `.zip`.

> Você precisará, posteriormente, ir até a pasta do plugin no terminal do seu servidor Web e executar o comando `composer install` caso escolha essa opção.

= Da origem =

Você precisará do Git instalado para contruir da origem. Para completar os passos a seguir, você precisará abrir um terminal de comando. Clone o repositório:

`git clone https://github.com/piggly-dev/wc-piggly-pix.git`

> Você precisará, posteriormente, executar o comando `composer install` caso escolha essa opção.

== Como utilizar? ==

Após a instalação do plugin, vá até `Plugins > Plugins instalados`, ative o plugin **Pix por Piggly para Woocommerce**. Assim que você ativar, o plugin já estará disponível em `Woocommerce > Configurações > Pagamentos` e você poderá inserir todas as configurações pertinentes.

**Preencha corretamente a sua chave Pix. Você pode testar nas configurações do plugin o funcionamento do Pix mesmo que o módulo esteja desativado.**

== Screenshots ==

1. Exemplo do método de pagamento durante o Checkout;
2. Exemplo das instruções com as informações de pagamento;
3. Método de pagamento nas configurações do Woocommerce;
4. Configurações do método de pagamento.

== Changelog ==

= 1.1.10 =

* Correção de bug no envio de e-mail;

= 1.1.9 =

* Correção de bugs para versões 7.3- do PHP;

= 1.1.8 =

* Correção de bugs, melhorias da documentação, controle de erros e inserção nas instruções via e-mail;

= 1.1.7 =

* Correções e melhorias;

= 1.1.6 =

* Correção do bug no campo Whatsapp, correção dos bugs com chaves vazias, controladores de e-mail e status;

= 1.1.5 =

* Atualização da formatação do campo **Identificador**;

= 1.1.4 =

* Botões para Whatsapp e Telegram, além de melhorias no layout;

= 1.1.3 =

* Suporte para o PHP 7.2 (conforme solicitado por muitos utilizadores);

= 1.1.2 =

* Atualização da biblioteca `piggly/php-pix` e do painel de configurações;

= 1.1.1 =

* Atualização da biblioteca `piggly/php-pix`;

= 1.1.0 =

* Correções de bugs;
* Melhorias na exibição do Pix no e-mail e na tela;
* Ajuste de visualizações com base nas opções selecionadas;
* Melhorias no núcleo do plugin;

= 1.0.3/1.0.4 =

* Correções de bugs e reposicionamento das descrições;

= 1.0.2 =

* Melhorias no design das informações de pagamento;

= 1.0.1 =

* Melhorias no design das informações de pagamento;
* Correções de pequenos bugs;
* Inclusão para encaminhar até a página para upload de arquivos;
* Inclusão da página "Teste seu Pix".

= 1.0.0 = 

* Versão inicial do plugin.

== Contribuições ==

Todas as contribuições são bem-vindas e serão bem recebidas ❤. 
Não esqueça de ler nossas políticas de contribuição em [Contribuindo](https://github.com/piggly-dev/wc-piggly-pix/blob/master/CONTRIBUTING.md).