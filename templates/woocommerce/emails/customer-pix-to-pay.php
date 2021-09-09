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

if ( !defined('ABSPATH') ) { exit; }
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php printf( esc_html__( 'Olá %s,', $domain ), esc_html( $order->get_billing_first_name() ) ); ?></p>
<p><?php printf( esc_html__( 'Para darmos continuidade ao seu pedido, é necessário realizar o pagamento via Pix.', $domain ), esc_html( $order->get_order_number() ) ); ?></p>
<p><?php printf(__( '<a href="%s">Clique aqui</a> para realizar o pagamento.', $domain ), Endpoints::getPaymentUrl($order)); ?></p>

<?php
/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

do_action( 'woocommerce_email_footer', $email );