<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<?php
use Piggly\WC\Pix\Gateway\PixGateway;

global $post;

$order   = new WC_Order( $post->ID );
$gateway = new PixGateway();
$data    = [];

$receipt = $order->get_meta('_wc_piggly_pix_receipt');

if ( !empty($receipt) ) :
	printf('<a href="%s" target="_blank" style="display: block; text-align: center; margin: 0 0 6px;" class="wpgly-button wpgly-success">%s</a>', $receipt, __('Último Comprovante Recebido', WC_PIGGLY_PIX_PLUGIN_NAME));
	echo '<p>Para ver todos os comprovantes do pedido, vá para Woocommerce > Comprovantes.</p>';
endif;

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
?>
<div class="wpgly">
<?php if ( !empty($data) ) : ?>
	<?php if ( !empty($data['qrcode']) ) : ?>
	<div class="pix-method">
		<?php echo '<img style="max-width:100%; height: auto;" src="'.$data['qrcode'].'" alt="QR Code de Pagamento" />'; ?>
	</div>
	<?php endif; ?>

	<p><strong class="wpgly-caption">Valor do Pix</strong> <span class="wpgly-data"><?=wc_price($data['amount']);?></span></p>
	<p><strong class="wpgly-caption">Chave Pix</strong> <span class="wpgly-data"><?=$data['key'];?></span></p>
	<p><strong class="wpgly-caption">Identificador</strong> <span class="wpgly-data"><?=$data['id'] ?? '-';?></span></p>
	<p><strong class="wpgly-caption">Pix Copia & Cola</strong> <code style="word-break: break-all;" class="wpgly-data"><?=$data['pix'];?></code></p>
<?php else : ?>
	<p>Pix não disponível para o pedido... verifique as configurações do plugin.</p>
<?php endif; ?>

<?php
if ( $gateway->is_payment_waiting($order) ) :
	printf('<button class="wpgly-pix-button wpgly-button wpgly-button-extended wpgly-accent" data-oid="%s" data-aid="%s" style="margin-bottom:6px">%s</button>', $order->get_id(), 'regenerate', __('Regenerar', WC_PIGGLY_PIX_PLUGIN_NAME));
endif; 
?>

<a href="<?=admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_piggly_pix_gateway' );?>" style="display: block; text-align: center;" class="wpgly-button wpgly-action">Configurações do Pix</a>
</div>