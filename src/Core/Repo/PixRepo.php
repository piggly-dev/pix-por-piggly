<?php
namespace Piggly\WooPixGateway\Core\Repo;

use Exception;
use Piggly\WooPixGateway\Core\Entities\PixEntity;
use Piggly\WooPixGateway\CoreConnector;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Scaffold\Internationalizable;

use WC_Order;

/**
 * The pix repository is the bridge between
 * pixs operations and the database.
 * 
 * @package \Piggly\WooPixGateway
 * @subpackage \Piggly\WooPixGateway\Core\Repo
 * @version 2.0.0
 * @since 2.0.0
 * @category Repositories
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license GPLv3 or later
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
class PixRepo extends Internationalizable
{
	/**
	 * Get a pix by ID.
	 *
	 * @param string $txid
	 * @param boolean $loadEntities Load entities attached to pix.
	 * @since 2.0.0
	 * @return PixEntity|null
	 */
	public function byId ( string $txid, bool $loadEntities = true ) : ?PixEntity
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'pgly_pix';

		$tx = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE `txid` = %s", $txid));

		if ( empty($tx) )
		{ return null; }

		return PixEntity::create($tx, $loadEntities);
	}

	/**
	 * Get a pix by e2eid.
	 *
	 * @param string $e2eid
	 * @param string|null $txid To exclude from select
	 * @param boolean $loadEntities Load entities attached to pix.
	 * @since 2.0.0
	 * @return PixEntity|null
	 */
	public function byE2eid ( string $e2eid, string $txid = null, bool $loadEntities = true ) : ?PixEntity
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'pgly_pix';

		$tx = empty($txid) 
					? $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE `e2eid` = %s", $e2eid))
					: $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE `e2eid` = %s AND `txid` != %s", $e2eid, $txid));

		if ( empty($tx) )
		{ return null; }

		return PixEntity::create($tx, $loadEntities);
	}

	/**
	 * Force to get pix, if can't
	 * then throw an exception.
	 *
	 * @param string $txid
	 * @param boolean $loadEntities
	 * @since 2.0.0
	 * @return PixEntity
	 * @throws Exception
	 */
	public function forceById ( string $txid, bool $loadEntities = true ) : PixEntity
	{
		$tx = $this->byId($txid, $loadEntities);

		if ( empty($tx) )
		{ throw new Exception(CoreConnector::__translate('Pix não encontrado'), 10); }

		return $tx;
	}

	/**
	 * Get the latest pix by status.
	 *
	 * @param WC_Order|integer $order
	 * @param array $status
	 * @since 2.0.0
	 * @return PixEntity|null
	 */
	public function latestStatus ( $order, array $status ) : ?PixEntity
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'pgly_pix';

		$order = $order instanceof WC_Order ? $order : new WC_Order($order);

		$tx = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $table_name WHERE `oid` = %s AND `status` IN (%s) ORDER BY `created_at` DESC LIMIT 1", 
				$order->get_id(),
				\implode(',', $status)
			)
		);

		if ( empty($tx) )
		{ return null; }

		return PixEntity::create($tx, false)->setOrder($order);
	}

	/**
	 * Get all pixs associated to an order.
	 * It will return an empty array if nothing was
	 * found.
	 *
	 * @param WC_Order|integer $order
	 * @since 2.0.0
	 * @return array<PixEntity>
	 */
	public function byOrder ( $order ) : array
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'pgly_pix';

		$order = $order instanceof WC_Order ? $order : new WC_Order($order);

		$txs = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM $table_name WHERE `oid` = %s ORDER BY `created_at`", 
				$order->get_id()
			)
		);

		if ( empty($txs) )
		{ return []; }

		$_txs = [];

		foreach ( $txs as $tx )
		{ 
			$_tx    = PixEntity::create($tx, false); 
			$_txs[] = $_tx->setOrder($order);
		}

		return $_txs;
	}
}