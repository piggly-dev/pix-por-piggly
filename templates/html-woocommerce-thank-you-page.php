<?php
/**
 * Provide a public-facing view for the plugin
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://github.com/caiquearaujo
 * @since      1.0.0
 *
 * @package    Piggly_Woocommerce_Pix
 * @subpackage Piggly_Woocommerce_Pix/templates
 */
?>

<style>
	.pix-block { margin: 36px auto; padding: 0 24px; max-width: 1280px; }
	.pix-logo { display:table;max-width: 32px;margin: 12px auto; }
	.pix-title { text-align:center; }
	.pix-subtitle { text-align: center; font-size: 14px; margin: 0 auto; display: table; max-width: 420px; font-style: italic; }
	.pix-instructions { font-size: 14px; text-align: center; display: table; padding: 24px; margin: 24px auto; background-color: #fff; border: 2px dashed #CCC; }
	.pix-instructions h3 { text-transform: uppercase; letter-spacing: 2px; }
	.pix-button { margin: 32px auto; position: relative;display: table;background-color: #87ff8e;font-weight: bold;color: #000;padding: 12px 24px;text-transform: uppercase;border: 1px solid #87ff8e;text-decoration: none;text-align: center;font-size: 12px;border-radius: 48px; }
	.pix-copy { width: 100%; position: relative; display: table; background-color: #87ff8e; font-weight: bold; color: #333333; padding: 12px; text-transform: uppercase; border: 1px solid #87ff8e; }
	.pix-method { margin: 72px auto; }
	.pix-method .pix-wrapper { margin: 0 auto; max-width: 420px; }
	.pix-method h4 { text-align: center; font-size: 24px; }
	.pix-method img, .pix-method svg { display: table; margin: 0 auto; background-color: #FFF; }
	.pix-method .pix-code { font-size: 12px; width: 100%; padding: 6px; border: 0; color: #000; background-color: #87ff8e; }
	.pix-method .pix-data { margin: 0 0 8px; font-size: 20px; text-align: center; }
	.pix-method .pix-data strong { font-size: 14px; font-weight: 900; text-transform: uppercase; display: table; margin: 0 auto -8px; }
	.pix-or { display: table; width: 100%; height: 1px; background-color: #e2e2e2; position: relative; }
	.pix-or::before { content: "OU";  position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); font-weight: bold; display: table; }
</style> 

<div class="pix-block">
	<svg class="pix-logo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 165.27 165.27"><path d="M137.25,145.29a24.11,24.11,0,0,1-17.16-7.1L95.3,113.4a4.71,4.71,0,0,0-6.52,0L63.9,138.28a24.12,24.12,0,0,1-17.16,7.11H41.85l31.4,31.39a25.11,25.11,0,0,0,35.5,0l31.49-31.49Z" transform="translate(-8.37 -18.87)"/><path d="M46.74,57.61A24.12,24.12,0,0,1,63.9,64.72L88.78,89.61a4.62,4.62,0,0,0,6.52,0l24.78-24.79a24.16,24.16,0,0,1,17.17-7.11h3L108.75,26.22a25.11,25.11,0,0,0-35.5,0L41.85,57.61Z" transform="translate(-8.37 -18.87)"/><path d="M166.28,83.75l-19-19a3.57,3.57,0,0,1-1.35.27h-8.65a17.11,17.11,0,0,0-12,5L100.45,94.76a11.9,11.9,0,0,1-16.82,0L58.75,69.88a17.11,17.11,0,0,0-12-5H36.1a3.44,3.44,0,0,1-1.28-.26L15.72,83.75a25.09,25.09,0,0,0,0,35.5l19.1,19.11a3.44,3.44,0,0,1,1.28-.26H46.74a17.11,17.11,0,0,0,12-5l24.88-24.88a12.19,12.19,0,0,1,16.82,0L125.24,133a17.11,17.11,0,0,0,12,5h8.65a3.57,3.57,0,0,1,1.35.27l19-19a25.09,25.09,0,0,0,0-35.5" transform="translate(-8.37 -18.87)"/></svg>
	<h2 class="pix-title">Pague agora com o <strong>Pix</strong></h2>

	<p class="pix-subtitle">
		<?php echo wptexturize( $data->instructions ); ?>
	</p>

	<?php if ( !empty($data->receipt_page_value) ) : ?>
		<a href="<?=$data->receipt_page_value?>" class="pix-button">Clique aqui para enviar o comprovante</a>
	<?php endif; ?>

	<?php if ( $data->pix_qrcode ) : ?>
	<div class="pix-method">
		<h4>Pague com o QR Code</h4>
		<?php echo '<img src="'.$qrcode.'" alt="QR Code de Pagamento" />'; ?>
	</div>
	<?php endif; ?>
		
	<?php if ( $data->pix_copypast ) : ?>
	<div class="pix-or"></div>
	<div class="pix-method">
		<h4>Pix Copie & Cole</h4>
		<div class="pix-wrapper">
			<input id="piggly_pix" class="pix-code" name="pix" value="<?=$pix;?>" readonly/>
			<button type="button" class="pix-copy" onclick="pigglyCopyPix();">Copiar Pix</button>
		</div>
	</div>
	<?php endif; ?>
		
	<?php if ( $data->pix_manual ) : ?>
	<div class="pix-or"></div>
	<div class="pix-method">
		<h4>Faça uma Transferência PIX</h4>
		<p class="pix-data"><strong>Tipo de Chave</strong> <?=$data->key_type?></p>
		<p class="pix-data"><strong>Chave Pix</strong> <?=$data->key_value?></p>
		<p class="pix-data"><strong>Valor</strong> R$ <?=$amount?></p>
	</div>
	<?php endif; ?>
</div>

<script>
	function pigglyCopyPix() {
		/* Get the text field */
		var copyText = document.getElementById("piggly_pix");

		/* Select the text field */
		copyText.select();
		copyText.setSelectionRange(0, 99999); /* For mobile devices */

		/* Copy the text inside the text field */
		document.execCommand("copy");
	}
</script>