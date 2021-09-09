# Operações

A versão **2.0.0** do plugin **Pix por Piggly** altera todo o processo de operações do plugin, excluindo e adicionando recursos com uma reforma completa no núcleo do gateway de pagamento.

## Gateway de Pagamento

### Recriação do Pix

> Atualiza o Pix independente do status do antigo Pix.

1. Encontra o pedido;
2. Cria um novo Pix com os dados atualizados e associa ao pedido;
3. Atualiza o status do Pix conforme o status do pedido:
   - Quando cancelado, cancela o Pix;
   - Quando não estiver mais aguardando pagamento, define o Pix como pago;
   - Para os demais casos, o status será aguardando pagamento do Pix.

### Criação do Pix

> Criado imediatamente após a conclusão do pedido.

1. Encontra o pedido;
2. Cria um novo Pix com os dados atualizados e associa ao pedido;
3. Atualiza o status do pedido para Pendente (`pending`);
4. Redireciona para a página de pagamento.

### Processamento do Pix

> Atualiza o pedido conforme o processamento do Pix.

1. Se o Pix foi criado, mas sem o envio do comprovante, então:
   - Notifica quando estiver próximo da expiração;
   - Impede o processamento quando estiver expirado.
2. Se o Pix estiver expirado ou cancelado, impede o processamento;
3. Obtém o pedido associado ao Pix, então:
   - Se o pedido foi cancelado, cancela o Pix.
4. Se o Pix tiver pago e o pedido aguardando pagamento, então:
   - Atualiza o pedido com o E2EID do Pix;
   - Atualiza o status do pedido para “pago”.

### Expiração do Pix

> O Pix pode ser expirado ou não, para ignorar a expiração basta definir `0`

1. Quando o pedido expirar nativamente via Woocommerce, verifica se o Pix está expirado, se não, bloqueia o cancelamento do pedido;
2. Se na rotina `cron` for detectado que o Pix foi expirado e o pedido continua aguardando pagamento e/ou o comprovante, então expira o Pix.

## Páginas

### Página de Pagamento

1. Localiza o pedido e o Pix;
2. Se o pedido não for localizado, interrompe o pagamento;
3. Se o Pix não existir, recria;
4. Se o Pix estiver expirado ou cancelado, não permite o pagamento;
5. Se o Pix já tiver sido pago, interrompe o pagamento;
6. Exibe os dados para pagamento Pix.

### Página do Comprovante

1. Localiza o pedido e o Pix;
2. Se o pedido não for localizado, interrompe o envio;
3. Se o Pix não existir, interrompe o envio;
4. Se o Pix estiver expirado ou cancelado, não permite o envio;
5. Se o Pix já tiver sido pago, interrompe o envio;