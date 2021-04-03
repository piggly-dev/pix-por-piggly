<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<?php
use Piggly\WC\Pix\Gateway\PixGateway;

global $post;

$order   = new WC_Order( $post->ID );
$gateway = new PixGateway();
$data    = [];

if ( $gateway->enabled === 'no' )
{
	// Get order meta data...
	$pixOrder = $order->get_meta('_wc_piggly_pix');

	if ( !empty($pixOrder) ) 
	{
		$data = [
			'qrcode' => $pixOrder['pix_qr'],
			'pix' => $pixOrder['pix_code'],
			'key' => $pixOrder['key_value'],
			'id' => $pixOrder['identifier'],
			'amount' => $pixOrder['amount']
		];
	}
}
else
{
	$data = $gateway->get_pix_data($order);

	$data = [
		'qrcode' => $data['qrcode'],
		'pix' => $data['pix'],
		'key' => $gateway->key_value,
		'id' => $gateway->identifier,
		'amount' => $data['amount']
	];
}

if ( !empty($data) ) :
?>
	<?php if ( !empty($data['qrcode']) ) : ?>
	<div class="pix-method">
		<?php echo '<img style="max-width:100%; height: auto;" src="'.$data['qrcode'].'" alt="QR Code de Pagamento" />'; ?>
	</div>
	<?php endif; ?>

	<p style="margin: 0; margin-bottom: 6px"><strong>Valor do Pix</strong>: <span><?=wc_price($data['amount']);?></span></p>
	<p style="margin: 0; margin-bottom: 6px"><strong>Chave Pix</strong>: <span><?=$data['key'];?></span></p>
	<p style="margin: 0; margin-bottom: 6px"><strong>Identificador</strong>: <span><?=$data['id'] ?? '-';?></span></p>
	<p style="margin: 0; margin-bottom: 6px"><strong>Pix Copia & Cola</strong>: <code style="word-break: break-all;"><?=$data['pix'];?></code></p>
<?php else : ?>
	<p>Pix não disponível para o pedido... verifique as configurações do plugin.</p>
<?php endif; ?>

<a href="<?=admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_piggly_pix_gateway' );?>" style="width: 100%; margin-bottom: 6px;" type="button" class="button">Ir para as Configurações do Pix</a>