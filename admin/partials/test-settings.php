<?php
use Piggly\Pix\Parser;
use Piggly\Pix\Payload;
use Piggly\Pix\StaticPayload;
?>

<?php if ( ((float)phpversion('Core') < 7.2) ) : ?>
<div class="error">
	<p><strong>O plugin exige a versão do PHP 7.2 ou acima</strong>. Contate seu servidor de hospedagem para atualizar.</p>
</div>
<?php endif; ?>

<?php if ( !Payload::supportQrCode() ) : ?>
<div class="error">
	<p>Você precisa da extensão <strong>GD</strong> do PHP para gerar os <strong>QR Codes</strong>. Instale e habilite a extensão no seu servidor web.</p>
</div>
<?php endif; ?>

<style>
#mainform p.submit { display: none; }
#mainform p.force-submit { display: block !important; }
.piggly-label { font-size: 12px; font-weight: bold; margin-bottom: 4px; display: block; }
.piggly-checkbox { display: table; margin: 12px 0; }
</style>
<h2>
	<a href="<?=admin_url( 'admin.php?page=wc-settings&tab=checkout' );?>"><?=__('Métodos de Pagamento', WC_PIGGLY_PIX_PLUGIN_NAME);?></a> > <a href="<?=admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_piggly_pix_gateway' );?>"><?=$data->method_title?> por Piggly</a> > Teste o seu Pix
</h2>
<h1>Veja se o seu Pix funciona!</h1>

<div style="max-width:640px; display: table">
	<p>
		Neste playground, você pode testar o Pix que é gerado durante os pedidos.
		Para isso, preencha os dados abaixo e clique em <strong>Gerar Pix</strong>.
	</p>

	<label class="piggly-label" for="amount"><code>Obrigatório</code> Valor do Pix</label>
	<input style="width:100%;" class="input-text regular-input " type="text" name="amount" id="amount">
	<p class="description">Simule o valor para pagamento do Pix.</p>
	
	<label class="piggly-label" for="order_id">Código do Pedido</label>
	<input style="width:100%;" class="input-text regular-input " type="text" name="order_id" id="order_id">
	<p class="description">Simule o código do pedido para verificar o identificador do Pix.</p>

	<p class="submit force-submit">
	<button name="save" class="button-primary woocommerce-save-button" type="submit" value="Gerar Pix">Gerar Pix</button>
	</p> 
	<?php

	$amount = str_replace(',', '.', filter_input( INPUT_POST, 'amount', FILTER_SANITIZE_STRING ) );
	$order_id = filter_input( INPUT_POST, 'order_id', FILTER_SANITIZE_STRING );

	if ( !empty($amount) ) :
		try
		{
			if ( !empty($order_id) ) :
				$data->instructions       = str_replace('{{pedido}}', $order_id, $data->instructions);
				$data->receipt_page_value = str_replace('{{pedido}}', $order_id, $data->receipt_page_value);
				$data->whatsapp_message   = str_replace('{{pedido}}', $order_id, $data->whatsapp_message);
				$data->telegram_message   = str_replace('{{pedido}}', $order_id, $data->telegram_message); 
				$data->identifier         = str_replace('{{id}}', $order_id, $data->identifier);
			endif;

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

			wc_get_template(
				'html-woocommerce-thank-you-page.php',
				array(
					'data' => $data,
					'pix' => $pix->getPixCode(),
					'qrcode' => $data->pix_qrcode && Payload::supportQrCode() ? $pix->getQRCode(Payload::OUTPUT_PNG, Payload::ECC_L) : '',
					'order_id' => $order_id,
					'amount' => $amount
				),
				'woocommerce/wc-piggly-pix/',
				WC_PIGGLY_PIX_PLUGIN_PATH.'templates/'
			);
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
	
	<h3>Problemas com o Pix?</h3>

	<p>
		O Pix ainda é muito recente e, apenas das padronizações do Banco Central do Brasil, 
		muitos bancos criaram algumas variações e definiram como aceitam determinadas chaves. 
		A nossa recomendação principal é: <em>utilize as chaves aleatórias</em>. Assim,
		você não expõe seus dados e ao mesmo tempo tem compatibilidade total de pagamentos.
	</p>

	<h4>Divergências entre Pix Copia & Cola e QR Codes</h4>

	<p>
		Há alguns relatos que alguns bancos leem o QR Code, mas não leem o Pix Copia & Cola. 
		Este não é um problema do plugin, o código Pix de ambos são o mesmo! Caso esteja curioso, 
		abra um leitor de QR Code e leia o código é examente o mesmo que o Pix Copia & Cola.
	</p>

	<p>
		Neste caso, precisamos verificar cada caso. E você pode contribuir com isso enviando um e-mail
		para <a href="mailto:dev@piggly.com.br">dev@piggly.com.br</a>. Ao enviar um e-mail, certifique-se de informar:
	</p>

	<ul>
		<li>Versão do Wordpress;</li>
		<li>Versão do WooCommerce;</li>
		<li>Banco Emitente (Conta Pix);</li>
		<li>Banco Pagador (que está utilizando o Código Pix);</li>
		<li>Tipo de Erro;</li>
		<li>Chave Pix gerada;</li>
	</ul>
</div>