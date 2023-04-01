<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Helpers;

use Exception;
use Piggly\WooPixGateway\Vendor\Psr\Log\LoggerInterface;
/**
 * Manages JSON response for WordPress.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Helpers
 * @version 2.0.0
 * @since 2.0.0
 * @category Helpers
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2023 Piggly Lab <dev@piggly.com.br>
 */
class JsonResponse
{
    /**
     * Check if has authorization.
     *
     * There are few options available, are they:
     *
     * 'nonce_name' => null, // Nonce field name at body to validate
     * 'nonce_action' => -1, // An array with allowed values for field
     * 'capability' => null, // A capability required to user
     *
     * @param array $body Request body.
     * @param array $options Options to check.
     * @since 2.0.0
     * @return void
     */
    public static function authorizationCheck(array $body, array $options = []) : void
    {
        if (!empty($options['nonce_name'])) {
            if (empty($body[$options['nonce_name']]) || !\wp_verify_nonce($body[$options['nonce_name']], $options['nonce_action'])) {
                static::exitOnError(new Exception('O nonce para o envio do formulário é inválido.', 401), 401);
            }
        }
        if (!empty($options['capability'])) {
            if (!\current_user_can($options['capability'])) {
                static::exitOnError(new Exception('Acesso não autorizado.', 403), 403);
            }
        }
    }
    /**
     * Convert exception to a json output error.
     * It gets the code and message from exception.
     *
     * @param Exception $e Exception error.
     * @param integer $status HTTP status code.
     * @since 2.0.0
     * @return void
     */
    public static function exitOnError(Exception $e, int $status = 500)
    {
        \wp_send_json_error(['success' => \false, 'code' => $e->getCode(), 'message' => $e->getMessage()], $status);
        exit;
    }
    /**
     * Do success.
     * It will auto exit php script.
     *
     * @param array $data Data to output.
     * @param integer $status HTTP status code.
     * @since 2.0.0
     * @return void
     */
    public static function exitOnSuccess(array $data, int $status = 200)
    {
        \wp_send_json_success($data, $status);
        exit;
    }
}
