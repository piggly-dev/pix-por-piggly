<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<h1>Importe os dados da sua Conta Pix</h1>
<?php
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST['error']) ) 
{ wp_redirect( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_piggly_pix_gateway&screen=pix&imported=true' ) ); }
?>

<div style="max-width:720px; display: table">
	<section class="notice notice-warning  is-dismissible">
		<p>
			<strong>Clientes do Banco Itaú</strong>: para gerar pagamentos via Pix
			fora do aplicativo do seu banco, é necessário solicitar uma autorização ao gerente
			para criação de QR Codes Simples (estáticos).
		</p> 
	</section>

	<p>
		Quando os dados da sua conta Pix são preenchidos incorretamente, podem haver incompatibilidades.
		Para facilitar o processo de cadastramento de uma Conta Pix, você pode inserir abaixo um
		<strong>código Pix</strong> gerado pelo seu banco. O plugin extrairá automaticamente
		os dados como <em>Nome do Titular, Cidade, Tipo da Chave e Chave</em> do seu Pix. 
		Os códigos Pix são gerados pelo aplicativo do seu banco e começam com: <code>000201</code>.
	</p>

	<?php if ( !$helpText ) : ?>
	<section class="notice notice-info">
		<p>
			<strong>Gere um código Pix no seu banco</strong>: acesse o aplicativo 
			do banco com sua conta Pix e crie um novo Pix para pagamento. Informe
			qualquer valor, apenas para gerar um Pix de pagamento. E, então,
			copie o "Pix Copia & Cola" gerado pelo seu banco e cole-o abaixo.
			Caso seu banco não tenha a opção "Pix Copia & Cola", recomendamos que
			leia o código do QR Code de pagamento gerado com algum aplicativo
			de terceito.
		</p> 
	</section>
	<?php endif; ?>

	<label class="piggly-label" for="woocommerce_wc_piggly_pix_gateway_pix_code">Código "Pix Copia & Cola" gerado pelo seu banco:</label>
	<input style="width:100%;" class="input-text regular-input " type="text" name="woocommerce_wc_piggly_pix_gateway_pix_code" id="woocommerce_wc_piggly_pix_gateway_pix_code">
	<p class="description">Ao colar o código e clicar em "Importar Configurações", os dados da sua Conta Pix serão extraídos do código.</p>

	<label class="piggly-label piggly-checkbox" for="woocommerce_wc_piggly_pix_gateway_auto_fix">
		<input type="checkbox" name="woocommerce_wc_piggly_pix_gateway_auto_fix" id="woocommerce_wc_piggly_pix_gateway_auto_fix" value="1" <?=(($data->auto_fix == 1) ? 'checked="checked"' : '');?>> Auto corrigir dados Pix que contenham caracteres inválidos.
	</label>
	
	<p class="description">
		A partir da versão <strong>2.3.0</strong> do Pix, alguns bancos podem apresentar
		erros ao ler códigos Pix com caracteres inválidos. A auto correção busca resolver esse problema.
		Caso você prefira que a auto correção não aconteça, remova a seleção acima.
		Entretanto, sem a auto correção, é possível que existam problemas de incompatibilidades
		Pix. Você poderá resolver esses problemas manualmente na guia <a href="<?=admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_piggly_pix_gateway&screen=support' );?>">Suporte</a>.
	</p> 

	<p class="submit force-submit">
	<button name="save" class="button-primary woocommerce-save-button" type="submit" value="Importar Configurações">Importar Configurações</button>
	</p> 
</div>