<?php
namespace Inkifi\Mediaclip\H;
use Inkifi\Mediaclip\Event as Ev;
use Inkifi\Mediaclip\H\AvailableForDownload\Pureprint;
use Inkifi\Mediaclip\H\AvailableForDownload\Pwinty;
use Inkifi\Mediaclip\H\Logger as L;
use Magento\Catalog\Model\Product;
use Mangoit\MediaclipHub\Model\Orders as MO;
use Mangoit\MediaclipHub\Setup\UpgradeSchema as Schema;
use Zend\Log\Logger as zL;
// 2019-02-24
final class AvailableForDownload {
	/**
	 * 2019-02-24
	 * @used-by p()
	 */
	private function __construct() {}

	/**
	 * 2019-02-24
	 * @used-by p()
	 */
	private function _p() {
    	$ev = Ev::s(); /** @var Ev $ev */
    	$l = ikf_logger('mediaclip_orders_download_shipment_status'); /** @var zL $l */
		$l->info($ev->oidE());
		$l->info($ev->j());
		$mo = $ev->mo(); /** @var MO $mo */
		L::l('mediaclipOrderData'); L::l($mo->getData());
		$mo->markAsDownloaded();
		$f = $ev->folder(); /** @var string $f */
		L::l("Upload folder: $f");
		'pwinty' === $f ? Pwinty::p() : Pureprint::p();
	}

	/**
	 * 2019-02-24
	 * @used-by \Mangoit\MediaclipHub\Controller\Index\OrderStatusUpdateEndpoint::execute()
	 */
	static function p() {$i = new self; /** @var self $i */ $i->_p();}
}