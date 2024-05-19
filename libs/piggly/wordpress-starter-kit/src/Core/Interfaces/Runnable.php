<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Interfaces;

/**
 * The runnable interface implements the run()
 * method to run all business logic applied to a
 * class.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Core\Interfaces
 * @version 1.0.0
 * @since 1.0.0
 * @category Interfaces
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
interface Runnable
{
    /**
     * Method to run all business logic.
     *
     * @since 1.0.0
     * @return void
     */
    public function run();
}
