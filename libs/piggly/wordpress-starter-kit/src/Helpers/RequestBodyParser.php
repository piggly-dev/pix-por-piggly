<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Helpers;

use Exception;
/**
 * Parse a request body with json.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Helpers
 * @version 1.0.9
 * @since 1.0.9
 * @category Helper
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2022 Piggly Lab <dev@piggly.com.br>
 */
class RequestBodyParser
{
    /**
     * Request method.
     *
     * @since 1.08
     * @var string
     */
    protected $_method;
    /**
     * Raw body.
     *
     * @since 1.08
     * @var string
     */
    protected $_raw;
    /**
     * Parsed body.
     *
     * @since 1.08
     * @var array
     */
    protected $_body;
    /**
     * Class constructor.
     *
     * @since 1.0.9
     * @return void
     */
    public function __construct()
    {
        $this->_method = \strtoupper(\htmlentities($_SERVER['REQUEST_METHOD']));
        $this->_raw = \file_get_contents('php://input');
        $this->_body = $this->resolve();
    }
    /**
     * Return if request method is POST.
     *
     * @since 1.0.9
     * @return boolean
     */
    public function isPOST() : bool
    {
        return $this->_method === 'POST';
    }
    /**
     * Return if request method is GET.
     *
     * @since 1.0.9
     * @return boolean
     */
    public function isGET() : bool
    {
        return $this->_method === 'GET';
    }
    /**
     * Get request method.
     *
     * @since 1.0.9
     * @return string
     */
    public function method() : string
    {
        return $this->_method;
    }
    /**
     * Get raw request body.
     *
     * @since 1.0.9
     * @return string
     */
    public function raw() : string
    {
        return $this->_raw;
    }
    /**
     * Get parsed request body.
     *
     * @since 1.0.9
     * @return array
     */
    public function body() : array
    {
        return $this->_body;
    }
    /**
     * Resolve the content type requested
     * and return an array with all data
     * parsed.
     *
     * @since 1.0.9
     * @return array
     */
    public function resolve() : array
    {
        $content_type = $_SERVER["CONTENT_TYPE"] ?? 'application/x-www-form-urlencoded';
        switch ($content_type) {
            case 'application/x-www-form-urlencoded':
                return $this->urlencode($this->_raw);
            case 'application/json':
                return $this->json($this->_raw);
            default:
                return $this->urlencode($this->_raw);
        }
    }
    /**
     * Parse raw string input to an array
     * based in application/json
     * content type.
     *
     * @param string $raw
     * @since 1.0.9
     * @return array
     */
    public function json(string $raw) : array
    {
        $data = \json_decode($raw, \true);
        if (\json_last_error() !== \JSON_ERROR_NONE) {
            throw new Exception('Cannot parse request body to JSON...');
        }
        return $data ?: [];
    }
    /**
     * Parse raw string input to an array
     * based in application/x-www-form-urlencoded
     * content type.
     *
     * @param string $raw
     * @since 1.0.9
     * @return array
     */
    public function urlencode(string $raw) : array
    {
        $data = [];
        \parse_str($raw, $data);
        return $data ?: [];
    }
}
