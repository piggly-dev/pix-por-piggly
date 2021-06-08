# Bem-vindo ao Pix por Piggly #

O melhor plugin para pagamentos via Pix no Woocommerce. Aplique desconto automático, personalize o comportamento e muito mais. Em breve, APIs Pix para atualizar o pedido automaticamente.

**Sempre atualize para continuar aproveitando**

O **Pix** é o mais novo método de pagamento eletrônico criado pelo Banco Central do Brasil.
Você encontra todos os detalhes na [página oficial](https://www.bcb.gov.br/estabilidadefinanceira/pix) do Pix.

O propósito deste plugin é permitir que você utilize o método de pagamento Pix em sua loja **Woocommerce** sem qualquer custo de forma simples, rápida e direta. Acesse as configurações do plugin em `Woocommerce > Configurações > Pagamentos > Pix`.

**Quer saber mais?** Assista ao vídeo tutorial de configuração no [Youtube](https://www.youtube.com/watch?v=PqRqXFgWOsg&t=200s).

> Se você apreciar a função deste plugin e quiser apoiar este trabalho, sinta-se livre para fazer qualquer doação para a chave aleatória Pix `aae2196f-5f93-46e4-89e6-73bf4138427b` ❤.

> Não esqueça de deixar a sua avaliação sobre o plugin! Isso nos incentivará a lançar mais atualizações e continuar prestando um suporte de qualidade.

# Novidades sobre a API Pix e atualização automática dos Pedidos #

Estamos preparando para vocês uma novidade única e imperdível. Até o final deste mês lançaremos a versão 1.4 para suporte as API Pix. Dessa forma, os Pix poderão ser verificados e processados automaticamente. Atualizando os pedidos no Woocommerce de forma automatizada. 

**Fique ligado nas próximas atualizações**.


# Filtros e Ações

A partir da versão 1.3.14 é possível utilizar os filtros e as ações abaixo:

## Filtros

* `wpgly_pix_discount` Personaliza o valor calculado para o desconto antes de aplicar.
* `wpgly_pix_before_create_pix_code` Personaliza ou altera o objeto Payload do Pix antes de gerar o código.
* `wpgly_pix_before_save_pix_metadata` Personaliza os metadados do Pix que serão salvos ao pedido antes de salvar o pedido.
* `wpgly_pix_after_create_api_response` Personaliza a `array` que é retornada para a API do Woocomerce.

## Ações

* `wpgly_pix_after_save_receipt_to_order` É executado após salvar um comprovante Pix nos metadados do pedido.
* `wpgly_pix_after_delete_receipt_from_order` É executado após deletar um comprovante Pix de um pedido.
* `wpgly_pix_after_process_payment` É executado durante o processamento do pagamento.

# Novidades da versão 1.3.0 #

Na versão **1.3.0** do plugin é possível:

1. Utilizar o shortcode `[pix-por-piggly-form]` para receber comprovantes sem precisar de plugins de terceiro. Ao utilizar o shortcode, os dados do pedido são capturados automaticamente. Quando um comprovante é enviado, o arquivo é verificado e salvo. O pedido também será atualizado para "Comprovante Pix Recebido". Veja mais detalhes na página de configuração do plugin;
2. Utilizar o shortcode `[pix-por-piggly]` sem enviar o parâmetro `order_id`. O ID do Pedido será capturado automaticamente se houver um pedido ativo na página na qual o shortcode foi posicionado.

## Principais melhorias ##

* ✅ Alteração da cor do ícone do Pix;
* ✅ Formulário integrado para envio de comprovante;
* ✅ (Opcional) Atualização automática do pedido com comprovante recebido;
* ✅ Desconto automático para pagamento via Pix.
* 
# Novidades da versão 1.2.0 #

A versão **1.2.0** mudou completamente o núcleo do plugin, para torná-lo mais eficiente e poderoso. Se você fez mudanças na estrutura do plugin esteja ciente que elas serão perdidas. Os templates de e-mail e do pagamento Pix foram atualizados para atender as melhorias.

## Principais Melhorias ##

* ✅ Reformulação das configurações;
* ✅ Criação da metabox Pix nos pedidos pagos via Pix;
* ✅ Otimização para geração dos QR Codes;
* ✅ Desconto automático para pagamento via Pix.

## Performance do QR Code ##

Antes, o plugin gerava o QR Code toda vez que o Pix era visto. E apresentava um "fix" para um e-mail que salvada um arquivo `.png` toda vez que o e-mail era enviado.

Para melhorar a performance do Pix e evitar processar desnecessariamente a imagem dos QR Codes. Agora, o plugin gerar o Pix pela primeira vez, salva o QR Code na pasta `uploads > pix-por-piggly > qr-codes` em um arquivo `.png` e grava nos meta dados do Pedido.

Dessa forma, se o pedido já foi pago, os meta dados serão mantidos e você sempre poderá conferir por qual chave aquele Pix foi pago, mesmo que decida mudar a chave.

Se o Pix ainda não foi pago, será gerado novamente somente se você mudar a chave Pix por qualquer razão. Do contrário, os meta dados gravado no pedido serão utilizados.

## Recursos que só o Pix por Piggly tem ##

* ✅ Tratamento automático de dados, não se preocupe com o que você digita. O plugin automaticamente detecta melhorias;
* ✅ Permita que o cliente envie o comprovante por uma página, pelo Whatsapp e/ou Telegram;
* ✅ Teste o seu Pix a qualquer hora, antes mesmo de habilitar o plugin;
* ✅ Aplique desconto automático, sem criação de cupons, ao realizar o pagamento via Pix;
* ✅ Visualize os dados do Pix gerado na página do pedido;
* ✅ Importe os dados Pix de uma chave Pix válida e preencha os dados da Conta Pix automaticamente;
* ✅ Utilize **Merge Tags**, em campos disponíveis, para substituir variáveis e customizar ainda mais as funções do plugin.
* ✅ Use o shortcode [pix-por-piggly] para importar o template do Pix em qualquer lugar. Veja mais em Shortcodes nas configurações do plugin;
* ✅ Use o shortcode [pix-por-piggly-form] para criar automaticamente o formulário para envio do comprovante Pix. Veja mais em Shortcodes nas configurações do plugin;
* ✅ Selecione o modelo de e-mail onde o Pix será enviado e o status do pedido enquanto aguarda a conferência do pagamento Pix;
* ✅ Suporte a API do Woocommerce.

# Como funciona? #

Assim como é feito atualmente com uma transferência eletrônica no Woocommerce, o **Pix por Piggly** permite aos consumidores escolherem o método de pagamento Pix, então eles recebem as instruções de pagamento e enviam o comprovante. Você também pode aplicar um desconto automático para pagamentos via Pix.

Bem simples né?

Para permitir isso, nosso plugin segue todas as propostas do padrão EMV®1, implementado pelo Banco Central do Brasil, você pode ler mais sobre isso em [Especificações Técnicas](https://www.bcb.gov.br/content/estabilidadefinanceira/forumpireunioes/Anexo%20I%20-%20QRCodes%20-%20Especifica%C3%A7%C3%A3o%20-%20vers%C3%A3o%201-1.pdf). Utilizamos a nossa bibliteca [piggly/php-pix](https://github.com/piggly-dev/php-pix) para manipular e gerar os códigos pix.

Dessa forma, nosso plugin gera os três principais métodos de pagamento Pix:

1. Um **QR Code** com o código Pix;
2. O código Pix em formato de texto para utilização da função **Pix Copia & Cola**; 
3. Pagamento manual com os dados Pix fornecidos.

Nosso plugin gera de forma automática o código Pix com base nas informações do Pedido e nas informações preenchidas na configuração do plugin. 

> Não importa como você digita a chave Pix, ela será automaticamente convertida para os formatos apropriados, okay? Caso ela esteja inválida, de acordo com o formato escolhido, você será notificado.

## Recebimento de Comprovantes Pix ##

> Em breve, será disponibilizado no plugin a API Pix que atualizará automaticamente os pedidos, sem necessidade do envio de comprovantes.

A partir da versão **1.3.0** é possível utilizar o shortcode `[pix-por-piggly-form]` para receber automaticamente o comprovante de pagamento do Pix. Ao utilizar o shortcode você conta com algumas vantagens únicas:

* ✅ O pedido e o e-mail do consumidor são capturados automaticamente;
* ✅ Caso não seja possível identificar o pedido, será solicitado o e-mail e o número do pedido ao consumidor;
* ✅ O consumidor poderá anexar imagens em JPG ou PNG, além de documento em PDF;
* ✅ O arquivo enviado será analisado pelo plugin para determinar se é um arquivo seguro e válido;
* ✅ Após enviar o comprovante, o comprovante será imediatamente anexado ao pedido *(quando identificado)*;
* ✅ Quando o pedido receber um comprovante Pix, o status será alterado para **Comprovante Pix Recebido** *(opcionalmente)*;

### Tutorial Básico ###

* Crie uma nova página para receber os comprovantes Pix;
* Insira na página o shortcode [pix-por-piggly-form];
* Em "Comprovante Pix" nas configurações do plugin, insira o link permanente da página criada em "Link para a Página do Comprovante";
* Pronto! Agora, os comprovantes Pix já podem ser recebidos na página.

### Jeito Tradicional ###

Você ainda pode receber os comprovantes como era antes da versão **1.3.0**, utilizando plugins de terceiros para criação de formulários e, então, obtendo o número do pedido a partir da URL configurada em "Link para a Página do Comprovante".

Você pode utilizar `{{pedido}}` na URL para obter o número do pedido, esse termo será substituído adequadamente. Assim, caso seu formulário permita o auto preenchimento via URL conseguirá preencher automaticamente o número do pedido para o cliente.

Por exemplo, com o número do pedido `1234` defina a URL em **Link para a Página do Comprovante** como, por exemplo, `https://minhaloja.com.br/comprovante-pix/?order_id={{pedido}}`. Nosso plugin traduzirá essa URL para `https://minhaloja.com.br/comprovante-pix/?order_id=1234`, basta então ler o campo `order_id` da URL com o seu formulário no campo apropriado.

Você também pode inserir seu número do Whatsapp e/ou usuário do Telegram para que seu cliente envie o comprovante de pagamento Pix por esses canais.

## Testes realizados ##

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

# Perguntas Frequentes #

## Qual é a licença do plugin? ##

Este plugin esta licenciado como GPLv2. Ele é distrubuido de forma gratuita.

## O que eu preciso para utilizar este plugin? ##

* Ter instalado o Wordpress 4.0 ou superior;
* Ter instalado o plugin WooCommerce 3.0 ou superior;
* Utilizar a versão 7.2 do PHP;
* Ter a extensão `gd` para PHP habilitada, veja detalhes [aqui](https://www.php.net/manual/pt_BR/book.image.php);
* Possuir uma conta bancária com Chave Pix.

## Posso utilizar com outros gateways de pagamento? ##

Sim, esse plugin funciona apenas como um método de pagamento adicional, assim como acontece com o método de transferência eletrônica.

## Como aplicar desconto automático? ##

Na página de configurações do Plugin, acesse **Pedidos & E-mails** e insira um valor e um rótulo para o desconto Pix. O desconto será automaticamente aplicado quando o cliente escolher o método de pagamento Pix.

## Como conferir o pagamento Pix? ##

A conferência do Pix ainda é manual, assim como acontece em uma transferência eletrônica. Para facilitar, o plugin gera os Pix com um código identificador. Esse código possuí o número do pedido e você pode personalizá-lo na página de configurações do Plugin. 

Abra o pedido criado no Woocommerce e verifique o código identificador do Pix, ao abrir o aplicativo do seu banco, você poderá ver detalhes sobre o recebimento Pix e, na maioria dos bancos, o pagamento estará identificado com o código identificador do Pix.

## Não tem como atualizar o pagamento Pix automáticamente? ##

Para validar se um Pix foi pago a maioria dos bancos emissores irão cobrar taxas, assim como os intermediadores de pagamento. Se você faz parte de um banco emissor que já implementa a API Pix, pode entrar em contato com a gente em [dev@piggly.com.br](mailto:dev@piggly.com.br) para que possamos implementar a solução.

## Gerei o código Pix, mas não consigo efetuar o pagamento. E agora? ##

Nas configurações do Plugin acesse "Suporte" e verifique a seção "O plugin gera o QR Code, mas não consigo pagá-lo", lá estarão algumas dicas automáticas que podem ajudar você. Se ainda sim precisar de algum suporte, abra um chamado enviando um e-mail para [dev@piggly.com.br](mailto:dev@piggly.com.br).

## Como customizar os templates? ##

Nas configurações do Plugin acesse "Suporte" e verifique a seção "Como substituir os templates de e-mail e da página de obrigado".

> **AVISO**: Ao customizar os templates você pode perder funcionalidades importantes do plugin e comportamentos pré-existentes nos templates originais. Tenha certeza sobre o que está fazendo para garantir que tudo funcione como deve ser. **Não prestaremos suporte para customizações**.

# Como instalar? #

## No diretório oficial do Wordpress ##

A página oficial do plugin pode ser encontrada em: [wordpress@pix-por-piggly](https://wordpress.org/plugins/pix-por-piggly/).

## No repositório do Github ##

Vá para [Releases](https://github.com/piggly-dev/piggly-views-wordpress/releases) neste repositório e faça o download em `.zip` da versão mais recente.

Então, no **Painel Administrativo** do Wordpress, vá em `Plugins > Adicionar novo` e clique em `Upload plugin` no topo da página para enviar o arquivo `.zip`.

> Você precisará, posteriormente, ir até a pasta do plugin no terminal do seu servidor Web e executar o comando `composer install` caso escolha essa opção.

## Da origem ##

Você precisará do Git instalado para contruir da origem. Para completar os passos a seguir, você precisará abrir um terminal de comando. Clone o repositório:

`git clone https://github.com/piggly-dev/wc-piggly-pix.git`

> Você precisará, posteriormente, executar o comando `composer install` caso escolha essa opção.

# Como utilizar? #

Após a instalação do plugin, vá até `Plugins > Plugins instalados`, ative o plugin **Pix por Piggly para Woocommerce**. Assim que você ativar, o plugin já estará disponível em `Woocommerce > Configurações > Pagamentos` e você poderá inserir todas as configurações pertinentes.

**Preencha corretamente a sua chave Pix. Você pode testar nas configurações do plugin o funcionamento do Pix mesmo que o módulo esteja desativado.**

# Screenshots #

1. Exemplo do método de pagamento durante o Checkout;
2. Exemplo das instruções com as informações de pagamento;
3. Método de pagamento nas configurações do Woocommerce;
4. Configurações do método de pagamento.
5. Preencha os dados da sua conta Pix;
6. Ou, importe os dados Pix de um código Pix Copia & Cola;
7. Configure pedidos, e-mails e desconto automático para o pagamento Pix;
8. Controle o envio de comprovantes dos pagamentos via Pix;
9. Teste o pagamento via Pix antes de habilitar o módulo;
10. Metabox Pix no pedido realizado via Pix.

# Changelog #

## 1.3.14 ##

* Bug no shortcode `[pix-por-piggly]` que não retorna o template;
* Bug no desconto de pagamento e valor corrigido quando há cupom de desconto aplicado;
* Liberação da tela para APIs;
* Acionamento de actions e filters.

## 1.3.13 ##

* Aumento de segurança na validação dos arquivos enviados como comprovantes;
* Correção de bug na página de "Comprovantes Pix";
* Outras melhorias e correções.

## 1.3.12 ##

* Pequenas correções e melhorias.

## 1.3.11 ##

* Correção de exibição duplicada dos shortcodes;
* Melhorias no sistema de upload dos comprovantes;
* Redirecionamento após comprovante recebido com sucesso;
* Outras correções e melhorias.

## 1.3.10 ##

* Validação dos arquivos .htaccess;
* Correção de problemas com valores Pix.

## 1.3.9 ##

* Bug na exibição do desconto no HTML;
* Formato numérico corrigido na página de pagamento via Pix.

## 1.3.8 ##

* Gestão eficiente e otimizada dos comprovantes Pix para exclusão e busca de comprovantes.

## 1.3.7 ##

* Correção no arquivo `.htaccess` que gera um erro 403 ao acessar os comprovantes.

## 1.3.6 ##

* Descrição avançada com Pix com passos para pagamento.

## 1.3.5 ##

* Escolher cor do ícone para o Pix;
* Ocultar o status "Comprovante Pix Recebido" no painel de pedidos;
* Correções e melhorias indicadas no suporte.

## 1.3.4 ##

* Correção do bug para a primeira instalação do plugin, retornando valores vazios.

## 1.3.3 ##

* A mudança do status para "Comprovante Pix Recebido" tornou-se opcional.

## 1.3.2 ##

* Correção para ocultar o botão "Enviar Comprovante".

## 1.3.1 ##

* Correção do erro fatal no método remove_qr_image.

## 1.3.0 ##

* Suporte a formulário nativo para envio dos comprovantes;
* Melhorias no shortcode [pix-por-piggly];
* Melhorias e correções em gerais.

## 1.2.4 ##

* Atualização dos paineis de configuração;
* Melhoria na criação dos arquivos de QR Code contra erros de cachê;
* Suporte a API do Woocommerce;
* Correção da leitura de telefones internacionais no campo de Telefone do Whatsapp.

## 1.2.3 ##

* Corrige avisos do PHP e permite o ID da transação vazio como `***`.
 
## 1.2.2 ##

* Correção da ausência do botão em Teste seu Pix.

## 1.2.1 ##

* Auto corrige automaticamente os campos do dados Pix baseado no Banco selecionado.

## 1.2.0 ##

* Reformulação das configurações;
* Criação da metabox Pix nos pedidos pagos via Pix;
* Otimização da geração dos QR Codes;
* Desconto automático para pagamento via Pix.

## 1.1.14 ##

* Dicas de apoio para preenchimento do Pix;
* Correções dos botões Whatsapp e Telegram no e-mail;
* Link para ver o pedido no e-mail ao invés do link para pagamento;
* Correções ao salvar configurações;
* Adição do caminho para sobrescrever os templates.

## 1.1.13 ##

* Adição do botão de configuração e ajustes na importação;

## 1.1.12 ##

* Correções de bugs;

## 1.1.11 ##

* Melhorias no texto de apoio e captura de erros com textos de apoio;

## 1.1.10 ##

* Correção de bug no envio de e-mail;

## 1.1.9 ##

* Correção de bugs para versões 7.3- do PHP;

## 1.1.8 ##

* Correção de bugs, melhorias da documentação, controle de erros e inserção nas instruções via e-mail;

## 1.1.7 ##

* Correções e melhorias;

## 1.1.6 ##

* Correção do bug no campo Whatsapp, correção dos bugs com chaves vazias, controladores de e-mail e status;

## 1.1.5 ##

* Atualização da formatação do campo **Identificador**;

## 1.1.4 ##

* Botões para Whatsapp e Telegram, além de melhorias no layout;

## 1.1.3 ##

* Suporte para o PHP 7.2 (conforme solicitado por muitos utilizadores);

## 1.1.2 ##

* Atualização da biblioteca `piggly/php-pix` e do painel de configurações;

## 1.1.1 ##

* Atualização da biblioteca `piggly/php-pix`;

## 1.1.0 ##

* Correções de bugs;
* Melhorias na exibição do Pix no e-mail e na tela;
* Ajuste de visualizações com base nas opções selecionadas;
* Melhorias no núcleo do plugin;

## 1.0.3/1.0.4 ##

* Correções de bugs e reposicionamento das descrições;

## 1.0.2 ##

* Melhorias no design das informações de pagamento;

## 1.0.1 ##

* Melhorias no design das informações de pagamento;
* Correções de pequenos bugs;
* Inclusão para encaminhar até a página para upload de arquivos;
* Inclusão da página "Teste seu Pix".

## 1.0.0  ##

* Versão inicial do plugin.