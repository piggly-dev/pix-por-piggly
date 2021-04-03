<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Notice: Missing WooCommerce.
 *
 * @package    WC_Piggly_Pix
 * @subpackage WC_Piggly_Pix/admin/partials
 * @author     Caique <caique@piggly.com.br>
 */

$is_installed = false; 
if ( ! class_exists ( 'woocommerce' ) ) { $is_installed = true; }

?>
<div class="error">
	<p><strong><?php esc_html_e( 'Pix por Piggly', WC_PIGGLY_PIX_PLUGIN_NAME ); ?></strong> <?php esc_html_e( 'necessita da última versão do Woocommerce para funcionar.', WC_PIGGLY_PIX_PLUGIN_NAME ); ?></p>

	<?php if ( $is_installed && current_user_can( 'install_plugins' ) ) : ?>
		<p><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=woocommerce/woocommerce.php&plugin_status=active' ), 'activate-plugin_woocommerce/woocommerce.php' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Ativar WooCommerce', WC_PIGGLY_PIX_PLUGIN_NAME ); ?></a></p>
	<?php else : ?>
		<?php if ( current_user_can( 'install_plugins' ) ) : ?>
			<p><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=woocommerce' ), 'install-plugin_woocommerce' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Instalar WooCommerce', WC_PIGGLY_PIX_PLUGIN_NAME ); ?></a></p>
		<?php else : ?>
			<p><a href="http://wordpress.org/plugins/woocommerce/" class="button button-primary"><?php esc_html_e( 'Instalar WooCommerce', WC_PIGGLY_PIX_PLUGIN_NAME ); ?></a></p>
		<?php endif; ?>
	<?php endif; ?>
</div> 