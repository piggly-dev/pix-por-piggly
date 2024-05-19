<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Post\Fields;

/**
 * Base implementation to a text input field.
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
class ExtendedSelectInputField extends InputField
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
     * @param mixed $lbl
     * @since 1.0.9
     * @return string
     */
    public function render($value = '', $lbl = '') : string
    {
        $this->changeValue($value);
        $id = $this->name(\true);
        $html = "<div class=\"pgly-wps--column pgly-wps-col--{$this->columnSize()}\">";
        $html .= "<div id=\"{$id}\" class=\"pgly-wps--field {$this->getCssForm()}--input {$this->getCssForm()}--eselect\" data-name=\"{$this->name()}\">";
        if (!empty($this->label())) {
            $html .= "<label class=\"pgly-wps--label\">{$this->label()}</label>";
        }
        $html .= "<div class=\"pgly-wps--select\">\n\t\t\t<div class=\"selected empty\" data-value=\"{$this->value()}\" data-label=\"{$lbl}\">\n\t\t\t\t<span>{$this->placeholder()}</span>\n\t\t\t\t<svg class=\"pgly-wps--arrow\" height=\"48\" viewBox=\"0 0 48 48\" width=\"48\"\n\t\t\t\t\txmlns=\"http://www.w3.org/2000/svg\">\n\t\t\t\t\t<path d=\"M14.83 16.42l9.17 9.17 9.17-9.17 2.83 2.83-12 12-12-12z\"></path>\n\t\t\t\t\t<path d=\"M0-.75h48v48h-48z\" fill=\"none\"></path>\n\t\t\t\t</svg>\n\t\t\t\t<svg class=\"pgly-wps--spinner pgly-wps-is-primary\" viewBox=\"0 0 50 50\">\n\t\t\t\t\t<circle class=\"path\" cx=\"25\" cy=\"25\" r=\"20\" fill=\"none\" stroke-width=\"5\"></circle>\n\t\t\t\t</svg>\n\t\t\t</div>\n\t\t\t<div class=\"items hidden\">\n\t\t\t\t<div class=\"placeholder clickable\">{$this->placeholder()}</div>\n\t\t\t\t<div class=\"container\"></div>\n\t\t\t</div>\n\t\t</div>";
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
