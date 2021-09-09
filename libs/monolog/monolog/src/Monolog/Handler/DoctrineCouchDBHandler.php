<?php

declare (strict_types=1);
/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Piggly\WooPixGateway\Vendor\Monolog\Handler;

use Piggly\WooPixGateway\Vendor\Monolog\Logger;
use Piggly\WooPixGateway\Vendor\Monolog\Formatter\NormalizerFormatter;
use Piggly\WooPixGateway\Vendor\Monolog\Formatter\FormatterInterface;
use Piggly\WooPixGateway\Vendor\Doctrine\CouchDB\CouchDBClient;
/**
 * CouchDB handler for Doctrine CouchDB ODM
 *
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class DoctrineCouchDBHandler extends AbstractProcessingHandler
{
    /** @var CouchDBClient */
    private $client;
    public function __construct(CouchDBClient $client, $level = Logger::DEBUG, bool $bubble = \true)
    {
        $this->client = $client;
        parent::__construct($level, $bubble);
    }
    /**
     * {@inheritDoc}
     */
    protected function write(array $record) : void
    {
        $this->client->postDocument($record['formatted']);
    }
    protected function getDefaultFormatter() : FormatterInterface
    {
        return new NormalizerFormatter();
    }
}
