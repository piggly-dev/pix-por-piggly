<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) { die; }

/**
 * The Pix Parser class.
 * 
 * This is used to parse and format data following patterns and standards of
 * a pix.
 *
 * @since      1.0.0
 * @package    WC_Piggly_Pix
 * @subpackage WC_Piggly_Pix/core/pix
 * @author     Caique <caique@piggly.com.br>
 */
class WC_Piggly_Pix_Parser
{
	const KEY_TYPE_RANDOM = 'random';
	const KEY_TYPE_DOCUMENT = 'document';
	const KEY_TYPE_EMAIL = 'email';
	const KEY_TYPE_PHONE = 'phone';

	/**
	 * Return the alias for key value.
	 * 
	 * @since 1.0.0
	 * @param string $key
	 * @return string
	 */
	public static function getAlias ( string $key ) : string 
	{
		switch ( $key )
		{
			case self::KEY_TYPE_RANDOM:
				return __('Chave Aleatória', WC_PIGGLY_PIX_PLUGIN_NAME);
			case self::KEY_TYPE_DOCUMENT:
				return __('CPF/CNPJ', WC_PIGGLY_PIX_PLUGIN_NAME);
			case self::KEY_TYPE_EMAIL:
				return __('E-mail', WC_PIGGLY_PIX_PLUGIN_NAME);
			case self::KEY_TYPE_PHONE:
				return __('Telefone', WC_PIGGLY_PIX_PLUGIN_NAME);
		}

		return __('Chave Desconhecida', WC_PIGGLY_PIX_PLUGIN_NAME);
	}

	/**
	 * Validate a $value based in the respective pix $key.
	 * 
	 * @since 1.0.0
	 * @param string $key Pix key.
	 * @param string $value Pix value.
	 * @throws Exception
	 */
	public static function validate ( string $key, string $value )
	{
		switch ( $key )
		{
			case self::KEY_TYPE_RANDOM:
				return self::validateRandom($value);
			case self::KEY_TYPE_DOCUMENT:
				return self::validateDocument($value);
			case self::KEY_TYPE_EMAIL:
				return self::validateEmail($value);
			case self::KEY_TYPE_PHONE:
				return self::validatePhone($value);
		}

		throw new Exception(sprintf(__('A chave `%s` é desconhecida.', WC_PIGGLY_PIX_PLUGIN_NAME), $key));
	}

	/**
	 * Validates the random key value.
	 * 
	 * @since 1.0.0
	 * @param string $random Pix key value.
	 * @throws Exception
	 */
	public static function validateRandom ( string $random )
	{
		if ( !preg_match('/[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}/', $random) )
		{ throw new Exception(sprintf(__('A chave aleatória `%s` está inválida.', WC_PIGGLY_PIX_PLUGIN_NAME), $random)); }
	}

	/**
	 * Validates the document key value.
	 * 
	 * @since 1.0.0
	 * @param string $document Pix key value.
	 * @throws Exception
	 */
	public static function validateDocument ( string $document )
	{
		if ( !preg_match('/([0-9]{2}[\.]?[0-9]{3}[\.]?[0-9]{3}[\/]?[0-9]{4}[-]?[0-9]{2})|([0-9]{3}[\.]?[0-9]{3}[\.]?[0-9]{3}[-]?[0-9]{2})/', $document) )
		{ throw new Exception(sprintf(__('A chave de CPF/CNPJ `%s` está inválida.', WC_PIGGLY_PIX_PLUGIN_NAME), $document)); }
	}

	/**
	 * Validates the email key value.
	 * 
	 * @since 1.0.0
	 * @param string $email Pix key value.
	 * @throws Exception
	 */
	public static function validateEmail ( string $email )
	{
		if ( !preg_match("/[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/", $email) )
		{ throw new Exception(sprintf(__('A chave de e-mail `%s` está inválida.', WC_PIGGLY_PIX_PLUGIN_NAME), $email)); }
	}

	/**
	 * Validates the phone key value.
	 * 
	 * @since 1.0.0
	 * @param string $phone Pix key value.
	 * @throws Exception
	 */
	public static function validatePhone ( string $phone )
	{
		if ( !preg_match('/(\(?\d{2}\)?\s)?(\d{4,5}\-\d{4})/', $phone) )
		{ throw new Exception(sprintf(__('A chave de telefone `%s` está inválida.', WC_PIGGLY_PIX_PLUGIN_NAME), $phone)); }
	}

	/**
	 * Parse a $value based in the respective pix $key.
	 * 
	 * @since 1.0.0
	 * @param string $key Pix key.
	 * @param string $value Pix value.
	 * @return string
	 * @throws Exception
	 */
	public static function parse ( string $key, string $value ) : string
	{
		switch ( $key )
		{
			case self::KEY_TYPE_DOCUMENT:
				return self::parseDocument($value);
			case self::KEY_TYPE_EMAIL:
				return self::parseEmail($value);
			case self::KEY_TYPE_PHONE:
				return self::parsePhone($value);
		}

		throw new Exception(sprintf(__('A chave `%s` é desconhecida.', WC_PIGGLY_PIX_PLUGIN_NAME), $key));
	}

	/**
	 * Parse any document string to a correct document format.
	 * 
	 * @since 1.0.0
	 * @param string $document
	 * @return string
	 */
	public static function parseDocument ( string $document ) : string
	{ return preg_replace('/[^\d]+/', '', $document); }

	/**
	 * Parse any email string to a correct email format.
	 * 
	 * @since 1.0.0
	 * @param string $email
	 * @return string
	 */
	public static function parseEmail ( string $email ) : string
	{ return str_replace('@', ' ', $email); }

	/**
	 * Parse any phone string to a correct phone format.
	 * 
	 * @since 1.0.0
	 * @param string $phone
	 * @return string
	 */
	public static function parsePhone ( string $phone ) : string
	{
		$phone = str_replace('+55', '', $phone);
		$phone = preg_replace('/[^\d]+/', '', $phone);
		return '+55'.$phone;
	}
}