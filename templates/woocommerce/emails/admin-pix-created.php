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

use Piggly\WooPixGateway\Core\Entities\PixEntity;

if ( !defined('ABSPATH') ) { exit; }

/** @var PixEntity $pix */
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p>Olá,</p>
<p><?php printf( esc_html__( 'Você recebeu um novo pedido #%s via Pix no valor de %s.', 'wc-piggly-pix' ), esc_html( $order->get_order_number() ), \wc_price($pix->getAmount()) ); ?></p>
<p><?php printf( esc_html__( 'Localize no aplicativo do seu banco, se disponível, um Pix com o identificador %s.', 'wc-piggly-pix' ), $pix->getTxid() ); ?></p>
<p><?php printf(__( '<a href="%s">Clique aqui</a> para visualizar o pedido', 'wc-piggly-pix' ), $order->get_edit_order_url()); ?></p>

<?php
/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

do_action( 'woocommerce_email_footer', $email );