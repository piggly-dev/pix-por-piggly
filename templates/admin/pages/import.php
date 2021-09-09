<?php

use Piggly\WooPixGateway\CoreConnector;

if ( ! defined( 'ABSPATH' ) ) { exit; }

$settings    = CoreConnector::settings();
$plugin_page = admin_url('admin.php?page='.CoreConnector::domain());
?>

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26.92 26.92" style="width: 24px; height: 24px"><path d="M23.35,23.39a3.93,3.93,0,0,1-2.8-1.16l-4-4a.75.75,0,0,0-1.06,0L11.4,22.25a3.94,3.94,0,0,1-2.79,1.16h-.8l5.12,5.11a4.08,4.08,0,0,0,5.78,0l5.13-5.13Z" transform="translate(-2.36 -2.8)"/><path d="M8.61,9.11a3.9,3.9,0,0,1,2.79,1.16l4.06,4.05a.75.75,0,0,0,1.06,0l4-4a4,4,0,0,1,2.8-1.15h.49L18.71,4a4.08,4.08,0,0,0-5.78,0L7.81,9.11Z" transform="translate(-2.36 -2.8)"/><path d="M28.08,13.37,25,10.27a.54.54,0,0,1-.22,0H23.35a2.82,2.82,0,0,0-2,.81l-4,4a1.94,1.94,0,0,1-1.37.57,1.91,1.91,0,0,1-1.37-.57l-4.06-4.05a2.74,2.74,0,0,0-2-.81H6.88a.65.65,0,0,1-.21,0L3.56,13.37a4.08,4.08,0,0,0,0,5.78l3.11,3.11a.65.65,0,0,1,.21,0H8.61a2.78,2.78,0,0,0,2-.81l4.06-4.05a2,2,0,0,1,2.74,0l4,4a2.78,2.78,0,0,0,2,.81h1.41a.54.54,0,0,1,.22.05l3.1-3.1a4.1,4.1,0,0,0,0-5.78" transform="translate(-2.36 -2.8)"/></svg>
<h1 class="pgly-wps--title pgly-wps-is-6">
	Pix por Piggly
</h1>

<div class="pgly-wps--row">
	<div class="pgly-wps--column">
		<h2 class="pgly-wps--title pgly-wps-is-5">Importe os dados da sua Conta Pix</h2>

		<div class="pgly-wps--notification pgly-wps-is-warning">
			⚠ Se você está com dificuldades de preencher os dados da
			Conta Pix ou se os dados preenchidos não estão funcionando
			tente importar um código Pix Copia & Cola emitido pelo seu
			banco abaixo.
		</div>

		<p>
			Quando os dados da sua conta Pix são preenchidos incorretamente,
			podem haver incompatibilidades.	Para facilitar o processo de 
			cadastramento de uma Conta Pix, você pode inserir abaixo um
			<strong>código Pix</strong> gerado pelo seu banco.
		</p>

		<div class="pgly-wps--notification pgly-wps-is-primary pgly-wps-is-light">
			<h4 class="pgly-wps--title pgly-wps-is-7">Como obter um código Pix do seu banco?</h4>
			Acesse o aplicativo do banco com sua conta Pix e crie um novo Pix 
			para pagamento. Informe	qualquer valor, apenas para gerar um Pix de 
			pagamento. E, então, copie o "Pix Copia & Cola" gerado pelo seu banco
			e cole-o abaixo. Caso seu banco não tenha a opção "Pix Copia & Cola", 
			recomendamos que leia o código do QR Code de pagamento gerado com 
			algum aplicativo de terceiro.
		</div>

		<div class="pgly-wps--field">
			<label class="pgly-wps--label" for="amount">Código Pix Copia & Cola</label>
			<div class="pgly-wps--content">
				<input 
					tabindex="0" 
					type="number" 
					id="pix-por-piggly-brcode" 
					name="pix-por-piggly-brcode" 
					placeholder="000201..." 
					type="text">
			</div>
			<p class="pgly-wps--description">
				Simule o valor para pagamento do Pix.
			</p>
		</div>

		<div class="pgly-wps--notification pgly-wps-is-warning pgly-wps-is-light">
			O plugin extrairá automaticamente os dados como <em>Nome do Titular, 
			Cidade, Tipo da Chave e Chave</em> do seu Pix. Os códigos Pix são
			gerados pelo aplicativo do seu banco e começam com: <code>000201</code>.
		</div>

		<div class="pgly-wps--response" id="pix-por-piggly--response">
		</div>

		<button
			class="pgly-wps--button pgly-wps-is-regular pgly-async--behaviour pgly-wps-is-primary"
			data-action="pgly_wc_piggly_pix_admin_import"
			data-form="pgly_wc_piggly_pix_admin_import"
			data-response-container="pix-por-piggly--response">
			Importar Pix Copia & Cola
		</button>
		
		<script>
			document.addEventListener('DOMContentLoaded', () => {
				new PglyWpsAsync({
					container: '#pgly-wps-plugin',
					responseContainer: 'pix-por-piggly--response',
					url: wcPigglyPix.ajax_url,
					x_security: wcPigglyPix.x_security,
					form: {
						pgly_wc_piggly_pix_admin_import: [
							{
								id: 'pix-por-piggly-brcode',
								name: 'pix',
								required: true
							}
						]
					},
					messages: {
						request_error: 'Ocorreu um erro ao processar a requisição',
						invalid_fields: 'Campos inválidos'
					},
					debug: true
				});
			});
		</script>

	</div>
</div>
						