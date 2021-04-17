<?php
namespace Piggly\WC\Pix\Gateway;

use Piggly\WC\Pix\WP\Helper as WP;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Load gateway if woocommerce is available.
 *
 * @since      1.2.0 
 * @package    Piggly\WC\Pix
 * @subpackage Piggly\WC\Pix
 * @author     Caique <caique@piggly.com.br>
 * @author     Piggly Lab <dev@piggly.com.br>
 */
class BaseGateway 
{
	/**
	 * Add all actions and filters to configure woocommerce
	 * gateways.
	 * 
	 * @since 1.2.0
	 * @return void
	 */
	public static function init () 
	{
		$base = new self();

		WP::add_filter('woocommerce_payment_gateways', $base, 'add_gateway');
		WP::add_filter('plugin_action_links_'.\WC_PIGGLY_PIX_BASE_NAME, $base, 'plugin_action_links');
		
		// Create shortcode
		add_shortcode( 'pix-por-piggly', array($base, 'pix_shortcode') );
		add_shortcode( 'pix-por-piggly-form', array($base, 'pix_form_shortcode') );
	}

	/**
	 * Add gateways to Woocommerce.
	 * 
	 * @since 1.2.0
	 * @return void
	 */
	public function add_gateway ( array $gateways )
	{
		array_push( $gateways, PixGateway::class );
		return $gateways;
	}

	/**
	 * Add links to plugin settings page.
	 * 
	 * @since 1.2.0
	 * @return void
	 */
	public function plugin_action_links ( $links )
	{
		$pluginLinks = array();

		$baseUrl = esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_piggly_pix_gateway' ) );

		$pluginLinks[] = sprintf('<a href="%s">%s</a>', $baseUrl, __('Configurações', \WC_PIGGLY_PIX_PLUGIN_NAME));
		$pluginLinks[] = sprintf('<a href="%s&screen=support">%s</a>', $baseUrl, __('Suporte', \WC_PIGGLY_PIX_PLUGIN_NAME));
		$pluginLinks[] = sprintf('<a href="%s">%s</a>', 'https://wordpress.org/plugins/pix-por-piggly/#reviews', __('Avalie o Plugin!', \WC_PIGGLY_PIX_PLUGIN_NAME));

		return array_merge( $pluginLinks, $links );
	}

	/**
	 * Add pix template when call the shortcode.
	 * 
	 * @param array $attrs
	 * @since 1.2.0
	 * @since 1.3.0 order_id will be not required, if empty try to get from current order...
	 * @return void
	 */
	public function pix_shortcode ( $attrs )
	{
		global $wp;

		$attrs = shortcode_atts( array('order_id' => null), $attrs );
		$order = $attrs['order_id'];

		if ( empty($order) )
		{
			$order_id  = $wp->query_vars['order'] ?? $wp->query_vars['order-received'] ?? null;
			$order_key = wc_clean( wp_unslash( $_GET['key'] ) );

			if ( !empty($order_id) )
			{ $order = wc_get_order($order_id); }
			else if ( !empty($order_key) )
			{ $order = wc_get_order((int)wc_get_order_id_by_order_key($order_key)); }

			if ( $order === false || empty($order) )
			{ return; }
		}
		else
		{
			// Get order
			$order = wc_get_order((int)$order);

			if ( $order === false || empty($order) )
			{ return; }
		}

		$gateway = new PixGateway();

		// If order is not payment waiting, return...
		if ( !$gateway->is_payment_waiting($order) )
		{ return; }

		$gateway->thankyou_page($order);
	}


