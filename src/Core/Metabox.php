<?php
namespace Piggly\WooPixGateway\Core;

use Piggly\WooPixGateway\CoreConnector;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Scaffold\Initiable;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\WP;

use WC_Order;
use WP_Post;

/**
 * Manages all metabox actions and filters.
 * 
 * @package \Piggly\WooBdmGateway
 * @subpackage \Piggly\WooBdmGateway\Core
 * @version 2.0.0
 * @since 2.0.0
 * @category Core
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license GPLv3 or later
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
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
		if ( !WP::is_pure_admin() )
		{ return; }

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

		if ( $order->get_payment_method() !== CoreConnector::plugin()->getName() )
		{ return; }

		// CSS and JS
		CoreConnector::enqueuePglyWpsAdmin(true);

		$pix = $order->get_meta('_pgly_wc_piggly_pix_latest_pix');

		if ( !empty($pix) )
		{
			add_meta_box(
				$this->_plugin->getDomain().'-metabox-pix-gateway',
				$this->__translate('Dados do Pix'),
				array( $this, 'display' ),
				'shop_order',
				'side',
				'high'
			);
		}
		else
		{
			$pix = $order->get_meta('_wc_piggly_pix');

			if ( !empty($pix) )
			{
				add_meta_box(
					$this->_plugin->getDomain().'-metabox-pix-gateway',
					$this->__translate('Dados do Pix'),
					array( $this, 'display_legacy' ),
					'shop_order',
					'side',
					'high'
				);
			}
		}
	}

	/**
	 * Display the legacy metabox.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function display_legacy ()
	{ require_once($this->_plugin->getTemplatePath().'admin/legacy-metabox.php');	}

	/**
	 * Display the metabox.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function display ()
	{ require_once($this->_plugin->getTemplatePath().'admin/metabox.php');	}
}