<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Post\Fields;

/**
 * Base implementation to a title.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Fields
 * @version 1.0.12
 * @since 1.0.12
 * @category Fields
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2022 Piggly Lab <dev@piggly.com.br>
 */
class TitleComponent extends HTMLField
{
    /**
     * Class constructor.
     *
     * @param array $options
     * @param array<array<HTMLField>> $rows
     * @since 1.0.12
     */
    public function __construct(array $options)
    {
        parent::__construct(\array_merge(['content' => null, 'level' => 1, 'size' => 1], $options));
    }
    /**
     * Render to HTML.
     *
     * @since 1.0.12
     * @return string
     */
    public function render($values = []) : string
    {
        return "<h{$this->_options['level']}  class=\"pgly-wps--title pgly-wps-is-{$this->_options['size']}\">{$this->_options['content']}</h{$this->_options['level']}>";
    }
}
