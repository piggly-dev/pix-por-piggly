=== Pix por Piggly (para Woocommerce) ===

Contributors: pigglydev, caiquearaujo
Tags: woocommerce, payment, pix, e-commerce, shop, ecommerce, pagamento
Requires at least: 4.0
Requires PHP: 7.2
Tested up to: 5.8
Stable tag: 2.0.11
License: GPLv2 or later
Language: pt_BR 
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Pix por Piggly v2.0.0 ==

**Importante**: Como a regra de versionamento de código manda, a versão 2.x será incompatível com a versão 1.x não tenha dúvidas disso. A versão 2.x foi projetada para ser totalmente compatível com as APIs do Pix, que atualizam automaticamente os pedidos, e essas APIs vão mudar sim o comportamento do Pix. Versões desatualizadas de MySQL e PHP podem ser o problema e dificultar a compatibilidade. E estamos nos esforçando para lançar micro-correções para essas necessidades. A qualquer momento é possível fazer o downgrade para a versão 1.x e continuar utilizando todos os recursos dela que já estão otimizados e não precisavam de atualização como uma versão 1.x.

O melhor plugin para pagamentos via Pix no Woocommerce. Na versão 2.0.0 o plugin está melhor mais dinâmico e muito mais responsivo. Veja mais detalhes.

**Sempre atualize para continuar aproveitando**

