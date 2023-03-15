<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Runners;

use Exception;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Interfaces\Runnable;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Repository\RepositoryInterface;
/**
 * Activate plugin.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Core\Runners
 * @version 1.0.7
 * @since 1.0.7
 * @category Runners
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2022 Piggly Lab <dev@piggly.com.br>
 */
class CreateTableRunner implements Runnable
{
    /**
     * Repository interface.
     *
     * @since 1.0.7
     * @var RepositoryInterface
     */
    protected RepositoryInterface $database;
    /**
     * Class constructor.
     *
     * @since 1.0.8
     */
    public function __construct(RepositoryInterface $database)
    {
        $this->database = $database;
    }
    /**
     * Method to run all business logic.
     *
     * @since 1.0.7
     * @return void
     */
    public function run()
    {
        if ($this->database::installedVersion() !== '0' || $this->database::tableExists()) {
            return;
        }
        if (!\function_exists('dbDelta')) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }
        try {
            $sql = $this->database::createTable();
            @dbDelta($sql);
            if (!$this->database::tableExists()) {
                @\trigger_error('Não foi possível criar o banco de dados...');
                return;
            }
            $this->database::updateVersion();
        } catch (Exception $e) {
            @\trigger_error('Não foi possível criar o banco de dados...');
            return;
        }
    }
}
