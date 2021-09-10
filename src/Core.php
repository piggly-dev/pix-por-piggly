<?php
namespace Piggly\WooPixGateway;

use DateTime;
use Piggly\WooPixGateway\Core\Admin;
use Piggly\WooPixGateway\Core\Ajax;
use Piggly\WooPixGateway\Core\Discount;
use Piggly\WooPixGateway\Core\Emails;
use Piggly\WooPixGateway\Core\Endpoints;
use Piggly\WooPixGateway\Core\Front;
use Piggly\WooPixGateway\Core\Metabox;
use Piggly\WooPixGateway\Core\Shortcode;
use Piggly\WooPixGateway\Core\Woocommerce;
use Piggly\WooPixGateway\Upgrade\VersionUpgrader;
use Piggly\WooPixGateway\WP\Cron;

use Piggly\WooPixGateway\Vendor\Monolog\Handler\StreamHandler;
use Piggly\WooPixGateway\Vendor\Monolog\Logger;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core as WordpressCore;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Interfaces\Runnable;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\WP;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Plugin;

/**
 * Plugin main core.
 * 
 * @package \Piggly\WooPixGateway
 * @subpackage \Piggly\WooPixGateway
 * @version 2.0.0
 * @since 2.0.0
 * @category Core
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license GPLv3 or later
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
class Core extends WordpressCore
{
	/**
	 * Startup plugin core with an activator,
	 * a deactivator and a upgrader.
	 *
	 * @param Plugin $plugin Master plugin settings.
	 * @param Runnable $activator Run at register_activation_hook()
	 * @param Runnable $deactivator Run at register_deactivation_hook()
	 * @param Runnable $upgrader Manage updates logic.
	 * @since 1.0.0
	 * @since 1.0.3 Plugin as param.
	 * @return void
	 */
	public function __construct(Plugin $plugin, Runnable $activator, Runnable $deactivator, Runnable $upgrader)
	{
		$this->plugin($plugin);

		if ( $this->must_upgrade() )
		{ return; }

		// Runnable classes
		$this->activator($activator);
		$this->deactivator($deactivator);
		$this->upgrader($upgrader);
	}

	/**
	 * Startup method with all actions and
	 * filter to run.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function startup ()
	{
		$this->debug()->changeState($this->settings()->bucket()->get('global')->get('debug', false));

		// Create logger
		$this->logger();

		if ( $this->must_upgrade() )
		{ (new VersionUpgrader())->run(); return; }

		// Admin global menu settings
		$this->initiable(Admin::class);
		$this->initiable(Ajax::class);
		$this->initiable(Discount::class);
		$this->initiable(Cron::class);
		$this->initiable(Emails::class);
		$this->initiable(Endpoints::class);
		$this->initiable(Shortcode::class);

		// After plugins loaded
		WP::add_action('plugins_loaded', $this, 'after_load' );
		// Init
		WP::add_action('init', $this, 'init_all' );
		// Admin init
		WP::add_action('wp_loaded', $this, 'wp_loaded' );
		// WP load
		WP::add_action('wp', $this, 'front_init' );
	}

	/**
	 * Run after plugin loaded...
	 * 
	 * @since 2.0.0
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

		$this->initiable(Metabox::class);
		$this->initiable(Woocommerce::class);
	}

	/**
	 * Run on admin init event.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function wp_loaded ()
	{
		$settings = CoreConnector::settings();

		// Update endpoints
		if ( $settings->get('upgraded_endpoints', false) )
		{
			global $wp_rewrite;
			$wp_rewrite->flush_rules(true);

			$settings->set('upgraded_endpoints', true);
			CoreConnector::settingsManager()->save();
		}
	}
	
	/**
	 * Run on wp event.
	 * 
	 * @since 2.0.0
	 * @return void
	 */
	public function front_init ()
	{ $this->initiable(Front::class); }

	/**
	 * Run on init event.
	 * 
	 * @since 2.0.0
	 * @return void
	 */
	public function init_all ()
	{
		register_post_status( 'wc-pix-receipt', array(
			'label' => CoreConnector::__translate('Comprovante Pix Recebido'),
			'public' => true,
			'show_in_admin_status_list' => true,
			'show_in_admin_all_list' => true,
			'exclude_from_search' => false,
			'label_count' => _n_noop( '<span class="count">(%s)</span> Comprovante Pix Recebido', '<span class="count">(%s)</span> Comprovantes Pix Recebidos', CoreConnector::domain() )
		) );
	}
	
	/**
	 * Display a notice warning Woocommerce is not activated or installed,
	 * and it's required.
	 * 
	 * @since 2.0.0
	 * @return void
	 */
	public function missing_woocommerce () 
	{
		$is_installed = false; 
		if ( !class_exists ( 'woocommerce' ) ) { $is_installed = true; }
		
		?>
		<div class="error">
			<p><?php CoreConnector::_etranslate('O plugin <strong>BDM Gateway</strong> necessita da última versão do Woocommerce para funcionar.'); ?></p>
		
			<?php if ( $is_installed && current_user_can( 'install_plugins' ) ) : ?>
				<p><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=woocommerce/woocommerce.php&plugin_status=active' ), 'activate-plugin_woocommerce/woocommerce.php' ) ); ?>" class="button button-primary"><?php CoreConnector::_etranslate( 'Ativar WooCommerce' ); ?></a></p>
			<?php else : ?>
				<?php if ( current_user_can( 'install_plugins' ) ) : ?>
					<p><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=woocommerce' ), 'install-plugin_woocommerce' ) ); ?>" class="button button-primary"><?php CoreConnector::_etranslate( 'Instalar WooCommerce' ); ?></a></p>
				<?php else : ?>
					<p><a href="http://wordpress.org/plugins/woocommerce/" class="button button-primary"><?php CoreConnector::_etranslate( 'Instalar WooCommerce' ); ?></a></p>
				<?php endif; ?>
			<?php endif; ?>
		</div> 
		<?php
	}

	/**
	 * Prepare and create logger.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	protected function logger ()
	{
		$path = ABSPATH.'wp-content/pix-por-piggly/';

		if ( !\is_dir($path) )
		{ wp_mkdir_p($path); }

		if ( !\file_exists($path.'.htaccess') )
		{ \file_put_contents($path.'.htaccess', 'Options -Indexes'); }

		$now = (new DateTime('now', wp_timezone()))->format('Y-m-d');

		$hash = \sprintf(
			'pix-por-piggly-%s-%s.log', 
			$now,
			\md5($now.\get_option('pgly_wc_piggly_pix_key', 'null'))
		);

		if ( !\file_exists($path.$hash) ) 
		{ touch($path.$hash); }

		// create a log channel
		$log = new Logger('wc-piggly-pix');
		$log->pushHandler(new StreamHandler($path.$hash, Logger::DEBUG));

		$this->debug()->setLogger($log);
	}

	/**
	 * Return if plugin must upgrade to new version.
	 *
	 * @since 2.0.0
	 * @return boolean
	 */
	private function must_upgrade ()
	{
		$version = \get_option('wc_piggly_pix_version', '0');

		// It may be not installed before
		if ( $version === '0' )
		{ return false; }

		return \version_compare($version, '2.0.0', '<' );
	}
}