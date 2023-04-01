<?php
/**
 * Verify account e-mail.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/plain/wc-bdm-verify-account.php.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails\Plain
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
echo esc_html( wp_strip_all_tags( $email_heading ) );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo "Olá,\n\n";
echo sprintf( esc_html__( 'Você recebeu um novo pedido #%s via Pix no valor de %s.', 'wc-piggly-pix' ), esc_html( $order->get_order_number() ), \wc_price($pix->getAmount()) ) . "\n\n";
echo sprintf( esc_html__( 'Localize no aplicativo do seu banco, se disponível, um Pix com o identificador %s.', 'wc-piggly-pix' ), $pix->getTxid() ) . "\n\n";
echo sprintf(__( '<a href="%s">Clique aqui</a> para visualizar o pedido', 'wc-piggly-pix' ), $order->get_edit_order_url()) . "\n\n";

echo "\n\n----------------------------------------\n\n";

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo esc_html( wp_strip_all_tags( wptexturize( $additional_content ) ) );
	echo "\n\n----------------------------------------\n\n";
}

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );