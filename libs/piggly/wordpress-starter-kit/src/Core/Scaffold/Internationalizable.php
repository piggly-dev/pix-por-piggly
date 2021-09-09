<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Scaffold;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Scaffold\Pluggable;
/**
 * Make internationalization easy.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Core\Scaffold
 * @version 1.0.3
 * @since 1.0.3
 * @category Scaffold
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
abstract class Internationalizable extends Pluggable
{
    /**
     * Translates $text and retrieves the singular or plural 
     * form based on the supplied number.
     *
     * @param string $single
     * @param string $plural
     * @param integer $number
     * @since 1.0.3
     * @since 1.0.4 Fixed text domain.
     * @return string
     */
    public function _ntranslate(string $single, string $plural, int $number) : string
    {
        return _n($single, $plural, $number, $this->_plugin->getDomain());
    }
    /**
     * Display the translation of $text.
     *
     * @param string $text
     * @since 1.0.3
     * @return void
     */
    public function _etranslate(string $text)
    {
        _e($text, $this->_plugin->getDomain());
    }
    /**
     * Retrieve the translation of $text.
     *
     * @param string $text
     * @since 1.0.3
     * @return string
     */
    public function __translate(string $text) : string
    {
        return __($text, $this->_plugin->getDomain());
    }
}
