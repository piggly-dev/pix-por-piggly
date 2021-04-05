<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<h1>Configurações de Pedidos e E-mails</h1>

<div style="max-width:720px; display: table">
	<div class="piggly-space"></div>
	<?php if ( class_exists('WC_Emails') ) : ?>
		<label class="piggly-label" for="woocommerce_wc_piggly_pix_gateway_email_status">Enviar no E-mail</label>
		<select style="width:100%; max-width: 100%;" class="select wc-enhanced-select" name="woocommerce_wc_piggly_pix_gateway_email_status" id="woocommerce_wc_piggly_pix_gateway_email_status">
			<?php
			$wc_emails = WC_Emails::instance();
			$emails    = $wc_emails->get_emails();
			$selected = $data->email_status;
 
			foreach ( $emails as $index => $email )
			{ 
				if ( $index === $selected )
				{ echo sprintf('<option value="%s" selected="selected">%s</option>', $index, $email->title); }
				else
				{ echo sprintf('<option value="%s">%s</option>', $index, $email->title); }
			}

			?>
		</select>
		<p class="description">Selecione em qual modelo de e-mail o pix deve ser enviado.</p>

		<div class="piggly-space"></div>
	
		<label class="piggly-label" for="woocommerce_wc_piggly_pix_gateway_email_position">Posição no E-mail</label>
		<select style="width:100%; max-width: 100%;" class="select wc-enhanced-select" name="woocommerce_wc_piggly_pix_gateway_email_position" id="woocommerce_wc_piggly_pix_gateway_email_position">
			<?php
			$selected = $data->email_position;
			$options  = [
				'before' => __('Acima da tabela do pedido', WC_PIGGLY_PIX_PLUGIN_NAME),
				'after' => __('Abaixo da tabela do pedido', WC_PIGGLY_PIX_PLUGIN_NAME),
			];

			foreach ( $options as $key => $value )
			{ 
				if ( $key === $selected )
				{ echo sprintf('<option value="%s" selected="selected">%s</option>', $key, $value); }
				else
				{ echo sprintf('<option value="%s">%s</option>', $key, $value); }
			}

			?>
		</select>
		<p class="description">Selecione em qual posição do e-mail o pix deve ser enviado.</p>
	<?php endif; ?>
 
	<div class="piggly-space"></div>
	
	<?php if ( function_exists('wc_get_order_statuses') ) : ?>
		<label class="piggly-label" for="woocommerce_wc_piggly_pix_gateway_order_status">Migrar para o Status após a criação do pedido</label>
		<select style="width:100%; max-width: 100%;" class="select wc-enhanced-select" name="woocommerce_wc_piggly_pix_gateway_order_status" id="woocommerce_wc_piggly_pix_gateway_order_status">
			<?php
			$status   = wc_get_order_statuses();
			$selected = $data->order_status;

			foreach ( $status as $key => $alias )
			{ 
				if ( $key === $selected )
				{ echo sprintf('<option value="%s" selected="selected">%s</option>', $key, $alias); }
				else
				{ echo sprintf('<option value="%s">%s</option>', $key, $alias); }
			}

			?>
		</select>
		<p class="description">Ao ser criado, informe qual deve ser o novo status do pedido. Por padrão será <strong>Aguardando</strong> <code>on-hold</code></p>
	
		<?php if ( !$helpText ) : ?>
			<div>
				<p>
					Utilizamos o status Aguardando, pois na nossa concepção a loja aguarda
					o cliente pagar o Pix. Se você criar status personalizados com plugins
					de terceiros, você pode alterar o status para aguardando o pagamento Pix
					de acordo com a sua grade de status.
				</p>
			</div>
		<?php endif ?>
	<?php endif; ?>
	
	<div class="piggly-space"></div>
	
	<label style="margin-bottom:0" class="piggly-label piggly-checkbox" for="woocommerce_wc_piggly_pix_gateway_hide_in_order">
		<input type="checkbox" name="woocommerce_wc_piggly_pix_gateway_hide_in_order" id="woocommerce_wc_piggly_pix_gateway_hide_in_order" value="1" <?=(($data->hide_in_order) ? 'checked="checked"' : '');?>> Ocultar pagamento Pix na página de Visualização do Pedido
	</label> 
	<p class="description">Ao não marcar, o pagamento Pix será exibido quando o cliente visualizar o pedido e ainda não tiver pago.</p>

	<div class="piggly-space"></div>
	
	<h3>Desconto para pagamento via Pix</h3>

	<label class="piggly-label" for="woocommerce_wc_piggly_pix_gateway_discount">Desconto para Pagamento via Pix <code class="piggly-featured">Deixe em branco para remover o desconto</code></label>
	<input value="<?=$data->discount?>" style="width:100%;" class="input-text regular-input " type="text" name="woocommerce_wc_piggly_pix_gateway_discount" id="woocommerce_wc_piggly_pix_gateway_discount">
	<p class="description">Utilize o simbolo % para cupom de desconto em %.</p>

	<div class="piggly-space"></div>
	
	<label class="piggly-label" for="woocommerce_wc_piggly_pix_gateway_discount_label">Rótulo do Desconto Aplicado</label>
	<input value="<?=$data->discount_label?>" style="width:100%;" class="input-text regular-input " type="text" name="woocommerce_wc_piggly_pix_gateway_discount_label" id="woocommerce_wc_piggly_pix_gateway_discount_label">
	
	<p class="submit force-submit">
	<button name="save" class="button-primary woocommerce-save-button" type="submit" value="Salvar">Salvar</button>
	</p>
</div>