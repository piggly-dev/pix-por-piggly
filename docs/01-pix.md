# Pix

Nas versões anteriores, o Pix era sempre criado e salvo em cachê no pedido. Para gerenciar melhor os tipos de Pix estáticos e dinâmicos, todos os Pix agora serão inseridos em uma tabela de dados e gerados com muito mais consistência.

A partir da versão **2.0.0**, o identificador do Pix será um código único aleatório composto por 25 caracteres válidos e não será mais manipulável pelas configurações do plugin.

## Categorização

| Tipo                | Descrição                                              |
| ------------------- | ------------------------------------------------------ |
| Estático `static`   | Um Pix para pagamento imediato, sem rastreio.          |
| Dinâmico `cob|cobv` | Um Pix de cobrança dinâmica, criado em uma API do Pix. |

## Status

| Status                | Descrição                                                    |
| --------------------- | ------------------------------------------------------------ |
| Criado `created`      | O Pix foi criado e aguarda por qualquer evento.              |
| Aguardando `waiting`  | O Pix recebeu um comprovante, mas ainda aguarda uma confirmação direta. |
| Expirado `expired`    | O Pix expirou e não pode mais ser pago.                      |
| Pago `paid`           | Quando detectado que o Pix foi pago.                         |
| Cancelado `cancelled` | Quando o Pix é cancelado por qualquer razão.                 |

A cada mudança de `status` a ação `pgly_wc_piggly_pix_updated_pix_status` é disparada, contendo os seguintes parâmetros (respectivamente):

| Parâmetro     | Descrição                                        |
| ------------- | ------------------------------------------------ |
| `$pix`        | O objeto `PixEntity`, com todos os dados do Pix. |
| `$old_status` | O status antigo do Pix.                          |
| `$new_status` | O novo status do Pix.                            |

## Payload

A entidade do Pix (`PixEntity`) é diferente do payload (`StaticPayload|DynamicPayload`). O payload é responsável por compilar os dados da entidade em um código Pix válido no formato `BRCode` utilizando a biblioteca `piggly/php-pix`.  Por padrão, o payload é criado como `StaticPayload`, mas é possível migrar para um `DynamicPayload`.

Após a criação do payload, o filtro `pgly_wc_piggly_pix_payload` é acionado com os seguintes parâmetros:

| Parâmetro  | Descrição                                          |
| ---------- | -------------------------------------------------- |
| `$payload` | O payload recém criado.                            |
| `$pix`     | O objeto `PixEntity`, com todos os dados do Pix.   |
| `$order`   | O objeto `WC_Order`, com todos os dados do pedido. |

O filtro pode ser utilizado para transformar ou alterar o objeto do payload, entretanto, é necessário utilizar a biblioteca pré-fixada disponível no plugin, acessível no `namespace` `Piggly\WooPixGateway\Vendor\Piggly\Pix`.

## Processamento

Antes dos Pix serem processados por uma `cron`, o filtro `pgly_wc_piggly_pix_process` será aplicado ao objeto `PixEntity`. É possível, portanto, manipular (e salvar) os dados do objeto, antes de continuar o processamento. Depois disso, com base nos dados do objeto `PixEntity` o pedido e o Pix serão alterados. Para mais detalhes [clique aqui](./02-operacoes).

### QR Codes

Agora, o plugin tem um processamento dedicado ao QR Code que evita a criação excessiva de QR Code e sempre mantem a pasta com QR Codes o mais limpa dentro do possível.

### Comprovantes

O mesmo acontece com os comprovantes que agora são armazenados de forma segura e sempre associados ao objeto `PixEntity`.

## Endpoints

Agora, o plugin conta com dois endpoints: pagamento e comprovante. Somente através desses endpoints será possível visualizar os dados para pagamento. Os **shortcodes** não foram descontinuados, mas devem ser evitados.

## E-mails

Por padrão, o plugin conta com a utilização dos e-mails transacionais do **Woocommerce**. Desde que o **Wordpress** seja capaz de enviar os e-mails, os seguintes serão enviados ao longo do ciclo de vida de um pedido:

| Destinatário | Classe                      | Descrição                                                 |
| ------------ | --------------------------- | --------------------------------------------------------- |
| Cliente      | `CustomerPixCloseToExpires` | Quando o Pix estiver perto de expirar.                    |
| Cliente      | `CustomerPixExpired`        | Quando o Pix expirar.                                     |
| Cliente      | `CustomerPixPaid`           | Quando o Pix for marcado como pago.                       |
| Cliente      | `CustomerPixToPay`          | Quando o Pix for criado envia as instruções de pagamento. |

A qualquer momento, é possível customizar os templates de todos os e-mails, para isso basta copiar a pasta `emails` disponível no diretório do plugin em `/templates/woocommerce/emails` para a pasta `woocommerce` dentro do seu tema.

> Copie `/path/to/plugin/templates/woocommerce/emails` para `/path/to/theme/woocommerce/emails` e edite os templates. Tenha cuidado com as variáveis disponíveis para cada template de e-mail.

> Novas notificações podem ser configuradas utilizando as `actions` disparadas pelo plugin.