<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Helpers;

use Piggly\WooPixGateway\Vendor\Psr\Log\LoggerInterface;
/**
 * The Debugger class manages the
 * LoggerInterface in a smart and simple way.
 * Preventing you to care about logging.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Helpers
 * @version 2.0.0
 * @since 2.0.0
 * @category Helpers
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2023 Piggly Lab <dev@piggly.com.br>
 */
class Debugger
{
    /**
     * Main application name.
     *
     * @var string
     * @since 2.0.0
     */
    protected $_name;
    /**
     * Main application logger.
     *
     * @var LoggerInterface
     * @since 2.0.0
     */
    protected $_logger;
    /**
     * Is debugging?
     *
     * @var bool
     * @since 2.0.0
     */
    protected $_debug = \false;
    /**
     * Force log to the next call.
     *
     * @var boolean
     * @since 2.0.0
     */
    protected $_force = \false;
    /**
     * Construct debugger.
     *
     * @param string $name Main application name.
     * @since 2.0.0
     * @return void
     */
    public function __construct(string $name)
    {
        $this->_name = $name;
    }
    /**
     * Get logger or null.
     *
     * @since 2.0.0
     * @return LoggerInterface|null
     */
    public function logger() : ?LoggerInterface
    {
        return $this->_logger ?? null;
    }
    /**
     * Change current debug logger.
     *
     * @param LoggerInterface $logger Logger instance.
     * @since 2.0.0
     * @return self
     */
    public function changeLogger(LoggerInterface $logger)
    {
        $this->_logger = $logger;
        return $this;
    }
    /**
     * Change debug state.
     *
     * @param bool $debug Is debugging.
     * @since 2.0.0
     * @return self
     */
    public function changeState(bool $debug = \true)
    {
        $this->_debug = $debug;
        return $this;
    }
    /**
     * System is unusable.
     *
     * You can call it even log is not set and
     * even it is not at debug mode. The log will
     * be recorded only when logger is set.
     *
     * @param string $message Message to log.
     * @param mixed[] $context Context to log.
     * @since 2.0.0
     * @return void
     */
    public function emergency(string $message, array $context = [])
    {
        $this->callLogger('emergency', $message, $context);
    }
    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database
     * unavailable, etc. This should trigger
     * the SMS alerts and wake you up.
     *
     * You can call it even log is not set and
     * even it is not at debug mode. The log will
     * be recorded only when logger is set.
     *
     * @param string $message Message to log.
     * @param mixed[] $context Context to log.
     * @since 2.0.0
     * @return void
     */
    public function alert(string $message, array $context = [])
    {
        $this->callLogger('alert', $message, $context);
    }
    /**
     * Critical conditions.
     *
     * Example: Application component unavailable,
     * unexpected exception.
     *
     * You can call it even log is not set and
     * even it is not at debug mode. The log will
     * be recorded only when logger is set.
     *
     * @param string $message Message to log.
     * @param mixed[] $context Context to log.
     * @since 2.0.0
     * @return void
     */
    public function critical(string $message, array $context = [])
    {
        $this->callLogger('critical', $message, $context);
    }
    /**
     * Runtime errors that do not require immediate
     * action but should typically be logged and monitored.
     *
     * You can call it even log is not set and
     * even it is not at debug mode. The log will
     * be recorded only when logger is set.
     *
     * @param string $message Message to log.
     * @param mixed[] $context Context to log.
     * @since 2.0.0
     * @return void
     */
    public function error(string $message, array $context = [])
    {
        $this->callLogger('error', $message, $context);
    }
    /**
     * Exceptional occurrences that are not errors.
     *
     * You can call it even log is not set and
     * even it is not at debug mode. The log will
     * be recorded only when logger is set.
     *
     * @param string $message Message to log.
     * @param mixed[] $context Context to log.
     * @since 2.0.0
     * @return void
     */
    public function warning(string $message, array $context = [])
    {
        $this->callLogger('warning', $message, $context);
    }
    /**
     * Interesting events.
     *
     * You can call it even log is not set and
     * even it is not at debug mode. The log will
     * be recorded only when logger is set.
     *
     * @param string $message Message to log.
     * @param mixed[] $context Context to log.
     * @since 2.0.0
     * @return void
     */
    public function info(string $message, array $context = [])
    {
        $this->callLogger('info', $message, $context);
    }
    /**
     * Detailed debug information.
     *
     * You can call it even log is not set and
     * even it is not at debug mode. The log will
     * be recorded only when it is debugging and
     * logger is set.
     *
     * @param string $message Message to log.
     * @param mixed[] $context Context to log.
     * @since 2.0.0
     * @return void
     */
    public function debug(string $message, array $context = [])
    {
        if ($this->_debug === \false) {
            return;
        }
        $this->callLogger('debug', $message, $context);
    }
    /**
     * Normal but significant events.
     *
     * You can call it even log is not set and
     * even it is not at debug mode. The log will
     * be recorded only when logger is set.
     *
     * @param string $message Message to log.
     * @param mixed[] $context Context to log.
     * @since 2.0.0
     * @return void
     */
    public function notice(string $message, array $context = [])
    {
        $this->callLogger('notice', $message, $context);
    }
    /**
     * Prepare logger before call it, validating
     * if it is debugging and logger is set.
     *
     * @param string $level Level to log.
     * @param string $message Message to log.
     * @param mixed[] $context Context to log.
     * @since 2.0.0
     * @return void
     */
    protected function callLogger(string $level, string $message, array $context = [])
    {
        $logger = $this->logger();
        if ($logger === null) {
            return;
        }
        $context = \array_merge(array('source' => $this->_name), $context);
        $logger->{$level}($message, $context);
    }
    /**
     * Get the debug state.
     *
     * @since 2.0.0
     * @return bool
     */
    public function isDebugging() : bool
    {
        return $this->_debug;
    }
}
