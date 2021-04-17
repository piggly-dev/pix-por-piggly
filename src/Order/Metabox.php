<?php
namespace Piggly\WC\Pix\Order;

use Piggly\WC\Pix\Gateway\PixGateway;
use Piggly\WC\Pix\WP\Debug;
use Piggly\WC\Pix\WP\Helper as WP;

use WC_Order;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Load metabox in order edit page only when
 * payment method is equal to pix.
 *
 * @since      1.2.0 
 * @package    Piggly\WC\Pix
 * @subpackage Piggly\WC\Pix
 * @author     Caique <caique@piggly.com.br>
 * @author     Piggly Lab <dev@piggly.com.br>
 */
class Metabox 
{
	/**
	 * Add all actions and filters to configure metabox.
	 * 
	 * @since 1.2.0
	 * @return void
	 */
	public static function init ()
	{
		$base = new self();

		WP::add_action('add_meta_boxes', $base, 'add', 10, 2 );
		WP::add_action('wp_ajax_wpgly_pix_update_order', $base, 'update_order');
	}

	/**
	 * Update order action thought ajax.
	 * 
	 * @since 1.2.4
	 * @return void
	 */
	public function update_order ()
	{
		if ( !is_admin() )
		{ 
			Debug::error('Uso restrito ao administrativo.');
			wp_send_json_error( __('Uso restrito ao administrativo.', \WC_PIGGLY_PIX_PLUGIN_NAME) );
			wp_die();
		}

		if ( !check_ajax_referer( 'wc_piggly_pix_gateway_nounce', 'security', false )
				|| !wp_verify_nonce( $_POST['security'], 'wc_piggly_pix_gateway_nounce' ) ) 
		{
			Debug::error('Token de segurança inválido.');
			wp_send_json_error( __('Token de segurança inválido.', \WC_PIGGLY_PIX_PLUGIN_NAME) );
			wp_die();
		}

		$order_id = filter_input ( INPUT_POST, 'oid', FILTER_SANITIZE_STRING);
		$order    = new WC_Order( $order_id );

		if ( !$order )
		{ 
			Debug::error('Pedido não localizado.');
			wp_send_json_error( __('Pedido não localizado.', \WC_PIGGLY_PIX_PLUGIN_NAME) );
			wp_die();
		}

		if ( strpos($order->get_payment_method(), 'wc_piggly_pix_gateway') )
		{ 
			Debug::error('O método de pagamento é incompatível.');
			wp_send_json_error( __('O método de pagamento é incompatível.', \WC_PIGGLY_PIX_PLUGIN_NAME) );
			wp_die();
		}

		$action  = filter_input ( INPUT_POST, 'aid', FILTER_SANITIZE_STRING);
		$return  = true;

		if ( $action === 'regenerate' )
		{
			$gateway = new PixGateway();
			$gateway->qr_regenerate = true;
			$gateway->get_pix_data($order);
		}

		if ( $return instanceof WP_Error )
		{
			Debug::info(sprintf('Erro desconhecido: %s', $return->get_error_message()));
			wp_send_json_error( $return->get_error_message() );
			wp_die();
		}

		Debug::info('Pedido atualizado com sucesso.');
		wp_send_json_success( __('Atualização realizada com sucesso.', \WC_PIGGLY_PIX_PLUGIN_NAME) );
		wp_die();
	}

	/**
	 * Add styles to order page.
	 * 
	 * @since 1.2.4
	 * @return void
	 */
	public function styles ()
	{
		if ( wp_style_is ( 'wpgly-wp-admin' ) )
		{ return; }

		wp_enqueue_style(
			'wpgly-wp-admin',
			\WC_PIGGLY_PIX_PLUGIN_URL.'assets/css/wpgly.wp.admin.min.css'
		);
	}

	/**
	 * Add scripts to order page.
	 * 
	 * @since 1.2.4
	 * @return void
	 */
	public function scripts ()
	{
		wp_enqueue_script(
			'wc_piggly_pix_gateway_update',
			\WC_PIGGLY_PIX_PLUGIN_URL.'assets/js/private/update.order.js',
			['jquery'],
			\WC_PIGGLY_PIX_PLUGIN_VERSION,
			true
		);

		wp_localize_script(
			'wc_piggly_pix_gateway_update',
			'wpgly_pix_payload',
			[
				'xhr_url' => admin_url( 'admin-ajax.php' ),
				'security'  => wp_create_nonce( 'wc_piggly_pix_gateway_nounce' )
			]
		);
	}

	/**
	 * Add metabox to order edit page only when
	 * payment method is equal to pix.
	 * 
	 * @since 1.2.0
	 * @return void
	 */
	public function add ( $post_type, $post )
	{
		if ( $post_type !== 'shop_order' )
		{ return; }

		$order = new \WC_Order( $post->ID );

		if ( $order->get_payment_method() !== 'wc_piggly_pix_gateway' )
		{ return; }

		// Startup
		$this->styles();
		$this->scripts();

		add_meta_box( 
			\WC_PIGGLY_PIX_PLUGIN_NAME.'-metabox-pix', 
			__('Pix', \WC_PIGGLY_PIX_PLUGIN_NAME), 
			array( $this, 'display'), 
			'shop_order', 
			'side', 
			'high' 
		);
	}

	/**
	 * Display the meta box in order edit page.
	 * 
	 * @since 1.2.0
	 * @return void
	 */
	public function display ()
	{
		require_once ( 
			\WC_PIGGLY_PIX_PLUGIN_PATH 
			. 'templates/admin/metabox.php' 
		);
	}
}