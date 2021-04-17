<?php
namespace Piggly\WC\Pix\WP;

use WC_Logger;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Create a log when debugging plugin.
 *
 * @since      1.2.0 
 * @package    Piggly\WC\Pix
 * @subpackage Piggly\WC\Pix\WP
 * @author     Caique <caique@piggly.com.br>
 * @author     Piggly Lab <dev@piggly.com.br>
 */
class Debug
{
	/**
	 * Logger
	 * @var WC_Logger
	 * @since 1.2.0
	 */
	protected $logger;

	/**
	 * Is debugging?
	 * @var bool
	 * @since 1.2.0
	 */
	protected $debug;

	/**
	 * Not allowed to construct as public mode.
	 * 
	 * @since 1.2.0
	 * @return void
	 */
	protected function __construct ()
	{ $this->debug = false; } 

	/**
	 * Returns a singleton instance of this class.
	 * 
	 * @since 1.2.0
	 * @return self
	 */
	protected static function getInstance ()
	{
		// Static instance
		static $instance;

		// If is null, creates a new instance
		if ( is_null ( $instance ) )
		{ $instance = new self(); }

		// Returns the static instance
		return $instance;
	}

	/**
	 * Return if is debugging.
	 * 
	 * @since 1.2.0
	 * @return bool
	 */
	public static function debugging () : bool
	{ return self::getInstance()->isDebugging(); }

	/**
	 * Get debug logger if is debugging.
	 * 
	 * @since 1.2.0
	 * @return WC_Logger|null
	 */
	public static function logger () : ? WC_Logger
	{ return self::getInstance()->getLogger(); }

	/**
	 * Change debug state.
	 * 
	 * @param bool $debug
	 * @since 1.2.0
	 * @return void
	 */
	public static function changeState ( bool $debug = true )
	{ return self::getInstance()->setDebug($debug); }

	/**
	 * Creates an info message at log, only if is debugging.
	 * 
	 * @param string $message
	 * @param mixed[] $context
	 * @since 1.2.0
	 * @return void
	 */
	public static function info ( string $message, array $context = [] )
	{
		$instance = self::getInstance();
		$logger = $instance->getLogger();
		
		$context = array_merge(array( 'source' => \WC_PIGGLY_PIX_PLUGIN_NAME ), $context);
		if ( !is_null($logger) ) $logger->info($message, $context);
	}

	/**
	 * Creates an error message at log, only if is debugging.
	 * 
	 * @param string $message
	 * @param mixed[] $context
	 * @since 1.2.0
	 * @return void
	 */
	public static function error ( string $message, array $context = [] )
	{
		$instance = self::getInstance();
		$logger = $instance->getLogger();
		
		$context = array_merge(array( 'source' => \WC_PIGGLY_PIX_PLUGIN_NAME ), $context);
		if ( !is_null($logger) ) $logger->error($message, $context);
	}

	/**
	 * Creates an error message at log, only if is debugging.
	 * 
	 * @param string $message
	 * @param mixed[] $context
	 * @since 1.2.0
	 * @return void
	 */
	public static function debug ( string $message, array $context = [] )
	{
		$instance = self::getInstance();
		$logger = $instance->getLogger();
		
		$context = array_merge(array( 'source' => \WC_PIGGLY_PIX_PLUGIN_NAME ), $context);
		if ( !is_null($logger) ) $logger->debug($message, $context);
	}

	/**
	 * Get logger if is debugging.
	 * 
	 * @since 1.2.0
	 * @return WC_Logger|null
	 */
	public function getLogger () 
	{
		if ( $this->debug ) 
		{
			if ( empty( $this->logger ) )
			{ $this->logger = \wc_get_logger(); }
			
			return $this->logger;
		}

		return null;
	}

	/**
	 * Set new debug state.
	 * 
	 * @param bool $debug
	 * @since 1.2.0
	 * @return self
	 */
	public function setDebug ( bool $debug = true )
	{ $this->debug = $debug; return $this; }

	/**
	 * Get the debug state.
	 * 
	 * @since 1.2.0
	 * @return bool
	 */
	public function isDebugging () : bool
	{ return $this->debug || false; }
}