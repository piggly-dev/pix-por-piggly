<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<?php

use Piggly\Pix\Enums\QrCode;
use Piggly\Pix\Parser;
use Piggly\Pix\StaticPayload;
?>

<h1 class="wpgly-title">Veja se o seu Pix funciona!</h1>

<?php if ( empty($this->key_value) ) : ?>
<div class="wpgly-wrapper">
	<div class="wpgly-notice-box wpgly-error">
		⚠ Antes de testar o Pix, você precisa preencher os 
		<a href="<?=$baseUrl.'&screen=pix';?>">Dados do Pix</a>.
	</div>
</div>
<?php else : ?>
<div class="wpgly-wrapper">
	<p>
		Neste playground, você pode testar o Pix que é gerado durante os pedidos.
		Para isso, preencha os dados abaixo e clique em <strong>Gerar Pix</strong>.
	</p>

	<div class="wpgly-notice-box wpgly-warning">
		⚠ Caso tenha algum problema com o processamento do Pix, acesse a seção
		<a href="<?=$baseUrl.'&screen=support'?>">Suporte</a> para saber como proceder.
	</div>

	<div class="wpgly-field">
		<label class="wpgly-label" for="amount"><code class="wpgly-error">*</code> Valor do Pix</label>
		<input value="1,00" type="text" name="amount" id="amount">
		<p class="description">Simule o valor para pagamento do Pix.</p>
	</div>

	<div class="wpgly-field">
		<label class="wpgly-label" for="order_id">Escolha um Pedido para Simular</label>
		<select class="wc-enhanced-select" name="order_id" id="order_id">
			<option value="000001">Nenhum selecionado</option>
			<?php 
			$orders = wc_get_orders( array('limit' => 10) );
			
			foreach ( $orders as $order )
			{ 
				$id = $order->get_id();
				echo sprintf('<option value="%s">Pedido #%s</option>', $id, $id); 
			}
			?>
		</select>
		<p class="description">Nenhuma alteração será feita no pedido selecionado.</p>
	</div>

	<p class="submit wpgly-submit">
		<button name="save" class="wpgly-button wpgly-action woocommerce-save-button" type="submit" value="Testar Pix">Testar Pix</button>
	</p>

	<h3 class="wpgly-title">Avisos</h3>
			
	<ul style="list-style: disc; padding: 18px;">
		<?php if ( strlen($this->store_name) >= 25 ) : ?>
		<li>
			O <strong>Nome do Loja</strong> possuí mais de <code>25</code> caracteres.
			Isso pode acarretar problemas de leitura do Pix em alguns bancos. Considere,
			por tanto, reduzir o nome.
		</li>
		<?php endif; ?>
		<?php if ( preg_match('/[^A-Za-z\s]/',$this->merchant_name) ) : ?>
		<li>
			O <strong>Nome da Loja</strong> contem números ou caracteres inválidos, remova-os. Alguns bancos
			não serão capazes de ler o código caso o Nome da Loja contenha números ou caracteres inválidos.
		</li>
		<?php endif; ?>
		<?php if ( strlen($this->merchant_name) >= 25 ) : ?>
		<li>
			O <strong>Nome do Titular</strong> possuí mais de <code>25</code> caracteres.
			Isso pode acarretar problemas de leitura do Pix em alguns bancos. Considere,
			por tanto, reduzir o nome.
		</li>
		<?php endif; ?>
		<?php if ( preg_match('/[^A-Za-z\s]/',$this->merchant_name) ) : ?>
		<li>
			O <strong>Nome do Titular</strong> contem números ou caracteres inválidos, remova-os. Alguns bancos
			não serão capazes de ler o código caso o Nome do Titular contenha números ou caracteres inválidos.
		</li>
		<?php endif; ?>
		<?php if ( strlen($this->merchant_city) >= 25 ) : ?>
		<li>
			A <strong>Cidade do Titular</strong> possuí mais de <code>25</code> caracteres.
			Isso pode acarretar problemas de leitura do Pix em alguns bancos. Considere,
			por tanto, reduzir o nome.
		</li>
		<?php endif; ?>
		<?php if ( preg_match('/[^A-Za-z\s]/',$this->merchant_city) ) : ?>
		<li>
			A <strong>Cidade do Titular</strong> contem números ou caracteres inválidos, remova-os. Alguns bancos
			não serão capazes de ler o código caso a Cidade do Titular contenha números ou caracteres inválidos.
		</li>
		<?php endif; ?>
		<?php if ( preg_match('/[^A-Za-z0-9\{\}]/',$this->identifier) ) : ?>
		<li>
			O <strong>Identificador</strong> contem caracteres inválidos, remova-os. Alguns bancos
			não serão capazes de ler o código caso o Identificador contenha caracteres inválidos.
		</li>
		<?php endif; ?>
		<?php if ( in_array($this->bank, [341,184,29,479,652]) ) : ?>
		<li>
			Clientes do <strong>Banco Itaú</strong>, devem entrar em contato
			com o gerente para solicitar que o banco libere a geração de códigos QR Codes
			Estáticos fora do aplicativo do banco. Do contrário, o QR Code não poderá ser pago.
		</li>
		<?php endif; ?>
		<li>
			Certifique-se de estar preenchendo os dados do Pix corretamente, caso tenha
			dúvidas recomendamos que utilize a ferramento <a href="<?=$baseUrl.'&screen=import'?>">Importar Pix</a>
			para importar um código Pix válido.
		</li>
	</ul>

	<?php

	$amount    = str_replace(',', '.', filter_input( INPUT_POST, 'amount', FILTER_SANITIZE_STRING ) );
	$order_id  = filter_input( INPUT_POST, 'order_id', FILTER_SANITIZE_STRING );

	try
	{ $order = new WC_Order($order_id); }
	catch ( Exception $e )
	{
		?>
		<div class="wpgly-notice-box wpgly-alert">
			<strong>Nenhum pedido foi carregado: <code><?=$e->getMessage();?></code>
		</div>
		<?php
	}
	
	$order_key = !$order ? '' : $order->get_order_key();

	if ( !empty($amount) ) :
		try
		{
			$this->instructions       = str_replace('{{pedido}}', $order_id, $this->instructions);
			$this->receipt_page_value = $this->parse_receipt_link($this->receipt_page_value, $order_id, $order_key);
			$this->whatsapp_message   = str_replace('{{pedido}}', $order_id, $this->whatsapp_message);
			$this->telegram_message   = str_replace('{{pedido}}', $order_id, $this->telegram_message); 
			$this->identifier         = str_replace('{{id}}', $order_id, $this->identifier);

			$pix = 
				(new StaticPayload())
					->setAmount((float)$amount)
					->setTid($this->identifier)
					->setPixKey($this->key_type, $this->key_value)
					->setDescription(sprintf('Compra em %s', $this->store_name))
					->setMerchantName($this->merchant_name)
					->setMerchantCity($this->merchant_city);
			
			// Get alias for pix
			$this->key_type_alias = Parser::getAlias($this->key_type); 

			?>
			<h3 class="wpgly-title">Dados de Depuração</h3>

			<p>Confira abaixo todos os valores que seu plugin está gerando 👇</p>

			<ul class="wpgly-ul">
				<li>Banco Origem: <code><?=$this->bank === 0 ? 'Nenhum' : 'CÓDIGO '.$this->bank;?></code></li>
				<li>Tipo da Chave: <code><?=$this->key_type_alias?></code></li>
				<li>Chave: <code><?=$this->key_value?></code></li>
				<li>Titular: <code><?=$this->merchant_name?></code></li>
				<li>Cidade: <code><?=$this->merchant_city?></code></li>
				<li>Identificador: <code><?=$this->identifier?></code></li>
				<li>Valor: <code>R$ <?=$amount?></code></li>
				<li>Código Pix: <code><?=$pix->getPixCode()?></code></li>
			</ul>

			<?php if ( !empty($this->receipt_page_value) || !empty($this->whatsapp) || !empty($this->telegram) ) : ?>
			<h3 class="wpgly-title">Links Gerados</h3>
			<p>Confira abaixo todos os links que seu plugin está gerando 👇</p>
			<ul class="wpgly-ul">
				<?php if ( !empty($this->receipt_page_value) ) : ?>
					<?php $link = $this->receipt_page_value; ?>
					<li>Link Página do Comprovante: <code><?=$link?></code> <a href="<?=$link?>" target="_blank">Abrir Link</a></li>
				<?php endif; ?>

				<?php if ( !empty($this->whatsapp) ) : ?>
					<?php $link = sprintf('https://wa.me/%s?text=%s',str_replace('+', '', $this->parse_phone($this->whatsapp)),urlencode($this->whatsapp_message)); ?>
					<li>Link Whatsapp: <code><?=$link?></code> <a href="<?=$link?>" target="_blank">Abrir Link</a></li>
					<li>Mensagem Whatsapp: <code><?=$this->whatsapp_message?></code></li>
				<?php endif; ?>

				<?php if ( !empty($this->telegram) ) : ?>
					<?php $link = sprintf('https://t.me/%s?text=%s',str_replace('@', '', $this->telegram),urlencode($this->telegram_message)); ?>
					<li>Link Telegram: <code><?=$link?></code> <a href="<?=$link?>" target="_blank">Abrir Link</a></li>
					<li>Mensagem Telegram: <code><?=$this->telegram_message?></code></li>
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
					'data' => $this,
					'pix' => $pix->getPixCode(),
					'qrcode' => $this->pix_qrcode && StaticPayload::supportQrCode() ? $pix->getQRCode(QrCode::OUTPUT_PNG, QrCode::ECC_L) : '',
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
			<div class="wpgly-notice-box wpgly-error">
				<strong>Um erro foi capturado, informe ao suporte: <code><?=$e->getMessage();?></code>
			</div>
			<?php
		}
	endif;
	?>
</div>
<?php endif; ?>