<?php
/**
 * Provide a public-facing view for the plugin
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://github.com/caiquearaujo
 * @since      1.1.0
 *
 * @package    Piggly_Woocommerce_Pix
 * @subpackage Piggly_Woocommerce_Pix/templates
 */

 $upload     = wp_upload_dir();
 $uploadPath = $upload['basedir'].'/qrcodes/';
 $uploadUrl  = $upload['baseurl'].'/qrcodes/';

 if ( ! file_exists( $uploadPath ) ) {
	wp_mkdir_p( $uploadPath );
}

 $img        = str_replace('data:image/png;base64,', '', $qrcode);
 $img        = str_replace(' ', '+', $img);
 $data_      = base64_decode($img);
 $fileName   = uniqid() . '.png';
 $file       = $uploadPath . $fileName;
 $success    = file_put_contents($file, $data_);
?>

<p>Caso tenha perdido o link para pagamento, ou fechado antes da conclusão, <a href="<?=$order->get_checkout_payment_url();?>">clique aqui</a>.</p>

<?php if ( $data->pix_qrcode && $success ) : ?>
<div style="margin: 72px auto; text-align: center;">
	<h4 style="text-align: center; font-size: 24px;">Pague com o QR Code</h4>
	<?php echo '<img style="margin: 0 auto; display: table; background-color: #FFF" src="'.$uploadUrl.$fileName.'" alt="QR Code de Pagamento" />'; ?>
</div>
<?php endif; ?>
	
<?php if ( $data->pix_copypast ) : ?>
<p style="text-align: center">- OU -<p>
<div style="margin: 72px auto;">
	<h4 style="text-align: center; font-size: 24px;">Pix Copie & Cole</h4>
	<p>Copie o código para realizar o pagamento <code style="background-color: #CCC; font-size: 12px"><?=$pix;?></code></p>
</div>
<?php endif; ?>
	
<?php if ( $data->pix_manual ) : ?>
<p style="text-align: center">- OU -<p>
<div style="margin: 72px auto;">
	<h4 style="text-align: center; font-size: 24px;">Faça uma Transferência PIX</h4>
	<p style="margin: 0 0 8px; font-size: 20px;"><strong style="font-size: 14px; font-weight: 900; text-transform: uppercase; display: table;">Tipo de Chave</strong> <?=$data->key_type?></p>
	<p style="margin: 0 0 8px; font-size: 20px;"><strong style="font-size: 14px; font-weight: 900; text-transform: uppercase; display: table;">Chave Pix</strong> <?=$data->key_value?></p>
	<p style="margin: 0 0 8px; font-size: 20px;"><strong style="font-size: 14px; font-weight: 900; text-transform: uppercase; display: table;">Valor</strong> R$ <?=$amount?></p>
</div>
<?php endif; ?>