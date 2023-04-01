<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Requirements;

use Exception;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Connector;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Interfaces\RequirementInterface;
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
     * @param array $params Requirements params.
     * @since 1.0.12
     * @throws Exception If current php version is lower than required.
     * @return void
     */
    public static function run(array $params = [])
    {
        $version = $params['required_version'] ?? '7.0.0';
        $response = \sprintf('The minimum required version for PHP is: %s', $params['required_version'] ?? '7.0.0');
        if (isset($params['custom_response'])) {
            $response = $params['custom_response'];
        }
        if (\version_compare(\phpversion(), $version, '<')) {
            throw new Exception($response);
        }
    }
}
