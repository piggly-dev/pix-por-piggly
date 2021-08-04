<?php
namespace Piggly\WooPixGateway\Core;

use Piggly\Wordpress\Core\Scaffold\Initiable;
use Piggly\Wordpress\Core\WP;
use WP_Post;

class Metabox extends Initiable
{
	
	/**
	 * Startup method with all actions and
	 * filter to run.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function startup ()
	{
		WP::add_action(
			'add_meta_boxes', 
			$this, 
			'add', 
			10, 
			2 
		);
	}

	/**
	 * Add metabox to order edit page only when
	 * payment method is equal to pix.
	 *
	 * @param string $post_type
	 * @param WP_Post $post
	 * @since 2.0.0
	 * @return void
	 */
	public function add ( $post_type, $post )
	{
		if ( $post_type !== 'shop_order' )
		{ return; }

		$order = new WC_Order($post->ID);

		if ( $order->get_payment_method() !== $this->_plugin->getName() )
		{ return; }

		$this->wp_enqueue();

		add_meta_box(
			$this->_plugin->getDomain().'-metabox-pix',
			$this->__translate('Dados do Pix'),
			array( $this, 'display' ),
			'shop_order',
			'side',
			'high'
		);
	}

	public function display ()
	{ require_once($this->_plugin->getTemplatePath().'admin/metabox.php');	}

	/**
	 * Enqueue CSS scripts.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function wp_enqueue ()
	{
		wp_enqueue_script(
			'wpgly-bdm-commerce-admin-basic-js',
			$this->_plugin->getUrl().'assets/dist/js/wpgly-bdm-commerce-admin.basic.js',
			[],
			'0.0.1',
			true
		);

		wp_localize_script(
			'wpgly-bdm-commerce-admin-basic-js',
			'bdmCommerce',
			[
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'x_security' => wp_create_nonce('wpgly_bdm_ajax'),
				'plugin_url' => admin_url('admin.php?page='.$this->_plugin->getDomain())
			]
		);

		wp_enqueue_style(
			'wpgly-wps-admin',
			$this->_plugin->getUrl().'assets/dist/css/wpgly-wps-admin.css'
		);
	}
}