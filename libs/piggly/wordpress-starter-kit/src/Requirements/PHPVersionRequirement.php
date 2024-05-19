<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Requirements;

use Exception;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Requirements\Interfaces\RequirementInterface;
/**
 * The runner validates if current php
 * version is similar to expected.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Requirements
 * @version 1.0.12
 * @since 1.0.12
 * @category Requirement
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
class PHPVersionRequirement implements RequirementInterface
{
    /**
     * Method to run all business logic.
     * $params has available options:
     *
     * required_version
     * custom_response
     *
     * @param $params
     * @since 1.0.12
     * @return void
     */
    public static function run(array $params = [])
    {
        if (\version_compare(\phpversion(), $params['required_version'], '<')) {
            throw new Exception($params['custom_response'] ?? 'The minimum required version for PHP is: ' . $params['required_version']);
        }
    }
}
