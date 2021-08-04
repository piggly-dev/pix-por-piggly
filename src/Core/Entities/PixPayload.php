<?php
namespace Piggly\WooPixGateway\Core\Entities;

use Piggly\Pix\Parser;
use WC_Order;

class PixPayload
{
	/**
	 * Order meta payload.
	 * 
	 * @since 2.0.0
	 * @var string
	 */
	const ORDER_META_PAYLOAD = '_wc_piggly_pix';

	/**
	 * Output data payload.
	 * @var array
	 * @since 2.0.0
	 */
	protected $_data;

	/**
	 * Order.
	 * @var WC_Order
	 * @since 2.0.0
	 */
	protected $_order;

	/**
	 * Fill payload with $order.
	 * 
	 * @param WC_Order $order
	 * @since 2.0.0
	 * @return self
	 */
	public static function fill ( WC_Order $order )
	{ return (new self())->load($order); }

	/**
	 * Create a new payload with $order.
	 * 
	 * @param WC_Order $order
	 * @return self
	 */
	public static function create ( WC_Order $order )
	{ return (new self())->load($order)->new(); }

	/**
	 * Load payload from $order by getting self::ORDER_META_PAYLOAD.
	 * If payload is found, set $this->_data to payload data.
	 * 
	 * @param WC_Order $order
	 * @since 2.0.0
	 * @return self
	 */
	public function load ( WC_Order $order )
	{
		$this->_data = [];
		$payload     = $order->get_meta(static::ORDER_META_PAYLOAD);

		if ( !empty($payload) )
		{ $this->_data = $payload; }		

		$this->_order = $order;
		return $this;
	}

	/**
	 * Export payload to an array
	 * 
	 * @since 2.0.0
	 * @return array
	 */
	public function export () : array
	{
		return [
			'code' => $this->_data['pix_code'],
			'qr' => $this->_data['pix_qr'],
			'key_value' => $this->_data['key_value'],
			'key_type' => $this->_data['key_alias'],
			'identifier' => $this->_data['identifier'],
			'store_name' => $this->_data['store_name'],
			'merchant_name' => $this->_data['merchant_name'],
			'merchant_city' => $this->_data['merchant_city']
		];
	}

	/**
	 * Save the current $this->_data to order meta as
	 * self::ORDER_META_PAYLOAD.
	 * 
	 * @filter wpgly_pix_before_save_pix_metadata
	 * @since 2.0.0
	 * @return bool
	 */
	public function save () : bool
	{
		if ( empty($this->_order) || empty($this->_data) )
		{ return false; }

		$this->_order->update_meta_data(
			static::ORDER_META_PAYLOAD,
			apply_filters(
				'wpgly_pix_before_save_pix_metadata',
				$this,
				$this->_order->get_id(),
				$this->_order
			)
		);

		$this->_order->save();
		return true;
	}

	/**
	 * Set the pix raw code.
	 *
	 * @param string $pixCode
	 * @since 2.0.0
	 * @return PixPayload
	 */
	public function setPixCode ( string $pixCode )
	{ return $this->set('pix_code', $pixCode); }

	/**
	 * Get pix raw code.
	 *
	 * @since 2.0.0
	 * @return string|null
	 */
	public function getPixCode () : ?string
	{ return $this->get('pix_code'); }

	/**
	 * Set the pix qr code url and path.
	 *
	 * @param string $url
	 * @param string $path
	 * @since 2.0.0
	 * @return PixPayload
	 */
	public function setQrCode ( string $url, string $path )
	{ return $this->set('pix_qr', $url)->set('qr_path', $path); }

	/**
	 * Get pix qr code url.
	 *
	 * @since 2.0.0
	 * @return string|null
	 */
	public function getQrCodeUrl () : ?string
	{ return $this->get('pix_qr'); }

	/**
	 * Get pix qr code path.
	 *
	 * @since 2.0.0
	 * @return string|null
	 */
	public function getQrCodePath () : ?string
	{ return $this->get('qr_path'); }

