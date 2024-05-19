<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Requirements\Interfaces;

/**
 * The requirements interface implements the run()
 * static method to run all business logic applied to a
 * class.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Requirements\Interfaces
 * @version 1.0.12
 * @since 1.0.12
 * @category Interfaces
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
interface RequirementInterface
{
    /**
     * Method to run all business logic.
     *
     * @since 1.0.12
     * @return void
     */
    public static function run(array $params);
}
