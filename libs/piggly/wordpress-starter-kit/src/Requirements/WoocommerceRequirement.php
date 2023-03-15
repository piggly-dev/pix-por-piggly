<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Requirements;

use Exception;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Requirements\Interfaces\RequirementInterface;
/**
 * The runner validates if Woocommerce is active.
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
class WoocommerceRequirement implements RequirementInterface
{
    /**
     * Method to run all business logic.
     * $params has available options:
     *
     * custom_response
     *
     * @param $params
     * @since 1.0.12
     * @return void
     */
    public static function run(array $params = [])
    {
        require_once \ABSPATH . '/wp-admin/includes/plugin.php';
        if (!is_plugin_active('woocommerce/woocommerce.php')) {
            throw new Exception($params['custom_response'] ?? 'Woocommerce must be activated to this plugin works.');
        }
    }
}
