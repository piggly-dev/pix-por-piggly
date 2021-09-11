<?php
namespace Piggly\WooPixGateway\Core\Processors;

use Exception;
use Piggly\WooPixGateway\Core\Entities\PixEntity;
use Piggly\WooPixGateway\CoreConnector;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Settings\KeyingBucket;

/**
 * The receipt processor will process the file sent
 * and attach it to the Pix Entity
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
class ReceiptProcessor
{
	/**
	 * Return the receipt data. Which include
	 * a URl and path.
	 * 
	 * It will return null if receipt is not
	 * valid or dangerous.
	 *
	 * @param PixEntity $pix
	 * @since 2.0.0
	 * @return array|null
	 */
	public function run ( PixEntity $pix) : ?array
	{
		// If there is no QRCode, then create
		if ( empty($pix->getReceipt()) )
		{ return $this->new($pix); }

		$data = $pix->getReceipt();

		// Delete file if exists
		if ( \file_exists($data['path']) )
		{ \unlink($data['path']); }
			
		return $this->new($pix);
	}

	/**
	 * Create a new QRCore and return the generated data.
	 * It will add data to pix and save it.
	 *
	 * @action pgly_wc_piggly_pix_after_save_receipt
	 * @param PixEntity $pix
	 * @since 2.0.0
	 * @return array|null
	 */
	protected function new ( PixEntity $pix ) : ?array
	{
		$expName = \explode('.', $_FILES['pgly_pix_receipt']['name']);

		// Extension
		$pathExt = pathinfo(basename($_FILES['pgly_pix_receipt']['name']),PATHINFO_EXTENSION);
		$nameExt = is_array( $expName ) ? $expName[count($expName)-1] : 'unknown';

		// Cannot validate mime type
		$mimeValidation = false;
		// If file should be trusted
		$trusted = true;

		try
		{
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mime = finfo_file($finfo, $_FILES['pgly_pix_receipt']['tmp_name']);
			finfo_close($finfo);
			
			// Validate mime type
			$mimeValidation = \in_array($mime, ['image/jpg','image/jpeg','image/png','application/pdf']);
			$trusted = $mimeValidation;
		}
		catch ( Exception $e )
		{ 
			CoreConnector::debugger()->force()->error(\sprintf(CoreConnector::__translate('O usuário tentou realizar o upload, mas o arquivo não foi encontrado em `%s`. Verifique as configurações do PHP e as permissões da pasta. Verifique, ainda, a biblioteca MAGIC e a extensão file do PHP.'), $_FILES['pgly_pix_receipt']['tmp_name']));
			throw new Exception(CoreConnector::__translate('O arquivo não pode ser enviado no momento. Tente novamente mais tarde.')); 
		}

		finally
		{
			if ( !$mimeValidation )
			{
				$mime = $_FILES['pgly_pix_receipt']['type'];
				// Trust in browser, but do a system alert...
				CoreConnector::debugger()->force()->info(\sprintf(CoreConnector::__translate('O arquivo `%s` enviado não é confiável...'), $_FILES['pgly_pix_receipt']['tmp_name']));
				$mimeValidation = \in_array($mime, ['image/jpg','image/jpeg','image/png','application/pdf']);
				$trusted = false;
			}
		}

		// Validate extension
		$validateExt = \in_array($pathExt, ['jpg','jpeg','png','pdf']) || \in_array($nameExt, ['jpg','jpeg','png','pdf']);
		
		if ( !$validateExt && !$mimeValidation )
		{ throw new Exception(CoreConnector::__translate('O nome do arquivo não indica uma imagem ou um PDF compatível.')); }

		if ( !$mimeValidation )
		{ throw new Exception(CoreConnector::__translate('O comprovante foi enviado em um tipo de arquivo não compatível. Envie uma imagem ou um PDF.')); }
		
		// Check file size
		if ($_FILES['pgly_pix_receipt']['size'] > 2000000) 
		{ throw new Exception(CoreConnector::__translate('O tamanho máximo permitido para o arquivo é 2MB, envie um arquivo menor.')); }

		$mapExt = ['image/jpg'=>'jpg','image/jpeg'=>'jpeg','image/png'=>'png','application/pdf'=>'pdf'];
		// Fix extension
		$extension = $validateExt ? $pathExt ?? $nameExt : $mapExt[$mime];
		
		$upload     = wp_upload_dir();
		$dirname    = dirname(CoreConnector::plugin()->getBasename());
		$uploadPath = $upload['basedir'].'/'.$dirname.'/receipts/';
		$uploadUrl  = $upload['baseurl'].'/'.$dirname.'/receipts/';
		$fileName   = md5('pix-'.$pix->getTxid().time()).'.'.$extension;
		$file       = $uploadPath.$fileName;

		if ( !\file_exists( $uploadPath ) ) 
		{ wp_mkdir_p($uploadPath); }

		if ( !\move_uploaded_file($_FILES['pgly_pix_receipt']['tmp_name'], $file) )
		{ 
			CoreConnector::debugger()->force()->error(\sprintf(CoreConnector::__translate('Não foi mover o arquivo de upload de `%s` para `%s`.'), $_FILES['pgly_pix_receipt']['tmp_name'], $file));
			throw new Exception(CoreConnector::__translate('Não foi possível enviar o comprovante agora.')); 
		}
		
		/** @var KeyingBucket $settings */
		$settings = CoreConnector::settings()->get('orders', new KeyingBucket());
			
		$order = $pix->getOrder();
		$pix->setReceipt($uploadUrl.$fileName, $file);

		$order->add_order_note(
			\sprintf(
				CoreConnector::__translate('Comprovante Pix Recebido, visualize-o na metabox do Pix ou acesse o link `%s`'),
				$uploadUrl.$fileName
			)
		);

		if ( !$order->has_status([$settings->get('receipt_status', 'on-hold')]) )
		{ 
			$order->update_status( 
				$settings->get('receipt_status', 'on-hold'),
			); 
		}

		$order->save();

		$pix
			->setReceipt($uploadUrl.$fileName, $file)
			->addToReceipt('trusted', $trusted)
			->updateStatus(PixEntity::STATUS_WAITING);

		\wc_maybe_reduce_stock_levels($order->get_id());

		// Do after save order
		do_action('pgly_wc_piggly_pix_after_save_receipt', $pix, $order, $order->get_id());
		
		return [
			'url' => $uploadUrl.$fileName, 
			'path' => $file
		];
	}
}