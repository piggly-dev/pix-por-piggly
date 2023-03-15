<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Post\Fields;

/**
 * Base implementation to a notification.
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
class NoticeComponent extends HTMLField
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
        parent::__construct(\array_merge(['content' => null, 'color' => 'primary'], $options));
    }
    /**
     * Render to HTML.
     *
     * @since 1.0.12
     * @return string
     */
    public function render($values = []) : string
    {
        return "<p class=\"pgly-wps--notification pgly-wps-is-{$this->_options['color']}\">{$this->_options['content']}</p>";
    }
}
