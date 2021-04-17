<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<h1 class="wpgly-title">Importe os dados da sua Conta Pix</h1>

<?php
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST['error']) ) 
{ wp_redirect( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_piggly_pix_gateway&screen=pix&imported=true' ) ); }
?>

<div class="wpgly-wrapper">
	<div class="wpgly-spacing"></div>

	<div class="wpgly-notice-box wpgly-warning">
		<strong>Clientes do Banco Itaú</strong>: para gerar pagamentos via Pix
		fora do aplicativo do seu banco, é necessário solicitar uma autorização ao gerente
		para criação de QR Codes Simples (estáticos).
	</div>

	<p>
		Quando os dados da sua conta Pix são preenchidos incorretamente, podem haver incompatibilidades.
		Para facilitar o processo de cadastramento de uma Conta Pix, você pode inserir abaixo um
		<strong>código Pix</strong> gerado pelo seu banco. O plugin extrairá automaticamente
		os dados como <em>Nome do Titular, Cidade, Tipo da Chave e Chave</em> do seu Pix. 
		Os códigos Pix são gerados pelo aplicativo do seu banco e começam com: <code>000201</code>.
	</p>

	<?php if ( !$helpText ) : ?>
	<div class="wpgly-notice-box">
		<h4 class="wpgly-title">Como obter um código Pix do seu banco?</h4>
		Acesse o aplicativo do banco com sua conta Pix e crie um novo Pix para pagamento. Informe
		qualquer valor, apenas para gerar um Pix de pagamento. E, então,
		copie o "Pix Copia & Cola" gerado pelo seu banco e cole-o abaixo.
		Caso seu banco não tenha a opção "Pix Copia & Cola", recomendamos que
		leia o código do QR Code de pagamento gerado com algum aplicativo
		de terceito.
	</div>
	<?php endif; ?>

	<div class="wpgly-field">
		<label class="wpgly-label" for="<?=$this->get_field_name('pix_code')?>">Código "Pix Copia & Cola" gerado pelo seu banco:</label>
		<input type="text" name="<?=$this->get_field_name('pix_code')?>" id="<?=$this->get_field_name('pix_code')?>">
		<p class="description">Ao colar o código e clicar em "Importar Configurações", os dados da sua Conta Pix serão extraídos do código.</p>
	</div>

	<div class="wpgly-field">
		<span class="wpgly-label">Auto Correção</span>
		<label class="wpgly-label wpgly-checkbox" for="<?=$this->get_field_name('auto_fix')?>">
			<input type="checkbox" name="<?=$this->get_field_name('auto_fix')?>" id="<?=$this->get_field_name('auto_fix')?>" value="1" <?=(($this->auto_fix == 1) ? 'checked="checked"' : '');?>> Habilite para auto corrigir dados do Pix que contenham caracteres ou formatos inválidos...
		</label>
	</div>
	
	<div class="wpgly-notice-box wpgly-action">
		A partir da versão <strong>2.3.0</strong> do Pix, alguns bancos podem apresentar
		erros ao ler códigos Pix com caracteres inválidos. A auto correção busca resolver esse problema.
		Caso você prefira que a auto correção não aconteça, remova a seleção acima.
		Entretanto, sem a auto correção, é possível que existam problemas de incompatibilidades
		Pix. Você poderá resolver esses problemas manualmente na guia <a href="<?=$baseUrl.'&screen=support';?>">Suporte</a>.
	</div> 
</div>

<div class="wpgly-spacing"></div>

<p class="submit wpgly-submit">
	<button name="save" class="wpgly-button wpgly-action woocommerce-save-button" type="submit" value="Importar Configurações">Importar Configurações</button>
</p> 