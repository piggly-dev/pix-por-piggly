<?php
namespace Piggly\WooPixGateway\Core\Entities;

use DateInterval;
use DateTime;
use Exception;
use Piggly\WooPixGateway\Core\Exceptions\DatabaseException;
use Piggly\WooPixGateway\Core\Processors\QrCodeProcessor;
use Piggly\WooPixGateway\Core\Repo\PixRepo;
use Piggly\WooPixGateway\CoreConnector;
use Piggly\WooPixGateway\Vendor\Piggly\Pix\AbstractPayload;
use Piggly\WooPixGateway\Vendor\Piggly\Pix\Parser;
use Piggly\WooPixGateway\Vendor\Piggly\Pix\StaticPayload;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Settings\KeyingBucket;
use RuntimeException;
use stdClass;
use WC_Order;

/**
 * Pix entity with all data associated to pix.
 * 
 * @package \Piggly\WooPixGateway
 * @subpackage \Piggly\WooPixGateway\Core\Entities
 * @version 2.0.0
 * @since 2.0.0
 * @category Entities
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license GPLv3 or later
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
class PixEntity
{
	/**
	 * @var string Regular pix type.
	 * @since 2.0.0
	 */
	const TYPE_STATIC = 'static';

	/**
	 * @var string Pix is underpayment expected.
	 * @since 2.0.0
	 */
	const TYPE_COB = 'cob';

	/**
	 * @var string Pix is overpayment expected.
	 * @since 2.0.0
	 */
	const TYPE_COBV = 'cobv';

	/**
	 * @var array All pix statuses.
	 * @since 2.0.0
	 */
	const TYPES = [
		self::TYPE_STATIC,
		self::TYPE_COB,
		self::TYPE_COBV
	];

	/**
	 * @var string Pix is created.
	 * @since 2.0.0
	 */
	const STATUS_CREATED = 'created';

	/**
	 * @var string Pix is waiting.
	 * @since 2.0.0
	 */
	const STATUS_WAITING = 'waiting';

	/**
	 * @var string Pix is expired.
	 * @since 2.0.0
	 */
	const STATUS_EXPIRED = 'expired';

	/**
	 * @var string Pix is paid.
	 * @since 2.0.0
	 */
	const STATUS_PAID = 'paid';

	/**
	 * @var string Pix is cancelled.
	 * @since 2.0.0
	 */
	const STATUS_CANCELLED = 'cancelled';

	/**
	 * @var array All pix statuses.
	 * @since 2.0.0
	 */
	const STATUSES = [
		self::STATUS_CREATED,
		self::STATUS_WAITING,
		self::STATUS_EXPIRED,
		self::STATUS_PAID,
		self::STATUS_CANCELLED
	];

	/**
	 * Pix id.
	 *
	 * @var string
	 * @since 2.0.0
	 */
	protected $pixid = null;

	/**
	 * Pix e2eid.
	 *
	 * @var string
	 * @since 2.0.0
	 */
	protected $e2eid = null;

	/**
	 * Pix store name.
	 *
	 * @var string
	 * @since 2.0.0
	 */
	protected $store_name = null;

	/**
	 * Pix merchant name.
	 *
	 * @var string
	 * @since 2.0.0
	 */
	protected $merchant_name = null;

	/**
	 * Pix merchant city.
	 *
	 * @var string
	 * @since 2.0.0
	 */
	protected $merchant_city = null;

	/**
	 * Pix key.
	 *
	 * @var string
	 * @since 2.0.0
	 */
	protected $key = null;

	/**
	 * Pix key type.
	 *
	 * @var string
	 * @since 2.0.0
	 */
	protected $key_type = null;

	/**
	 * Pix description.
	 *
	 * @var string|null
	 * @since 2.0.0
	 */
	protected $description = null;

	/**
	 * Pix discount.
	 *
	 * @var float|null
	 * @since 2.0.0
	 */
	protected $discount = null;

	/**
	 * Pix amount.
	 *
	 * @var float
	 * @since 2.0.0
	 */
	protected $amount = 0;

	/**
	 * Pix bank.
	 *
	 * @var integer|null
	 * @since 2.0.0
	 */
	protected $bank = null;

	/**
	 * Pix order.
	 *
	 * @var WC_Order|null
	 * @since 2.0.0
	 */
	protected $oid = null;

	/**
	 * Pix type.
	 *
	 * @var string
	 * @since 2.0.0
	 */
	protected $type = 'static';

	/**
	 * Pix status.
	 *
	 * @var string
	 * @since 2.0.0
	 */
	protected $status = 'created';

	/**
	 * Pix brcode.
	 *
	 * @var string|null
	 * @since 2.0.0
	 */
	protected $brcode = null;

	/**
	 * QRCode data with path
	 * and url.
	 *
	 * @var array
	 * @since 2.0.0
	 */
	protected $qrcode = [];

	/**
	 * Receipt data with path
	 * and url.
	 *
	 * @var array
	 * @since 2.0.0
	 */
	protected $receipt = [];

	/**
	 * Pix metadata.
	 *
	 * @var array
	 * @since 2.0.0
	 */
	protected $metadata = [];

	/**
	 * Expires at.
	 *
	 * @var DateTime
	 * @since 2.0.0
	 */
	protected $expires_at = null;
	
	/**
	 * Updated at.
	 *
	 * @var DateTime
	 * @since 2.0.0
	 */
	protected $update_at = null;

	/**
	 * Created at.
	 *
	 * @var DateTime
	 * @since 2.0.0
	 */
	protected $created_at = null;

	/**
	 * If is loaded.
	 *
	 * @var bool
	 * @since 2.0.0
	 */
	protected $loaded = false;
	
	/**
	 * Create a new pix entity.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function __construct ()
	{ $this->txid = $this->generateTxid(25); }


	/**
	 * Set pix id.
	 *
	 * @param string $pixid
	 * @since 2.0.0
	 * @return PixEntity
	 */
	public function setTxid ( string $pixid )
	{ $this->txid = $pixid; return $this; }

	/**
	 * Get pix id.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function getTxid () : string
	{ return $this->txid; }

	/**
	 * Set pix e2eid.
	 *
	 * @param string $e2eid
	 * @since 2.0.0
	 * @return PixEntity
	 */
	public function setE2eid ( string $e2eid )
	{ $this->e2eid = $e2eid; return $this; }

	/**
	 * Get pix e2eid.
	 *
	 * @since 2.0.0
	 * @return string|null
	 */
	public function getE2eid () : ?string
	{ return $this->e2eid; }

	/**
	 * Set pix merchant data.
	 *
	 * @param string $store_name
	 * @param string $merchant_name
	 * @param string $merchant_city
	 * @since 2.0.0
	 * @return PixEntity
	 */
	public function setMerchantData ( string $store_name, string $merchant_name, string $merchant_city )
	{ return $this->setStoreName($store_name)->setMerchantName($merchant_name)->setMerchantCity($merchant_city); }

	/**
	 * Set pix store name.
	 *
	 * @param string $store_name
	 * @since 2.0.0
	 * @return PixEntity
	 */
	public function setStoreName ( string $store_name )
	{ $this->store_name = $store_name; return $this; }

	/**
	 * Get pix store name.
	 *
	 * @since 2.0.0
	 * @return string|null
	 */
	public function getStoreName () : ?string
	{ return $this->store_name; }

	/**
	 * Set pix merchant name.
	 *
	 * @param string $merchant_name
	 * @since 2.0.0
	 * @return PixEntity
	 */
	public function setMerchantName ( string $merchant_name )
	{ $this->merchant_name = $merchant_name; return $this; }

	/**
	 * Get pix merchant name.
	 *
	 * @since 2.0.0
	 * @return string|null
	 */
	public function getMerchantName () : ?string
	{ return $this->merchant_name; }

	/**
	 * Set pix merchant city.
	 *
	 * @param string $merchant_city
	 * @since 2.0.0
	 * @return PixEntity
	 */
	public function setMerchantCity ( string $merchant_city )
	{ $this->merchant_city = $merchant_city; return $this; }

	/**
	 * Get pix merchant city.
	 *
	 * @since 2.0.0
	 * @return string|null
	 */
	public function getMerchantCity () : ?string
	{ return $this->merchant_city; }

	/**
	 * Set the pix key data.
	 *
	 * @param string $type
	 * @param string $value
	 * @since 2.0.0
	 * @return PixEntity
	 */
	public function setPixKey ( string $type, string $value )
	{ 
		$this->key = $value;
		$this->key_type = $type; 
		return $this; 
	}

	/**
	 * Get pix key type.
	 *
	 * @since 2.0.0
	 * @return string|null
	 */
	public function getPixKeyType () : ?string
	{ return $this->key_type; }

	/**
	 * Get pix key value.
	 *
	 * @since 2.0.0
	 * @return string|null
	 */
	public function getPixKeyValue () : ?string
	{ return $this->key; }

	/**
	 * Get pix key alias.
	 *
	 * @since 2.0.0
	 * @return string|null
	 */
	public function getPixKeyAlias () : ?string
	{ return empty($this->key_type) ? null : Parser::getAlias($this->key_type); }

	/**
	 * Set pix description.
	 *
	 * @param string $description
	 * @since 2.0.0
	 * @return PixEntity
	 */
	public function setDescription ( string $description )
	{ $this->description = $description; return $this; }

	/**
	 * Get pix description.
	 * 
	 * @since 2.0.0
	 * @return string
	 */
	public function getDescription () : ?string
	{ return $this->description; }

	/**
	 * Set pix amount.
	 *
	 * @param float $amount
	 * @since 2.0.0
	 * @return PixEntity
	 */
	public function setAmount ( float $amount )
	{ $this->amount = $amount; return $this; }

	/**
	 * Get pix amount.
	 *
	 * @since 2.0.0
	 * @return float
	 */
	public function getAmount () : float
	{ return $this->amount; }

	/**
	 * Set pix discount.
	 *
	 * @param float $discount
	 * @since 2.0.0
	 * @return PixEntity
	 */
	public function setDiscount ( float $discount )
	{ $this->discount = $discount; return $this; }

	/**
	 * Get pix discount.
	 *
	 * @since 2.0.0
	 * @return float
	 */
	public function getDiscount () : float
	{ return $this->discount; }

	/**
	 * Set pix bank.
	 *
	 * @param float $bank
	 * @since 2.0.0
	 * @return PixEntity
	 */
	public function setBank ( int $bank )
	{ $this->bank = $bank; return $this; }

	/**
	 * Get pix bank.
	 *
	 * @since 2.0.0
	 * @return int|null
	 */
	public function getBank () : ?int
	{ return $this->bank; }

	/**
	 * Set pix order. When $oid is a integer
	 * try to load order data. When $oid is not found
	 * as a order, then it will not set a order
	 * to the pix entity.
	 *
	 * @param WC_Order|integer $oid
	 * @since 2.0.0
	 * @return PixEntity
	 */
	public function setOrder ( $oid )
	{ 
		$order = $oid instanceof WC_Order ? $oid : \wc_get_order($oid); 

		if ( $order !== false ) 
		{ $this->oid = $order; }
		
		return $this; 
	}

	/**
	 * Get pix order.
	 * It may be null. Try to load when not loaded.
	 *
	 * @since 2.0.0
	 * @return WC_Order|null
	 */
	public function getOrder () : ?WC_Order
	{ 
		// When order is set and is an integer
		// try to load it as a order, returning
		// null if order is not found
		if ( !empty($this->oid) || \is_int($this->oid) )
		{
			$order = \wc_get_order($this->oid); 

			if ( $order !== false ) 
			{ 
				$this->oid = $order; 
				return $this->oid;
			}

			return null;
		}
	
		return null; 
	}

	/**
	 * Set pix status.
	 *
	 * @param string $status
	 * @since 2.0.0
	 * @return PixEntity
	 * @throws RuntimeException when status is invalid.
	 */
	public function setStatus ( string $status )
	{
		if ( !\in_array($status, static::STATUSES, true) )
		{ throw new RuntimeException(\sprintf('Status must be one of following: `%s`.', implode('`, `', static::STATUSES))); }

		$this->status = $status; 
		return $this; 
	}

	/**
	 * Set the new $status and update pix
	 * entity by saving it.
	 *
	 * @action pgly_wc_piggly_pix_updated_pix_status
	 * @param string $status
	 * @return PixEntity
	 * @throws RuntimeException|DatabaseException
	 */
	public function updateStatus ( string $status )
	{
		if ( !\in_array($status, static::STATUSES, true) )
		{ throw new RuntimeException(\sprintf('Status must be one of following: `%s`.', implode('`, `', static::STATUSES))); }

		\do_action('pgly_wc_piggly_pix_updated_pix_status', $this, $this->status, $status);
		$this->status = $status; 
		return $this->save(); 
	}

	/**
	 * Get pix status.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function getStatus () : string
	{ return $this->status; }

	/**
	 * Get pix status label.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function getStatusLabel () : string
	{
		switch ( $this->status )
		{
			case static::STATUS_CREATED:
			case static::STATUS_WAITING:
				return CoreConnector::__translate('Aguardando');
				break;
			case static::STATUS_EXPIRED:
				return CoreConnector::__translate('Expirado');
				break;
			case static::STATUS_PAID:
				return CoreConnector::__translate('Pago');
				break;
			case static::STATUS_CANCELLED:
				return CoreConnector::__translate('Cancelado');
				break;
		}

		return CoreConnector::__translate('Aguardando');
	}

	/**
	 * Get pix status color.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function getStatusColor () : string
	{
		switch ( $this->status )
		{
			case static::STATUS_CREATED:
			case static::STATUS_WAITING:
				return 'warning';
				break;
			case static::STATUS_EXPIRED:
			case static::STATUS_CANCELLED:
				return 'danger';
				break;
			case static::STATUS_PAID:
				return 'success';
				break;
		}

		return 'neutral';
	}

	/**
	 * Check if this pix status is equal
	 * to $status.
	 *
	 * @param string $status
	 * @since 2.0.0
	 * @return boolean
	 */
	public function isStatus ( string $status ) : bool
	{ return $this->status === $status; }

	/**
	 * Set pix type.
	 *
	 * @param string $type
	 * @since 2.0.0
	 * @return PixEntity
	 * @throws RuntimeException when type is invalid.
	 */
	public function setType ( string $type )
	{
		if ( !\in_array($type, static::TYPES, true) )
		{ throw new RuntimeException(\sprintf('Type must be one of following: `%s`.', implode('`, `', static::TYPES))); }

		$this->type = $type; 
		return $this; 
	}

	/**
	 * Get pix type.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function getType () : string
	{ return $this->type; }

	/**
	 * Get pix status label.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function getTypeLabel () : string
	{
		switch ( $this->type )
		{
			case static::TYPE_STATIC:
				return CoreConnector::__translate('Estático');
				break;
			case static::TYPE_COB:
				return CoreConnector::__translate('Cobrança Imediata');
				break;
			case static::TYPE_COBV:
				return CoreConnector::__translate('Cobrança com Vencimento');
				break;
		}

		return CoreConnector::__translate('Normal');
	}

	/**
	 * Check if this pix type is equal
	 * to $type.
	 *
	 * @param string $type
	 * @since 2.0.0
	 * @return boolean
	 */
	public function isType ( string $type ) : bool
	{ return $this->type === $type; }

	/**
	 * Set pix brcode.
	 *
	 * @param string $brcode
	 * @since 2.0.0
	 * @return PixEntity
	 */
	public function setBrCode ( string $brcode )
	{ 
		$this->brcode = $brcode; 
		return $this; 
	}

	/**
	 * Get pix brcode.
	 *
	 * @since 2.0.0
	 * @return string|null
	 */
	public function getBrCode () : ?string
	{ return $this->brcode ?? null; }

	/**
	 * Set QRCode url and path.
	 *
	 * @param string $url
	 * @param string $path
	 * @since 2.0.0
	 * @return PixEntity
	 */
	public function setQrCode ( string $url, string $path )
	{ 
		$this->qrcode = ['url' => $url, 'path' => $path]; 
		return $this; 
	}

	/**
	 * Get QRCode data with url and path.
	 *
	 * @since 2.0.0
	 * @return array
	 */
	public function getQrCode () : array
	{ return $this->qrcode ?? []; }

	/**
	 * Create the QRCode.
	 *
	 * @since 2.0.0
	 * @return array|null
	 */
	public function createQRCode ( bool $fresh = false ) : ?array
	{ return (new QrCodeProcessor())->run($this, $fresh); }

	/**
	 * Set receipt url and path.
	 *
	 * @param string $url
	 * @param string $path
	 * @since 2.0.0
	 * @return PixEntity
	 */
	public function setReceipt ( string $url, string $path )
	{ 
		$this->receipt = ['url' => $url, 'path' => $path]; 
		return $this; 
	}

	/**
	 * Add data to receipt...
	 *
	 * @param string $key
	 * @param string $value
	 * @since 2.0.0
	 * @return PixEntity
	 */
	public function addToReceipt ( string $key, $value )
	{
		$this->receipt[$key] = $value;
		return $this; 
	}

	/**
	 * Get receipt data with url and path.
	 *
	 * @since 2.0.0
	 * @return array
	 */
	public function getReceipt () : array
	{ return $this->receipt ?? []; }

	/**
	 * Set pix metadata.
	 *
	 * @param array $metadata
	 * @since 2.0.0
	 * @return PixEntity
	 */
	public function setMetadata ( array $metadata )
	{ $this->metadata = $metadata; return $this; }

	/**
	 * Get pix metadata.
	 *
	 * @since 2.0.0
	 * @return array
	 */
	public function getMetadata () : array
	{ return $this->metadata ?? []; }

	/**
	 * Set expiration interval from now.
	 *
	 * @param DateInterval $interval
	 * @since 2.0.0
	 * @return PixEntity
	 */
	public function setExpiration ( DateInterval $interval )
	{
		$this->expires_at = (new DateTime('now', wp_timezone()))->add($interval);
		return $this;
	}

	/**
	 * Return if current pix is expired.
	 *
	 * @param boolean $auto_update Auto update status when is expired, 
	 * 									 but with another status.
	 * @since 2.0.0
	 * @return boolean
	 */
	public function isExpired ( bool $auto_update = true ) : bool
	{
		if ( $this->status === static::STATUS_EXPIRED )
		{ return true; }

		if ( \is_null($this->expires_at) )
		{ return false; }

		$now = new DateTime('now', wp_timezone());
		$exp = $this->expires_at instanceof DateTime ? $this->expires_at : new DateTime($this->expires_at, wp_timezone());

		if ( $now > $exp )
		{
			if ( $auto_update ) 
			{
				try
				{ 
					$this->updateStatus(static::STATUS_EXPIRED); 

					if ( !empty($this->getOrder()) )
					{
						$this->getOrder()->add_order_note(
							sprintf(
								CoreConnector::__translate('Pix `%s` expirado'),
								$this->getTxid()
							)
						);
					}
				}
				catch ( Exception $e )
				{}
			}

			return true;
		}

		return false;
	}

	/**
	 * Check if transacion is closest to expiration date.
	 *
	 * @since 2.0.0
	 * @return boolean
	 */
	public function isClosestToExpires () : bool
	{
		if ( $this->status === static::STATUS_EXPIRED )
		{ return false; }

		if ( \is_null($this->expires_at) )
		{ return false; }

		// Get the minutes
		$minutes = CoreConnector::settings()->get('orders')->get('closest_lifetime', 10);

		$now = new DateTime('now', wp_timezone());
		$exp = $this->expires_at instanceof DateTime ? $this->expires_at : new DateTime($this->expires_at, wp_timezone());
		$clo = $exp->sub(new DateInterval('PT'.$minutes.'M'));

		return $now >= $clo && $now < $exp;
	}

	/**
	 * Expires at.
	 *
	 * @since 2.0.0
	 * @return DateTime|null
	 */
	public function getExpiresAt () : ?DateTime
	{ return empty($this->expires_at) ? null : ($this->expires_at instanceof DateTime ? $this->expires_at : new DateTime($this->expires_at, wp_timezone())); }

	/**
	 * Created at.
	 *
	 * @since 2.0.0
	 * @return DateTime
	 */
	public function getCreatedAt () : DateTime
	{ return $this->created_at instanceof DateTime ? $this->created_at : new DateTime($this->created_at, wp_timezone()); }

	/**
	 * Updated at.
	 *
	 * @since 2.0.0
	 * @return DateTime
	 */
	public function getUpdatedAt () : DateTime
	{ return $this->updated_at instanceof DateTime ? $this->updated_at : new DateTime($this->updated_at, wp_timezone()); }

	/**
	 * Return true when pix
	 * was loaded from database.
	 *
	 * @since 2.0.0
	 * @return boolean
	 */
	public function isLoaded () : bool
	{ return $this->loaded; }

	/**
	 * Return if pix is paid.
	 *
	 * @since 2.0.0
	 * @return boolean
	 */
	public function isPaid () : bool
	{ return $this->status === static::STATUS_PAID; }

	/**
	 * Save current pix.
	 *
	 * @since 2.0.0
	 * @return PixEntity
	 * @throws DatabaseException
	 */
	public function save ()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'pgly_pix';
		$this->update_at = new DateTime('now', \wp_timezone());

		if ( $this->loaded )
		{ 
			CoreConnector::debugger()->debug('Update to database', $this->export());
			$response = $wpdb->update($table_name, $this->export(['txid']), ['txid' => $this->txid]); 
		}
		else
		{ 
			try
			{
				$tx = (new PixRepo(CoreConnector::plugin()))->byId($this->getTxid());

				if ( !empty($tx) )
				{ $this->setTxid($this->generateTxid()); }
			}
			catch ( Exception $e )
			{ $this->setTxid($this->generateTxid()); }

			CoreConnector::debugger()->debug('Insert to database', $this->export());
			$response = $wpdb->insert($table_name, $this->export());
			$this->loaded = true;
		}

		if ( $response === false )
		{ 
			CoreConnector::debugger()->error(
				CoreConnector::__translate(
					\sprintf(
						'Não foi possível salvar o pix %s no banco de dados: %s', 
						$this->txid,
						empty($wpdb->last_error) ? $wpdb->last_query : $wpdb->last_error
					)
				)
			);

			throw new DatabaseException(CoreConnector::__translate(\sprintf('Não foi possível salvar a transação local no banco de dados.', $this->txid))); 
		}

		return $this;
	}

	/**
	 * Create pix from payload.
	 *
	 * @param stdClass $payload
	 * @param boolean $loadEntities
	 * @since 2.0.0
	 * @return PixEntity
	 */
	public static function create ( stdClass $payload, $loadEntities = true ) : PixEntity
	{
		$pix = new PixEntity();

		$pix->loaded = true;

		$pix->txid = $payload->txid;
		$pix->e2eid = $payload->e2eid ?? null;
		$pix->store_name = $payload->store_name ?? null;
		$pix->merchant_name = $payload->merchant_name ?? null;
		$pix->merchant_city = $payload->merchant_city ?? null;
		$pix->key = $payload->key ?? null;
		$pix->key_type = $payload->key_type ?? null;
		$pix->description = $payload->description ?? null;
		$pix->bank = empty($payload->bank) ? null : \intval($payload->bank);
		$pix->amount = \floatval($payload->amount);
		$pix->discount = \floatval($payload->discount);
		$pix->type = $payload->type;
		$pix->status = $payload->status ?? 'created';

		$pix->brcode = $payload->brcode ?? null;
		$pix->qrcode = !empty($payload->qrcode) ? json_decode($payload->qrcode, true) : null;
		$pix->receipt = !empty($payload->receipt) ? json_decode($payload->receipt, true) : null;
		$pix->metadata = !empty($payload->metadata) ? json_decode($payload->metadata, true) : null;
		
		if ( !\is_null($payload->oid) && $loadEntities )
		{ $pix->setOrder(\intval($payload->oid)); }

		if ( !empty($payload->expires_at) )
		{ $pix->expires_at = new DateTime($payload->expires_at, wp_timezone()); }

		$pix->created_at = new DateTime($payload->created_at, wp_timezone());
		$pix->updated_at = new DateTime($payload->updated_at, wp_timezone());

		return $pix;
   }

	/**
	 * Mount a new pix entity with order data.
	 *
	 * @param WC_Order $order
	 * @since 2.0.0
	 * @since 2.0.3 Bank must be always int...
	 * @return PixEntity
	 * @throws Exception
	 */
	public static function mount ( WC_Order $order ) : PixEntity
	{
		$pix = new PixEntity();

		try
		{
			$order_number = \method_exists($order, 'get_order_number') ? $order->get_order_number() : $order->get_id();
			$order_amount = $order->get_total();

			/** @var KeyingBucket $account */
			$account = CoreConnector::settings()->get('account', new KeyingBucket());
			/** @var KeyingBucket $orders */
			$orders = CoreConnector::settings()->get('orders', new KeyingBucket());

			$description = $account->get('description', 'Compra em {{store_name}}');

			$description = \str_replace('{{store_name}}', $account->get('store_name', ''), $description);
			$description = \str_replace('{{order_number}}', $order_number, $description);
			
			$pix
				->setOrder($order)
				->setAmount($order_amount)
				->setBank(\intval($account->get('bank', null)))
				->setPixKey(
					$account->get('key_type'),
					$account->get('key_value')
				)
				->setMerchantData(
					$account->get('store_name', null),
					$account->get('merchant_name', null),
					$account->get('merchant_city', null)
				)
				->setDescription(
					$description
				);
			
			if ( !empty($orders->get('expires_after', 0)) )
			{ $pix->setExpiration(new DateInterval('PT'.\strval($orders->get('expires_after', 24)).'H')); }
			
			/** @var AbstractPayload $pixPayload */
			$payload = $pix->createPayload($order);

			$pix->setBrCode($payload->getPixCode());
			$pix->createQRCode();

			$pix->save();
			return $pix;
		}
		catch ( Exception $e )
		{
			CoreConnector::debugger()->force()->error(
				\sprintf(
					CoreConnector::__translate('Ocorreu um erro ao gerar o Pix: %s'), 
					$e->getMessage()
				)
			);

			throw $e;
		}
	}

	/**
	 * Get the payload array returned to client.
	 *
	 * @since 2.0.0
	 * @return array
	 */
	public function getPayload () : array
	{
		$data = [			
			'txid' => $this->txid,
			'identifier' => $this->txid,
			'e2eid' => $this->e2eid,
			'status' => $this->status,
			'store_name' => $this->store_name ?? null,
			'merchant_name' => $this->merchant_name ?? null,
			'merchant_city' => $this->merchant_city ?? null,
			'key_value' => $this->key ?? null,
			'key_type' => $this->key_type ?? null,
			'description' => $this->description ?? null,
			'bank' => $this->bank ?? null,
			'code' => $this->brcode ?? null,
			'qr' => $this->qrcode['url'] ?? null,
			'receipt' => $this->receipt['url'] ?? null,
			'metadata' => $this->metadata ?? null,
			'discount' => $this->discount ?? 0,
			'amount' => $this->amount ?? 0,
			'expires_at' => empty($this->expires_at) ? null : $this->expires_at->format('Y-m-d H:i:s'),
			'_version' => 'v2'
		];

		return $data;
	}

	/**
	 * Export class data to an array.
	 *
	 * @since 2.0.0
	 * @return array
	 * @throws RuntimeException
	 */
	public function export ( array $remove = [] ) : array
	{
		$data = [
			'txid' => $this->txid,
			'e2eid' => $this->e2eid ?? null,
			'store_name' => $this->store_name ?? null,
			'merchant_name' => $this->merchant_name ?? null,
			'merchant_city' => $this->merchant_city ?? null,
			'key' => $this->key ?? null,
			'key_type' => $this->key_type ?? null,
			'description' => $this->description ?? null,
			'bank' => $this->bank ?? null,
			'oid' => !\is_null($this->getOrder()) ? $this->getOrder()->get_id() : null,
			'type' => $this->type,
			'status' => $this->status,
			'brcode' => $this->brcode ?? null,
			'qrcode' => \json_encode($this->getQrCode()),
			'receipt' => \json_encode($this->getReceipt()),
			'metadata' => \json_encode($this->getMetadata()),
			'discount' => $this->discount ?? 0,
			'amount' => $this->amount ?? 0,
			'expires_at' => empty($this->expires_at) ? null : $this->expires_at->format('Y-m-d H:i:s'),
			'updated_at' => empty($this->expires_at) ? null : $this->expires_at->format('Y-m-d H:i:s')
		];

		foreach ( $remove as $key )
		{ unset($data[$key]); }

		return $data;
	}

	/**
	 * Create the pix payload as StaticPayload
	 * or DynamicPayload.
	 *
	 * @filter pgly_wc_piggly_pix_payload
	 * @param WC_Order $order
	 * @since 2.0.0
	 * @return AbstractPayload
	 */
	protected function createPayload ( 
		WC_Order $order
	) : AbstractPayload
	{
		if ( empty($this->getAmount()) )
		{ throw new Exception(CoreConnector::__translate('O valor do Pix é inválido')); }

		if ( empty($this->getPixKeyType()) || empty($this->getPixKeyValue()) )
		{ throw new Exception(CoreConnector::__translate('É necessário preencher a Chave Pix')); }

		/** @var StaticPayload $default */
		$default = 
			(new StaticPayload())
				->setAmount($this->getAmount() ?? 0)
				->setTid($this->getTxid())
				->setPixKey($this->getPixKeyType(), $this->getPixKeyValue());

		if ( !empty($this->getMerchantName()))
		{ $default->setMerchantName($this->getMerchantName()); }

		if ( !empty($this->getMerchantCity()))
		{ $default->setMerchantCity($this->getMerchantCity()); }

		if ( !empty($this->getStoreName()) )
		{ $default->setDescription(\sprintf(CoreConnector::__translate('Compra em %s'), $this->getStoreName())); }
	
		$pix = intval(\get_option('wc_piggly_pix_counter', 0));
		\update_option('wc_piggly_pix_counter', $pix++);

		return \apply_filters('pgly_wc_piggly_pix_payload', $default, $this, $order);
	}

	/**
	 * Generate a txid.
	 * 
	 * @param string $length
	 * @since 2.0.0
	 * @return string
	 */
	protected function generateTxid ( int $length = 25 ) : string
	{
		$gen  = \str_shuffle('1357902468QEWRYTUOIPADSFHGJLKZCXVNBM');
		$txid = '';
		
		for ( $i = 0; $i < $length; $i++ )
		{ $txid .= substr($gen, (rand()%(strlen($gen))), 1); }

    	return $txid;
	}
	/**
	 * Parse any phone string to a correct phone format.
	 * 
	 * @since 2.0.0
	 * @param string $phone
	 * @return string
	 */
	public static function parse_phone ( string $phone ) : string
	{
		if ( strpos($phone, '+') !== false )
		{
			if ( strpos($phone, '+55') !== false )
			{
				$phone = str_replace('+55', '', $phone);
				$phone = preg_replace('/[^\d]+/', '', $phone);
				return '+55'.$phone;
			}
			else
			{
				$phone = preg_replace('/[^\d]+/', '', $phone);
				return '+'.$phone;
			}
		}
		else
		{
			$phone = str_replace('+55', '', $phone);
			$phone = preg_replace('/[^\d]+/', '', $phone);
			return '+55'.$phone;
		}
	}
}