O **Pix** é o mais novo método de pagamento eletrônico criado pelo Banco Central do Brasil. Você encontra todos os detalhes na [página oficial](https://www.bcb.gov.br/estabilidadefinanceira/pix) do Pix.

O plugin é permitir que você utilize o método de pagamento Pix em sua loja **Woocommerce** sem qualquer custo de forma simples, rápida e direta. Acesse as configurações do plugin em `Pix por Piggly` no menu lateral.

> Se você apreciar a função deste plugin e quiser apoiar este trabalho, sinta-se livre para fazer qualquer doação para a chave aleatória Pix `aae2196f-5f93-46e4-89e6-73bf4138427b` ❤.

> Não esqueça de deixar a sua avaliação sobre o plugin! Isso nos incentivará a lançar mais atualizações e continuar prestando um suporte de qualidade.

== Recursos que só o Pix por Piggly tem ==

* ✅ Tratamento automático de dados, não se preocupe com o que você digita. O plugin automaticamente detecta melhorias;
* ✅ Permita que o cliente envie o comprovante por uma página segura, pelo Whatsapp e/ou Telegram;
* ✅ Atualize automaticamente o pedido quando um comprovante for enviado;
* ✅ Defina uma data de expiração de pagamento do Pix;
* ✅ Envio de e-mails para os eventos: quando o Pix estiver próximo de expirar, quando o Pix expirar, quando o Pix for pago e quando o Pix for criado para pagamento;
* ✅ Teste o seu Pix a qualquer hora, antes mesmo de habilitar o plugin;
* ✅ Aplique desconto automático, sem criação de cupons, ao realizar o pagamento via Pix;
* ✅ Visualize os dados do Pix gerado na página do pedido;
* ✅ Importe os dados Pix de uma chave Pix válida e preencha os dados da Conta Pix automaticamente;
* ✅ Utilize **Merge Tags**, em campos disponíveis, para substituir variáveis e customizar ainda mais as funções do plugin;
* ✅ Páginas dedicadas para o pagamento do Pix e envio de comprovantes, que podem ser acessados via “Minha Conta”;
* ✅ Suporte a API do Woocommerce.

== Versão `2.0.0` ==

Na versão **2.0.0** promovemos várias mudanças no formato no plugin, tanto para facilitar quando para deixar o fluxo de pagamento mais simples e dinâmico. Algumas opções foram removidas, enquanto outras foram mantidas. Leia abaixo em detalhes tudo que está diferente.

= E-mails =

= 👎 Antes =

👉 Era possível escolher o modelo de e-mail na qual o pagamento Pix seria anexado e, ainda, escolher a posição deste pagamento.

= ❌ Por que mudamos? =

Muitos relatavam conflitos e dificuldades para gerenciar o conteúdo do e-mail, enquanto outros utilizavam plugins desatualizados que quebravam os e-mails. Isso acontecia, pois dependiamos de uma `action` localizada no modelo de e-mail selecionado para carregar os dados do Pix.

= 👍 Agora =

👉 Criamos diversos modelos de e-mails, entre eles: quando o Pix estiver próximo de expirar, quando o Pix expirar, quando o Pix for pago e quando o Pix for criado para pagamento.

👉 Não anexamos mais as informações do Pix no e-mail para evitar **SPAM** e compartilhamento desnecessário dos dados. Criamos um link único para o cliente acessar e visualizar todos os dados de pagamento novamente.

= Comprovantes

= 👎 Antes =

👉 Era possível selecionar uma página para enviar o comprovante e utilizar qualquer formulário desejado. Também era possível utilizar o shortcode `[pix-por-piggly-form]` para utilizar o recurso nativo do plugin para recebimento de comprovantes.

= ❌ Por que mudamos? =

Alguns clientes enviavam de forma errada ou a forma como o shortcode `[pix-por-piggly-form]` era utilizado prejudicava a experiência criando diversos comprovantes desnecessários e produzindo muito lixo na pasta de uploads.

= 👍 Agora =

👉 Será utilizado um link permanente exclusivo para que o usuário faça o envio do comprovante Pix, garantindo todas as validações necessárias para que o usuário envie sempre para o pedido correto.

👉 O comprovante enviado será automaticamente associado ao Pix relacionado ao pedido e sempre será considerado o último comprovante enviado.

= Pedidos =

= 👎 Antes =

👉 Ao selecionar o Pix, o pedido automaticamente migrava o status para `Aguardando o Pagamento`, também era possível utilizar o status `Comprovante Pix Recebido` quando o comprovante era enviado.

= ❌ Por que mudamos? =

Alguns usuários acharam o status `Comprovante Pix Recebido` muito complicado e tinham rotinas que impediam o uso.

Migrar para o status `Aguardando o Pagamento` também não é mais uma opção, uma vez que os Pix podem ser confirmados tanto por API quanto por comprovantes.

= 👍 Agora =

👉 Agora, por padrõa, o pedido ficará como `Pendente` atéq ue o cliente envie o comprovante ou que uma API Pix atualize o Pix como pago.

👉 Quando o cliente enviar um comprovante, o status é migrado para sair da situação como `Pendente`.

👉 Também foi adicionado um recurso para atualizar automaticamente o status do pedido para `Pago` quando o Pix for pago.

👉 Tanto o status para Comprovante Enviado quanto para Pedido Pago podem ser configurados. **Não recomendamos que o Comprovante Enviado marque o pedido como pago...**

= Endpoints =

= 👍 Agora =

👉 Foram criados dois endpoints exclusivos dentro do ambiente "Minha Conta" do Woocommerce. Um para o realizar o pagamento pendente do Pix e outro para enviar o comprovante de pagamento.

👉 Os endpoints podem ser acessados a qualquer momento desde que o cliente tenha autorização e eles estejam liberados para acesso.

= Templates =

= 👍 Agora =

👉 Atualizamos todos os templates, será necessário revisá-los para que eles funcionem corretamente caso você tenha realizado alguma personalização.

= Filtros e Ações =

A partir da versão **2.0.0** é possível utilizar os filtros e as ações abaixo:

= 👍 Filtros =

* `pgly_wc_piggly_pix_discount_applied` Personaliza o valor calculado para o desconto antes de aplicar;
* `pgly_wc_piggly_pix_payload` Personaliza ou altera o objeto Payload do Pix antes de gerar o código;
* `pgly_wc_piggly_pix_pending_status` Personaliza o status de `pending` ao criar um pedido com pagamento Pix;
* `pgly_wc_piggly_pix_process` Personaliza o objeto `PixEntity` antes de processar o Pix.

= 👍 Ações =

* `pgly_wc_piggly_pix_webhook` Executa o webhook do Pix;
* `pgly_wc_piggly_pix_to_pay` Logo após o Pix ser criado e associado ao pedido;
* `pgly_wc_piggly_pix_after_save_receipt` Logo após criar (e salvar) o comprovante do Pix;
* `pgly_wc_piggly_pix_close_to_expires` Quando o Pix está próximo da expiração.

= Principais melhorias =

* ✅ Alteração da cor do ícone do Pix;
* ✅ Formulário integrado para envio de comprovante;
* ✅ (Opcional) Atualização automática do pedido com comprovante recebido;
* ✅ Desconto automático para pagamento via Pix.
* ✅ Reformulação das configurações;
* ✅ Criação da metabox Pix nos pedidos pagos via Pix;
* ✅ Otimização para geração dos QR Codes;
* ✅ Desconto automático para pagamento via Pix.

== Performance ==

Toda a estrutura e código do plugin foram atualizados para garantir a melhor performance, agora todos os dados de um Pix criado são salvos em uma tabela de dados que evita a recriação do Pix. Os QR Codes e os comprovantes também são associados ao Pix que permite um único arquivo de cada, mantendo a biblioteca de uploads sempre limpa.

== Como funciona? ==

Assim como é feito atualmente com uma transferência eletrônica no Woocommerce, o **Pix por Piggly** permite aos consumidores escolherem o método de pagamento Pix, então eles recebem as instruções de pagamento e enviam o comprovante. Você também pode aplicar um desconto automático para pagamentos via Pix.

Bem simples né?

Para permitir isso, nosso plugin segue todas as propostas do padrão EMV®1, implementado pelo Banco Central do Brasil, você pode ler mais sobre isso em [Especificações Técnicas](https://www.bcb.gov.br/content/estabilidadefinanceira/forumpireunioes/Anexo%20I%20-%20QRCodes%20-%20Especifica%C3%A7%C3%A3o%20-%20vers%C3%A3o%201-1.pdf). Utilizamos a nossa bibliteca [piggly/php-pix](https://github.com/piggly-dev/php-pix) para manipular e gerar os códigos pix.

Dessa forma, nosso plugin gera os três principais métodos de pagamento Pix:

1. Um **QR Code** com o código Pix;
2. O código Pix em formato de texto para utilização da função **Pix Copia & Cola**; 
3. Pagamento manual com os dados Pix fornecidos.

Nosso plugin gera de forma automática o código Pix com base nas informações do Pedido e nas informações preenchidas na configuração do plugin. 

> Não importa como você digita a chave Pix, ela será automaticamente convertida para os formatos apropriados, okay? Caso ela esteja inválida, de acordo com o formato escolhido, você será notificado.

== Testes realizados ==

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

== Perguntas Frequentes ==

= Qual é a licença do plugin? =

Este plugin esta licenciado como GPLv2. Ele é distrubuido de forma gratuita.

= O que eu preciso para utilizar este plugin? =

* Ter instalado o Wordpress 4.0 ou superior;
* Ter instalado o plugin WooCommerce 3.0 ou superior;
* Utilizar a versão 7.2 do PHP;
* Ter a extensão `gd` para PHP habilitada, veja detalhes [aqui](https://www.php.net/manual/pt_BR/book.image.php);
* Possuir uma conta bancária com Chave Pix.

= Posso utilizar com outros gateways de pagamento? =

Sim, esse plugin funciona apenas como um método de pagamento adicional, assim como acontece com o método de transferência eletrônica.

= Como aplicar desconto automático? =

Na página de configurações do Plugin, acesse **Pedidos** e insira um valor e um rótulo para o desconto Pix. O desconto será automaticamente aplicado quando o cliente escolher o método de pagamento Pix.

= Como conferir o pagamento Pix? =

A conferência do Pix ainda é manual, assim como acontece em uma transferência eletrônica. Para facilitar, o plugin gera os Pix com um código identificador. Esse código possuí um valor estático de 25 caracteres. 

Abra o pedido criado no Woocommerce e verifique o código identificador do Pix, ao abrir o aplicativo do seu banco, você poderá ver detalhes sobre o recebimento Pix e, na maioria dos bancos, o pagamento estará identificado com o código identificador do Pix.

= Não tem como atualizar o pagamento Pix automaticamente?

Para validar se um Pix foi pago a maioria dos bancos emissores irão cobrar taxas, assim como os intermediadores de pagamento. Se você faz parte de um banco emissor que já implementa a API Pix, pode entrar em contato com a gente em [dev@piggly.com.br](mailto:dev@piggly.com.br) para que possamos implementar a solução.

= Gerei o código Pix, mas não consigo efetuar o pagamento. E agora? =

Nas configurações do Plugin acesse "Suporte" e verifique a seção "O plugin gera o QR Code, mas não consigo pagá-lo", lá estarão algumas dicas automáticas que podem ajudar você. Se ainda sim precisar de algum suporte, abra um chamado enviando um e-mail para [dev@piggly.com.br](mailto:dev@piggly.com.br).

= Como customizar os templates? =

Nas configurações do Plugin acesse "Suporte" e verifique a seção "Como substituir os templates de e-mail e da página de obrigado".

> **AVISO**: Ao customizar os templates você pode perder funcionalidades importantes do plugin e comportamentos pré-existentes nos templates originais. Tenha certeza sobre o que está fazendo para garantir que tudo funcione como deve ser. **Não prestaremos suporte para customizações**.

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
4. Configurações gerais do método de pagamento;
5. Preencha os dados da sua conta Pix;
6. Ou, importe os dados Pix de um código Pix Copia & Cola;
7. Configure pedidos, e-mails e desconto automático para o pagamento Pix;
8. Controle o envio de comprovantes dos pagamentos via Pix;
9. Teste o pagamento via Pix antes de habilitar o módulo;
10. Metabox Pix no pedido realizado via Pix.

== Changelog ==

= 2.0.11 =

- Opção para reduzir o estoque do pedido assim que o Pix é criado;
- Posição dos links de comprovante;
- Estoque reduzido assim que o comprovante Pix é enviado;
- Opção de ocultar valor do Pix antes dos dados Pix.

= 2.0.9/2.0.10 =

- Micro-correções.

= 2.0.8 =

- Correção no banco de dados.

= 2.0.7 =

- Correção no banco de dados.
- Notificação sobre atualização dos Links permanentes.

= 2.0.6 =

- Correção para salvar informações de desconto Pix.

= 2.0.4/2.0.5 =

- Correção no banco de dados.

= 2.0.3 =

- Correção para aceitar a ausência de banco no Pix.

= 2.0.2 =

- Notifica sobre atualização dos endpoints.

= 2.0.1 =

- Micro correções.

= 2.0.0 =

- Novo release com mudanças substanciais no núcleo do plugin.

= 1.3.14 =

* Bug no shortcode `[pix-por-piggly]` que não retorna o template;
* Bug no desconto de pagamento e valor corrigido quando há cupom de desconto aplicado;
* Liberação da tela para APIs;
* Acionamento de actions e filters.

= 1.3.13 =

* Aumento de segurança na validação dos arquivos enviados como comprovantes;
* Correção de bug na página de "Comprovantes Pix";
* Outras melhorias e correções.

= 1.3.12 =

* Pequenas correções e melhorias.

= 1.3.11 =

* Correção de exibição duplicada dos shortcodes;
* Melhorias no sistema de upload dos comprovantes;
* Redirecionamento após comprovante recebido com sucesso;
* Outras correções e melhorias.

= 1.3.10 =

* Validação dos arquivos .htaccess;
* Correção de problemas com valores Pix.

= 1.3.9 =

* Bug na exibição do desconto no HTML;
* Formato numérico corrigido na página de pagamento via Pix.

= 1.3.8 =

* Gestão eficiente e otimizada dos comprovantes Pix para exclusão e busca de comprovantes.

= 1.3.7 =

* Correção no arquivo `.htaccess` que gera um erro 403 ao acessar os comprovantes.

= 1.3.6 =

* Descrição avançada com Pix com passos para pagamento.

= 1.3.5 =

* Escolher cor do ícone para o Pix;
* Ocultar o status "Comprovante Pix Recebido" no painel de pedidos;
* Correções e melhorias indicadas no suporte.

= 1.3.4 =

* Correção do bug para a primeira instalação do plugin, retornando valores vazios.

= 1.3.3 =

* A mudança do status para "Comprovante Pix Recebido" tornou-se opcional.

= 1.3.2 =

* Correção para ocultar o botão "Enviar Comprovante".

= 1.3.1 =

* Correção do erro fatal no método remove_qr_image.

= 1.3.0 =

* Suporte a formulário nativo para envio dos comprovantes;
* Melhorias no shortcode [pix-por-piggly];
* Melhorias e correções em gerais.

= 1.2.4 =

* Atualização dos paineis de configuração;
* Melhoria na criação dos arquivos de QR Code contra erros de cachê;
* Suporte a API do Woocommerce;
* Correção da leitura de telefones internacionais no campo de Telefone do Whatsapp.

= 1.2.3 =

* Corrige avisos do PHP e permite o ID da transação vazio como `***`.

= 1.2.2 =

* Correção da ausência do botão em Teste seu Pix.

= 1.2.1 =

* Auto corrige automaticamente os campos do dados Pix baseado no Banco selecionado.

= 1.2.0 =

* Reformulação das configurações;
* Criação da metabox Pix nos pedidos pagos via Pix;
* Otimização da geração dos QR Codes;
* Desconto automático para pagamento via Pix.

= 1.1.14 =

* Dicas de apoio para preenchimento do Pix;
* Correções dos botões Whatsapp e Telegram no e-mail;
* Link para ver o pedido no e-mail ao invés do link para pagamento;
* Correções ao salvar configurações;
* Adição do caminho para sobrescrever os templates.

= 1.1.13 =

* Adição do botão de configuração e ajustes na importação;

= 1.1.12 =

* Correções de bugs;

= 1.1.11 =

* Melhorias no texto de apoio e captura de erros com textos de apoio;

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

== Upgrade Notice ==

= 2.0.0 =

* Revise as configurações do plugin, mudanças substanciais foram feitas.
