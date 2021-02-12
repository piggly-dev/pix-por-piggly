<?php
// If this file is called directly, abort.

use Piggly\Pix\Parser;
use Piggly\Pix\Payload;

if ( ! defined( 'WPINC' ) ) { die; }

/**
 * The main core to the payment gateway.
 *
 *
 * @package    WC_Piggly_Pix
 * @subpackage WC_Piggly_Pix/core/pix
 * @author     Caique <caique@piggly.com.br>
 */
class WC_Piggly_Pix_Gateway extends WC_Payment_Gateway 
{
	/**
	 * Startup payment gatewat component.
	 * 
	 * @since 1.0.0
	 */
	public function __construct()
	{
		$this->id = 'wc_piggly_pix_gateway';
		$this->icon = apply_filters( 'woocommerce_gateway_icon', WC_PIGGLY_PIX_PLUGIN_URL.'assets/pix-payment-icon.png' );
		$this->has_fields = false;
		$this->method_title = __( 'Pix', WC_PIGGLY_PIX_PLUGIN_NAME );
		$this->method_description = __( 'Habilite o pagamento de pedidos via Pix. Este plugin automaticamente adiciona as instruções Pix na Página de Obrigado e na Página do Pedido.', WC_PIGGLY_PIX_PLUGIN_NAME );
		$this->supports = array('products');

		// Method with all the options fields
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// All settings
		$this->title = $this->get_option( 'title', __('Faça um Pix', WC_PIGGLY_PIX_PLUGIN_NAME) );
		$this->description = $this->get_option( 'description', __('Você não precisa ter uma chave cadastrada. Pague os seus pedidos via Pix.', WC_PIGGLY_PIX_PLUGIN_NAME) );
		$this->pix_qrcode = $this->asBool($this->get_option( 'pix_qrcode', 'no' ));
		$this->pix_copypast = $this->asBool($this->get_option( 'pix_copypast', 'no' ));
		$this->pix_manual = $this->asBool($this->get_option( 'pix_manual', 'no' ));
		$this->store_name = $this->get_option( 'store_name' );
		$this->merchant_name = $this->get_option( 'merchant_name' );
		$this->merchant_city = $this->get_option( 'merchant_city' );
		$this->key_type = $this->get_option( 'key_type', Parser::KEY_TYPE_RANDOM );
		$this->key_value = $this->get_option( 'key_value' );
		$this->email_status = $this->get_option( 'email_status', 'WC_Email_Customer_On_Hold_Order' );
		$this->email_position = $this->get_option( 'email_position', 'before' );
		$this->order_status = $this->get_option( 'order_status', 'wc-on-hold' );
		$this->instructions = $this->get_option( 'instructions', __('Faça um Pix abaixo e envie seu comprovante', WC_PIGGLY_PIX_PLUGIN_NAME) );
		$this->identifier = $this->get_option( 'identifier', 'P{{id}}' );
		$this->receipt_page_value = $this->get_option( 'receipt_page_value' );
		$this->whatsapp = $this->get_option( 'whatsapp' );
		$this->telegram = $this->get_option( 'telegram' );
		$this->whatsapp_message = $this->get_option( 'whatsapp_message', __('Segue o comprovante para o pedido {{pedido}}:', WC_PIGGLY_PIX_PLUGIN_NAME) );
		$this->telegram_message = $this->get_option( 'telegram_message', __('Segue o comprovante para o pedido {{pedido}}:', WC_PIGGLY_PIX_PLUGIN_NAME) );
		$this->enabled = $this->get_option( 'enabled', 'no' );

		// When it is admin...
		if ( is_admin() ) 
		{ add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) ); }

		// This action hook loads the thank you page
		add_action( 'woocommerce_thankyou_'.$this->id, array( $this, 'thankyou_page' ), 5, 1 );
		// Add method instructions in order details page 
		add_action( 'woocommerce_order_details_before_order_table', array( $this, 'page_instructions' ), 5, 1);
		// Customer Emails
		add_action( 'woocommerce_email_'.$this->email_position.'_order_table', array( $this, 'email_instructions' ), 10, 4 );
	}

	/**
	 * Setup all form fields.
	 * 
	 * @since 1.0.0
	 */
	public function init_form_fields()
	{
		$this->form_fields = array(
			'enabled' => array(
				'type'        => 'checkbox',
				'default'     => 'no'
			),
			'unique_payment' => array(
				'type'        => 'checkbox',
				'default'     => 'no'
			),
			'pix_qrcode' => array(
				'type'        => 'checkbox',
				'default'     => 'no'
			),
			'pix_copypast' => array(
				'type'        => 'checkbox',
				'default'     => 'no'
			),
			'pix_manual' => array(
				'type'        => 'checkbox',
				'default'     => 'no'
			),
			'pix_code' => array(
				'type'        => 'text',
				'description' => __('Tem um código Pix válido? Coloque-o aqui, clique em salvar e os dados principais serão extraídos dele.', WC_PIGGLY_PIX_PLUGIN_NAME)
			),
			'title' => array(
				'type'        => 'text',
				'default'     => __('Faça um PIX', WC_PIGGLY_PIX_PLUGIN_NAME)
			),
			'description' => array(
				'type'        => 'text',
				'default'     => __('Você não precisa ter uma chave cadastrada. Pague os seus pedidos via Pix.', WC_PIGGLY_PIX_PLUGIN_NAME),
			),
			'store_name' => array(
				'type'        => 'text'
			),
			'merchant_name' => array(
				'type'        => 'text',
				'required'	  => true
			),
			'merchant_city' => array(
				'type'        => 'text',
				'required'	  => true
			),
			'key_type' => array(
				'type'        => 'select',
				'required'	  => true,
				'default'	  => Parser::KEY_TYPE_RANDOM
			),
			'email_status' => array(
				'type'        => 'select',
				'default'	  => 'WC_Email_Customer_On_Hold_Order',
			),
			'email_position' => array(
				'type'        => 'select',
				'default'	  => 'before',
			),
			'order_status' => array(
				'type'        => 'select',
				'default'	  => 'wc-on-hold',
			),
			'key_value' => array(
				'type'        => 'text',
				'required'	  => true
			),
			'instructions' => array(
				'type'        => 'textarea',
				'default'	  => __('Faça o pagamento via PIX e envie o comprovante para <dados></br>O pedido será liberado assim que a confirmação do pagamento for efetuada.', WC_PIGGLY_PIX_PLUGIN_NAME),
				'required'	  => true
			),
			'identifier' => array(
				'type'        => 'text',
				'default'	  => 'P-{{id}}'
			),
			'receipt_page_value' => array(
				'type'        => 'text',
			),
			'whatsapp' => array(
				'type'        => 'text',
			),
			'telegram' => array(
				'type'        => 'text',
			),
			'whatsapp_message' => array(
				'type'        => 'text',
				'default'	  => 'Segue o comprovante para o pedido {{pedido}}:'
			),
			'telegram_message' => array(
				'type'        => 'text',
				'default'	  => 'Segue o comprovante para o pedido {{pedido}}:'
			)
		);
	}

	/**
	 * Page for testing.
	 * 
	 * @since 1.1.0
	 */
	public function admin_options () 
	{
		$screen = filter_input( INPUT_GET, 'screen', FILTER_SANITIZE_STRING );

		if ( $screen === 'testing' ) :
			wc_get_template(
				'test-settings.php',
				array(
					'data' => $this
				),
				'',
				WC_PIGGLY_PIX_PLUGIN_PATH.'admin/partials/'
			);

			return;
		endif;

		wc_get_template(
			'main-settings.php',
			array(
				'data' => $this
			),
			'',
			WC_PIGGLY_PIX_PLUGIN_PATH.'admin/partials/'
		);
	}

	/**
	 * Show pix instructions when viewing the order.
	 * 
	 * @since 1.0.0
	 */
	public function page_instructions ( $order )
	{
		if( $this->isPaymentWaiting($order) && $this->isViewingOrder() )
		{ do_action( 'woocommerce_thankyou_'.$order->get_payment_method(), $order->get_id() ); }
	}
	
	/**
	 * Add content to the WC emails.
	 *
	 * @access public
	 * @param WC_Order $order
	 * @param bool $sent_to_admin
	 * @param bool $plain_text
	 */
	public function email_instructions( $order, $sent_to_admin, $plain_text = false, $email ) 
	{
		if ( get_class($email) === $this->email_status ) {
			$pixData = $this->getPix( $order );

			wc_get_template(
				'email-woocommerce-pix.php',
				array_merge( $pixData, [ 'order' => $order] ),
				'',
				WC_PIGGLY_PIX_PLUGIN_PATH.'templates/'
			);
		}
	}
	
	/**
	 * Output for the order received page.
	 * 
	 * @since 1.0.0
	 */
	public function thankyou_page ( $order_id ) 
	{
		// Getting order object
		$order = wc_get_order($order_id);

		// Return if $order not found.
		if ( !$order )
		{ return; }
		
		$pixData = $this->getPix( $order );

		wc_get_template(
			'html-woocommerce-thank-you-page.php',
			$pixData,
			'',
			WC_PIGGLY_PIX_PLUGIN_PATH.'templates/'
		);
	}

	/**
	 * Get an array with all pix data. 
	 * @since 1.1.0
	 */
	protected function getPix ( WC_Order $order ) : array
	{
		$order_id = $order->get_order_number();
		$amount   = $order->get_total();
		
		$this->instructions       = str_replace('{{pedido}}', $order_id, $this->instructions);
		$this->receipt_page_value = str_replace('{{pedido}}', $order_id, $this->receipt_page_value);
		$this->whatsapp_message   = str_replace('{{pedido}}', $order_id, $this->whatsapp_message);
		$this->telegram_message   = str_replace('{{pedido}}', $order_id, $this->telegram_message);
		$this->identifier         = str_replace('{{id}}', $order_id, $this->identifier);

		$pix = 
			(new Piggly\Pix\Payload())
				->setPixKey($this->key_type, $this->key_value)
				->setDescription(sprintf('Compra em %s', $this->store_name))
				->setMerchantName($this->merchant_name)
				->setMerchantCity($this->merchant_city)
				->setAmount($amount)
				->setAsReusable(true)
				->setTid($this->identifier);

		// Get alias for pix
		$this->key_type = Piggly\Pix\Parser::getAlias($this->key_type); 

		return array(
			'data' => $this,
			'pix' => $pix->getPixCode(),
			'qrcode' => $this->pix_qrcode ? $pix->getQRCode(Payload::OUTPUT_PNG) : '',
			'order_id' => $order_id,
			'amount' => $amount
		);
	}

	/**
	 * Process the payment and return the result.
	 *
	 * @param int $order_id
	 * @return array
	 */
	public function process_payment ( $order_id ) 
	{
		global $woocommerce;

		// Load order
		$order = new WC_Order( $order_id );
		
		// Mark as on-hold (we're awaiting the payment)
		$order->update_status( 
			str_replace('wc-', '', $this->order_status), 
			__( 'Aguardando pagamento via Pix', WC_PIGGLY_PIX_PLUGIN_NAME ) 
		);
 
		// Remove cart
		$woocommerce->cart->empty_cart();
		
		// Return thank-you redirect
		return array(
			'result' 	=> 'success',
			'redirect'	=> $this->get_return_url( $order )
		);
	}

	/**
	 * Check if required fields are filled and validate the pix value
	 * based in the pix key setted.
	 * 
	 * @since 1.0.0
	 */
	function process_admin_options()
	{
		if( isset( $_GET['screen'] ) && '' !== $_GET['screen'] ) 
		{ return; }

		$field   = 'woocommerce_'.$this->id.'_';
		$pixCode = filter_input( INPUT_POST, $field.'pix_code', FILTER_SANITIZE_STRING );

		if ( !empty($pixCode) )
		{
			$reader = new \Piggly\Pix\Reader($pixCode);

			$_POST[$field.'key_value'] = $reader->getPixKey();
			$_POST[$field.'key_type']  = \Piggly\Pix\Parser::getKeyType($_POST[$field.'key_value']);
			$_POST[$field.'merchant_name'] = $reader->getMerchantName();
			$_POST[$field.'merchant_city'] = $reader->getMerchantCity();

			unset($_POST[$field.'pix_code']);
			
			parent::process_admin_options();
			return;
		}

		$required = array(
			'key_type' => 'Tipo da Chave',
			'key_value' => 'Chave PIX',
			'merchant_name' => 'Nome do Titular da Conta',
			'merchant_city' => 'Cidade do Titular da Conta',
			'instructions' => 'Instruções do PIX'
		);

		foreach ( $required as $key => $value )
		{
			$postValue = filter_input( INPUT_POST, $field.$key, FILTER_SANITIZE_STRING );

			if ( empty ( $postValue ) || is_null ( $postValue ) )
			{ 
				WC_Admin_Settings::add_error( sprintf('Por favor, preencha o campo `%s`.', $value) );
				return false; 
			}
		}

		$keyType  = filter_input( INPUT_POST, $field.'key_type', FILTER_SANITIZE_STRING );
		$keyValue = filter_input( INPUT_POST, $field.'key_value', FILTER_SANITIZE_STRING );

		if ( empty( $keyType ) || empty( $keyValue ) )
		{ 
			WC_Admin_Settings::add_error( 'Os valores da chave não foram preenchidos.' );
			return false; 
		}

		// Validates the key
		try
		{ Piggly\Pix\Parser::validate($keyType,$keyValue); }
		catch ( Exception $e )
		{
			WC_Admin_Settings::add_error($e->getMessage());
			return false;
		}

		parent::process_admin_options();
	}

	/**
	 * Return if is payment waiting.
	 * 
	 * @since 1.0.0
	 * @return bool
	 */
	private function isPaymentWaiting ( WC_Order $order ) : bool
	{ return $this->id === $order->get_payment_method() && in_array( $order->get_status(), array( 'new', 'on-hold' ) ); }

	/**
	 * Return if endpoint url is 'view-order'.
	 * 
	 * @since 1.0.0
	 * @return bool
	 */
	private function isViewingOrder () : bool
	{ return is_wc_endpoint_url( 'view-order' ); }

	/**
	 * Tries to convert a value to bool.
	 * 
	 * @since 1.0.0
	 * @return bool
	 */
	public function asBool ( $value ) : bool
	{
		if ( is_string( $value ) )
		{
			if ( $value === 'yes' || $value === 'true' || $value === true )
			{ return true; }
			else if ( $value === 'no' || $value === 'false' || $value === false )
			{ return false; } 
		}

		return (bool)$value;
	}
}