# CHANGELOG

Abaixo, todos os registros de alteração do código-fonte.

## 1.x ##

### 1.0.0  ###

* Versão inicial do plugin.

### 1.0.1 ###

* Melhorias no design das informações de pagamento;
* Correções de pequenos bugs;
* Inclusão para encaminhar até a página para upload de arquivos;
* Inclusão da página "Teste seu Pix".

### 1.0.2 ###

* Melhorias no design das informações de pagamento;

### 1.0.3/1.0.4 ###

* Correções de bugs e reposicionamento das descrições;

### 1.1.0 ###

* Correções de bugs;
* Melhorias na exibição do Pix no e-mail e na tela;
* Ajuste de visualizações com base nas opções selecionadas;
* Melhorias no núcleo do plugin;

### 1.1.1 ###

* Atualização da biblioteca `piggly/php-pix`;

### 1.1.2 ###

* Atualização da biblioteca `piggly/php-pix` e do painel de configurações;

### 1.1.3 ###

* Suporte para o PHP 7.2 (conforme solicitado por muitos utilizadores);

### 1.1.4 ###

* Botões para Whatsapp e Telegram, além de melhorias no layout;

### 1.1.5 ###

* Atualização da formatação do campo **Identificador**;

### 1.1.6 ###

* Correção do bug no campo Whatsapp, correção dos bugs com chaves vazias, controladores de e-mail e status;

### 1.1.7 ###

* Correções e melhorias;

### 1.1.8 ###

* Correção de bugs, melhorias da documentação, controle de erros e inserção nas instruções via e-mail;

### 1.1.9 ###

* Correção de bugs para versões 7.3- do PHP;

### 1.1.10 ###

* Correção de bug no envio de e-mail;

### 1.1.11 ###

* Melhorias no texto de apoio e captura de erros com textos de apoio;

### 1.1.12 ###

* Correções de bugs;

### 1.1.13 ###

* Adição do botão de configuração e ajustes na importação;

### 1.1.14 ###

* Dicas de apoio para preenchimento do Pix;
* Correções dos botões Whatsapp e Telegram no e-mail;
* Link para ver o pedido no e-mail ao invés do link para pagamento;
* Correções ao salvar configurações;
* Adição do caminho para sobrescrever os templates.

### 1.2.0 ###

* Reformulação das configurações;
* Criação da metabox Pix nos pedidos pagos via Pix;
* Otimização da geração dos QR Codes;
* Desconto automático para pagamento via Pix.

### 1.2.1 ###

* Auto corrige automaticamente os campos do dados Pix baseado no Banco selecionado.

### 1.2.2 ###

* Correção da ausência do botão em Teste seu Pix.

### 1.2.3 ###

* Corrige avisos do PHP e permite o ID da transação vazio como `***`.

### 1.2.4 ###

* Atualização dos paineis de configuração;
* Melhoria na criação dos arquivos de QR Code contra erros de cachê;
* Suporte a API do Woocommerce;
* Correção da leitura de telefones internacionais no campo de Telefone do Whatsapp.

### 1.3.0 ###

* Suporte a formulário nativo para envio dos comprovantes;
* Melhorias no shortcode [pix-por-piggly];
* Melhorias e correções em gerais.

### 1.3.1 ###

* Correção do erro fatal no método remove_qr_image.

### 1.3.2 ###

* Correção para ocultar o botão "Enviar Comprovante".

### 1.3.3 ###

* A mudança do status para "Comprovante Pix Recebido" tornou-se opcional.

### 1.3.4 ###

* Correção do bug para a primeira instalação do plugin, retornando valores vazios.

### 1.3.5 ###

* Escolher cor do ícone para o Pix;
* Ocultar o status "Comprovante Pix Recebido" no painel de pedidos;
* Correções e melhorias indicadas no suporte.

### 1.3.6 ###

* Descrição avançada com Pix com passos para pagamento.

### 1.3.7 ###

* Correção no arquivo `.htaccess` que gera um erro 403 ao acessar os comprovantes.

### 1.3.8 ###

* Gestão eficiente e otimizada dos comprovantes Pix para exclusão e busca de comprovantes.

### 1.3.9 ###

* Bug na exibição do desconto no HTML;
* Formato numérico corrigido na página de pagamento via Pix.

### 1.3.10 ###

