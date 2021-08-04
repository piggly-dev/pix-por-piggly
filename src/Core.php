<?php
namespace Piggly\WooPixGateway;

use DateTime;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Piggly\BdmApiClient\BdmApi;
use Piggly\Wordpress\Core as WordpressCore;
use Piggly\Wordpress\Core\WP;
use Piggly\WooPixGateway\Core\Admin;
use Piggly\WooPixGateway\Core\Ajax;
use Piggly\WooPixGateway\Core\Emails;
use Piggly\WooPixGateway\Core\Front;
use Piggly\WooPixGateway\Core\Metabox;
use Piggly\WooPixGateway\WP\Cron;

if ( ! defined( 'ABSPATH' ) ) { exit; }

class Core extends WordpressCore
{
	/**
	 * Startup method with all actions and
	 * filter to run.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function startup ()
	{
		// Create logger
		$this->logger();

		// Admin global menu settings
		$this->initiable(Admin::class);
		$this->initiable(Cron::class);
		$this->initiable(Emails::class);
		$this->initiable(Front::class);
		$this->initiable(Ajax::class);

		// After plugins loaded
		WP::add_action('plugins_loaded', $this, 'after_load' );
	}

	/**
	 * Run after plugin loaded...
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public function after_load ()
	{
		// Display an admin notice when WooCommerce is not enabled.
		if ( !class_exists('WC_Order') )
		{
			WP::add_action('admin_notices', $this, 'missing_woocommerce');
			return;
		}
		
		// Allow float value to quantity
		remove_filter('woocommerce_stock_amount', 'intval');
		add_filter('woocommerce_stock_amount', 'floatval');

		$this->initiable(Metabox::class);
	}
	
	/**
	 * Display a notice warning Woocommerce is not activated or installed,
	 * and it's required.
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public function missing_woocommerce () 
	{
		$is_installed = false; 
		if ( !class_exists ( 'woocommerce' ) ) { $is_installed = true; }
		
		?>
		<div class="error">
			<p><?php $this->_etranslate('O plugin <strong>BDM Commerce</strong> necessita da última versão do Woocommerce para funcionar.'); ?></p>
		
			<?php if ( $is_installed && current_user_can( 'install_plugins' ) ) : ?>
				<p><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=woocommerce/woocommerce.php&plugin_status=active' ), 'activate-plugin_woocommerce/woocommerce.php' ) ); ?>" class="button button-primary"><?php $this->_etranslate( 'Ativar WooCommerce' ); ?></a></p>
			<?php else : ?>
				<?php if ( current_user_can( 'install_plugins' ) ) : ?>
					<p><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=woocommerce' ), 'install-plugin_woocommerce' ) ); ?>" class="button button-primary"><?php $this->_etranslate( 'Instalar WooCommerce' ); ?></a></p>
				<?php else : ?>
					<p><a href="http://wordpress.org/plugins/woocommerce/" class="button button-primary"><?php $this->_etranslate( 'Instalar WooCommerce' ); ?></a></p>
				<?php endif; ?>
			<?php endif; ?>
		</div> 
		<?php
	}

	/**
	 * Display a notice warning PHP version is obsolete.
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public function insecure_php ()
	{
		?>
		<div class="notice notice-error">
			<p>
				<?php $this->_etranslate( 'Seu website está utilizando uma <strong>versão não recomendada</strong> do PHP que não é mais suportada. Por favor, comunique seu servidor de hospedagem para realizar a atualização ou faça a migração de servidor.' );?>
				<br>
				<br>
				<?php $this->_etranslate( 'Para não gerar problemas de incompatibilidade, o plugin <strong>BDM Commerce</strong> não será ativado' );?>
			</p>
		</div>
		<?php

		// In case this is on plugin activation.
		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );
	}

	/**
	 * Prepare and create logger.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function logger ()
	{
		$path = ABSPATH.'wp-content/bdm-commerce/';

		if ( !\is_dir($path) )
		{ wp_mkdir_p($path); }

		if ( !\file_exists($path.'.htaccess') )
		{ \file_put_contents($path.'.htaccess', 'Options -Indexes'); }

		$now = (new DateTime('now', wp_timezone()))->format('Y-m-d');

		$hash = \sprintf(
			'bdm-commerce-%s-%s.log', 
			$now,
			\md5($now.\get_option('wpgly_bdm_commerce_key', 'null'))
		);

		if ( !\file_exists($path.$hash) ) 
		{ touch($path.$hash); }

		// create a log channel
		$log = new Logger('bdm-commerce');
		$log->pushHandler(new StreamHandler($path.$hash, Logger::DEBUG));

		$this->debug()->setLogger($log);

		/** @var BdmApi $bdmApi */
		$bdmApi = $this->_plugin->bucket()->get('api');

		if ( $this->settings()->bucket()->get('log_api', false) )
		{ $bdmApi->setLog($log); }

		$bdmApi->setDebug($this->debug()->isDebugging());
	}
}