<?php
namespace Piggly\WooPixGateway\Core\Managers;

use Exception;
use Piggly\WooPixGateway\CoreHelper;
use Piggly\Wordpress\Core\Scaffold\Internationalizable;
use Piggly\Wordpress\Settings\KeyingBucket;
use Piggly\Wordpress\Settings\NonKeyingBucket;

/**
 * Manages all plugin settings.
 * 
 * @package \Piggly\WpBDM
 * @subpackage \Piggly\WpBDM\Core\Managers
 * @version 1.0.0
 * @since 1.0.0
 * @category Managers
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license PGLY <key>
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
class SettingsManager extends Internationalizable
{
	/**
	 * All sections available to settings.
	 * 
	 * @var array
	 * @since 1.0.0
	 */
	const SETTINGS_SECTIONS = [
		'global',
		'customization',
		'discount',
		'orders',
		'rating',
		'users',
		'wallet'
	];

	/**
	 * Export settings as array.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function getSettings () : array
	{ return $this->settings()->bucket()->export() ?? []; }

	/**
	 * Save and parse settings data by $section.
	 * $section must be a value of static::SETTINGS_SECTION.
	 *
	 * @param string $section
	 * @param array $data
	 * @since 1.0.0
	 * @return void
	 * @throws Exception
	 */
	public function saveSettings ( string $section, array $data )
	{
		if ( !\in_array($section, static::SETTINGS_SECTIONS, true) )
		{ throw new Exception($this->__translate('Não foi possível salvar: Configurações inválidas.')); }

		switch ( $section )
		{
			case 'global':
				$this->saveGlobal($data);
				break;
			case 'customization':
				$this->saveCustomization($data);
				break;
			case 'discount':
				$this->saveDiscount($data);
				break;
			case 'orders':
				$this->saveOrders($data);
				break;
			case 'rating':
				$this->saveRating($data);
				break;
			case 'users':
				$this->saveUsers($data);
				break;
			case 'wallet':
				$this->saveWallet($data);
				break;
		}

		// Always save
		$this->_plugin->settings()->save();
	}

	/**
	 * Save wallet section.
	 *
	 * @param array $data
	 * @since 1.0.0
	 * @return void
	 * @throws Exception
	 */
	protected function saveGlobal ( array $data )
	{
		/** @var KeyingBucket $settings */
		$settings = $this->_plugin->settings()->bucket();

		$this->prepare(
			$settings,
			$data,
			[
				'debug' => [ 'default' => false, 'sanitize' => \FILTER_VALIDATE_BOOL ],
				'log_api' => [ 'default' => false, 'sanitize' => \FILTER_VALIDATE_BOOL ],
				'bypass_balance' => [ 'default' => false, 'sanitize' => \FILTER_VALIDATE_BOOL ],
				'bypass_transfer' => [ 'default' => false, 'sanitize' => \FILTER_VALIDATE_BOOL ]
			]
		);
	}

	/**
	 * Save wallet section.
	 *
	 * @param array $data
	 * @since 1.0.0
	 * @return void
	 * @throws Exception
	 */
	protected function saveCustomization ( array $data )
	{
		/** @var KeyingBucket $settings */
		$settings = $this->_plugin->settings()->bucket()->getAndCreate('customizations', new KeyingBucket());

		$this->prepare(
			$settings,
			$data,
			[
				'discount_applied' => [ 'default' => '', 'sanitize' => \FILTER_SANITIZE_STRING ],
				'tx_user' => [ 'default' => '', 'sanitize' => \FILTER_SANITIZE_STRING ],
				'tx_expired' => [ 'default' => '', 'sanitize' => \FILTER_SANITIZE_STRING ],
				'tx_minimum' => [ 'default' => '', 'sanitize' => \FILTER_SANITIZE_STRING ],
				'tx_maximum' => [ 'default' => '', 'sanitize' => \FILTER_SANITIZE_STRING ],
				'tx_balance' => [ 'default' => '', 'sanitize' => \FILTER_SANITIZE_STRING ],
				'tx_notfound' => [ 'default' => '', 'sanitize' => \FILTER_SANITIZE_STRING ]
			]
		);
	}

	/**
	 * Save discount section.
	 *
	 * @param array $data
	 * @since 1.0.0
	 * @return void
	 * @throws Exception
	 */
	protected function saveDiscount ( array $data )
	{
		/** @var KeyingBucket $settings */
		$settings = $this->_plugin->settings()->bucket()->getAndCreate('discount', new KeyingBucket());

		// Rule settings
		/** @var NonKeyingBucket $rules */
		$rules = $settings->getAndCreate('rules', new NonKeyingBucket());

		if ( $rules instanceof KeyingBucket ) $rules = new NonKeyingBucket();
		
		$rules->set($data['rules']);
	}

	/**
	 * Save orders section.
	 *
	 * @param array $data
	 * @since 1.0.0
	 * @return void
	 * @throws Exception
	 */
	protected function saveOrders ( array $data )
	{
		/** @var KeyingBucket $settings */
		$settings = $this->_plugin->settings()->bucket()->getAndCreate('orders', new KeyingBucket());

		$this->prepare(
			$settings,
			$data,
			[
				'session_timing' => [ 'default' => 300, 'sanitize' => \FILTER_VALIDATE_INT ],
				'buying_minimum' => [ 'default' => 0, 'sanitize' => \FILTER_VALIDATE_FLOAT ],
				'buying_maximum' => [ 'default' => 0, 'sanitize' => \FILTER_VALIDATE_FLOAT ],
				'transaction_frequency' => [ 'default' => 'everyfifteen', 'sanitize' => \FILTER_SANITIZE_STRING, 'allowed' => Cron::AVAILABLE_FREQUENCIES ],
				'transaction_limit' => [ 'default' => 20, 'sanitize' => \FILTER_VALIDATE_INT ]
			]
		);

		Cron::create($this->_plugin);
	}

	/**
	 * Save rating section.
	 *
	 * @param array $data
	 * @since 1.0.0
	 * @return void
	 * @throws Exception
	 */
	protected function saveRating ( array $data )
	{
		/** @var KeyingBucket $settings */
		$settings = $this->_plugin->settings()->bucket()->getAndCreate('rating', new KeyingBucket());
		
		$this->prepare(
			$settings,
			$data,
			[
				'rating_frequency' => [ 'default' => 'hourly', 'sanitize' => \FILTER_SANITIZE_STRING, 'allowed' => Cron::AVAILABLE_FREQUENCIES ],
				'showing_frequency' => [ 'default' => 'daily', 'sanitize' => \FILTER_SANITIZE_STRING, 'allowed' => Cron::AVAILABLE_FREQUENCIES ],
				'showing_limit' => [ 'default' => 30, 'sanitize' => \FILTER_VALIDATE_INT ]
			]
		);
		
		Cron::create($this->_plugin);
	}

	/**
	 * Save users section.
	 *
	 * @param array $data
	 * @since 1.0.0
	 * @return void
	 * @throws Exception
	 */
	protected function saveUsers ( array $data )
	{
		/** @var KeyingBucket $settings */
		$settings = $this->_plugin->settings()->bucket()->getAndCreate('users', new KeyingBucket());
		
		$this->prepare(
			$settings,
			$data,
			[
				'full_name' => [ 'default' => false, 'sanitize' => \FILTER_VALIDATE_BOOL ],
				'otp_enabled' => [ 'default' => true, 'sanitize' => \FILTER_VALIDATE_BOOL ],
				'otp_timing' => [ 'default' => 60, 'sanitize' => \FILTER_VALIDATE_INT ],
				'otp_length' => [ 'default' => 6, 'sanitize' => \FILTER_VALIDATE_INT ]
			]
		);
	}

	/**
	 * Save wallet section.
	 *
	 * @param array $data
	 * @since 1.0.0
	 * @return void
	 * @throws Exception
	 */
	protected function saveWallet ( array $data )
	{
		/** @var KeyingBucket $settings */
		$settings = $this->_plugin->settings()->bucket()->getAndCreate('wallet', new KeyingBucket());

		$settings->set('pub_key_constant', defined('WPGLY_BDM_ADDRESS_PUB_KEY'));
		$settings->set('priv_key_constant', defined('WPGLY_BDM_ADDRESS_PRIV_KEY'));

		if ( $settings->get('pub_key_constant') )
		{ $data['pub_key'] = '***********************'; }
		
		if ( $settings->get('priv_key_constant') )
		{ $data['priv_key'] = '***********************'; }

		$this->prepare(
			$settings,
			$data,
			[
				'address' => [ 'default' => '', 'required' => true, 'sanitize' => \FILTER_SANITIZE_STRING ],
				'pub_key' => [ 'default' => '', 'required' => true, 'sanitize' => \FILTER_SANITIZE_STRING ],
				'priv_key' => [ 'default' => '', 'required' => true, 'sanitize' => \FILTER_SANITIZE_STRING ],
				'balance_frequency' => [ 'default' => 'daily', 'sanitize' => \FILTER_SANITIZE_STRING, 'allowed' => Cron::AVAILABLE_FREQUENCIES ],
				'effective_balance_alert' => [ 'default' => 0, 'sanitize' => \FILTER_VALIDATE_FLOAT ],
				'reserved_balance_alert' => [ 'default' => 0, 'sanitize' => \FILTER_VALIDATE_FLOAT ],
				'balance_margin' => [ 'default' => 0, 'sanitize' => \FILTER_VALIDATE_FLOAT ]
			]
		);

		Cron::createBalance($this->_plugin);
	}

	/**
	 * Apply required and optional parses to
	 * $data array.
	 *
	 * @param array $data
	 * @param KeyingBucket $settings
	 * @param array $optional
	 * @since 1.0.0
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
			{ throw new Exception($this->__translate('Campo obrigatório não preenchido')); }

			$settings->set($key, $value);
		}
	} 

	/**
	 * Get default settings.
	 *
	 * @since 1.0.0
	 * @return KeyingBucket
	 */
	public static function defaults () : KeyingBucket
	{
		$settings = [
			'global' => [
				'debug' => false
			],
			'gateway' => [
				'enabled' => false,
				'icon' => 'pix-payment-icon',
				'title' => CoreHelper::__translate('Faça um Pix'),
				'description' => CoreHelper::__translate('Você não precisa ter uma chave cadastrada. Pague os seus pedidos via Pix.'),
				'advanced_description' => true,
				'instructions' => CoreHelper::__translate('Faça o pagamento via PIX. O pedido número {{order_number}} será liberado assim que a confirmação do pagamento for efetuada.'),
				'shows' => [
					'qrcode' => false,
					'copypast' => true,
					'manual' => true,
				]
			],
			'emails' => [
				'model' => 'WC_Email_Customer_On_Hold_Order',
				'position' => 'before'
			],
			'orders' => [
				'waiting_status' => 'on-hold',
				'receipt_status' => 'on-hold',
				'paid_status' => 'processing',
				'after_receipt' => '',
				'expires_after' => '',
				'hide_in_order' => false,
				'cron_frequency' => 'everyfifteen'
			],
			'account' => [
				'store_name' => '',
				'bank' => '',
				'key_type' => '',
				'key_value' => '',
				'merchant_name' => '',
				'merchant_city' => '',
				'identifier' => '{{order_number}}',
				'fix' => true,
				'regenerate' => false,
			],
			'discount' => [
				'value' => '',
				'type' => 'PERCENT',
				'label' => CoreHelper::__translate('Desconto Pix Aplicado')
			],
			'receipts' => [
				'whatsapp' => [
					'number' => '',
					'message' => CoreHelper::__translate('Segue o comprovante para o pedido {{order_number}}:'),
				],
				'telegram' => [
					'number' => '',
					'message' => CoreHelper::__translate('Segue o comprovante para o pedido {{order_number}}:'),
				]
			]
		];

		return (new KeyingBucket())->import($settings);
	}

	/**
	 * Return if $var is filled.
	 *
	 * @param mixed $var
	 * @since 1.0.0
	 * @return boolean
	 */
	protected function isFilled ( $var )
	{ return !\is_null($var) && $var !== ''; }
}