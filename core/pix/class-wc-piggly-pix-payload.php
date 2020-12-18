<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) { die; }

/**
 * The Pix Payload class.
 * 
 * This is used to set up pix data and follow the EMV®1 pattern and standards.
 * When set up all data, the export() method will generate the full pix payload.
 *
 * @since      1.0.0
 * @package    WC_Piggly_Pix
 * @subpackage WC_Piggly_Pix/core/pix
 * @author     Caique <caique@piggly.com.br>
 */
class WC_Piggly_Pix_Payload
{
	/** Version of QRCPS-MPM payload. */
	const ID_PAYLOAD_FORMAT_INDICATOR = '00';
	/** Point of initiation method. When set to 12, means can be used only one time. */
	const ID_POINT_OF_INITIATION_METHOD = '01';
	/** Merchant account information. */
	const ID_MERCHANT_ACCOUNT_INFORMATION = '26';
	/** Merchant account GUI information */
	const ID_MERCHANT_ACCOUNT_INFORMATION_GUI = '00';
	/** Merchant account key information */
	const ID_MERCHANT_ACCOUNT_INFORMATION_KEY = '01';
	/** Merchant account description information */
	const ID_MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION = '02';
	/** Merchant account url information */
	const ID_MERCHANT_ACCOUNT_INFORMATION_URL = '25';
	/** Merchant account category code */
	const ID_MERCHANT_CATEGORY_CODE = '52';
	/** Transaction currency code */
	const ID_TRANSACTION_CURRENCY = '53';
	/** Transaction amount code */
	const ID_TRANSACTION_AMOUNT = '54';
	/** Country code */
	const ID_COUNTRY_CODE = '58';
	/** Merchant name */
	const ID_MERCHANT_NAME = '59';
	/** Merchant city */
	const ID_MERCHANT_CITY = '60';
	/** Additional data field */
	const ID_ADDITIONAL_DATA_FIELD_TEMPLATE = '62';
	/** Additional data field TID */
	const ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TID = '05';
	/** CRC16 */
	const ID_CRC16 = '63';
	
	/**
	 * Pix key.
	 * @var string
	 * @since 1.0.0
	 */
	protected $pixKey;

	/**
	 * Payment description.
	 * @var string
	 * @since 1.0.0
	 */
	protected $description;

	/**
	 * Merchant name.
	 * @var string
	 * @since 1.0.0
	 */
	protected $merchantName;

	/**
	 * Merchant city.
	 * @var string
	 * @since 1.0.0
	 */
	protected $merchantCity;

	/**
	 * Pix Transaction ID.
	 * @var string
	 * @since 1.0.0
	 */
	protected $tid;

	/**
	 * Transaction amount.
	 * @var string
	 * @since 1.0.0
	 */
	protected $amount;

	/**
	 * Defines if payment is unique.
	 * @var boolean
	 * @since 1.0.0
	 */
	protected $uniquePayment = false;

	/**
	 * Set the current pix key.
	 * @param string $pixKey Pix key.
	 * @since 1.0.0
	 * @return self
	 */
	public function setPixKey ( string $pixKey )
	{ $this->pixKey = $pixKey; return $this; }

	/**
	 * Set the current pix description.
	 * @param string $description Pix description.
	 * @since 1.0.0
	 * @return self
	 */
	public function setDescription ( string $description )
	{ $this->description = $this->cutData($description); return $this; }

	/**
	 * Set the current pix merchant name.
	 * @param string $merchantName Pix merchant name.
	 * @since 1.0.0
	 * @return self
	 */
	public function setMerchantName ( string $merchantName )
	{ $this->merchantName = $this->cutData($merchantName); return $this; }

	/**
	 * Set the current pix merchant city.
	 * @param string $merchantCity Pix merchant city.
	 * @since 1.0.0
	 * @return self
	 */
	public function setMerchantCity ( string $merchantCity )
	{ $this->merchantCity = $this->cutData($merchantCity); return $this; }

	/**
	 * Set the current pix transaction id.
	 * @param string $tid Pix transaction id.
	 * @since 1.0.0
	 * @return self
	 */
	public function setTid ( string $tid )
	{ $this->tid = $this->cutData($tid); return $this; }

	/**
	 * Set the current pix transaction amount.
	 * @param string $amount Pix transaction amount.
	 * @since 1.0.0
	 * @return self
	 */
	public function setAmount ( float $amount )
	{ $this->amount = (string) number_format( $amount, 2, '.', '' ); return $this; }

	/**
	 * Set the current pix unique payment format.
	 * @param string $uniquePayment Pix unique payment format.
	 * @since 1.0.0
	 * @return self
	 */
	public function setUniquePayment ( bool $uniquePayment = true )
	{ $this->uniquePayment = $uniquePayment; return $this; }

