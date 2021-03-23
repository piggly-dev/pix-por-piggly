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

use Piggly\Pix\Parser;

$upload     = wp_upload_dir();
$uploadPath = $upload['basedir'].'/qrcodes/';
$uploadUrl  = $upload['baseurl'].'/qrcodes/';

if ( ! file_exists( $uploadPath ) ) 
{ wp_mkdir_p( $uploadPath ); }

$sucess = false;

if ( !empty( $qrcode ) )
{
	$img        = str_replace('data:image/png;base64,', '', $qrcode);
	$img        = str_replace(' ', '+', $img);
	$data_      = base64_decode($img);
	$fileName   = uniqid() . '.png';
	$file       = $uploadPath . $fileName;
	$success    = file_put_contents($file, $data_);
}
?>

<p>
	Caso tenha perdido o link para pagamento, ou fechado antes da conclusão, 
	<a href="<?=$order->get_checkout_order_received_url();?>">clique aqui</a>.
	<?php echo wptexturize( $data->instructions ); ?>
</p>

<?php if ( !empty($data->receipt_page_value) || !empty($data->whatsapp) || !empty($data->telegram) ) : ?>
	<p>Utilize os links abaixo para enviar seu comprovante:</p>
	<div style="text-align:center;">
	<?php if ( !empty($data->receipt_page_value) ) : ?>
		<a href="<?=$data->receipt_page_value?>" style="margin: 5px; position: relative; vertical-align: middle; display: inline-table; background-color: #87ff8e;font-weight: bold;color: #000;padding: 12px 24px;border: 1px solid #87ff8e;text-decoration: none;text-align: center;font-size: 14px;border-radius: 48px;">
			Enviar Comprovante
		</a>
	<?php endif; ?>

	<?php if ( !empty($data->whatsapp) ) : ?>
		<a href="<?=sprintf('https://wa.me/%s?text=%s',str_replace('+', '', Parser::parsePhone($data->whatsapp)),urlencode($data->whatsapp_message))?>" style="margin: 5px; position: relative; vertical-align: middle; display: inline-table;font-weight: bold;color: #000;padding: 12px 24px;border: 1px solid;background-color: #25D366; border-color: #25D366;text-decoration: none;text-align: center;font-size: 14px;border-radius: 48px;">
			Comprovante via Whatsapp
		</a>
	<?php endif; ?>

	<?php if ( !empty($data->telegram) ) : ?>
		<a href="<?=sprintf('https://t.me/%s?text=%s',str_replace('@', '', $data->telegram),urlencode($data->telegram_message))?>" style="margin: 5px; position: relative; vertical-align: middle; display: inline-table;font-weight: bold;color: #000;padding: 12px 24px;border: 1px solid;background-color: #6CC1E3; border-color: #6CC1E3;text-decoration: none;text-align: center;font-size: 14px;border-radius: 48px;">
			Comprovante via Telegram
		</a>
	<?php endif; ?>
	</div>
<?php endif; ?>

<?php if ( $data->pix_qrcode && $success ) : ?>
<div style="margin: 36px auto; text-align: center;">
	<h4 style="text-align: center; font-size: 24px;">Pague com o QR Code</h4>
	<?php echo '<img style="margin: 0 auto; display: table; background-color: #FFF" src="'.$uploadUrl.$fileName.'" alt="QR Code de Pagamento" />'; ?>
</div>
<?php endif; ?>
	
<?php if ( $data->pix_copypast ) : ?>
<p style="text-align: center">- OU -<p>
<div style="margin: 36px auto;">
	<h4 style="text-align: center; font-size: 24px;">Pix Copie & Cole</h4>
	<p>Copie o código para realizar o pagamento <code style="background-color: #CCC; font-size: 12px"><?=$pix;?></code></p>
</div>
<?php endif; ?>
	
<?php if ( $data->pix_manual ) : ?>
<p style="text-align: center">- OU -<p>
<div style="margin: 36px auto;">
	<h4 style="text-align: center; font-size: 24px;">Faça uma Transferência PIX</h4>
	<p style="margin: 0 0 8px; font-size: 20px;"><strong style="font-size: 14px; font-weight: 900; text-transform: uppercase; display: table;">Tipo de Chave</strong> <?=$data->key_type_alias?></p>
	<p style="margin: 0 0 8px; font-size: 20px;"><strong style="font-size: 14px; font-weight: 900; text-transform: uppercase; display: table;">Chave Pix</strong> <?=$data->key_value?></p>
	<p style="margin: 0 0 8px; font-size: 20px;"><strong style="font-size: 14px; font-weight: 900; text-transform: uppercase; display: table;">Valor</strong> R$ <?=$amount?></p>
</div>
<?php endif; ?>