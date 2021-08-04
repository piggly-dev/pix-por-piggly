<?php
namespace Piggly\WooPixGateway\Core;

use Exception;
use Piggly\WooPixGateway\Core\Processors\PixProcessor;
use Piggly\Wordpress\Core\Scaffold\Initiable;
use Piggly\Wordpress\Settings\KeyingBucket;
use WC_Order;

class Shortcode extends Initiable
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
		// Create shortcode to pix data
		add_shortcode( 'pix-por-piggly', array($this, 'pix_shortcode') );
		// Create shortcode to pix form
		add_shortcode( 'pix-por-piggly-form', array($this, 'pix_form_shortcode') );
	}

	/**
	 * Add pix template when call the shortcode.
	 * 
	 * @param array $attrs
	 * @since 2.0.0
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

		// If order is not payment waiting, return...
		if ( !$this->isPaymentWaiting($order) )
		{ return; }

		// Load payload
		$payload = (new PixProcessor($this->_plugin))->get($order);

		return wc_get_template_html(
			'html-woocommerce-thank-you-page.php',
			[
				'payload' => $payload,
				'order' => $order
			],
			WC()->template_path().\dirname($this->_plugin->getBasename()).'/',
			$this->_plugin->getTemplatePath()
		);
	}
	
	/**
	 * Add pix form template when call the shortcode.
	 * Requires "key" query string parameter with order_key.
	 * 
	 * @param array $attrs
	 * @since 1.3.0
	 * @since 1.3.11 Melhorias e redirecionamento
	 * @return void
	 */
	public function pix_form_shortcode ( $attrs )
	{
		$data = [];

		$data['sent'] = $_SERVER['REQUEST_METHOD'] === 'POST';
		$data['link'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";		
		$data['error'] = false;
		$data['auto_fill'] = true;
		$data['permalink'] = false;

		if ( $data['sent'] )
		{ 
			try
			{ $this->validateReceiptForm(); }
			catch ( Exception $e )
			{ 
				$this->debug()->error($e->getMessage());
				$data['error'] = $e->getMessage(); 
			}

			$settings = get_option( 'woocommerce_wc_piggly_pix_gateway_settings', [] );

			/** @var KeyingBucket $settings */
			$settings = $this->_settings->bucket()->get('orders', new KeyingBucket());

			if ( !empty($settings->get('after_receipt')) )
			{ $data['permalink'] = get_permalink($settings->get('after_receipt')); }
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
		
		return wc_get_template_html(
			'html-woocommerce-form.php',
			$data,
			WC()->template_path().\dirname($this->_plugin->getBasename()).'/',
			$this->_plugin->getTemplatePath()
		);		
	}

	/**
	 * Validate and process receipt form data.
	 *
	 * @action wpgly_woo_pix_gateway_after_save_receipt
	 * @since 2.0.0
	 * @return void
	 * @throws Exception
	 */
	protected function validateReceiptForm ()
	{
		global $wpdb;

		// processing data
		$email = filter_input( INPUT_POST, 'wpgly_pix_email', \FILTER_SANITIZE_STRING );
		$order_key = filter_input( INPUT_POST, 'wpgly_pix_order_key', \FILTER_SANITIZE_STRING );
		$nonce = filter_input( INPUT_POST, 'wpgly_pix_nonce', \FILTER_SANITIZE_STRING );
		$auto_fill = filter_input( INPUT_POST, 'wpgly_pix_auto_fill', \FILTER_SANITIZE_STRING ) == 'true' ? true : false;

		if ( !wp_verify_nonce( $nonce, 'wpgly-pix-receipt' ) )
		{ throw new Exception($this->__translate('Não foi possível validar o formulário no momento.')); }

		if ( empty($email) || empty($order_key) )
		{ throw new Exception($this->__translate('Nem todos os campos foram preenchidos.')); }

		if ( empty($_FILES['wpgly_pix_receipt']))
		{ throw new Exception($this->__translate('O comprovante não foi enviado.')); }

		$expName = \explode('.', $_FILES['wpgly_pix_receipt']['name']);

		// Extension
		$pathExt = pathinfo(basename($_FILES['wpgly_pix_receipt']['name']),PATHINFO_EXTENSION);
		$nameExt = is_array( $expName ) ? $expName[count($expName)-1] : 'unknown';

		// Cannot validate mime type
		$mimeValidation = false;
		// If file should be trusted
		$trusted = true;

		try
		{
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mime = finfo_file($finfo, $_FILES['wpgly_pix_receipt']['tmp_name']);
			finfo_close($finfo);
			
			// Validate mime type
			$mimeValidation = \in_array($mime, ['image/jpg','image/jpeg','image/png','application/pdf']);
			$trusted = $mimeValidation;
		}
		catch ( Exception $e )
		{ 
			$this->debug()->force()->error(\sprintf($this->__translate('O usuário tentou realizar o upload, mas o arquivo não foi encontrado em `%s`. Verifique as configurações do PHP e as permissões da pasta. Verifique, ainda, a biblioteca MAGIC e a extensão file do PHP.'), $_FILES['wpgly_pix_receipt']['tmp_name']));
			throw new Exception($this->__translate('O arquivo não pode ser enviado no momento. Tente novamente mais tarde.')); 
		}

		finally
		{
			if ( !$mimeValidation )
			{
				$mime = $_FILES['wpgly_pix_receipt']['type'];
				// Trust in browser, but do a system alert...
				$this->debug()->force()->info(\sprintf($this->__translate('O arquivo `%s` enviado não é confiável...'), $_FILES['wpgly_pix_receipt']['tmp_name']));
				$mimeValidation = \in_array($mime, ['image/jpg','image/jpeg','image/png','application/pdf']);
				$trusted = false;
			}
		}

		// Validate extension
		$validateExt = \in_array($pathExt, ['jpg','jpeg','png','pdf']) || \in_array($nameExt, ['jpg','jpeg','png','pdf']);
		
		if ( !$validateExt && !$mimeValidation )
		{ throw new Exception($this->__translate('O nome do arquivo não indica uma imagem ou um PDF compatível.')); }

		if ( !$mimeValidation )
		{ throw new Exception($this->__translate('O comprovante foi enviado em um tipo de arquivo não compatível. Envie uma imagem ou um PDF.')); }
		
		// Check file size
		if ($_FILES['wpgly_pix_receipt']['size'] > 2000000) 
		{ throw new Exception($this->__translate('O tamanho máximo permitido para o arquivo é 2MB, envie um arquivo menor.')); }

		$mapExt = ['image/jpg'=>'jpg','image/jpeg'=>'jpeg','image/png'=>'png','application/pdf'=>'pdf'];
		// Fix extension
		$extension = $validateExt ? $pathExt ?? $nameExt : $mapExt[$mime];
		$filename = md5($order_key.time()).'.'.$extension;
		
		$upload     = wp_upload_dir();
		$uploadPath = $upload['basedir'].'/'.\WC_PIGGLY_PIX_DIR_NAME.'/receipts/';
		$uploadUrl  = $upload['baseurl'].'/'.\WC_PIGGLY_PIX_DIR_NAME.'/receipts/';

		if ( !\file_exists( $uploadPath ) ) 
		{ wp_mkdir_p($uploadPath); }

		if ( !\move_uploaded_file($_FILES['wpgly_pix_receipt']['tmp_name'], $uploadPath.$filename) )
		{ 
			$this->debug()->force()->error(\sprintf($this->__translate('Não foi mover o arquivo de upload de `%s` para `%s`.'), $_FILES['wpgly_pix_receipt']['tmp_name'], $uploadPath.$filename));
			throw new Exception($this->__translate('Não foi possível enviar o comprovante agora.')); 
		}
										
		if ( $auto_fill )
		{
			$order = wc_get_order((int)wc_get_order_id_by_order_key($order_key));

			if ( $order !== false )
			{ 
				$order_key = $order->get_id();
				$order->update_meta_data('_wc_piggly_pix_receipt', $uploadUrl.$filename); 

				/** @var KeyingBucket $settings */
				$settings = $this->_settings->bucket()->get('orders', new KeyingBucket());
				
				if ( $settings->get('receipt_status', 'on-hold') !== 'on-hold' )
				{ $order->update_status( $settings->get('receipt_status'), $this->__translate('Comprovante Pix Recebido.') ); }

				$order->save();

				// Do after save order
				do_action('wpgly_woo_pix_gateway_after_save_receipt', $order, $order->get_id());
			}
		}

		$table_name = $wpdb->prefix . 'wpgly_pix_receipts';

		$insert_data = [
			'order_number' => $order_key,
			'customer_email' => $email,
			'pix_receipt' => $uploadUrl.$filename,
			'auto_fill' => (int)$auto_fill,
			'trusted' => (int)$trusted
		];

		$wpdb->insert(
			$table_name,
			$insert_data
		);
	}

	/**
	 * Verify if is payment waiting.
	 *
	 * @param WC_Order $order
	 * @since 2.0.0
	 * @return boolean
	 */
	protected function isPaymentWaiting ( 
		WC_Order $order 
	) : bool
	{
		/** @var KeyingBucket $settings */
		$settings = $this->_settings->bucket()->get('orders', new KeyingBucket());
		
		$expected = [
			'new',
			'on-hold',
			$settings->get('waiting_status', 'on-hold'),
			$settings->get('receipt_status', 'on-hold')
		];

		return (
			($this->_plugin->getName() === $order->get_payment_method()
			&& $order->has_status($expected))
		);
	}
}