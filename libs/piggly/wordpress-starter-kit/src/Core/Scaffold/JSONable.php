<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Scaffold;

use Exception;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Scaffold\Initiable;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\WP;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Helpers\BodyValidator;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Helpers\RequestBodyParser;
/**
 * All main helper methods to manage
 * the  json endpoints, does not
 * matter if you are using the ajax
 * or rest api formats.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Core\Scaffold
 * @version 1.0.9
 * @since 1.0.9
 * @category Scaffold
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
abstract class JSONable extends Initiable
{
    /**
     * Response status code.
     *
     * @var int
     * @since 1.0.9
     */
    protected $_status_code = null;
    /**
     * Handle all endpoints.
     *
     * @since 1.0.9
     * @return void
     */
    public abstract function handlers();
    /**
     * Parse $fields from request body.
     * To see $schema structure see BodyValidator class.
     *
     * There are few options available, are they:
     *
     * 'nonce_name' => null, // Nonce field name at body to validate
     * 'nonce_action' => -1, // An array with allowed values for field
     * 'capability' => null, // A capability required to user
     *
     * @see BodyValidator
     * @param RequestBodyParser $parser
     * @param array $options
     * @since 1.0.9
     * @return array
     * @throws Exception
     */
    protected function parse(RequestBodyParser $parser, array $schema = [], array $options = []) : array
    {
        try {
            $body = $parser->body();
            $parsed = BodyValidator::validate($body, $schema);
            $this->authorizationCheck($body, $options);
            return $parsed;
        } catch (Exception $e) {
            $this->status(422)->error($e);
        }
    }
    /**
     * Check if has authorization.
     *
     * There are few options available, are they:
     *
     * 'nonce_name' => null, // Nonce field name at body to validate
     * 'nonce_action' => -1, // An array with allowed values for field
     * 'capability' => null, // A capability required to user
     *
     * @param array $body
     * @param array $options
     * @since 1.0.12
     * @return void
     */
    protected function authorizationCheck(array $body, array $options = []) : void
    {
        $options = \array_merge(['nonce_name' => 'x_security', 'nonce_action' => static::nonceAction(), 'capability' => static::capability()], $options);
        if (!empty($options['nonce_name'])) {
            if (empty($body[$options['nonce_name']]) || !\wp_verify_nonce($body[$options['nonce_name']], $options['nonce_action'])) {
                $this->status(401)->error(new Exception('O nonce para o envio do formulário é inválido.', 401));
            }
        }
        if (!empty($options['capability'])) {
            if (!\current_user_can($options['capability'])) {
                $this->status(403)->error(new Exception('Acesso não autorizado.', 403));
            }
        }
    }
    /**
     * Set the status code before output error
     * or success response.
     *
     * @param integer $status
     * @since 1.0.9
     * @return JSONable
     */
    protected function status(int $status)
    {
        $this->_status_code = $status;
        return $this;
    }
    /**
     * Convert exception to a json output error.
     * It gets the code and message from exception.
     *
     * @param Exception $e
     * @since 1.0.9
     * @return void
     */
    protected function error(Exception $e, bool $log = \false)
    {
        $this->exit(['code' => $e->getCode(), 'message' => $e->getMessage()], $log);
    }
    /**
     * Do success.
     * It will auto exit php script.
     *
     * @param array $data
     * @since 1.0.9
     * @return void
     */
    protected function success(array $data)
    {
        \wp_send_json_success($data, $this->_status_code);
        exit;
    }
    /**
     * Do error.
     * It will auto exit php script.
     *
     * @param array $err
     * @param boolean $log Should log this error?
     * @since 1.0.9
     * @return void
     */
    private function exit(array $err, bool $log = \false)
    {
        if ($log) {
            $this->debug()->force()->error($err['message'], ['code' => $err['code']]);
        }
        \wp_send_json_error($err, $this->_status_code);
        exit;
    }
    /**
     * Get capability to edit/remove.
     *
     * @since 1.0.12
     * @return string
     */
    public static function capability() : string
    {
        return '';
    }
    /**
     * Get nonce action name.
     *
     * @since 1.0.12
     * @return string
     */
    public static abstract function nonceAction() : string;
}
