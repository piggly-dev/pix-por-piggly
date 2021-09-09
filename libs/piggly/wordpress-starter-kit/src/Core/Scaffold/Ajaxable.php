<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Scaffold;

use Exception;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Scaffold\Initiable;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\WP;
/**
 * All main helper methods to manage
 * the ajax endpoints.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Core\Scaffold
 * @version 1.0.5
 * @since 1.0.5
 * @category Scaffold
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
abstract class Ajaxable extends Initiable
{
    /**
     * Ajax status code.
     *
     * @var int
     * @since 1.0.5
     */
    protected $_status_code = null;
    /**
     * Startup method with all actions and
     * filter to run.
     *
     * @since 1.0.5
     * @return void
     */
    public function startup()
    {
        WP::add_action('init', $this, 'handlers');
    }
    /**
     * Handle all endpoints to ajax.
     * 
     * @since 1.0.5
     * @return void
     */
    public abstract function handlers();
    /**
     * Parse $fields from request body.
     * $fields is an array with $key as field name
     * and $value as another array containing:
     * 
     * sanitize: (int) sanitize/validate php filter
     * allowed?: (array) all values allowed to field
     * required: (boolean) if field is required
     * default: (mixed) default value to field
     *
     * @param array $fields
     * @since 1.0.5
     * @return array
     * @throws Exception
     */
    protected function parseBody(array $fields = []) : array
    {
        $parsed = [];
        $errors = [];
        foreach ($fields as $key => $meta) {
            switch ($meta['sanitize']) {
                case \FILTER_VALIDATE_FLOAT:
                    $value = \filter_var(\str_replace(',', '.', $_POST[$key]), \FILTER_VALIDATE_FLOAT, \FILTER_NULL_ON_FAILURE);
                    break;
                default:
                    $value = \filter_input(\INPUT_POST, $key, $meta['sanitize'], \FILTER_NULL_ON_FAILURE);
                    break;
            }
            $value = \filter_input(\INPUT_POST, $key, $meta['sanitize'], \FILTER_NULL_ON_FAILURE);
            if (!$this->isFilled($value) || !\in_array($value, $meta['allowed'] ?? [$value], \true)) {
                $value = $meta['default'];
            }
            if (!$this->isFilled($value) && ($meta['required'] ?? \false)) {
                $errors[] = $key;
                continue;
            }
            $parsed[$key] = $value;
        }
        if ($errors) {
            $this->status(422)->error(['code' => 1, 'message' => \sprintf($this->__translate('Invalid fields: `%s`'), \implode('`, `', $errors))]);
            exit;
        }
        return $parsed;
    }
    /**
     * Return if $var is filled.
     *
     * @param mixed $var
     * @since 1.0.5
     * @return boolean
     */
    protected function isFilled($var)
    {
        return !\is_null($var) && $var !== '';
    }
    /**
     * Validate nonce and check if is doing ajax.
     * It will ignore nonce when debug is set to
     * true.
     * 
     * @see check_ajax_referer
     * @param string $nonce_name
     * @param string $nonce_param Nonce param at query
     * @since 1.0.5
     * @return Ajaxable
     */
    protected function prepare(string $nonce_name, string $nonce_param)
    {
        if (!WP::is_doing_ajax()) {
            exit;
        }
        if (!$this->debug()->isDebugging()) {
            if (!\check_ajax_referer($nonce_name, $nonce_param, \false)) {
                $this->status(401)->error(['code' => 1, 'message' => $this->__translate('Invalid request')]);
                exit;
            }
        }
        return $this;
    }
    /**
     * User need capability before continue.
     *
     * @since 1.0.5
     * @return Ajaxable
     */
    protected function need_capability(string $capability)
    {
        if (!\current_user_can($capability)) {
            $this->status(403)->error(['code' => 2, 'message' => $this->__translate('Not allowed')]);
            exit;
        }
        return $this;
    }
    /**
     * Set the status code before output error
     * or success response.
     *
     * @param integer $status
     * @since 1.0.5
     * @return Ajaxable
     */
    protected function status(int $status)
    {
        $this->_status_code = $status;
        return $this;
    }
    /**
     * Do success.
     * It will auto exit php script.
     *
     * @param array $data
     * @since 1.0.5
     * @return void
     */
    protected function success(array $data)
    {
        \wp_send_json_success($data, $this->_status_code);
        exit;
    }
    /**
     * Convert exception to a json output error.
     * It gets the code and message from exception.
     *
     * @param Exception $e
     * @since 1.0.5
     * @return void
     */
    protected function exceptionError(Exception $e, bool $log = \false)
    {
        $this->error(['code' => $e->getCode(), 'message' => $e->getMessage()], $log);
    }
    /**
     * Do error.
     * It will auto exit php script.
     *
     * @param array $err
     * @param boolean $log Should log this error?
     * @since 1.0.5
     * @return void
     */
    protected function error(array $err, bool $log = \false)
    {
        if ($log) {
            $this->debug()->force()->error($err['message'], ['code' => $err['code']]);
        }
        \wp_send_json_error($err, $this->_status_code);
        exit;
    }
}
