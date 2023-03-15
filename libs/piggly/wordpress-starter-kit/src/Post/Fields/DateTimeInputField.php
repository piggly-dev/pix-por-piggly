<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Post\Fields;

use DateTimeImmutable;
/**
 * Base implementation to a number input field.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Fields
 * @version 1.0.7
 * @since 1.0.7
 * @category Fields
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2022 Piggly Lab <dev@piggly.com.br>
 */
class DateTimeInputField extends TextInputField
{
    /**
     * Input type.
     *
     * @since 1.0.9
     * @var string
     */
    protected $type = 'datetime-local';
    /**
     * Class constructor.
     *
     * @since 1.0.9
     */
    public function __construct(array $options)
    {
        parent::__construct($options);
        $this->_options['parse'] = function ($value) {
            if (empty($value)) {
                return null;
            }
            if ($value instanceof DateTimeImmutable) {
                return \esc_attr($value->format('Y-m-d\\TH:i'));
            }
            return \esc_attr((new DateTimeImmutable($value, \wp_timezone()))->format('Y-m-d\\TH:i'));
        };
        $this->_options['transform'] = function ($value) {
            if (empty($value)) {
                return null;
            }
            return new DateTimeImmutable($value, \wp_timezone());
        };
    }
}
