<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<?php
use Piggly\Pix\Parser;
?>
<h1>Configurações dos Comprovantes</h1>

<div style="max-width:720px; display: table">
	<h3>Página para enviar o Comprovante <code class="piggly-featured">Não preencha para ocultar</code></h3>
	<p class="description">Informe o link completo para a página de destino.</p>
	<input value="<?=$data->receipt_page_value?>" style="width:100%;" class="input-text regular-input " type="text" name="woocommerce_wc_piggly_pix_gateway_receipt_page_value" id="woocommerce_wc_piggly_pix_gateway_receipt_page_value">
	<p class="description">Quando preenchido, adiciona um botão para ir até a página.</p>
	<p class="description"><strong>Pré-visualize</strong> <code><?=str_replace('{{pedido}}', '123456', $data->receipt_page_value);?></code></p>
	
	<div>
		<h4>Merge Tags</h4>
		<p><code>{{pedido}}</code> Insira para substituir para fazer referência ao número do pedido.</p>
	
		<?php if ( !$helpText ) : ?>
		<h4>Recomendações</h4>
		<p>
			Crie um formulário com os plugins <strong>Gravity Forms</strong>, <strong>WP Forms</strong> ou similares.
			Coloque esse formulário em uma nova página, por exemplo <code>https://minhaloja.com.br/comprovante-pix</code>. Insira os
			campos: <em>Número do Pedido</em>, <em>E-mail</em>, <em>Comprovante Pix (permitindo envio de arquivos png, jpg ou pdf apenas)</em>.
		</p>
		<p>
			No campo <em>Número do Pedido</em> preencha o valor dele dinâmicamente com a <strong>URL Query String</strong>, por exemplo,
			<code>order_id</code>. Assim, preencha a <strong>Página do Comprovante</strong> como <code>https://minhaloja.com.br/comprovante-pix/?order_id={{pedido}}</code>.
			Ao fazer isso, o formulário na página <code>/comprovante-pix</code> receberá o número do pedido automaticamente. Facilitando para o comprador.
		</p>
		<?php endif ?>
	</div>
	
	<h3>Whatsapp para enviar o Comprovante <code class="piggly-featured">Não preencha para ocultar</code></h3>
	<p class="description">Informe seu telefone em qualquer formato, ajustaremos para você.</p>
	<input value="<?=$data->whatsapp?>" style="width:100%;" class="input-text regular-input " type="text" name="woocommerce_wc_piggly_pix_gateway_whatsapp" id="woocommerce_wc_piggly_pix_gateway_whatsapp">
	<p class="description"><strong>Pré-visualize</strong> <code><?=str_replace('+', '', Parser::parsePhone($data->whatsapp));?></code></p>
	<label class="piggly-label" for="woocommerce_wc_piggly_pix_gateway_whatsapp_message">Mensagem inicial para ser enviada</label>
	<input value="<?=$data->whatsapp_message?>" style="width:100%;" class="input-text regular-input " type="text" name="woocommerce_wc_piggly_pix_gateway_whatsapp_message" id="woocommerce_wc_piggly_pix_gateway_whatsapp_message">
	<p class="description"><strong>Pré-visualize</strong> <code><?=str_replace('{{pedido}}', '123456', $data->whatsapp_message);?></code></p>
	
	<div>
		<h4>Merge Tags da Mensagem Inicial</h4>
		<p><code>{{pedido}}</code> Insira para substituir para fazer referência ao número do pedido.</p>
	</div>

	<h3>Usuário do Telegram para enviar o Comprovante <code class="piggly-featured">Não preencha para ocultar</code></h3>
	<p class="description">Informe somente o seu nome de usuário com ou sem @, ajustaremos para você.</p>
	<input value="<?=$data->telegram?>" style="width:100%;" class="input-text regular-input " type="text" name="woocommerce_wc_piggly_pix_gateway_telegram" id="woocommerce_wc_piggly_pix_gateway_telegram">
	<p class="description"><strong>Pré-visualize</strong> <code><?=str_replace('@', '', $data->telegram);?></code></p>
	<label class="piggly-label" for="woocommerce_wc_piggly_pix_gateway_telegram_message">Mensagem inicial para ser enviada</label>
	<input value="<?=$data->telegram_message?>" style="width:100%;" class="input-text regular-input " type="text" name="woocommerce_wc_piggly_pix_gateway_telegram_message" id="woocommerce_wc_piggly_pix_gateway_telegram_message">
	<p class="description"><strong>Pré-visualize</strong> <code><?=str_replace('{{pedido}}', '123456', $data->telegram_message);?></code></p>
	
	<div>
		<h4>Merge Tags da Mensagem Inicial</h4>
		<p><code>{{pedido}}</code> Insira para substituir para fazer referência ao número do pedido.</p>
	</div>

	<p class="submit force-submit">
	<button name="save" class="button-primary woocommerce-save-button" type="submit" value="Salvar">Salvar</button>
	</p>
</div>