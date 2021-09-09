<?php
namespace Piggly\WooPixGateway\Core\Processors;

use Piggly\WooPixGateway\Core\Entities\PixEntity;
use Piggly\WooPixGateway\CoreConnector;

use Piggly\WooPixGateway\Vendor\chillerlan\QRCode\QRCode;
use Piggly\WooPixGateway\Vendor\chillerlan\QRCode\QROptions;

/**
 * The QRCode processor will make sure the pix
 * entity has a QRCode available and, when needed, recreates
 * it saving to the upload folder.
 * 
 * @package \Piggly\WooPixGateway
 * @subpackage \Piggly\WooPixGateway\Core\Processors
 * @version 2.0.0
 * @since 2.0.0
 * @category Repositories
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license GPLv3 or later
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
class QrCodeProcessor
{
	/**
	 * Return the QRCode data. Which include
	 * a URl and path.
	 * 
	 * It will return null if QRCode is not
	 * supported.
	 *
	 * @param PixEntity $pix
	 * @param boolean $fresh Force to recreate QRCode.
	 * @since 2.0.0
	 * @return array|null
	 */
	public function run ( PixEntity $pix, bool $fresh = false ) : ?array
	{
		if ( !static::supportQrCode() )
		{ return null; }

		// If there is no QRCode, then create
		if ( empty($pix->getQrCode()) )
		{ return $this->new($pix); }

		$data = $pix->getQrCode();

		// If file was deleted, then recreate
		if ( !\file_exists($data['path']) )
		{ return $this->new($pix); }

		// Force to recreate
		if ( $fresh )
		{ 
			// Delete file if exists
			if ( \file_exists($data['path']) )
			{ \unlink($data['path']); }
			
			return $this->new($pix); 
		}

		return $data;
	}

	/**
	 * Create a new QRCore and return the generated data.
	 * It will add data to pix and save it.
	 *
	 * @param PixEntity $pix
	 * @since 2.0.0
	 * @return array|null
	 */
	protected function new ( PixEntity $pix ) : ?array
	{
		$upload     = wp_upload_dir();
		$dirname    = dirname(CoreConnector::plugin()->getBasename());
		$uploadPath = $upload['basedir'].'/'.$dirname.'/qr-codes/';
		$uploadUrl  = $upload['baseurl'].'/'.$dirname.'/qr-codes/';
		$fileName   = md5('pix-'.$pix->getTxid().time()).'.png';
		$file       = $uploadPath.$fileName;

		if ( !file_exists( $uploadPath ) ) 
		{ wp_mkdir_p($uploadPath); }

		if ( file_exists($file) )
		{ unlink($file); }

		$img     = str_replace('data:image/png;base64,', '', $this->create($pix->getBrCode()) );
		$img     = str_replace(' ', '+', $img);
		$data_   = base64_decode($img);
		$success = file_put_contents($file, $data_);

		if ( !$success )
		{ 
			CoreConnector::debugger()->force()->error(CoreConnector::__translate('Não foi possível criar o QR Code...'));
			return null;
		}

		// Update pix
		$pix->setQrCode($uploadUrl.$fileName, $file)->save();
		
		return [
			'url' => $uploadUrl.$fileName, 
			'path' => $file
		];
	}

	/**
	 * Create the QRCode.
	 *
	 * @param string $data
	 * @since 2.0.0
	 * @return mixed
	 */
	protected function create ( string $data )
	{
		$options = new QROptions([
			'outputType'   => QRCode::OUTPUT_IMAGE_PNG,
			'eccLevel'     => QRCode::ECC_M
		]);

		return (new QRCode($options))->render($data);
	}

	/**
	 * Check if support the gd extension.
	 * 
	 * @since 2.0.0
	 * @return boolean
	 */
	public static function supportQrCode () : bool
	{ return (extension_loaded('gd') && function_exists('gd_info')); }
}