<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Post\Interfaces;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Entities\AbstractEntity;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Post\Fields\Form;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Repository\WPRepository;
/**
 * Interface for post types.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Post\Interfaces
 * @version 1.0.12
 * @since 1.0.12
 * @category Interface
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2022 Piggly Lab <dev@piggly.com.br>
 */
interface PostTypeInterface
{
    /**
     * Get the HTML form.
     *
     * @since 1.0.12
     * @return Form
     */
    public function form() : Form;
    /**
     * Get custom post type icon.
     *
     * @since 1.0.12
     * @return string
     */
    public static function getIcon() : string;
    /**
     * Get custom post type slug.
     *
     * @since 1.0.12
     * @return string
     */
    public static function getSlug() : string;
    /**
     * Get custom post type singular name.
     *
     * @since 1.0.12
     * @return string
     */
    public static function singularName() : string;
    /**
     * Get custom post type plural name.
     *
     * @since 1.0.12
     * @return string
     */
    public static function pluralName() : string;
    /**
     * Get custom post type field prefix.
     *
     * @since 1.0.12
     * @return string
     */
    public static function fieldPrefix() : string;
    /**
     * Get the current repository.
     *
     * @param array $options
     * @since 1.0.12
     * @return AbstractEntity
     */
    public static function entityModel(array $options = []);
    /**
     * Get the current repository.
     *
     * @since 1.0.12
     * @return WPRepository
     */
    public static function getRepo();
    /**
     * Get the current table.
     *
     * @since 1.0.12
     * @return RecordTable
     */
    public static function getTable();
}
