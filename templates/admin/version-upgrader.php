<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } 
?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26.92 26.92" style="width: 24px; height: 24px"><path d="M23.35,23.39a3.93,3.93,0,0,1-2.8-1.16l-4-4a.75.75,0,0,0-1.06,0L11.4,22.25a3.94,3.94,0,0,1-2.79,1.16h-.8l5.12,5.11a4.08,4.08,0,0,0,5.78,0l5.13-5.13Z" transform="translate(-2.36 -2.8)"/><path d="M8.61,9.11a3.9,3.9,0,0,1,2.79,1.16l4.06,4.05a.75.75,0,0,0,1.06,0l4-4a4,4,0,0,1,2.8-1.15h.49L18.71,4a4.08,4.08,0,0,0-5.78,0L7.81,9.11Z" transform="translate(-2.36 -2.8)"/><path d="M28.08,13.37,25,10.27a.54.54,0,0,1-.22,0H23.35a2.82,2.82,0,0,0-2,.81l-4,4a1.94,1.94,0,0,1-1.37.57,1.91,1.91,0,0,1-1.37-.57l-4.06-4.05a2.74,2.74,0,0,0-2-.81H6.88a.65.65,0,0,1-.21,0L3.56,13.37a4.08,4.08,0,0,0,0,5.78l3.11,3.11a.65.65,0,0,1,.21,0H8.61a2.78,2.78,0,0,0,2-.81l4.06-4.05a2,2,0,0,1,2.74,0l4,4a2.78,2.78,0,0,0,2,.81h1.41a.54.54,0,0,1,.22.05l3.1-3.1a4.1,4.1,0,0,0,0-5.78" transform="translate(-2.36 -2.8)"/></svg>
<h1 class="pgly-wps--title pgly-wps-is-4">
	Pix por Piggly
</h1>

<div class="pgly-wps--row">
	<div class="pgly-wps--column pgly-wps-col--6">
		<p>
			O plugin <strong>Pix por Piggly</strong> passou por um processo
			de transformação na sua versão <code>2.0.0</code>. Para garantir
			que tudo esteja funcionando devidamente, precisamos atualizar
			o sistema para certificar que está tudo compatível.
		</p>
		<br>

		<p>
			Como a regra de versionamento de código manda, a versão 2.x será 
			incompatível com a versão 1.x não tenha dúvidas disso. A versão 2.x 
			foi projetada para ser totalmente compatível com as APIs do Pix, 
			que atualizam automaticamente os pedidos, e essas APIs vão mudar 
			sim o comportamento do Pix. Versões desatualizadas de MySQL e PHP 
			podem ser o problema e dificultar a compatibilidade. E estamos nos 
			esforçando para lançar micro-correções para essas necessidades. A 
			qualquer momento é possível fazer o downgrade para a versão 1.x e 
			continuar utilizando todos os recursos dela que já estão otimizados 
			e não precisavam de atualização como uma versão 1.x.
		</p>
		<br>

		<p>
			Tenha certeza de que você quer continuar nesta versão antes de atualizar.
		</p>
		<br>
		
		<div class="pgly-wps--explorer pgly-wps-is-compact">
			<strong>Sua versão do plugin</strong> 
			<span><?=get_option('wc_piggly_pix_version', '?');?></span>
		</div>
		
		<div class="pgly-wps--explorer pgly-wps-is-compact">
			<strong>Sua versão do banco de dados</strong> 
			<span><?=get_option('wc_piggly_pix_dbversion', '?');?></span>
		</div>

		<br>
		<p>
			Após, a atualização:
		</p>
		<br>
		
		<div class="pgly-wps--explorer pgly-wps-is-compact pgly-wps-is-warning">
			<strong>Nova versão do plugin</strong> 
			<span>2.0.0</span>
		</div>
		
		<div class="pgly-wps--explorer pgly-wps-is-compact pgly-wps-is-warning">
			<strong>Nova versão do banco de dados</strong> 
			<span>2.0.0</span>
		</div>

		<br>
		<p>
			O processo de atualização poderá demorar alguns instantes.
			Quando o processo for concluído, você terá acesso a todas
			as configurações do plugin e aos novos recursos. 
			<strong>Suas configurações serão preservadas.</strong>
		</p>
		<br>

		<div class="pgly-wps--notification pgly-wps-is-danger">
			Recomendamos que revise as suas configurações do Pix após
			o processo de atualização ser concluído, atualize os
			Links permanentes do seu Wordpress para carregar os 
			endpoints Pix e limpe o cachê.
		</div>

		<button 
			class="pgly-wps--button pgly-async--behaviour pgly-wps-is-warning"
			data-action="pgly_wc_piggly_pix_upgrader"
			>
			Iniciar processo de atualização
			<svg 
				class="pgly-wps--spinner pgly-wps-is-white"
				viewBox="0 0 50 50">
				<circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
			</svg>
		</button>
	</div>
	<div class="pgly-wps--column pgly-wps-col--6">
		<div class="pgly-wps--notification pgly-wps-is-danger">
			Aguarde o processo ser concluído antes de sair desta página.
		</div>

		<script>
			document.addEventListener('DOMContentLoaded', () => {
				new PglyWpsAsync({
					container: '#pgly-wps-plugin',
					responseContainer: 'pgly-wps--response',
					url: wcPigglyPix.ajax_url,
					x_security: wcPigglyPix.x_security,
					messages: {
						request_error: 'Ocorreu um erro ao processar a requisição',
						invalid_fields: 'Campos inválidos'
					},
					debug: true
				});
			});
		</script>
		<div class="pgly-wps--response" id="pgly-wps--response">
		</div>
	</div>
</div>
