<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

global $post;

$order = new WC_Order( $post->ID );
$pix   = $order->get_meta('_wc_piggly_pix');
$data  = [];
?>

<div id="pgly-pix-por-piggly" class="pgly-wps--settings" style="padding: 10px;">
	<h3 class="pgly-wps--title pgly-wps-is-6">Pix (Legado)</h3>
	<p>
		Esse pedido foi associado a versão do Pix inferior
		a <strong>2.0.0</strong>, você ainda pode ver os dados, mas esses
		dados não podem ser atualizados.
	</p>

	<div class="pgly-wps--space"></div>
	<div class="pgly-wps--response" id="pix-por-piggly--response"></div>

	<button 
		class="pgly-wps--button pgly-async--behaviour pgly-wps-is-primary"
		data-action="pgly_wc_piggly_pix_admin_update"
		data-response-container="pix-por-piggly--response"
		data-refresh="true"
		data-order="<?=$order->get_id();?>">
		Atualizar Versão
		<svg 
			class="pgly-wps--spinner pgly-wps-is-primary"
			viewBox="0 0 50 50">
			<circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
		</svg>
	</button>

	<?php if (empty($order) || empty($pix)) : ?>
		<p>Nenhum pix associado ao pedido.</p>
	<?php return; endif; ?>

	<?php if ( !empty($pix['pix_qr']) ) : ?>
	<div>
		<?php echo '<img style="max-width:100%; height: auto;" src="'.$pix['pix_qr'].'" alt="QR Code de Pagamento" />'; ?>
	</div>
	<?php endif; ?>

	<div class="pgly-wps--explorer pgly-wps-is-compact">
		<strong>Valor do Pix</strong>
		<span><?=\wc_price($pix['amount']);?></span>
	</div>
	<div class="pgly-wps--explorer pgly-wps-is-compact">
		<strong>Chave Pix</strong>
		<span><?=$pix['key_value'];?></span>
	</div>
	<div class="pgly-wps--explorer pgly-wps-is-compact">
		<strong>Identificador</strong>
		<span><?=$pix['identifier'];?></span>
	</div>
	<div class="pgly-wps--explorer pgly-wps-is-compact">
		<strong>Pix Copia & Cola</strong>
		<span><?=$pix['pix_code'];?></span>
	</div>

	<?php
	$receipt = $order->get_meta('_wc_piggly_pix_receipt');
	if ( !empty($receipt) ) : ?>
		<a 
			href="<?=$receipt?>" 
			target="_blank" 
			style="display: block; text-align: center; margin: 0 0 6px;" 
			class="pgly-wps--button pgly-wps-is-expanded pgly-wps-is-success">
			Comprovante Pix
		</a>
	<?php endif; ?>

	<script>
		document.addEventListener('DOMContentLoaded', () => {
			new PglyWpsAsync({
				container: '#pgly-pix-por-piggly',
				responseContainer: 'pix-por-piggly--response',
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
</div>