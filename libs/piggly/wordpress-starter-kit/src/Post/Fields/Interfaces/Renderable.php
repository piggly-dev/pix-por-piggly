<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Post\Fields\Interfaces;

/**
 * The renderable interface implements the render()
 * method to render object to HTML.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Post\Fields\Interfaces
 * @version 1.0.0
 * @since 1.0.0
 * @category Interfaces
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
interface Renderable
{
    /**
     * Render to HTML with value.
     *
     * @param mixed $value
     * @since 1.0.9
     * @return string
     */
    public function render($value) : string;
}
