# Pix por Piggly v3.0.0

[WORK IN PROGRESS]

![Branch Lançamento](https://img.shields.io/badge/branch%2Fmaster%20v3.x.x-brightgreen?style=flat-square) ![Branch Desenvolvimento](https://img.shields.io/badge/branch%2Fdev-dev%20v3.x.x-orange?style=flat-square) ![Versão Atual](https://img.shields.io/badge/version-v2.0.25-brightgreen?style=flat-square) ![PHP](https://img.shields.io/badge/php-%5E7.2%20%7C%20%5E8.0-blue?style=flat-square) ![Software License](https://img.shields.io/badge/license-GPL%202.0-brightgreen?style=flat-square)

**Importante**: Como a regra de versionamento de código manda, a versão 2.x será incompatível com a versão 1.x não tenha dúvidas disso. A versão 2.x foi projetada para ser totalmente compatível com as APIs do Pix, que atualizam automaticamente os pedidos, e essas APIs vão mudar sim o comportamento do Pix. Versões desatualizadas de MySQL e PHP podem ser o problema e dificultar a compatibilidade. E estamos nos esforçando para lançar micro-correções para essas necessidades. A qualquer momento é possível fazer o downgrade para a versão 1.x e continuar utilizando todos os recursos dela que já estão otimizados e não precisavam de atualização como uma versão 1.x.

O melhor plugin para pagamentos via Pix no Woocommerce. Na versão 2.0.0 o plugin está melhor mais dinâmico e muito mais responsivo. Veja mais detalhes.

**Sempre atualize para continuar aproveitando**

O **Pix** é o mais novo método de pagamento eletrônico criado pelo Banco Central do Brasil. Você encontra todos os detalhes na [página oficial](https://www.bcb.gov.br/estabilidadefinanceira/pix) do Pix.

O plugin é permitir que você utilize o método de pagamento Pix em sua loja **Woocommerce** sem qualquer custo de forma simples, rápida e direta. Acesse as configurações do plugin em `Pix por Piggly` no menu lateral.

> Se você apreciar a função deste plugin e quiser apoiar este trabalho, sinta-se livre para fazer qualquer doação para a chave aleatória Pix `aae2196f-5f93-46e4-89e6-73bf4138427b` ❤.

> Não esqueça de deixar a sua avaliação sobre o plugin! Isso nos incentivará a lançar mais atualizações e continuar prestando um suporte de qualidade.

## Recursos que só o Pix por Piggly tem ##

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

## Versão `2.0.0`

Na versão **2.0.0** promovemos várias mudanças no formato no plugin, tanto para facilitar quando para deixar o fluxo de pagamento mais simples e dinâmico. Algumas opções foram removidas, enquanto outras foram mantidas. Leia abaixo em detalhes tudo que está diferente.
### E-mails

#### 👎 Antes

👉 Era possível escolher o modelo de e-mail na qual o pagamento Pix seria anexado e, ainda, escolher a posição deste pagamento.

#### ❌ Por que mudamos?

Muitos relatavam conflitos e dificuldades para gerenciar o conteúdo do e-mail, enquanto outros utilizavam plugins desatualizados que quebravam os e-mails. Isso acontecia, pois dependiamos de uma `action` localizada no modelo de e-mail selecionado para carregar os dados do Pix.

#### 👍 Agora

👉 Criamos diversos modelos de e-mails, entre eles: quando o Pix estiver próximo de expirar, quando o Pix expirar, quando o Pix for pago e quando o Pix for criado para pagamento.

👉 Não anexamos mais as informações do Pix no e-mail para evitar **SPAM** e compartilhamento desnecessário dos dados. Criamos um link único para o cliente acessar e visualizar todos os dados de pagamento novamente.

### Comprovantes

#### 👎 Antes

👉 Era possível selecionar uma página para enviar o comprovante e utilizar qualquer formulário desejado. Também era possível utilizar o shortcode `[pix-por-piggly-form]` para utilizar o recurso nativo do plugin para recebimento de comprovantes.

#### ❌ Por que mudamos?

Alguns clientes enviavam de forma errada ou a forma como o shortcode `[pix-por-piggly-form]` era utilizado prejudicava a experiência criando diversos comprovantes desnecessários e produzindo muito lixo na pasta de uploads.

#### 👍 Agora

👉 Será utilizado um link permanente exclusivo para que o usuário faça o envio do comprovante Pix, garantindo todas as validações necessárias para que o usuário envie sempre para o pedido correto.

👉 O comprovante enviado será automaticamente associado ao Pix relacionado ao pedido e sempre será considerado o último comprovante enviado.

### Pedidos

#### 👎 Antes

👉 Ao selecionar o Pix, o pedido automaticamente migrava o status para `Aguardando o Pagamento`, também era possível utilizar o status `Comprovante Pix Recebido` quando o comprovante era enviado.

#### ❌ Por que mudamos?

Alguns usuários acharam o status `Comprovante Pix Recebido` muito complicado e tinham rotinas que impediam o uso.

Migrar para o status `Aguardando o Pagamento` também não é mais uma opção, uma vez que os Pix podem ser confirmados tanto por API quanto por comprovantes.

#### 👍 Agora

👉 Agora, por padrõa, o pedido ficará como `Pendente` atéq ue o cliente envie o comprovante ou que uma API Pix atualize o Pix como pago.

👉 Quando o cliente enviar um comprovante, o status é migrado para sair da situação como `Pendente`.

👉 Também foi adicionado um recurso para atualizar automaticamente o status do pedido para `Pago` quando o Pix for pago.

👉 Tanto o status para Comprovante Enviado quanto para Pedido Pago podem ser configurados. **Não recomendamos que o Comprovante Enviado marque o pedido como pago...**

### Endpoints

#### 👍 Agora

👉 Foram criados dois endpoints exclusivos dentro do ambiente "Minha Conta" do Woocommerce. Um para o realizar o pagamento pendente do Pix e outro para enviar o comprovante de pagamento.

👉 Os endpoints podem ser acessados a qualquer momento desde que o cliente tenha autorização e eles estejam liberados para acesso.

### Templates

#### 👍 Agora

👉 Atualizamos todos os templates, será necessário revisá-los para que eles funcionem corretamente caso você tenha realizado alguma personalização.

### Filtros e Ações

A partir da versão **2.0.0** é possível utilizar os filtros e as ações abaixo:

#### 👍 Filtros

* `pgly_wc_piggly_pix_discount_applied` Personaliza o valor calculado para o desconto antes de aplicar;
* `pgly_wc_piggly_pix_payload` Personaliza ou altera o objeto Payload do Pix antes de gerar o código;
* `pgly_wc_piggly_pix_pending_status` Personaliza o status de `pending` ao criar um pedido com pagamento Pix;
* `pgly_wc_piggly_pix_process` Personaliza o objeto `PixEntity` antes de processar o Pix.

#### 👍 Ações

* `pgly_wc_piggly_pix_webhook` Executa o webhook do Pix;
* `pgly_wc_piggly_pix_to_pay` Logo após o Pix ser criado e associado ao pedido;
* `pgly_wc_piggly_pix_after_save_receipt` Logo após criar (e salvar) o comprovante do Pix;
* `pgly_wc_piggly_pix_close_to_expires` Quando o Pix está próximo da expiração.

### Principais melhorias ###

* ✅ Alteração da cor do ícone do Pix;
* ✅ Formulário integrado para envio de comprovante;
* ✅ (Opcional) Atualização automática do pedido com comprovante recebido;
* ✅ Desconto automático para pagamento via Pix.
* ✅ Reformulação das configurações;
* ✅ Criação da metabox Pix nos pedidos pagos via Pix;
* ✅ Otimização para geração dos QR Codes;
* ✅ Desconto automático para pagamento via Pix.

## Performance ##

Toda a estrutura e código do plugin foram atualizados para garantir a melhor performance, agora todos os dados de um Pix criado são salvos em uma tabela de dados que evita a recriação do Pix. Os QR Codes e os comprovantes também são associados ao Pix que permite um único arquivo de cada, mantendo a biblioteca de uploads sempre limpa.

## Como funciona? ##

Assim como é feito atualmente com uma transferência eletrônica no Woocommerce, o **Pix por Piggly** permite aos consumidores escolherem o método de pagamento Pix, então eles recebem as instruções de pagamento e enviam o comprovante. Você também pode aplicar um desconto automático para pagamentos via Pix.

Bem simples né?

Para permitir isso, nosso plugin segue todas as propostas do padrão EMV®1, implementado pelo Banco Central do Brasil, você pode ler mais sobre isso em [Especificações Técnicas](https://www.bcb.gov.br/content/estabilidadefinanceira/forumpireunioes/Anexo%20I%20-%20QRCodes%20-%20Especifica%C3%A7%C3%A3o%20-%20vers%C3%A3o%201-1.pdf). Utilizamos a nossa bibliteca [piggly/php-pix](https://github.com/piggly-dev/php-pix) para manipular e gerar os códigos pix.

Dessa forma, nosso plugin gera os três principais métodos de pagamento Pix:

1. Um **QR Code** com o código Pix;
2. O código Pix em formato de texto para utilização da função **Pix Copia & Cola**;
3. Pagamento manual com os dados Pix fornecidos.

Nosso plugin gera de forma automática o código Pix com base nas informações do Pedido e nas informações preenchidas na configuração do plugin.

> Não importa como você digita a chave Pix, ela será automaticamente convertida para os formatos apropriados, okay? Caso ela esteja inválida, de acordo com o formato escolhido, você será notificado.

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

## Perguntas Frequentes ##

### Qual é a licença do plugin? ###

Este plugin esta licenciado como GPLv2. Ele é distrubuido de forma gratuita.

### O que eu preciso para utilizar este plugin? ###

* Ter instalado o Wordpress 4.0 ou superior;
* Ter instalado o plugin WooCommerce 3.0 ou superior;
* Utilizar a versão 7.2 do PHP;
* Ter a extensão `gd` para PHP habilitada, veja detalhes [aqui](https://www.php.net/manual/pt_BR/book.image.php);
* Possuir uma conta bancária com Chave Pix.

### Posso utilizar com outros gateways de pagamento? ###

Sim, esse plugin funciona apenas como um método de pagamento adicional, assim como acontece com o método de transferência eletrônica.

### Como aplicar desconto automático? ###

Na página de configurações do Plugin, acesse **Pedidos** e insira um valor e um rótulo para o desconto Pix. O desconto será automaticamente aplicado quando o cliente escolher o método de pagamento Pix.

### Como conferir o pagamento Pix? ###

A conferência do Pix ainda é manual, assim como acontece em uma transferência eletrônica. Para facilitar, o plugin gera os Pix com um código identificador. Esse código possuí um valor estático de 25 caracteres.

Abra o pedido criado no Woocommerce e verifique o código identificador do Pix, ao abrir o aplicativo do seu banco, você poderá ver detalhes sobre o recebimento Pix e, na maioria dos bancos, o pagamento estará identificado com o código identificador do Pix.

### Não tem como atualizar o pagamento Pix automaticamente?

Para validar se um Pix foi pago a maioria dos bancos emissores irão cobrar taxas, assim como os intermediadores de pagamento. Se você faz parte de um banco emissor que já implementa a API Pix, pode entrar em contato com a gente em [dev@piggly.com.br](mailto:dev@piggly.com.br) para que possamos implementar a solução.

### Gerei o código Pix, mas não consigo efetuar o pagamento. E agora? ###

Nas configurações do Plugin acesse "Suporte" e verifique a seção "O plugin gera o QR Code, mas não consigo pagá-lo", lá estarão algumas dicas automáticas que podem ajudar você. Se ainda sim precisar de algum suporte, abra um chamado enviando um e-mail para [dev@piggly.com.br](mailto:dev@piggly.com.br).

### Como customizar os templates? ###

Nas configurações do Plugin acesse "Suporte" e verifique a seção "Como substituir os templates de e-mail e da página de obrigado".

> **AVISO**: Ao customizar os templates você pode perder funcionalidades importantes do plugin e comportamentos pré-existentes nos templates originais. Tenha certeza sobre o que está fazendo para garantir que tudo funcione como deve ser. **Não prestaremos suporte para customizações**.

## Como instalar? ##

### No diretório oficial do Wordpress ###

A página oficial do plugin pode ser encontrada em: [wordpress@pix-por-piggly](https://wordpress.org/plugins/pix-por-piggly/).

### No repositório do Github ###

Vá para [Releases](https://github.com/piggly-dev/piggly-views-wordpress/releases) neste repositório e faça o download em `.zip` da versão mais recente.

Então, no **Painel Administrativo** do Wordpress, vá em `Plugins > Adicionar novo` e clique em `Upload plugin` no topo da página para enviar o arquivo `.zip`.

> Você precisará, posteriormente, ir até a pasta do plugin no terminal do seu servidor Web e executar o comando `composer install` caso escolha essa opção.

### Da origem ###

Você precisará do Git instalado para contruir da origem. Para completar os passos a seguir, você precisará abrir um terminal de comando. Clone o repositório:

`git clone https://github.com/piggly-dev/wc-piggly-pix.git`

> Você precisará, posteriormente, executar o comando `composer install` caso escolha essa opção.

## Como utilizar? ##

Após a instalação do plugin, vá até `Plugins > Plugins instalados`, ative o plugin **Pix por Piggly para Woocommerce**. Assim que você ativar, o plugin já estará disponível em `Woocommerce > Configurações > Pagamentos` e você poderá inserir todas as configurações pertinentes.

**Preencha corretamente a sua chave Pix. Você pode testar nas configurações do plugin o funcionamento do Pix mesmo que o módulo esteja desativado.**

## Dependências ##

Esse plugin tem as seguintes dependências:

* PHP 7.2+.
* WordPress 4.0+;
* WooCommerce 3.0+.

## Changelog ##

Veja o arquivo [CHANGELOG](CHANGELOG.md) para informação sobre todas as atualizações do código-fonte.

## Contribuições ##

Veja o arquivo [CONTRIBUTING](CONTRIBUTING.md) para informações antes de enviar sua contribuição.

## Segurança ##

Se você descobrir qualquer issue relacionada a segurança, por favor, envie um e-mail para [dev@piggly.com.br](mailto:dev@piggly.com.br) ao invés de utilizar o rastreador de issues do Github.

## Créditos ##

- [Caique Araujo](https://github.com/caiquearaujo)
- [Todos os colaboradores](../../contributors)

## Apoie o projeto ##

**Piggly Studio** é uma agência localizada no Rio de Janeiro, Brasil. Se você apreciar a função desta biblioteca e quiser apoiar este trabalho, sinta-se livre para fazer qualquer doação para a chave aleatória Pix `aae2196f-5f93-46e4-89e6-73bf4138427b` ❤.

## Licença ##

GPL 2.0. Veja [LICENSE](LICENSE) para mais informações.