	/**
	 * Add pix form template when call the shortcode.
	 * Requires "key" query string parameter with order_key.
	 * 
	 * @param array $attrs
	 * @since 1.3.0
	 * @return void
	 */
	public function pix_form_shortcode ( $attrs )
	{
		global $wpdb;
		$data = [];

		$data['sent'] = $_SERVER['REQUEST_METHOD'] === 'POST';
		$data['link'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";		
		$data['error'] = false;
		$data['auto_fill'] = true;

		if ( $data['sent'] )
		{
			// processing data
			$email = filter_input( INPUT_POST, 'wpgly_pix_email', \FILTER_SANITIZE_STRING );
			$order_key = filter_input( INPUT_POST, 'wpgly_pix_order_key', \FILTER_SANITIZE_STRING );
			$nonce = filter_input( INPUT_POST, 'wpgly_pix_nonce', \FILTER_SANITIZE_STRING );
			$auto_fill = filter_input( INPUT_POST, 'wpgly_pix_auto_fill', \FILTER_SANITIZE_STRING ) == 'true' ? true : false;

			if ( !wp_verify_nonce( $nonce, 'wpgly-pix-receipt' ) )
			{ $data['error'] = 'Não foi possível validar o formulário no momento.'; }
			else
			{
				if ( empty($email) || empty($order_key) )
				{ $data['error'] = 'Nem todos os campos foram preenchidos.'; }
				else
				{
					if ( empty($_FILES['wpgly_pix_receipt']))
					{ $data['error'] = 'O comprovante não foi enviado.'; }
					else
					{
						$upload     = wp_upload_dir();
						$uploadPath = $upload['basedir'].'/'.\WC_PIGGLY_PIX_DIR_NAME.'/receipts/';
						$uploadUrl  = $upload['baseurl'].'/'.\WC_PIGGLY_PIX_DIR_NAME.'/receipts/';
						$extension  = pathinfo(basename($_FILES['wpgly_pix_receipt']['name']),PATHINFO_EXTENSION);

						// Check file size
						if ($_FILES['wpgly_pix_receipt']['size'] > 2000000) 
						{ $data['error'] = 'O tamanho máximo permitido para o arquivo é 2MB, envie um arquivo menor.'; }
						else if ( !\in_array($extension, ['jpg','jpeg','png','pdf']) )
						{ $data['error'] = 'O comprovante foi enviado em um tipo de arquivo não compatível. Envie uma imagem ou um PDF.'; }
						else
						{
							$finfo = finfo_open(FILEINFO_MIME_TYPE);
							$mime = finfo_file($finfo, $_FILES['wpgly_pix_receipt']['tmp_name']);
							finfo_close($finfo);

							if ( !in_array($mime, ['image/jpg','image/jpeg','image/png','application/pdf']))
							{ $data['error'] = 'O comprovante foi enviado em um tipo de arquivo não compatível. Envie uma imagem ou um PDF.'; }
							else
							{
								$filename = md5($order_key.time()).'.'.$extension;

								if ( !\file_exists( $uploadPath ) ) 
								{ wp_mkdir_p($uploadPath); }

								if ( !\move_uploaded_file($_FILES['wpgly_pix_receipt']['tmp_name'], $uploadPath.$filename) )
								{ $data['error'] = 'Não foi possível enviar o comprovante agora.'; }
								else
								{									
									if ( $auto_fill )
									{
										$order = wc_get_order((int)wc_get_order_id_by_order_key($order_key));

										if ( $order !== false )
										{ 
											$order_key = $order->get_id();
											$order->update_meta_data('_wc_piggly_pix_receipt', $uploadUrl.$filename); 
											$order->update_status( 'pix-receipt', __('Comprovante Pix Recebido.', \WC_PIGGLY_PIX_PLUGIN_NAME) ); 
											$order->save();
										}
									}

									$wpdb->insert(
										$wpdb->prefix . 'wpgly_pix_receipts',
										[
											'order_number' => $order_key,
											'customer_email' => $email,
											'pix_receipt' => $uploadUrl.$filename,
											'auto_fill' => (int)$auto_fill,
										]
									);
								}
							}
						}
					}
				}
			}
		}
		
		if ( !$data['sent'] || $data['error'] !== false )
		{
			$order_key = filter_input( INPUT_GET, 'key', \FILTER_SANITIZE_STRING );

			if ( empty($order_key) )
			{ $data['auto_fill'] = false; }
			else
			{
				// Get order
				$order = wc_get_order((int)wc_get_order_id_by_order_key($order_key));

				if ( !$order )
				{ $data['auto_fill'] = false; }
				else
				{
					$data['order_key'] = $order_key;
					$data['email'] = $order->get_billing_email();
				}
			}
		}
		
		$data['_nonce'] = wp_create_nonce('wpgly-pix-receipt');
		
		wc_get_template(
			'html-woocommerce-form.php',
			$data,
			WC()->template_path().\WC_PIGGLY_PIX_DIR_NAME.'/',
			\WC_PIGGLY_PIX_PLUGIN_PATH.'templates/'
		);		
	}
}