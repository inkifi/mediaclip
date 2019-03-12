<?php
namespace Inkifi\Mediaclip\H;
use Inkifi\Mediaclip\Event as Ev;
use Inkifi\Mediaclip\H\AvailableForDownload\Pureprint;
use Inkifi\Mediaclip\H\AvailableForDownload\Pwinty;
use Inkifi\Mediaclip\H\Logger as L;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Item as OI;
use Mangoit\MediaclipHub\Model\Orders as MO;
use Mangoit\MediaclipHub\Setup\UpgradeSchema as Schema;
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
		$ev->markOIAsAvailableForDownload();
		$mo = $ev->mo(); /** @var MO $mo */
		L::l('mediaclipOrderData'); L::l($mo->getData());
		if ($ev->areAllOIAvailableForDownload()) {
			// 2019-03-13 This flag is never used.
			$mo->markAsAvailableForDownload();
		}
		if ($ev->areOIOfTheSameTypeAvailableForDownload()) {
			L::l("Upload folder: {$ev->type()}");
			$ev->isPwinty() ? Pwinty::p() : Pureprint::p();
		}
	}

	/**
	 * 2019-02-24
	 * @used-by \Mangoit\MediaclipHub\Controller\Index\OrderStatusUpdateEndpoint::execute()
	 */
	static function p() {$i = new self; /** @var self $i */ $i->_p();}
}