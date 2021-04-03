<?php
namespace Piggly\WC\Pix\Order;

use Piggly\WC\Pix\WP\Helper as WP;

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