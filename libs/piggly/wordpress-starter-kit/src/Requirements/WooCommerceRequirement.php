<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Requirements;

use Exception;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Connector;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Interfaces\RequirementInterface;
/**
 * The runner validates if Woocommerce is active.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Requirements
 * @version 2.0.0
 * @since 2.0.0
 * @category Requirement
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
class WooCommerceRequirement implements RequirementInterface
{
    /**
     * Method to run all business logic.
     * $params has available options:
     *
     * custom_response
     *
     * @param array $params Requirements params.
     * @since 2.0.0
     * @throws Exception If WooCommerce is not available.
     * @return void
     */
    public static function run(array $params = [])
    {
        require_once \ABSPATH . '/wp-admin/includes/plugin.php';
        $response = 'Woocommerce must be activated to this plugin works.';
        if (isset($params['custom_response'])) {
            $response = $params['custom_response'];
        }
        if (!\is_plugin_active('woocommerce/woocommerce.php')) {
            throw new Exception($response);
        }
    }
}
