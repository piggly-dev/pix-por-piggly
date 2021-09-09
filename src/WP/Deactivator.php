<?php
namespace Piggly\WooPixGateway\WP;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Interfaces\Runnable;

/**
 * Desactivate plugin.
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
class Deactivator implements Runnable
{
	/**
	 * Method to run all business logic.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function run ()
	{
		// Remove all cronjobs
		Cron::destroy();
	}
}