<?php
/**
 * Verify account e-mail.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/wc-bdm-verify-account.php.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 4.0.0
 */

use Piggly\WooPixGateway\Core\Endpoints;
use Piggly\WooPixGateway\Core\Entities\PixEntity;

if ( !defined('ABSPATH') ) { exit; }

/** @var PixEntity $pix */
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php printf( esc_html__( 'Olá %s,', $domain ), esc_html( $order->get_billing_first_name() ) ); ?></p>
<p><?php printf( esc_html__( 'O pagamento do seu pedido #%s irá expirar em %s e o seu Pix ainda não foi confirmado.', $domain ), esc_html( $order->get_order_number() ), $pix->getExpiresAt()->format('d/m/Y H:i:s') ); ?></p>
<p><?php printf(__( 'Caso você já tenha pago seu pedido, <a href="%s">clique aqui</a> para enviar o comprovante e continuar com o processo de aprovação do pedido.', $domain ), Endpoints::getReceiptUrl($order)); ?></p>
<p><?php printf(__( 'Ou se preferir, <a href="%s">clique aqui</a> para visualizar o Pix e efetuar um novo pagamento.', $domain ), Endpoints::getPaymentUrl($order)); ?></p>

<?php
/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

do_action( 'woocommerce_email_footer', $email );