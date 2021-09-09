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

use Piggly\WooPixGateway\Core\Endpoints;

defined( 'ABSPATH' ) || exit;

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
echo esc_html( wp_strip_all_tags( $email_heading ) );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo sprintf( esc_html__( 'Olá %s,', $domain ), esc_html( $order->get_billing_first_name() ) ) . "\n\n";
echo sprintf( esc_html__( 'O pagamento do seu pedido #%s irá expirar em %s e o seu Pix ainda não foi confirmado.', $domain ), esc_html( $order->get_order_number() ), $pix->getExpiresAt()->format('d/m/Y H:i:s') ) . "\n\n";
echo sprintf(__( 'Caso você já tenha pago seu pedido, <a href="%s">clique aqui</a> para enviar o comprovante e continuar com o processo de aprovação do pedido.', $domain ), Endpoints::getReceiptUrl($order)) . "\n\n";
echo sprintf(__( 'Ou se preferir, <a href="%s">clique aqui</a> para visualizar o Pix e efetuar um novo pagamento.', $domain ), Endpoints::getPaymentUrl($order)) . "\n\n";

echo "\n\n----------------------------------------\n\n";

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo esc_html( wp_strip_all_tags( wptexturize( $additional_content ) ) );
	echo "\n\n----------------------------------------\n\n";
}

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );