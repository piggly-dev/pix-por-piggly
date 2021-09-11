<?php
namespace Piggly\WooPixGateway\Core\Managers;

use Exception;
use Piggly\Pix\Exceptions\InvalidPixKeyException;
use Piggly\Pix\Exceptions\InvalidPixKeyTypeException;
use Piggly\WooPixGateway\Core\Woocommerce;
use Piggly\WooPixGateway\CoreConnector;
use Piggly\WooPixGateway\WP\Cron;

use Piggly\WooPixGateway\Vendor\Piggly\Pix\Parser;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Settings\KeyingBucket;

/**
 * Manages all plugin settings.
 * 
 * @package \Piggly\WpBDM
 * @subpackage \Piggly\WpBDM\Core\Managers
 * @version 2.0.0
 * @since 2.0.0
 * @category Managers
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license GPLv3 or later <key>
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
class SettingsManager
{
	/**
	 * All sections available to settings.
	 * 
	 * @var array
	 * @since 2.0.0
	 */
	const SETTINGS_SECTIONS = [
		'global',
		'gateway',
		'emails',
		'orders',
		'account',
		'discount',
		'receipts'
	];

	/**
	 * Export settings as array.
	 *
	 * @since 2.0.0
	 * @return array
	 */
	public function getSettings () : array
	{ 
		/** @var KeyingBucket $settings */
		$settings = CoreConnector::settings();
		
		$statusesOp = [];
		$statuses   = Woocommerce::getAvailableStatuses();

		foreach ( $statuses as $key => $label )
		{
			$statusesOp[] = [
				'value' => \str_replace('wc-','',$key),
				'label' => $label
			];
		}

		$pages = \array_map(function ($page) {
				return ['value' => \strval($page->ID), 'label' => $page->post_title];
			},
			\get_pages([
				'sort_order' => 'asc',
				'sort_column' => 'post_title',
				'hierarchical' => 1,
				'exclude' => '',
				'include' => '',
				'meta_key' => '',
				'meta_value' => '',
				'authors' => '',
				'child_of' => 0,
				'parent' => -1,
				'exclude_tree' => '',
				'number' => '',
				'offset' => 0,
				'post_type' => 'page',
				'post_status' => 'publish'
			])
		);

		$settings->getAndCreate('runtime', [
			'statuses' => $statusesOp,
			'pages' => $pages
		]);
		
		return CoreConnector::settingsManager()->bucket()->export() ?? []; 
	}

	/**
	 * Save and parse settings data by $section.
	 * $section must be a value of static::SETTINGS_SECTION.
	 *
	 * @param string $section
	 * @param array $data
	 * @since 2.0.0
	 * @return void
	 * @throws Exception
	 */
	public function saveSettings ( string $section, array $data )
	{
		if ( !\in_array($section, static::SETTINGS_SECTIONS, true) )
		{ throw new Exception(CoreConnector::__translate('Não foi possível salvar: Configurações inválidas.')); }

		switch ( $section )
		{
			case 'global':
				$this->saveGlobal($data);
				break;
			case 'gateway':
				$this->saveGateway($data);
				break;
			case 'emails':
				$this->saveEmails($data);
				break;
			case 'orders':
				$this->saveOrders($data);
				break;
			case 'account':
				$this->saveAccount($data);
				break;
			case 'discount':
				$this->saveDiscount($data);
				break;
			case 'receipts':
				$this->saveReceipts($data);
				break;
		}

		// Always save
		CoreConnector::settingsManager()->save();
	}

	/**
	 * Save wallet section.
	 *
	 * @param array $data
	 * @since 2.0.0
	 * @return void
	 * @throws Exception
	 */
	protected function saveGlobal ( array $data )
	{
		/** @var KeyingBucket $settings */
		$settings = CoreConnector::settings()->getAndCreate('global', new KeyingBucket());

		$this->prepare(
			$settings,
			$data,
			[
				'debug' => [ 'default' => false, 'sanitize' => \FILTER_VALIDATE_BOOLEAN ]
			]
		);
	}

	/**
	 * Save discount section.
	 *
	 * @param array $data
	 * @since 2.0.0
	 * @return void
	 * @throws Exception
	 */
	protected function saveGateway ( array $data )
	{
		/** @var KeyingBucket $settings */
		$settings = CoreConnector::settings()->getAndCreate('gateway', new KeyingBucket());

		$this->prepare(
			$settings,
			$data,
			[
				'enabled' => [ 'default' => false, 'sanitize' => \FILTER_VALIDATE_BOOLEAN ],
				'title' => [ 'default' => CoreConnector::__translate('Faça um Pix'), 'sanitize' => \FILTER_SANITIZE_STRING ],
				'description' => [ 'default' => CoreConnector::__translate('Você não precisa ter uma chave cadastrada. Pague os seus pedidos via Pix.'), 'sanitize' => \FILTER_SANITIZE_STRING ],
				'advanced_description' => [ 'default' => false, 'sanitize' => \FILTER_VALIDATE_BOOLEAN ],
				'instructions' => [ 'default' => CoreConnector::__translate('Faça o pagamento via PIX. O pedido número {{order_number}} será liberado assim que a confirmação do pagamento for efetuada.'), 'sanitize' => \FILTER_SANITIZE_STRING ],
				'shows_qrcode' => [ 'default' => false, 'sanitize' => \FILTER_VALIDATE_BOOLEAN ],				
				'shows_copypast' => [ 'default' => false, 'sanitize' => \FILTER_VALIDATE_BOOLEAN ],				
				'shows_manual' => [ 'default' => false, 'sanitize' => \FILTER_VALIDATE_BOOLEAN ],
				'shows_amount' => [ 'default' => false, 'sanitize' => \FILTER_VALIDATE_BOOLEAN ],
			]
		);	
		
		if ( $settings->get('enabled', false) && empty(CoreConnector::settings()->get('account')->get('key_value')) )
		{ throw new Exception(CoreConnector::__translate('Configure sua conta Pix antes...')); }
	}

	/**
	 * Save emails section.
	 *
	 * @param array $data
	 * @since 2.0.0
	 * @return void
	 * @throws Exception
	 */
	protected function saveEmails ( array $data )
	{
		/** @var KeyingBucket $settings */
		$settings = CoreConnector::settings()->getAndCreate('emails', new KeyingBucket());

		$this->prepare(
			$settings,
			$data,
			[
				'model' => [ 'default' => 'WC_Email_Customer_On_Hold_Order', 'sanitize' => \FILTER_SANITIZE_STRING ],
				'position' => [ 'default' => 'before', 'sanitize' => \FILTER_SANITIZE_STRING ]
			]
		);
	}

	/**
	 * Save orders section.
	 *
	 * @param array $data
	 * @since 2.0.0
	 * @return void
	 * @throws Exception
	 */
	protected function saveOrders ( array $data )
	{
		/** @var KeyingBucket $settings */
		$settings = CoreConnector::settings()->getAndCreate('orders', new KeyingBucket());

		$allowedStatus = [];
		$statuses      = Woocommerce::getAvailableStatuses();

		foreach ( $statuses as $key => $label )
		{ $allowedStatus[] = \str_replace('wc-','',$key); }

		$this->prepare(
			$settings,
			$data,
			[
				'decrease_stock' => [ 'default' => false, 'sanitize' => \FILTER_VALIDATE_BOOLEAN ],
				'receipt_status' => [ 'default' => 'on-hold', 'sanitize' => \FILTER_SANITIZE_STRING, 'allowed' => $allowedStatus ],
				'paid_status' => [ 'default' => 'processing', 'sanitize' => \FILTER_SANITIZE_STRING, 'allowed' => $allowedStatus ],
				'after_receipt' => [ 'default' => 0, 'sanitize' => \FILTER_VALIDATE_INT ],
				'hide_in_order' => [ 'default' => false, 'sanitize' => \FILTER_VALIDATE_BOOLEAN ],
				'expires_after' => [ 'default' => 24, 'sanitize' => \FILTER_VALIDATE_INT ],
				'closest_lifetime' => [ 'default' => 60, 'sanitize' => \FILTER_VALIDATE_INT ],
				'cron_frequency' => [ 'default' => 'everyfifteen', 'sanitize' => \FILTER_SANITIZE_STRING, 'allowed' => Cron::AVAILABLE_FREQUENCIES ],
			]
		);

		Cron::create(CoreConnector::plugin());
	}

	/**
	 * Save account section.
	 *
	 * @param array $data
	 * @since 2.0.0
	 * @return void
	 * @throws Exception
	 */
	protected function saveAccount ( array $data )
	{
		/** @var KeyingBucket $settings */
		$settings = CoreConnector::settings()->getAndCreate('account', new KeyingBucket());
		
		$this->prepare(
			$settings,
			$data,
			[
				'store_name' => [ 'default' => '', 'sanitize' => \FILTER_SANITIZE_STRING ],
				'bank' => [ 'default' => 0, 'sanitize' => \FILTER_VALIDATE_INT ],
				'merchant_name' => [ 'default' => '', 'required' => true, 'sanitize' => \FILTER_SANITIZE_STRING ],
				'merchant_city' => [ 'default' => '', 'required' => true, 'sanitize' => \FILTER_SANITIZE_STRING ],
				'key_type' => [ 'default' => '', 'required' => true, 'sanitize' => \FILTER_SANITIZE_STRING ],
				'key_value' => [ 'default' => '', 'required' => true, 'sanitize' => \FILTER_SANITIZE_STRING ],
				'description' => [ 'default' => 'Compra em {{store_name}}', 'sanitize' => \FILTER_SANITIZE_STRING ],
				'fix' => [ 'default' => false, 'sanitize' => \FILTER_VALIDATE_BOOLEAN ]
			]
		);

		if ( $settings->get('fix', false) )
		{
			$settings->set('store_name', $this->replacesChar($settings->get('store_name', '')));
			$settings->set('merchant_name', $this->replacesChar($settings->get('merchant_name')));
			$settings->set('merchant_city', $this->replacesChar($settings->get('merchant_city')));
		}

		// Validates the key
		try
		{ Parser::validate($settings->get('key_type'), $settings->get('key_value')); }
		catch ( InvalidPixKeyTypeException $e )
		{ throw new Exception(CoreConnector::__translate('Chave inválida: O tipo selecionado é incompatível.')); }
		catch ( InvalidPixKeyException $e )
		{ throw new Exception(\sprintf(CoreConnector::__translate('Chave inválida: O valor `%s` é incompatível com o tipo de chave selecionado.'), $settings->get('key_value'))); }
		catch ( Exception $e )
		{ throw new Exception(\sprintf(CoreConnector::__translate('Chave inválida: `%s`'), $e->getMessage())); }
		
		$settings->set('key_value', Parser::parse($settings->get('key_type'), $settings->get('key_value')));
	}

	/**
	 * Save discount section.
	 *
	 * @param array $data
	 * @since 2.0.0
	 * @return void
	 * @throws Exception
	 */
	protected function saveDiscount ( array $data )
	{
		/** @var KeyingBucket $settings */
		$settings = CoreConnector::settings()->getAndCreate('discount', new KeyingBucket());
		
		$this->prepare(
			$settings,
			$data,
			[
				'value' => [ 'default' => 0, 'sanitize' => \FILTER_VALIDATE_FLOAT ],
				'type' => [ 'default' => 'PERCENT', 'sanitize' => \FILTER_SANITIZE_STRING, 'allowed' => ['PERCENT', 'FIXED'] ],
				'label' => [ 'default' => 'Desconto Pix Aplicado', 'sanitize' => \FILTER_SANITIZE_STRING ]
			]
		);
	}

	/**
	 * Save receipts section.
	 *
	 * @param array $data
	 * @since 2.0.0
	 * @return void
	 * @throws Exception
	 */
	protected function saveReceipts ( array $data )
	{
		/** @var KeyingBucket $settings */
		$settings = CoreConnector::settings()->getAndCreate('receipts', new KeyingBucket());

		$this->prepare(
			$settings,
			$data,
			[
				'whatsapp_number' => [ 'default' => '', 'sanitize' => \FILTER_SANITIZE_STRING ],
				'whatsapp_message' => [ 'default' => 'Segue o comprovante para o pedido {{order_number}}:', 'sanitize' => \FILTER_SANITIZE_STRING ],
				'telegram_number' => [ 'default' => '', 'sanitize' => \FILTER_SANITIZE_STRING ],
				'telegram_message' => [ 'default' => 'Segue o comprovante para o pedido {{order_number}}:', 'sanitize' => \FILTER_SANITIZE_STRING ],
				'receipt_page' => [ 'default' => false, 'sanitize' => \FILTER_VALIDATE_BOOLEAN ],
				'shows_receipt' => [ 'default' => 'up', 'sanitize' => \FILTER_SANITIZE_STRING ],
			]
		);
	}

	/**
	 * Apply required and optional parses to
	 * $data array.
	 *
	 * @param array $data
	 * @param KeyingBucket $settings
	 * @param array $optional
	 * @since 2.0.0
	 * @return array
	 * @throws Exception
	 */
	public function prepare ( KeyingBucket $settings, array $data, array $fields = [] )
	{
		foreach ( $fields as $key => $meta )
		{
			$value = \filter_var( $data[$key] ?? null, $meta['sanitize'], FILTER_NULL_ON_FAILURE );

			if ( !$this->isFilled($value) || !\in_array($value, ($meta['allowed'] ?? [$value]), true) )
			{ $value = $settings->get($key, $meta['default']); }

			if ( !$this->isFilled($value) && ($meta['required'] ?? false) )
			{ throw new Exception(CoreConnector::__translate('Campo obrigatório não preenchido')); }

			$settings->set($key, $value);
		}
	} 

	/**
	 * Get default settings.
	 *
	 * @since 2.0.0
	 * @return KeyingBucket
	 */
	public static function defaults () : KeyingBucket
	{
		$settings = [
			'upgraded' => false,
			'upgraded_endpoints' => false,
			'global' => [
				'debug' => false
			],
			'gateway' => [
				'enabled' => false,
				'icon' => 'pix-payment-icon',
				'title' => __('Faça um Pix', 'wc-piggly-pix'),
				'description' => __('Você não precisa ter uma chave cadastrada. Pague os seus pedidos via Pix.', 'wc-piggly-pix'),
				'advanced_description' => true,
				'instructions' => __('Faça o pagamento via PIX. O pedido número {{order_number}} será liberado assim que a confirmação do pagamento for efetuada.', 'wc-piggly-pix'),
				'shows_qrcode' => true,
				'shows_copypast' => true,
				'shows_manual' => true,
				'shows_amount' => true
			],
			'orders' => [
				'receipt_status' => 'on-hold',
				'paid_status' => 'processing',
				'after_receipt' => '',
				'expires_after' => 24,
				'closest_lifetime' => 60,
				'cron_frequency' => 'daily',
				'decrease_stock' => true
			],
			'account' => [
				'store_name' => '',
				'bank' => '',
				'key_type' => '',
				'key_value' => '',
				'merchant_name' => '',
				'merchant_city' => '',
				'description' =>'Compra em {{store_name}}',
				'fix' => true
			],
			'discount' => [
				'value' => '',
				'type' => 'PERCENT',
				'label' => __('Desconto Pix Aplicado', 'wc-piggly-pix')
			],
			'receipts' => [
				'shows_receipt' => 'up',
				'receipt_page' => true,
				'whatsapp_number' => '',
				'whatsapp_message' => __('Segue o comprovante para o pedido {{order_number}}:', 'wc-piggly-pix'),
				'telegram_number' => '',
				'telegram_message' => __('Segue o comprovante para o pedido {{order_number}}:', 'wc-piggly-pix')
			]
		];

		return (new KeyingBucket())->import($settings);
	}

	/**
	 * Return if $var is filled.
	 *
	 * @param mixed $var
	 * @since 2.0.0
	 * @return boolean
	 */
	protected function isFilled ( $var )
	{ return !\is_null($var) && $var !== ''; }

	/**
	 * Replaces any invalid character to a valid one.
	 * 
	 * @since 1.1.0
	 * @param string $str
	 * @return string
	 */
	private function replacesChar ( string $str ) : string
	{
		$invalid = array("Á", "À", "Â", "Ä", "Ă", "Ā", "Ã", "Å", "Ą", "Æ", "Ć", "Ċ", "Ĉ", "Č", "Ç", "Ď", "Đ", "Ð", "É", "È", "Ė", "Ê", "Ë", "Ě", "Ē", "Ę", "Ə", "Ġ", "Ĝ", "Ğ", "Ģ", "á", "à", "â", "ä", "ă", "ā", "ã", "å", "ą", "æ", "ć", "ċ", "ĉ", "č", "ç", "ď", "đ", "ð", "é", "è", "ė", "ê", "ë", "ě", "ē", "ę", "ə", "ġ", "ĝ", "ğ", "ģ", "Ĥ", "Ħ", "Í", "Ì", "İ", "Î", "Ï", "Ī", "Į", "Ĳ", "Ĵ", "Ķ", "Ļ", "Ł", "Ń", "Ň", "Ñ", "Ņ", "Ó", "Ò", "Ô", "Ö", "Õ", "Ő", "Ø", "Ơ", "Œ", "ĥ", "ħ", "ı", "í", "ì", "î", "ï", "ī", "į", "ĳ", "ĵ", "ķ", "ļ", "ł", "ń", "ň", "ñ", "ņ", "ó", "ò", "ô", "ö", "õ", "ő", "ø", "ơ", "œ", "Ŕ", "Ř", "Ś", "Ŝ", "Š", "Ş", "Ť", "Ţ", "Þ", "Ú", "Ù", "Û", "Ü", "Ŭ", "Ū", "Ů", "Ų", "Ű", "Ư", "Ŵ", "Ý", "Ŷ", "Ÿ", "Ź", "Ż", "Ž", "ŕ", "ř", "ś", "ŝ", "š", "ş", "ß", "ť", "ţ", "þ", "ú", "ù", "û", "ü", "ŭ", "ū", "ů", "ų", "ű", "ư", "ŵ", "ý", "ŷ", "ÿ", "ź", "ż", "ž");
		$valid   = array("A", "A", "A", "A", "A", "A", "A", "A", "A", "AE", "C", "C", "C", "C", "C", "D", "D", "D", "E", "E", "E", "E", "E", "E", "E", "E", "G", "G", "G", "G", "G", "a", "a", "a", "a", "a", "a", "a", "a", "a", "ae", "c", "c", "c", "c", "c", "d", "d", "d", "e", "e", "e", "e", "e", "e", "e", "e", "g", "g", "g", "g", "g", "H", "H", "I", "I", "I", "I", "I", "I", "I", "IJ", "J", "K", "L", "L", "N", "N", "N", "N", "O", "O", "O", "O", "O", "O", "O", "O", "CE", "h", "h", "i", "i", "i", "i", "i", "i", "i", "ij", "j", "k", "l", "l", "n", "n", "n", "n", "o", "o", "o", "o", "o", "o", "o", "o", "o", "R", "R", "S", "S", "S", "S", "T", "T", "T", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "W", "Y", "Y", "Y", "Z", "Z", "Z", "r", "r", "s", "s", "s", "s", "B", "t", "t", "b", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "w", "y", "y", "y", "z", "z", "z");
		$str     = str_ireplace( $invalid, $valid, $str );
		$str     = preg_replace('/[\!\.\,\@\#\$\%\&\*\(\)\/\?\{\}]+/', ' ', $str);
		$str     = preg_replace('/[\s]{2,}/', ' ', $str);

		return $str;
	}
}