* Validação dos arquivos .htaccess;
* Correção de problemas com valores Pix.

### 1.3.11 ###

* Correção de exibição duplicada dos shortcodes;
* Melhorias no sistema de upload dos comprovantes;
* Redirecionamento após comprovante recebido com sucesso;
* Outras correções e melhorias.

### 1.3.12 ###

* Pequenas correções e melhorias.

### 1.3.13 ###

* Aumento de segurança na validação dos arquivos enviados como comprovantes;
* Correção de bug na página de "Comprovantes Pix";
* Outras melhorias e correções.

### 1.3.14 ###

* Bug no shortcode `[pix-por-piggly]` que não retorna o template;
* Bug no desconto de pagamento e valor corrigido quando há cupom de desconto aplicado;
* Liberação da tela para APIs;
* Acionamento de actions e filters.

## 2.x ##

### 2.0.0 ###

* Novo release com mudanças substanciais no núcleo do plugin.

### 2.0.1 ###

* Micro correções.

### 2.0.2 ###

* Notifica sobre atualização dos endpoints.

### 2.0.3 ###

* Correção para aceitar a ausência de banco no Pix.

### 2.0.4/2.0.5 ###

* Correção no banco de dados.

### 2.0.6 ###

* Correção para salvar informações de desconto Pix.

### 2.0.7 ###

* Correção no banco de dados.
* Notificação sobre atualização dos Links permanentes.

### 2.0.8 ###

* Correção no banco de dados.

### 2.0.9/2.0.11 ###

* Micro correções.

### 2.0.11 ###

* Opção para reduzir o estoque do pedido assim que o Pix é criado;
* Posição dos links de comprovante;
* Estoque reduzido assim que o comprovante Pix é enviado;
* Opção de ocultar valor do Pix antes dos dados Pix.

### 2.0.12/2.0.13 ###

* Correção de bugs nas configurações do plugin;
* Adição da personalização do status de aguardando o pagamento.

### 2.0.14 ###

* Personalização do destinatário dos e-mails administrativos;
* Correção no ícone do Pix;
* Opção para cancelar o pedido quando o Pix expirar;
* Recriação da cronjob ao atualizar.

### 2.0.15 ###

* Correção de bug na metabox do pedido;

### 2.0.16 ###

* E-mail quando o Pix for criado;
* Exibição da data de expiração no template de pagamento;
* Melhorias na interface de configuração;
* Melhorias de comunicação nas páginas do plugin.

### 2.0.17 ###

* Correção nos modelos de e-mail;

### 2.0.18 ###

* Correção da detecção de Pix próximo à expiração;
* Habilitação da API do Woocommerce para pedidos Pix;
* Melhoria nos logs de debug para identificar envio dos e-mails.

### 2.0.19 ###

* Correção de permissão de logs durante execução da cronjob;
* Correção na data de atualização do Pix;
* Correção no envio de notificação de Pix próximo a expiração;
* Remoção do Pix ao remover o pedido;
* Atualização automática para a página de pedido concluído, quando em uso da API do Pix;
* Correção da atualização da Cron Job para executar a cada minuto;
* Ação para executar os webhooks da API do Pix;
* Exibição do e2eid identificando o pagamento do Pix, quando em uso da API do Pix;
* Rotina para limpeza dos Pix expirados ou cancelados;
* Reorganização do menu Pix por Piggly.

### 2.0.20 ###

* Melhorias nas mensagens de retorno de erro e sucesso.

### 2.0.21 ###

* Bug na atualização do Pix.

### 2.0.22 ###

* Limpeza dos logs;
* Alerta de Modo Debug ativo;
* Filtragem básica dos Pix criados.

### 2.0.23 ###

* Pequenas correções;
* Prevenção de cancelamento automático.

### 2.0.24 ###

* Pequenas correções.

### 2.0.25 ###

* Adição do menu "Processamento do Pix" para maior controle da cronjob e do processamento imediato do Pix.
* Novos avisos adicionados e nomenclatura dos Pix alterada.

### 2.0.26 ###

* Correção de problema na atualização de configuração do Pix.

### 2.0.27/2.0.28 ###

* Correções do Wordpress.

## 3.x ##

### 3.0.0 ###

* Renovação das funcionalidades completa do plugin, otimizando problemas e corrigindo todos os bugs anteriores reescrevendo todas as funcionalidades.