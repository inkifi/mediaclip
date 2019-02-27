<?php
namespace Inkifi\Mediaclip\H;
use Inkifi\Mediaclip\Event as Ev;
use Inkifi\Mediaclip\H\AvailableForDownload\Pureprint;
use Inkifi\Mediaclip\H\AvailableForDownload\Pwinty;
use Inkifi\Mediaclip\H\Logger as L;
use Magento\Catalog\Model\Product;
use Magento\Sales\Model\Order\Item as OI;
use Mangoit\MediaclipHub\Model\Orders as mOrder;
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
		//Set mediaclip order status to 1 as the order is downloaded
		$mOrder = df_new_om(mOrder::class); /** @var mOrder $mOrder */
		$mOrderC = $mOrder->getCollection();
		$mOrderC->addFieldToFilter('magento_order_id', ['eq' => $ev->oidE()]);
		// 2018-08-17 Dmitry Fedyuk
		if ($mOrderData = df_first($mOrderC->getData())) {
			L::l('mediaclipOrderData'); L::l($mOrderData);
			$mOrder->setId($mOrderData['id']);
			$mOrder->setOrderDownloadStatus(1);
			$mOrder->save();
			$product_id = $ev['storeData/productId'];
			$product = df_new_om(Product::class)->load($product_id);
			$uploadfolder = $product->getMediaclipUploadFolder();
			L::l("Upload folder: $uploadfolder");
			'pwinty' === $uploadfolder ? Pwinty::p() : Pureprint::p();
		}
	}

	/**
	 * 2019-02-24
	 * @used-by \Mangoit\MediaclipHub\Controller\Index\OrderStatusUpdateEndpoint::execute()
	 */
	static function p() {$i = new self; /** @var self $i */ $i->_p();}
}