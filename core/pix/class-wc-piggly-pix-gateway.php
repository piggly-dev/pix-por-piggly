<?php
// If this file is called directly, abort.
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
		$this->method_description = __( 'Habilite o pagamento de pedidos via pix.', WC_PIGGLY_PIX_PLUGIN_NAME );
		$this->supports = array('products');

		// Method with all the options fields
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// All settings
		$this->title = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );
		$this->unique_payment = $this->get_option( 'unique_payment' );
		$this->pix_qrcode = $this->get_option( 'pix_qrcode' );
		$this->pix_copypast = $this->get_option( 'pix_copypast' );
		$this->pix_manual = $this->get_option( 'pix_manual' );
		$this->store_name = $this->get_option( 'store_name' );
		$this->merchant_name = $this->get_option( 'merchant_name' );
		$this->merchant_city = $this->get_option( 'merchant_city' );
		$this->key_type = $this->get_option( 'key_type' );
		$this->key_value = $this->get_option( 'key_value' );
		$this->instructions = $this->get_option( 'instructions' );
		$this->enabled = $this->get_option( 'enabled' );

		// When it is admin...
		if ( is_admin() ) 
		{
			// This action hook saves the settings
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		}

		// This action hook loads the thank you page
		add_action( 'woocommerce_thankyou_'.$this->id, array( $this, 'thankyou_page' ), 5, 1 );
		// Add method instructions in order details page 
		add_action( 'woocommerce_order_details_after_order_table', array( $this, 'page_instructions' ), 5, 1);
		// Customer Emails
		add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
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
				'title'       => __('Habilitar/Desabilitar', WC_PIGGLY_PIX_PLUGIN_NAME),
				'label'       => __('Habilite o Pagamento via Pix', WC_PIGGLY_PIX_PLUGIN_NAME),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no'
			),
			'unique_payment' => array(
				'title'       => __('BR Code Único', WC_PIGGLY_PIX_PLUGIN_NAME),
				'label'       => __('Marque se o código PIX gerado só poderá ser utilizado uma única vez.', WC_PIGGLY_PIX_PLUGIN_NAME),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no'
			),
			'pix_qrcode' => array(
				'title'       => __('Exibir QR Code', WC_PIGGLY_PIX_PLUGIN_NAME),
				'label'       => __('Marque para exibir nas instruções o QR Code do Pix.', WC_PIGGLY_PIX_PLUGIN_NAME),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no'
			),
			'pix_copypast' => array(
				'title'       => __('Exibir Pix Copie & Cole', WC_PIGGLY_PIX_PLUGIN_NAME),
				'label'       => __('Marque para exibir nas instruções o Pix Copie & Cole.', WC_PIGGLY_PIX_PLUGIN_NAME),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no'
			),
			'pix_manual' => array(
				'title'       => __('Exibir Pix Manual', WC_PIGGLY_PIX_PLUGIN_NAME),
				'label'       => __('Marque para exibir nas instruções os dados manuais para o pix.', WC_PIGGLY_PIX_PLUGIN_NAME),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no'
			),
			'title' => array(
				'title'       => __('Título', WC_PIGGLY_PIX_PLUGIN_NAME),
				'type'        => 'text',
				'description' => __('Isso controla o que o usuário vê durante o checkout.', WC_PIGGLY_PIX_PLUGIN_NAME),
				'default'     => __('Faça um PIX', WC_PIGGLY_PIX_PLUGIN_NAME),
				'desc_tip'    => true,
			),
			'description' => array(
				'title'       => __('Descrição', WC_PIGGLY_PIX_PLUGIN_NAME),
				'type'        => 'text',
				'description' => __('Isso controla a curta descrição que o usuário vê durante o checkout.', WC_PIGGLY_PIX_PLUGIN_NAME),
				'default'     => __('Você não precisa ter uma chave cadastrada. Pague os seus pedidos via Pix.', WC_PIGGLY_PIX_PLUGIN_NAME),
				'desc_tip'    => true,
				'custom_attributes' => [
					'maxlength' => 25
				]
			),
			'store_name' => array(
				'title'       => __('Nome da Loja', WC_PIGGLY_PIX_PLUGIN_NAME),
				'type'        => 'text',
				'description' => __('Informe o nome da loja para acrescentar na descrição do Pix.', WC_PIGGLY_PIX_PLUGIN_NAME),
				'required'	  => true,
				'desc_tip'    => true,
				'custom_attributes' => [
					'maxlength' => 25
				]
			),
			'merchant_name' => array(
				'title'       => __('Nome do Titular da Conta', WC_PIGGLY_PIX_PLUGIN_NAME),
				'type'        => 'text',
				'description' => __('Informe o nome do titular da conta que irá receber o PIX. Como consta no Banco.', WC_PIGGLY_PIX_PLUGIN_NAME),
				'required'	  => true,
				'desc_tip'    => true,
				'custom_attributes' => [
					'maxlength' => 25
				]
			),
			'merchant_city' => array(
				'title'       => __('Cidade do Titular da Conta', WC_PIGGLY_PIX_PLUGIN_NAME),
				'type'        => 'text',
				'description' => __('Informe a cidade do titular da conta que irá receber o PIX. Como consta no Banco.', WC_PIGGLY_PIX_PLUGIN_NAME),
				'required'	  => true,
				'desc_tip'    => true,
				'custom_attributes' => [
					'maxlength' => 25
				]
			),
			'key_type' => array(
				'title'       => __('Tipo da Chave', WC_PIGGLY_PIX_PLUGIN_NAME),
				'type'        => 'select',
				'description' => __('Informe o tipo da chave PIX a ser compartilhada.', WC_PIGGLY_PIX_PLUGIN_NAME),
				'options'     => [
					'random' => __('Chave Aleatória', WC_PIGGLY_PIX_PLUGIN_NAME),
					'document' => __('CPF/CNPJ', WC_PIGGLY_PIX_PLUGIN_NAME),
					'phone' => __('Telefone', WC_PIGGLY_PIX_PLUGIN_NAME),
					'email' => __('E-mail', WC_PIGGLY_PIX_PLUGIN_NAME)
				],
				'required'	  => true,
				'desc_tip'    => true,
			),
			'key_value' => array(
				'title'       => __('Chave PIX', WC_PIGGLY_PIX_PLUGIN_NAME),
				'type'        => 'text',
				'description' => __('Digite sua Chave PIX da forma como ela foi cadastrada.', WC_PIGGLY_PIX_PLUGIN_NAME),
				'required'	  => true,
				'desc_tip'    => true
			),
			'instructions' => array(
				'title'       => __('Instruções do PIX', WC_PIGGLY_PIX_PLUGIN_NAME),
				'type'        => 'textarea',
				'description' => __('Explique ao cliente como comunicar o pagamento via PIX. Substitua <dados> pelos dados de envio.', WC_PIGGLY_PIX_PLUGIN_NAME),
				'default'	  => __('Faça o pagamento via PIX e envie o comprovante para <dados></br>O pedido será liberado assim que a confirmação do pagamento for efetuada.', WC_PIGGLY_PIX_PLUGIN_NAME),
				'required'	  => true,
				'desc_tip'    => true
			),
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
	public function email_instructions( $order, $sent_to_admin, $plain_text = false ) 
	{
		if ( !$sent_to_admin && $this->isPaymentWaiting($order) ) {
			$this->generatePix($order);
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
		
		$this->generatePix( $order );
	}

	protected function generatePix ( WC_Order $order )
	{
		$qrcode = '';

		$pix = 
			(new WC_Piggly_Pix_Payload)
				->setPixKey($this->key_value)
				->setDescription(sprintf('%s #%s', $this->store_name, $order->get_order_number()))
				->setMerchantName($this->merchant_name)
				->setMerchantCity($this->merchant_city)
				->setAmount($order->get_total())
				->setUniquePayment($this->asBool($this->unique_payment))
				->setTid($order->get_order_number())
				->export();
		
		if ( $this->asBool( $this->pix_qrcode ) )
		{ 
			/** Include composer libraries */
			include_once WC_PIGGLY_PIX_PLUGIN_PATH . '/vendor/autoload.php'; 
			$qrcode = (new chillerlan\QRCode\QRCode)->render($pix);
		}

		$data = array(
			'key_type' => array(
				'title'       => 'Tipo da Chave',
				'value'       => WC_Piggly_Pix_Parser::getAlias($this->key_type)
			),
			'key_value' => array(
				'title'       => 'Chave PIX',
				'value'       => WC_Piggly_Pix_Parser::parse($this->key_type, $this->key_value)
			),
			'merchant_name' => array(
				'title'       => 'Nome do Titular da Conta',
				'value'       => $this->merchant_name
			),
			'merchant_city' => array(
				'title'       => 'Cidade do Titular da Conta',
				'value'       => $this->merchant_city
			),
			'instructions' => array(
				'title'       => 'Instruções do PIX',
				'value'       => $this->instructions
			)
		);

		wc_get_template(
			'html-woocommerce-thank-you-page.php',
			array(
				'data' => $data,
				'pix' => $pix,
				'qrcode' => $qrcode,
				'pix_qrcode' => $this->asBool($this->pix_qrcode),
				'pix_copypast' => $this->asBool($this->pix_copypast),
				'pix_manual' => $this->asBool($this->pix_manual),
				'amount' => $order->get_total()
			),
			'',
			WC_PIGGLY_PIX_PLUGIN_PATH.'templates/'
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
		$order->update_status( 'on-hold', __( 'Aguardando pagamento via Pix', 'piggly-woocommerce-pix' ) );

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
		$field = 'woocommerce_'.$this->id.'_';

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

			if ( empty ( $postValue ) )
			{ 
				WC_Admin_Settings::add_error( sprintf('Por favor, preencha o campo `%s`.', $value) );
				return false;
			}
		}

		$keyType  = $postValue = filter_input( INPUT_POST, $field.'key_type', FILTER_SANITIZE_STRING );
		$keyValue = $postValue = filter_input( INPUT_POST, $field.'key_value', FILTER_SANITIZE_STRING );

		// Validates the key
		try
		{ WC_Piggly_Pix_Parser::validate($keyType,$keyValue); }
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
	private function asBool ( $value ) : bool
	{
		if ( is_string( $value ) )
		{
			if ( $value === 'yes' || $value === 'true' )
			{ return true; }
			else if ( $value === 'no' || $value === 'false' )
			{ return true; }
		}

		return (bool)$value;
	}
}