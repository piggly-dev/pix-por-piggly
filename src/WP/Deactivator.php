<?php
namespace Piggly\WooPixGateway\WP;

use Piggly\Wordpress\Core\Interfaces\Runnable;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Deactivator implements Runnable
{
	/**
	 * Method to run all business logic.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function run ()
	{
		// Remove all cronjobs
		Cron::destroy();
	}
}