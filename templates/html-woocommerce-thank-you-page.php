<?php
/**
 * Provide a public-facing view for the plugin
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://github.com/caiquearaujo
 * @since      1.0.0
 *
 * @package    Piggly_Woocommerce_Pix
 * @subpackage Piggly_Woocommerce_Pix/public/partials
 */
?>

<div style="margin: 36px auto">
	<svg style="display:table;max-width: 32px;margin: 12px auto;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 165.27 165.27"><path d="M137.25,145.29a24.11,24.11,0,0,1-17.16-7.1L95.3,113.4a4.71,4.71,0,0,0-6.52,0L63.9,138.28a24.12,24.12,0,0,1-17.16,7.11H41.85l31.4,31.39a25.11,25.11,0,0,0,35.5,0l31.49-31.49Z" transform="translate(-8.37 -18.87)"/><path d="M46.74,57.61A24.12,24.12,0,0,1,63.9,64.72L88.78,89.61a4.62,4.62,0,0,0,6.52,0l24.78-24.79a24.16,24.16,0,0,1,17.17-7.11h3L108.75,26.22a25.11,25.11,0,0,0-35.5,0L41.85,57.61Z" transform="translate(-8.37 -18.87)"/><path d="M166.28,83.75l-19-19a3.57,3.57,0,0,1-1.35.27h-8.65a17.11,17.11,0,0,0-12,5L100.45,94.76a11.9,11.9,0,0,1-16.82,0L58.75,69.88a17.11,17.11,0,0,0-12-5H36.1a3.44,3.44,0,0,1-1.28-.26L15.72,83.75a25.09,25.09,0,0,0,0,35.5l19.1,19.11a3.44,3.44,0,0,1,1.28-.26H46.74a17.11,17.11,0,0,0,12-5l24.88-24.88a12.19,12.19,0,0,1,16.82,0L125.24,133a17.11,17.11,0,0,0,12,5h8.65a3.57,3.57,0,0,1,1.35.27l19-19a25.09,25.09,0,0,0,0-35.5" transform="translate(-8.37 -18.87)"/></svg>
	<h2 style="text-align:center;">Pague agora com o <strong>Pix</strong></h2>

	<p style="font-size:18px;">Para dar continuidade ao pagamento, utilize um dos métodos abaixo para pagar com o PIX e siga as instruções:</p>
	<blockquote><?php echo wpautop( wptexturize( $data->instructions ) ); ?></blockquote>

	<?php if ( !empty($data->receipt_page_value) ) : ?>
		<a href="<?=$data->receipt_page_value?>" style="text-align: center; display: table; margin: 12px auto; font-size: 16px; font-style: normal; text-decoration: none; padding: 12px; color: #565656; border: 2px solid #dcdcdc;">Clique aqui para enviar o comprovante</a>
	<?php endif; ?>

	<div style="text-align:center;display:table;width: 100%;">
		<?php if ( $data->pix_qrcode ) : ?>
		<div style="display:inline-table; max-width: 320px; padding:24px">
			<h3 style="font-size:24px;">A) Escaneie o QR Code abaixo com o aplicativo do seu banco:</h3>
			<p><?php echo '<img style="margin:12px auto" src="'.$qrcode.'" alt="QR Code de Pagamento" />'; ?></p>
		</div>
		<?php endif; ?>
		<?php if ( $data->pix_copypast ) : ?>
		<div style="display:inline-table; max-width: 320px; padding:24px">
			<h3>B) Utilize a função Pix Copie & Cole no aplicativo do seu banco e cole o código abaixo:</h3>
			<input id="piggly_pix" style="font-size: 12px; width: 100%; padding: 6px; border: 0; color: #000; background-color: #efefef;" name="pix" value="<?=$pix;?>" readonly/>
			<button style="width: 100%;position: relative;display: table;background-color: #eeeeee;border-color: #eeeeee;color: #333333;" onclick="pigglyCopyPix();">Copiar Pix</button>
		</div>
		<?php endif; ?>
		<?php if ( $data->pix_manual ) : ?>
		<div style="display:inline-table; max-width: 320px; padding:24px">
			<h3>C) Faça uma <strong>Transferência PIX</strong> com os dados abaixo no aplicativo do seu banco:</h3>
			<p style="margin: 0 0 8px; font-size: 20px;"><strong style="font-size: 14px; font-weight: 900; text-transform: uppercase; display: table; margin: 0 auto -8px;">Tipo de Chave</strong> <?=$data->key_type?></p>
			<p style="margin: 0 0 8px; font-size: 20px;"><strong style="font-size: 14px; font-weight: 900; text-transform: uppercase; display: table; margin: 0 auto -8px;">Chave Pix</strong> <?=$data->key_value?></p>
			<p style="margin: 0 0 8px; font-size: 20px;"><strong style="font-size: 14px; font-weight: 900; text-transform: uppercase; display: table; margin: 0 auto -8px;">Valor</strong> R$ <?=$amount?></p>
		</div>
		<?php endif; ?>
	</div>
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