	/**
	 * Generate the full pix payload.
	 * @since 1.0.0
	 * @return string
	 */
	public function export () 
	{
		$payload = 
			$this->formatData(self::ID_PAYLOAD_FORMAT_INDICATOR, '01') .
			// Point of initiation method
			$this->getPointOfInitiationMethod() .
			// Merchant account information
			$this->getMerchantAccountInformation() .
			$this->formatData(self::ID_MERCHANT_CATEGORY_CODE,'0000') .
			$this->getTransaction() .
			$this->formatData(self::ID_COUNTRY_CODE,'BR') .
			$this->getMerchantInformation() .
			// Additional data field template
			$this->getAdditionalDataFieldTemplate();

		return $payload . $this->getCRC16( $payload );
	}

	/**
	 * Get the current transaction data.
	 * 
	 * @since 1.0.0
	 * @return string
	 */
	protected function getTransaction () : string 
	{
		$currency = $this->formatData(
			self::ID_TRANSACTION_CURRENCY,
			'986'
		);
		
		$amount = $this->formatData(
			self::ID_TRANSACTION_AMOUNT,
			$this->amount
		);

		return $currency.$amount;
	}

	/**
	 * Get the current merchant information.
	 * 
	 * @since 1.0.0
	 * @return string
	 */
	protected function getMerchantInformation () : string 
	{
		$currency = $this->formatData(
			self::ID_MERCHANT_NAME,
			$this->merchantName
		);
		
		$amount = $this->formatData(
			self::ID_MERCHANT_CITY,
			$this->merchantCity
		);

		return $currency.$amount;
	}

	/**
	 * Get the current merchant account information.
	 * 
	 * @since 1.0.0
	 * @return string
	 */
	protected function getMerchantAccountInformation () : string
	{
		// Global bank domain
		$gui = $this->formatData(
			self::ID_MERCHANT_ACCOUNT_INFORMATION_GUI,
			'br.gov.bcb.pix'
		);

		// Current pix key
		$key = $this->formatData(
			self::ID_MERCHANT_ACCOUNT_INFORMATION_KEY,
			$this->pixKey
		);

		// Current pix description
		$description = $this->formatData(
			self::ID_MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION,
			$this->description
		);

		return $this->formatData(
			self::ID_MERCHANT_ACCOUNT_INFORMATION,
			$gui.$key.$description
		);
	}

	/**
	 * Get the current addictional data field template.
	 * 
	 * @since 1.0.0
	 * @return string
	 */
	protected function getAdditionalDataFieldTemplate ()
	{
		// Current pix transaction id
		$tid = $this->formatData(
			self::ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TID,
			$this->tid
		);

		return $this->formatData(
			self::ID_ADDITIONAL_DATA_FIELD_TEMPLATE,
			$tid
		);
	}

	/**
	 * Get the current point of initiation method.
	 * 
	 * @since 1.0.0
	 * @return string
	 */
	protected function getPointOfInitiationMethod ()
	{
		return $this->uniquePayment ?
						$this->formatData(
							self::ID_POINT_OF_INITIATION_METHOD,
							'12'
						) :
						$this->formatData(
							self::ID_POINT_OF_INITIATION_METHOD,
							'11'
						) ;
	}

	/**
	 * Get the current CRC16 by following standard values provieded by BACEN.
	 * 
	 * @since 1.0.0
	 * @param string $payload The full payload.
	 * @return string
	 */
	protected function getCRC16 ( string $payload )
	{
		// Standard data
		$payload .= self::ID_CRC16.'04';

		// Standard values by BACEN
		$polynomial = 0x1021;
		$response   = 0xFFFF;

		// Checksum
		if ( ( $length = strlen($payload ) ) > 0 ) 
		{
			for ( $offset = 0; $offset < $length; $offset++ ) 
			{
				$response ^= ( ord( $payload[$offset] ) << 8 );
				
				for ( $bitwise = 0; $bitwise < 8; $bitwise++ ) 
				{
					if ( ( $response <<= 1 ) & 0x10000 ) 
					{ $response ^= $polynomial; }

					$response &= 0xFFFF;
				}
			}
	  }

	  // CRC16 calculated
	  return self::ID_CRC16.'04' . strtoupper( dechex( $response ) );
	}

	/**
	 * Return formated data following the EMV patterns.
	 * 
	 * @since 1.0.0
	 * @param string $id Data ID.
	 * @param string $valueSize Data value.
	 * @return string Formated data.
	 */
	protected function formatData ( string $id, string $value ) : string 
	{
		$valueSize = str_pad( mb_strlen($value), 2, '0', STR_PAD_LEFT );
		return $id.$valueSize.$value;
	}

	/**
	 * Cut data more than $maxLenght.
	 * 
	 * @since 1.0.0
	 * @return string
	 */
	private function cutData ( string $value, int $maxLenght = 25 )
	{
		if ( strlen($value) > $maxLenght )
		{ return substr($value, 0, 25); }

		return $value;
	}
}