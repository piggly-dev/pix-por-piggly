<?php
namespace Piggly\WooPixGateway\Core\Emails;

use Piggly\WooPixGateway\Core\Entities\PixEntity;
use Piggly\WooPixGateway\CoreConnector;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\WP;

use WC_Email;
use WC_Order;

/**
 * Sent when a receipt is received.
 * 
 * @package \Piggly\WooPixGateway
 * @subpackage \Piggly\WooPixGateway\Core\Emails
 * @version 2.0.0
 * @since 2.0.0
 * @category Entities
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license GPLv3 or later
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
class AdminPixReceiptSent extends WC_Email
{
	/**
	 * Pix.
	 *
	 * @var PixEntity
	 * @since 2.0.0
	 */
	protected $pix;

	/**
	 * Construct class to verify account e-mail.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function __construct ()
	{
		$this->id = 'wc_piggly_pix_admin_receipt_sent';
		$this->title = CoreConnector::__translate('Comprovante Pix Enviado');
		$this->description = CoreConnector::__translate('E-mail enviado quando um comprovante Pix foi enviado.');

		$this->customer_email = false;

		$this->placeholders   = array(
			'{order_date}'   => '',
			'{order_number}' => '',
		);

		// email template path
		$this->template_html  = 'woocommerce/emails/admin-pix-receipt-sent.php';
		$this->template_plain = 'woocommerce/emails/plain/admin-pix-receipt-sent.php';
		$this->template_base  = CoreConnector::plugin()->getTemplatePath();

		WP::add_action('pgly_wc_piggly_pix_after_save_receipt', $this, 'trigger', 10, 3);
		
		parent::__construct();
		$this->manual = true;

		// Other settings.
		$this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );
	}

	/**
	 * Prepare and send e-mail.
	 *
	 * @param PixEntity $pix
	 * @param WC_Order $order
	 * @param integer $order_id
	 * @since 2.0.0
	 * @return void
	 */
	public function trigger ( PixEntity $pix, WC_Order $order, int $order_id )
	{
		$this->setup_locale();

		if ( !empty($order) )
		{
			CoreConnector::debugger()->debug(\sprintf('Disparo de e-mail %s para %s', $this->id, $order->get_billing_email()));

			// Placeholders
			$this->placeholders['{order_date}']   = wc_format_datetime( $order->get_date_created() );
			$this->placeholders['{order_number}'] = $order->get_order_number();

			$this->pix = $pix;

			if ( $this->is_enabled() && $this->get_recipient() )
			{
				$this->send(
					$this->get_recipient(),
					$this->get_subject(),
					$this->get_content(),
					$this->get_headers(),
					$this->get_attachments()
				);
			}
		}
		
		$this->restore_locale();
	}

	/**
	 * Get email subject.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function get_default_subject() {
		return CoreConnector::__translate('Comprovante Pix enviado para o Pedido #{order_number}');
	}

	/**
	 * Get email heading.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function get_default_heading() {
		return CoreConnector::__translate('O cliente enviou um comprovante de pagamento para o Pix');
	}

	/**
	 * Get the email content in HTML format.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function get_content_html() {
		return wc_get_template_html( 
			$this->template_html, 
			array(
				'pix' => $this->pix,
				'order' => $this->pix->getOrder(),
				'domain' => CoreConnector::domain(),
				'additional_content' => $this->get_additional_content(),
				'email_heading' => $this->get_heading(),
				'sent_to_admin' => false,
				'plain_text'    => false,
				'email'         => $this
			), 
			'', 
			$this->template_base 
		);
	}

	/**
	 * Get content plain.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function get_content_plain() {
		return wc_get_template_html( 
			$this->template_plain, 
			array(
				'pix' => $this->pix,
				'order' => $this->pix->getOrder(),
				'domain' => CoreConnector::domain(),
				'additional_content' => $this->get_additional_content(),
				'email_heading' => $this->get_heading(),
				'sent_to_admin' => false,
				'plain_text'    => true,
				'email'         => $this
			), 
			'', 
			$this->template_base 
		);
	}
}