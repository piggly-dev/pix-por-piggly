<?php
namespace Piggly\WooPixGateway\Core;

use Piggly\WooPixGateway\Core\Emails\AdminPixPaid;
use Piggly\WooPixGateway\Core\Emails\AdminPixReceiptSent;
use Piggly\WooPixGateway\Core\Emails\CustomerPixCloseToExpires;
use Piggly\WooPixGateway\Core\Emails\CustomerPixExpired;
use Piggly\WooPixGateway\Core\Emails\CustomerPixPaid;
use Piggly\WooPixGateway\Core\Emails\CustomerPixToPay;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Scaffold\Initiable;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\WP;

/**
 * Register all woocommerce e-mails.
 * 
 * @package \Piggly\WooPixGateway
 * @subpackage \Piggly\WooPixGateway\Core
 * @version 2.0.0
 * @since 2.0.0
 * @category Core
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license GPLv3 or later
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
class Emails extends Initiable
{
	/**
	 * Startup method with all actions and
	 * filter to run.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function startup ()
	{
		WP::add_filter(
			'woocommerce_email_classes',
			$this,
			'register_emails',
			10,
			1
		);
	}

	/**
	 * Return all emails classes included.
	 *
	 * @return array
	 * @since 2.0.0
	 */
	public function register_emails ( $emails )
	{
		$classes = [
			'PixGatewayAdminPixReceiptSent' => AdminPixReceiptSent::class,
			'PixGatewayAdminPixPaid' => AdminPixPaid::class,
			'PixGatewayCustomerPixCloseToExpires' => CustomerPixCloseToExpires::class,
			'PixGatewayCustomerPixExpired' => CustomerPixExpired::class,
			'PixGatewayCustomerPixPaid' => CustomerPixPaid::class,
			'PixGatewayCustomerPixToPay' => CustomerPixToPay::class,
		];

		foreach ( $classes as $class => $namespace )
		{
			if ( !isset( $emails[$class] ) )
			{ $emails[$class] = new $namespace(); }
		}
		
		return $emails;
	}
}