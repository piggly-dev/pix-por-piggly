<?php
namespace Piggly\WooPixGateway\WP;

use Exception;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Interfaces\Runnable;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Scaffold\Internationalizable;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\WP;

/**
 * Upgrade plugin.
 * 
 * @package \Piggly\WooPixGateway
 * @subpackage \Piggly\WooPixGateway\WP
 * @version 2.0.0
 * @since 2.0.0
 * @category WP
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license GPLv3 or later
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
class Upgrader extends Internationalizable implements Runnable
{
	/**
	 * Method to run all business logic.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function run ()
	{ 
		if ( !WP::is_admin() )
		{ return; }

		// Current version
		$version = \get_option('wc_piggly_pix_version', '0' );

		// If version greater than plugin version, ignore
		if ( \version_compare($version, $this->_plugin->getVersion(), '>=') )
		{ return; }

		$this->rebuild_paths();
		
		if ( \version_compare($version, '2.0.4', '<') )
		{ 
			$this->_plugin->settings()->bucket()->set('upgraded_endpoints', true); 
			$this->_plugin->settings()->save();
			
			WP::add_action(
				'admin_notices', 
				$this,
				'upgrader_notice' 
			);
		}

		// New version
		\update_option('wc_piggly_pix_version', $this->_plugin->getVersion());
	}

	/**
	 * Upgrade database when needed.
	 * 
	 * @since 2.0.4
	 * @return void
	 */
	public function update_database ()
	{
		global $wpdb;

		$ivl_db_version = \WC_PIGGLY_PIX_DB_VERSION;
		$ins_db_version = get_option('wc_piggly_pix_dbversion', '0' );

		if ( \version_compare($ins_db_version, $ivl_db_version, '>=' ) )
		{ return; }

		$prefix = $wpdb->prefix;
		$table_name = $prefix . 'pgly_pix';

		if ( \version_compare($ins_db_version, '2.0.1', '<' ) )
		{
			try
			{ $wpdb->query("ALTER TABLE $table_name MODIFY COLUMN `status` enum('created','waiting','expired','paid','cancelled') NOT NULL DEFAULT 'created' AFTER `type`"); }
			catch ( Exception $e )
			{ $this->_plugin->debugger()->force()->error('Não foi possível atualizar o banco de dados...'); }
		}

		update_option('wc_piggly_pix_dbversion', $ivl_db_version);
	}

	/**
	 * Check if paths are created and validated them.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	protected function rebuild_paths ()
	{
		/** @var string $base_dir Upload base dir */
		$base_dir = wp_upload_dir()['basedir'];
		/** @var string $pix_dir Folder name */
		$pix_dir  = \dirname($this->_plugin->getBasename());

		// INDEX
		// Check for .htaccess file
		$PATH = \sprintf('%s/%s/.htaccess', $base_dir, $pix_dir);

		if ( !\file_exists( $PATH ) )
		{ file_put_contents( $PATH, 'Options -Indexes' ); }

		// Check for index.php file
		$PATH = \sprintf('%s/%s/index.php', $base_dir, $pix_dir);

		if ( !\file_exists( $PATH ) )
		{ file_put_contents( $PATH, '<?php // Silence is golden' ); }

		// QR CODES

		// QR CODE PATH
		$PATH = sprintf('%s/%s/qr-codes/', $base_dir, $pix_dir);

		// Create folder if not exists...
		if ( !\file_exists( $PATH ) ) 
		{ wp_mkdir_p($PATH); }

		// Check for .htaccess file
		$PATH = \sprintf('%s/%s/qr-codes/.htaccess', $base_dir, $pix_dir);

		if ( !\file_exists( $PATH ) )
		{ file_put_contents( $PATH, 'Options -Indexes' ); }

		// Check for index.php file
		$PATH = sprintf('%s/%s/qr-codes/index.php', $base_dir, $pix_dir);

		if ( !\file_exists( $PATH ) )
		{ file_put_contents( $PATH, '<?php // Silence is golden' ); }

		// RECEIPTS

		// RECEIPTS PATH
		$PATH = \sprintf('%s/%s/receipts/', $base_dir, $pix_dir);

		// Create folder if not exists...
		if ( !\file_exists( $PATH ) ) 
		{ wp_mkdir_p($PATH); }

		// Check for .htaccess file
		$PATH = sprintf('%s/%s/receipts/.htaccess', $base_dir, $pix_dir);

		if ( !\file_exists( $PATH ) )
		{ file_put_contents( $PATH, 'Options -Indexes' ); }

		// Check for index.php file
		$PATH = sprintf('%s/%s/receipts/index.php', $base_dir, $pix_dir);

		if ( !\file_exists( $PATH ) )
		{ file_put_contents( $PATH, '<?php // Silence is golden' ); }
	}

	/**
	 * Shows about the update.
	 * 
	 * @since 2.0.2
	 * @return void
	 */
	public function upgrader_notice ()
	{
		?>
		<div class="notice notice-warning">
			<p>
				Acesse <strong>Configurações > Links permanentes</strong>
				e apenas clique em <strong>Salvar Alterações</strong>
				para que o plugin <strong>Pix por Piggly</strong>
				funcione corretamente. Não esqueça de limpar o cachê.
			</p>
		</div>
		<?php
	}
}