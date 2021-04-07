<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<?php
use Piggly\Pix\Parser;
use Piggly\Pix\Payload;
use Piggly\Pix\StaticPayload;
?>

<h1>Veja se o seu Pix funciona!</h1>

<?php if ( empty($data->key_value) ) : ?>
<div style="max-width:720px; display: table">
	<p class="notice notice-error" style="padding: 10px">
		⚠ Antes de testar o Pix, você precisa preencher os 
		<a href="<?=admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_piggly_pix_gateway&screen=pix' );?>">Dados do Pix</a>.
	</p>
</div>
<?php else : ?>
<div style="max-width:720px; display: table">
	<p>
		Neste playground, você pode testar o Pix que é gerado durante os pedidos.
		Para isso, preencha os dados abaixo e clique em <strong>Gerar Pix</strong>.
	</p>

	<label class="piggly-label" for="amount"><code class="piggly-featured">Obrigatório</code> Valor do Pix</label>
	<input value="1,00" style="width:100%;" class="input-text regular-input " type="text" name="amount" id="amount">
	<p class="description">Simule o valor para pagamento do Pix.</p>

	<label class="piggly-label" for="order_id">Escolha um pedido para simular</label>
	<select style="width:100%; max-width: 100%;" class="select  wc-enhanced-select" name="order_id" id="order_id">
		<option value="000001">Nenhum selecionado</option>
		<?php 
		$orders = wc_get_orders( array('limit' => 10) );
		
		foreach ( $orders as $order )
		{ 
			$id = method_exists($order, 'get_order_number') ? $order->get_order_number() : $order->get_id();
			echo sprintf('<option value="%s">Pedido #%s</option>', $id, $id); 
		}
		?>
	</select>
	<p class="description">Nenhuma alteração será feita no pedido selecionado.</p>

	<p class="submit force-submit" style="display:block">
	<button name="save" class="button-primary woocommerce-save-button" type="submit" value="Gerar Pix">Gerar Pix</button>
	</p> 
	
	<p class="notice notice-warning" style="padding: 10px"><em>
		⚠ Caso tenha algum problema com o processamento do Pix, acesse a seção
		<a href="<?=admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_piggly_pix_gateway&screen=support' )?>">Suporte</a> para saber como proceder.
	</p>
	<?php

	$amount   = str_replace(',', '.', filter_input( INPUT_POST, 'amount', FILTER_SANITIZE_STRING ) );
	$order_id = filter_input( INPUT_POST, 'order_id', FILTER_SANITIZE_STRING );

	if ( !empty($amount) ) :
		try
		{
			$data->instructions       = str_replace('{{pedido}}', $order_id, $data->instructions);
			$data->receipt_page_value = str_replace('{{pedido}}', $order_id, $data->receipt_page_value);
			$data->whatsapp_message   = str_replace('{{pedido}}', $order_id, $data->whatsapp_message);
			$data->telegram_message   = str_replace('{{pedido}}', $order_id, $data->telegram_message); 
			$data->identifier         = str_replace('{{id}}', $order_id, $data->identifier);

			$pix = 
				(new StaticPayload())
					->setPixKey($data->key_type, $data->key_value)
					->setDescription(sprintf('Compra em %s', $data->store_name))
					->setMerchantName($data->merchant_name)
					->setMerchantCity($data->merchant_city)
					->setAmount((float)$amount)
					->setTid($data->identifier);
			
			// Get alias for pix
			$data->key_type_alias = Parser::getAlias($data->key_type); 

			?>
			<h3>Dados de Depuração</h3>
			<p>Confira abaixo todos os valores que seu plugin está gerando 👇</p>
			<ul style="list-style: disc; padding: 0px 25px">
				<li>Banco Origem: <code><?=$data->bank === 0 ? 'Nenhum' : 'CÓDIGO '.$data->bank;?></code></li>
				<li>Tipo da Chave: <code><?=$data->key_type_alias?></code></li>
				<li>Chave: <code><?=$data->key_value?></code></li>
				<li>Titular: <code><?=$data->merchant_name?></code></li>
				<li>Cidade: <code><?=$data->merchant_city?></code></li>
				<li>Identificador: <code><?=$data->identifier?></code></li>
				<li>Valor: <code>R$ <?=$amount?></code></li>
				<li>Código Pix: <code><?=$pix->getPixCode()?></code></li>
			</ul>

			<?php if ( !empty($data->receipt_page_value) || !empty($data->whatsapp) || !empty($data->telegram) ) : ?>
			<h3>Links Gerados</h3>
			<p>Confira abaixo todos os links que seu plugin está gerando 👇</p>
			<ul style="list-style: disc; padding: 0px 25px">
				<?php if ( !empty($data->receipt_page_value) ) : ?>
					<?php $link = $data->receipt_page_value; ?>
					<li>Link Página do Comprovante: <code><?=$link?></code> <a href="<?=$link?>" target="_blank">Abrir Link</a></li>
				<?php endif; ?>

				<?php if ( !empty($data->whatsapp) ) : ?>
					<?php $link = sprintf('https://wa.me/%s?text=%s',str_replace('+', '', Parser::parsePhone($data->whatsapp)),urlencode($data->whatsapp_message)); ?>
					<li>Link Whatsapp: <code><?=$link?></code> <a href="<?=$link?>" target="_blank">Abrir Link</a></li>
					<li>Mensagem Whatsapp: <code><?=$data->whatsapp_message?></code></li>
				<?php endif; ?>

				<?php if ( !empty($data->telegram) ) : ?>
					<?php $link = sprintf('https://t.me/%s?text=%s',str_replace('@', '', $data->telegram),urlencode($data->telegram_message)); ?>
					<li>Link Telegram: <code><?=$link?></code> <a href="<?=$link?>" target="_blank">Abrir Link</a></li>
					<li>Mensagem Telegram: <code><?=$data->telegram_message?></code></li>
				<?php endif; ?>		
			</ul>
			<?php endif; ?>

			<p>Confira abaixo o template que está sendo carregado 👇</p>
			
			<?php
			$loadedTemplate = \WC_PIGGLY_PIX_PLUGIN_PATH.'templates/html-woocommerce-thank-you-page.php';

			if ( file_exists( get_template_directory().WC()->template_path().\WC_PIGGLY_PIX_DIR_NAME.'//templates/html-woocommerce-thank-you-page.php') )
			{ $loadedTemplate = get_template_directory().WC()->template_path().\WC_PIGGLY_PIX_DIR_NAME.'//templates/html-woocommerce-thank-you-page.php'; }
			?>

			<p>Template carregado em <code><?=$loadedTemplate?></code></p>
			<p>Ao substituir o template, não esqueça de copiar o template original em <code><?='wp-content/plugins/'.\WC_PIGGLY_PIX_DIR_NAME.'/templates/html-woocommerce-thank-you-page.php'?></code></p>

			<div style="background-color: #FFF; padding: 25px 12px; border-radius: 12px">
			<?php

			wc_get_template(
				'html-woocommerce-thank-you-page.php',
				array(
					'data' => $data,
					'pix' => $pix->getPixCode(),
					'qrcode' => $data->pix_qrcode && Payload::supportQrCode() ? $pix->getQRCode(Payload::OUTPUT_PNG, Payload::ECC_L) : '',
					'order_id' => $order_id,
					'amount' => $amount
				),
				WC()->template_path().\WC_PIGGLY_PIX_DIR_NAME.'/',
				WC_PIGGLY_PIX_PLUGIN_PATH.'templates/'
			);
			?>
			</div>
			<?php
		}
		catch ( Exception $e )
		{ 
			?>
			<p class="notice notice-error" style="padding: 10px">
				<strong>Um erro foi capturado, informe o suporte: <code><?=$e->getMessage();?></code>
			</p>
			<?php
		}
	endif;
	?>
</div>
<?php endif; ?>