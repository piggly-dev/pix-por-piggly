<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Interfaces;

/**
 * The requirements interface implements the run()
 * static method to run all business logic applied to a
 * class.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Core\Interfaces
 * @version 2.0.0
 * @since 2.0.0
 * @category Interfaces
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2022 Piggly Lab <dev@piggly.com.br>
 */
interface RequirementInterface
{
    /**
     * Method to run all business logic.
     *
     * @param array $params Requirements params.
     * @since 2.0.0
     * @return void
     */
    public static function run(array $params);
}
