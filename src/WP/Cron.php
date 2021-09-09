<?php
namespace Piggly\WooPixGateway\WP;

use Piggly\WooPixGateway\Core\Entities\PixEntity;
use Piggly\WooPixGateway\Core\Gateway\PixGateway;
use Piggly\WooPixGateway\CoreConnector;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Scaffold\Initiable;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\WP;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Plugin;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Settings\KeyingBucket;

/**
 * Cronjob tasks.
 * 
 * @package \Piggly\WooPixGateway
 * @subpackage \Piggly\WooPixGateway\WP
 * @version 2.0.0
 * @since 2.0.0
 * @category WP
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license GPLv3 or later
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
class Cron extends Initiable
{
	/**
	 * Available frequencies.
	 * 
	 * @var string
	 * @since 2.0.0
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
	 * @since 2.0.0
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
			'pgly_cron_wc_piggly_pix_processing',
			$this,
			'processing'
		);
	}

	/**
	 * Get all orders paid with Pix, and run action
	 * pgly_wc_piggly_pix_process to processing
	 * payment data and update pix.
	 *
	 * @action pgly_wc_piggly_pix_process
	 * @since 2.0.0
	 * @return void
	 */
	public function processing ()
	{
		WC()->mailer();
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'pgly_pix';
		$gateway = new PixGateway();

		CoreConnector::debugger()->debug(CoreConnector::__translate('Iniciando a tarefa cron para processamento dos Pix'));
		
		// All non-static pixs
		$pixs = $wpdb->get_results("SELECT * FROM $table_name WHERE `status` = 'created' OR status = 'waiting'");

		foreach ( $pixs as $pix )
		{ $gateway->process_pending(\apply_filters('pgly_wc_piggly_pix_process', PixEntity::create($pix))); }
	}

	/**
	 * All schedules available to current cron jobs.
	 *
	 * @param array $schedules
	 * @since 2.0.0
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
	 * @since 2.0.0
	 * @return void
	 */
	public static function create ( Plugin $plugin ) 
	{
		/** @var KeyingBucket $settings */
		$settings = $plugin->settings()->bucket()->get('orders', new KeyingBucket());

		// --- Cronjob to do transactions
		if ( wp_next_scheduled('pgly_cron_wc_piggly_pix_processing') )
		{ wp_clear_scheduled_hook( 'pgly_cron_wc_piggly_pix_processing' ); }

		wp_schedule_event(
			current_time('timestamp'), 
			$settings->get('cron_frequency', 'everyfifteen'), 
			'pgly_cron_wc_piggly_pix_processing' 
		);
	}

	/**
	 * Destroy cron jobs.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function destroy ()
	{ wp_clear_scheduled_hook( 'pgly_cron_wc_piggly_pix_processing' ); }
}