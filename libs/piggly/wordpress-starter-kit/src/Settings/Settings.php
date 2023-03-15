<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Settings;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Connector;
/**
 * Engine for parse settings data and save it.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Settings
 * @version 1.0.12
 * @since 1.0.12
 * @category Settings
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
abstract class Settings
{
    /**
     * All allowed sections for settings.
     *
     * @since 1.0.12
     * @var array
     */
    protected $_sections = [];
    /**
     * Call the right method to prepare section
     * and save body data into it.
     *
     * prepare_{$section}($body) method
     * must return $body prepared to be saved.
     *
     * @param string $section
     * @param array $body
     * @since 1.0.12
     * @return void
     */
    public function save(string $section, array $body)
    {
        $method = 'prepare_' . $section;
        if (\method_exists($this, $method)) {
            $body = $this->{$method}($body);
        }
        /** @var KeyingBucket $settings */
        $settings = $this->data()->getAndCreate($section, new KeyingBucket());
        foreach ($body as $field => $value) {
            $settings->set($field, $value);
        }
        $this->manager()->save();
    }
    /**
     * Access the settings current data.
     *
     * @since 1.0.12
     * @return KeyingBucket
     */
    public function data() : KeyingBucket
    {
        return Connector::settings();
    }
    /**
     * Access the settings manager.
     *
     * @since 1.0.12
     * @return Manager
     */
    public function manager() : Manager
    {
        return Connector::settingsManager();
    }
    /**
     * Get all settings defaults.
     *
     * @since 1.0.12
     * @return KeyingBucket
     */
    public static abstract function defaults() : KeyingBucket;
}
