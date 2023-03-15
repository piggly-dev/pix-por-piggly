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
class ExtendedMultipleMediaInputField extends InputField
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
            return \array_map(function ($v) {
                if (empty($value)) {
                    return null;
                }
                return \intval($v);
            }, $value);
        };
    }
    /**
     * Render to HTML with value.
     *
     * @param mixed $value
     * @param mixed $src
     * @since 1.0.9
     * @return string
     */
    public function render($value = '', $src = '', array $labels = []) : string
    {
        $this->changeValue($value);
        $lbls = \array_merge(['clean' => 'Clean All', 'select' => 'Add More'], $labels);
        $html = "<div class=\"pgly-wps--column pgly-wps-col--{$this->columnSize()}\">";
        $html .= "<div class=\"pgly-wps--field pgly-wps--media-wrapper {$this->getCssForm()}--input {$this->getCssForm()}--multiple-media\" data-name=\"{$this->name()}\">";
        if (!empty($this->label())) {
            $html .= "<label class=\"pgly-wps--label\">{$this->label()}</label>";
        }
        $html .= '<div class="pgly-wps--images"></div>';
        if ($this->isRequired()) {
            $html .= '<span class="pgly-wps--badge pgly-wps-is-danger" style="margin-top: 6px; margin-right: 6px">Obrigatório</span>';
        }
        $html .= '<span class="pgly-wps--message"></span>';
        if (!empty($this->description())) {
            $html .= "<p class=\"pgly-wps--description\">{$this->description()}</p>";
        }
        $html .= "<div class=\"pgly-wps--action-bar\">\n\t\t\t<button class=\"pgly-wps--button pgly-wps-is-compact pgly-wps-is-primary pgly-wps--select\">{$lbls['select']}</button>\n\t\t\t<button class=\"pgly-wps--button pgly-wps-is-compact pgly-wps-is-danger pgly-wps--clean\">{$lbls['clean']}</button>\n\t\t</div>";
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }
}
