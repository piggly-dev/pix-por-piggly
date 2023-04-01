<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Interfaces;

use stdClass;
/**
 * Interface for a recordable model.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Interfaces
 * @version 2.0.0
 * @since 2.0.0
 * @category Interfaces
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license piggly
 * @copyright 2023 Piggly Lab <dev@piggly.com.br>
 */
interface RecordableModelInterface
{
    /**
     * Get model primary key value.
     *
     * When null, should means that it is not preloaded
     * from the database.
     *
     * @since 2.0.0
     * @return mixed
     */
    public function primaryKey();
    /**
     * Get the primary key name.
     *
     * @since 2.0.0
     * @return string
     */
    public function primaryKeyName() : string;
    /**
     * Mark record as preloaded from database associating its primary key.
     * Is expected that model id is available on database.
     *
     * @param mixed $primary Model primary key value.
     * @since 2.0.0
     * @return RecordableModelInterface
     */
    public function preload($primary);
    /**
     * Get if it is preloaded from database.
     *
     * @since 2.0.0
     * @return boolean
     */
    public function isPreloaded() : bool;
    /**
     * Create record object for database from model object.
     *
     * @since 2.0.0
     * @return stdClass
     */
    public function toRecord() : stdClass;
    /**
     * Create model object from record object.
     *
     * @param stdClass $record Record object from database.
     * @since 2.0.0
     * @return self
     */
    public static function fromRecord(stdClass $record);
}
