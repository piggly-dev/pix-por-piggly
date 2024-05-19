<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Post\Fields;

/**
 * Base implementation to a textarea input field.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Fields
 * @version 1.0.9
 * @since 1.0.9
 * @category Fields
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2022 Piggly Lab <dev@piggly.com.br>
 */
class TextAreaNoEscapeInputField extends TextAreaInputField
{
    /**
     * Class constructor.
     *
     * @since 1.0.9
     */
    public function __construct(array $options)
    {
        parent::__construct($options);
        $this->_options['parse'] = function ($value) {
            return $value;
        };
    }
}
