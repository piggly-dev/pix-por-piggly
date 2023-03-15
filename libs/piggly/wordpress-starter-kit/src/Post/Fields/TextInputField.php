<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Post\Fields;

/**
 * Base implementation to a text input field.
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
class TextInputField extends InputField
{
    /**
     * Input type.
     *
     * @since 1.0.9
     * @var string
     */
    protected $type = 'text';
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
            return \esc_attr($value);
        };
    }
    /**
     * Render to HTML with value.
     *
     * @param mixed $value
     * @param mixed $default
     * @since 1.0.9
     * @return string
     */
    public function render($value = '') : string
    {
        $this->changeValue($value);
        $id = $this->name(\true);
        $vl = $this->value();
        $html = "<div class=\"pgly-wps--column pgly-wps-col--{$this->columnSize()}\">";
        $html .= "<div class=\"pgly-wps--field {$this->getCssForm()}--input {$this->getCssForm()}--text\" data-name=\"{$this->name()}\">";
        if (!empty($this->label())) {
            $html .= "<label class=\"pgly-wps--label\">{$this->label()}</label>";
        }
        $html .= "<input id=\"{$id}\" name=\"{$id}\" placeholder=\"{$this->placeholder()}\" type=\"{$this->type}\" value=\"{$vl}\">";
        if ($this->isRequired()) {
            $html .= '<span class="pgly-wps--badge pgly-wps-is-danger" style="margin-top: 6px; margin-right: 6px">Obrigatório</span>';
        }
        $html .= '<span class="pgly-wps--message"></span>';
        if (!empty($this->description())) {
            $html .= "<p class=\"pgly-wps--description\">{$this->description()}</p>";
        }
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }
}
