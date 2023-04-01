<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Database;

use Exception;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Interfaces\RunnableInterface;
/**
 * Table runner.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Database
 * @version 2.0.0
 * @since 2.0.0
 * @category Database
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license piggly
 * @copyright 2023 Piggly Lab <dev@piggly.com.br>
 */
class CreateTableRunner implements RunnableInterface
{
    /**
     * Repository interface.
     *
     * @since 2.0.0
     * @var AbstractTableSchema
     */
    protected AbstractTableSchema $database;
    /**
     * Class constructor.
     *
     * @param AbstractTableSchema $database Database schema.
     * @since 2.0.0
     */
    public function __construct(AbstractTableSchema $database)
    {
        $this->database = $database;
    }
    /**
     * Method to run all business logic.
     *
     * @since 2.0.0
     * @throws Exception If table not exists after try to create.
     * @return void
     */
    public function run()
    {
        if ($this->database::installedVersion() !== '0' || $this->database::tableExists()) {
            return;
        }
        if (!\function_exists('dbDelta')) {
            require_once \ABSPATH . 'wp-admin/includes/upgrade.php';
        }
        dbDelta($this->database::createTable());
        if (!$this->database::tableExists()) {
            throw new Exception(\sprintf('Table %s not exists', $this->database::tableName()));
        }
        $this->database::updateVersion();
    }
}
