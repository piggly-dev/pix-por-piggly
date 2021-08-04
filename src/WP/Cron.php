<?php
namespace Piggly\WooPixGateway\WP;

use Piggly\WooPixGateway\Core\Entities\PixPayload;
use Piggly\WooPixGateway\Core\Gateway\PixGateway;
use Piggly\Wordpress\Core\Scaffold\Initiable;
use Piggly\Wordpress\Core\WP;
use Piggly\Wordpress\Plugin;
use Piggly\Wordpress\Settings\KeyingBucket;

use WC_Order;

class Cron extends Initiable
{
	/**
	 * Available frequencies.
	 * 
	 * @var string
	 * @since 1.0.0
	 */
	const AVAILABLE_FREQUENCIES = [
		'everyfifteen', 
		'twicehourly', 
		'hourly', 
		'daily', 
		'weekly', 
		'monthly'
	];

	/**
	 * Startup method with all actions and
	 * filter to run.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function startup ()
	{
		WP::add_filter(
			'cron_schedules',
			$this,
			'schedules',
			99
		);

		WP::add_action(
			'wpgly_cron_woo_pix_gateway_processing',
			$this,
			'processing'
		);
	}

	/**
	 * Get all orders paid with Pix, and run action
	 * wpgly_woo_pix_gateway_process to processing
	 * payment data and update pix.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function processing ()
	{
		/** @var KeyingBucket $settings */
		$settings = $plugin->settings()->bucket()->get('orders', new KeyingBucket());

		$this->debug()->debug($this->__translate('Iniciando a tarefa cron para processamento dos Pix'));
		
		$orders = wc_get_orders( array(
			'limit'          => -1,
			'payment_method' => $this->_plugin->getName(),
			'status'         => [
				$settings->get('waiting_status', 'on-hold'),
				$settings->get('receipt_status', 'on-hold')
			]
		) );

		foreach ( $orders as $order )
		{
			$order   = $order instanceof WC_Order ? $order : new WC_Order($order);
			$gateway = wc_get_payment_gateway_by_order($order);
			
			if ( $gateway instanceof PixGateway )
			{ do_action('wpgly_woo_pix_gateway_process', PixPayload::fill($order)); }
		}
	}

	/**
	 * All schedules available to current cron jobs.
	 *
	 * @param array $schedules
	 * @since 1.0.0
	 * @return array
	 */
	public function schedules ( array $schedules ) : array
	{
		$schedules['everyminute'] = [
			'interval' => 60,
			'display' => 'Uma vez a cada minuto'
		];

		$schedules['everyfifteen'] = [
			'interval' => 900,
			'display' => 'Uma vez a cada quinze minutos'
		];

		$schedules['twicehourly'] = [
			'interval' => 1800,
			'display' => 'Duas vezes a cada hora'
		];

		$schedules['monthly'] = [
			'interval' => 2635200,
			'display' => 'Uma vez por mês'
		];

		return $schedules;
	}

	/**
	 * Create cron jobs.
	 *
	 * @param Plugin $plugin
	 * @since 1.0.0
	 * @return void
	 */
	public static function create ( Plugin $plugin ) 
	{
		/** @var KeyingBucket $settings */
		$settings = $plugin->settings()->bucket()->get('orders', new KeyingBucket());

		// --- Cronjob to do transactions
		if ( wp_next_scheduled('wpgly_cron_woo_pix_gateway_processing') )
		{ wp_clear_scheduled_hook( 'wpgly_cron_woo_pix_gateway_processing' ); }

		wp_schedule_event(
			current_time('timestamp'), 
			$settings->get('cron_frequency', 'everyfifteen'), 
			'wpgly_cron_woo_pix_gateway_processing' 
		);
	}

	/**
	 * Destroy cron jobs.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function destroy ()
	{
		wp_clear_scheduled_hook( 'wpgly_cron_woo_pix_gateway_processing' );
	}
}