	/**
	 * Set the pix amount.
	 *
	 * @param float $amount
	 * @since 2.0.0
	 * @return PixPayload
	 */
	public function setAmount ( float $amount )
	{ return $this->set('amount', $amount); }

	/**
	 * Get pix amount.
	 *
	 * @since 2.0.0
	 * @return string|null
	 */
	public function getAmount () : ?float
	{ return $this->get('amount'); }

	/**
	 * Set the pix key data.
	 *
	 * @param string $type
	 * @param string $value
	 * @since 2.0.0
	 * @return PixPayload
	 */
	public function setPixKey ( string $type, string $value )
	{ return $this->set('key_type', $type)->set('key_value', $value)->set('key_alias', Parser::getAlias($type)); }

	/**
	 * Get pix  key type.
	 *
	 * @since 2.0.0
	 * @return string|null
	 */
	public function getPixKeyType () : ?string
	{ return $this->get('key_type'); }

	/**
	 * Get pix key value.
	 *
	 * @since 2.0.0
	 * @return string|null
	 */
	public function getPixKeyValue () : ?string
	{ return $this->get('key_value'); }

	/**
	 * Get pix key alias.
	 *
	 * @since 2.0.0
	 * @return string|null
	 */
	public function getPixKeyAlias () : ?string
	{ return $this->get('key_alias'); }

	/**
	 * Set the pix identifier.
	 *
	 * @param string $identifier
	 * @since 2.0.0
	 * @return PixPayload
	 */
	public function setIdentifier ( string $identifier )
	{ return $this->set('identifier', $identifier); }

	/**
	 * Get pix identifier.
	 *
	 * @since 2.0.0
	 * @return string|null
	 */
	public function getIdentifier () : ?string
	{ return $this->get('identifier'); }

	/**
	 * Set the pix merchant data.
	 *
	 * @param string $storeName
	 * @param string $merchantName
	 * @param string $merchantCity
	 * @since 2.0.0
	 * @return PixPayload
	 */
	public function setMerchantData ( string $storeName, string $merchantName, string $merchantCity )
	{ return $this->set('store_name', $storeName)->set('merchant_name', $merchantName)->set('merchant_city', $merchantCity); }

	/**
	 * Get pix store name.
	 *
	 * @since 2.0.0
	 * @return string|null
	 */
	public function getStoreName () : ?string
	{ return $this->get('store_name'); }

	/**
	 * Get pix merchant name.
	 *
	 * @since 2.0.0
	 * @return string|null
	 */
	public function getMerchantName () : ?string
	{ return $this->get('merchant_name'); }

	/**
	 * Get pix merchant city.
	 *
	 * @since 2.0.0
	 * @return string|null
	 */
	public function getMerchantCity () : ?string
	{ return $this->get('merchant_city'); }

	/**
	 * Set a new data $key to payload with $value.
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @since 2.0.0
	 * @return self
	 */
	public function set ( string $key, $value )
	{ $this->_data[$key] = $value; return $this; }

	/**
	 * Get a $key data in payload or return $default
	 * if $key not found.
	 * 
	 * @param string $key
	 * @param mixed $default
	 * @since 2.0.0
	 * @return mixed
	 */
	public function get ( string $key, $default = null )
	{ return $this->_data[$key] ?? $default; }

	/**
	 * Get all $data.
	 * 
	 * @since 2.0.0
	 * @return array
	 */
	public function getAll () : array
	{ return $this->_data ?? []; }
	
	/**
	 * Return if payload has data.
	 *
	 * @since 2.0.0
	 * @return boolean
	 */
	public function hasData () : bool
	{ return !empty($this->_data); }

	/**
	 * Get order object associated to pix payload.
	 * 
	 * @since 2.0.0
	 * @return WC_Order|null
	 */
	public function getOrder () : ?WC_Order
	{ return $this->_order; }
}