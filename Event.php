<?php
namespace Inkifi\Mediaclip;
use Magento\Catalog\Model\Product as P;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Item as OI;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as OIC;
use Magento\Store\Model\Store;
use Mangoit\MediaclipHub\Model\Orders as MO;
use Mangoit\MediaclipHub\Setup\UpgradeSchema as Schema;
/**
 * 2018-08-16
 * 2019-02-24
 * 2019-03-12
 * 1) A response looks like:
 *	{
 *		"id": "cd241106-d3c6-4a37-b7c6-6dc7b2841012",
 *		"order": {
 *			"storeData": {
 *				"orderId": "58312"
 *			}
 *		},
 *		"projectId": "e68702ce-9f1f-452c-9ecc-00f8b0a7808c",
 *		"status": {
 *			"effectiveDateUtc": "2019-01-29T11:57:45.6913868Z",
 *			"value": "AvailableForDownload"
 *		},
 *		"storeData": {
 *			"lineNumber": 1,
 *			"productId": "74134",
 *			"properties": {
 *				"option_details": "{\"20530\":\"44379\",\"20639\":\"44848\"}",
 *				"storeProductId": "74134"
 *			}
 *		}
 *	}
 * 2) A single Magento order can contain multiple order items.
 * 2.1) Usually these items have DIFFERENT `mediaclip_project_id` values:
 *		SELECT * FROM
 *			sales_order_item oi1
 *		INNER JOIN
 *			sales_order_item oi2
 *		ON
 *				oi1.item_id <> oi2.item_id
 *			AND
 *				oi1.order_id = oi2.order_id
 *			AND
 *				oi1.mediaclip_project_id <> oi2.mediaclip_project_id
 *		ORDER BY oi1.created_at DESC;
 * 2.2) Sometimes these items have THE SAME `mediaclip_project_id` value:
 *		SELECT * FROM
 *			sales_order_item oi1
 *		INNER JOIN
 *			sales_order_item oi2
 *		ON
 *				oi1.item_id <> oi2.item_id
 *			AND
 *				oi1.order_id = oi2.order_id
 *			AND
 *				oi1.mediaclip_project_id = oi2.mediaclip_project_id
 *		ORDER BY oi1.created_at DESC;
 * An example is the 54609 order: https://inkifi.com/canvaspr_admin/sales/order/view/order_id/54609
 * It has 2 order items with the same «294c156e-e208-4bf0-9196-bea69d170996» value for `mediaclip_project_id`.
 * 3)
 * «Mediaclip sends separate notifications for each order item.
 * It is a "per order line" update, meaning that if your order contains three lines,
 * you will receive an update message for each individual line.»
 * https://doc.mediacliphub.com/pages/Store%20endpoints/statusUpdateEndpoint.html
 * As I understand, usually we can identify the Magento order item related to a particular event
 * by a combination of the `orderId` and `projectId` values
 * (a single `projectId` value does not identify an order because an order can be cancelled and repeated,
 * so 2 different orders can contain order items with the same `projectId` value).
 * There are some rare cases mentioned in the part 2.2 above when a single order
 * contains 2 different order items with the same `projectId` value.
 * See the 54609 order as an example: https://inkifi.com/canvaspr_admin/sales/order/view/order_id/54609
 * I do not know how to identify an order item in an event in this case.
 */
final class Event extends \Df\API\Document {
	/**
	 * 2019-03-13
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload::_p()
	 * @return bool
	 */
	function areAllOIAvailableForDownload() {return $this->_areAllOIAvailableForDownload(
		$this->o()->getItems()
	);}

	/**
	 * 2019-03-13
	 * @return bool
	 */
	function areOIOfTheSameTypeAvailableForDownload() {return $this->_areAllOIAvailableForDownload(
		$this->oiOfTheSameType()
	);}

	/**
	 * 2019-03-13
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload::_p()
	 * @return bool
	 */
	function isPwinty() {return ikf_product_is_pwinty($this->product());}

	/**
	 * 2019-03-13
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload::_p()
	 */
	function markOIAsAvailableForDownload() {
		$oi = $this->oi(); /** @var OI $oi */
		$oi[Schema::OI__ITEM_DOWNLOAD_STATUS] = 1;
		$oi->save();
	}

	/**
	 * 2019-03-13
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload::_p()
	 * @used-by \Inkifi\Pwinty\AvailableForDownload::_p()
	 * @return MO
	 */
	function mo() {return dfc($this, function() {return MO::byOIdE($this->oidE());});}

	/**
	 * 2019-02-27
	 * @used-by store()
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::_p()
	 * @return O
	 */
	function o() {return dfc($this, function() {return df_order($this->oidI());});}

	/**
	 * 2019-03-13 See the comment in the class header.
	 * @used-by markOIAsAvailableForDownload()
	 * @used-by \Inkifi\Pwinty\AvailableForDownload::_p()
	 * @return OI
	 */
	function oi() {return dfc($this, function() {
		$oic = df_oic(); /** @var OIC $oic */
		$oic->addFieldToFilter('mediaclip_project_id', ['eq' => $this->projectId()]);
		$oic->addFieldToFilter('order_id', ['eq' => $this->oidI()]);
		df_assert_eq(1, $oic->count());
		return $oic->getFirstItem();
	});}

	/**
	 * 2019-02-24
	 * @used-by mo()
	 * @used-by oidI()
	 * @used-by \Mangoit\MediaclipHub\Controller\Index\OrderStatusUpdateEndpoint::pAvailableForDownload()
	 * @used-by \Mangoit\MediaclipHub\Controller\Index\OrderStatusUpdateEndpoint::pShipped()
	 * @return string	«58312» or «staging-58312»
	 */
	function oidE() {return dfc($this, function() {return $this['order/storeData/orderId'];});}

	/**
	 * 2019-02-24
	 * @used-by o()
	 * @used-by oi()
	 * @used-by \Mangoit\MediaclipHub\Controller\Index\OrderStatusUpdateEndpoint::l()
	 * @used-by \Mangoit\MediaclipHub\Controller\Index\OrderStatusUpdateEndpoint::pAvailableForDownload()
	 * @used-by \Mangoit\MediaclipHub\Controller\Index\OrderStatusUpdateEndpoint::pShipped()
	 * @return int	«58312»
	 */
	function oidI() {return dfc($this, function() {return ikf_eti($this->oidE());});}

	/**
	 * 2019-03-13
	 * @used-by areOIOfTheSameTypeAvailableForDownload()
	 * @return OI[]
	 */
	function oiOfTheSameType() {return dfc($this, function() {
		$t = $this->type(); /** @var string $t */
		return array_values(array_filter(
			$this->o()->getItems()
			,function(OI $i) use($t) {return $t === $i->getProduct()[Schema::P__UPLOAD_FOLDER];}
		));
	});}

	/**
	 * 2019-02-27
	 * @used-by oi()
	 * @used-by \Inkifi\Pwinty\AvailableForDownload::_p()
	 * @used-by \Inkifi\Mediaclip\H\Shipped::p()
	 * @return string	«4a9a1d14-0807-42ab-9a03-e2d54d9b8d12»
	 */
	function projectId() {return $this['projectId'];}

	/**
	 * 2019-03-13
	 * @used-by \Inkifi\Pwinty\AvailableForDownload::_p()
	 * @return Store
	 */
	function store() {return $this->o()->getStore();}

	/**
	 * 2019-03-13
	 * @used-by isPwinty()
	 * @used-by oiOfTheSameType()
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload::_p()
	 * @return string	«ascendia», «prodigi», «pwinty»
	 */
	function type() {return ikf_product_printer($this->product());}

	/**
	 * 2019-03-13
	 * @used-by areAllOIAvailableForDownload()
	 * @used-by areOIOfTheSameTypeAvailableForDownload()
	 * @param OI[] $oiA
	 * @return bool
	 */
	private function _areAllOIAvailableForDownload(array $oiA) {return !df_find(
		$oiA, function(OI $oi) {return !$oi[Schema::OI__ITEM_DOWNLOAD_STATUS];}
	);}

	/**
	 * 2019-03-13
	 * @used-by isPwinty()
	 * @used-by type()
	 * @return P
	 */
	private function product() {return dfc($this, function() {return df_product($this->productId());});}

	/**
	 * 2019-03-13
	 * @used-by product()
	 * @return int	«74134»
	 */
	private function productId() {return intval($this['storeData/productId']);}

	/**
	 * 2019-02-24
	 * @used-by \Inkifi\Mediaclip\H\Shipped::p()
	 * @used-by \Inkifi\Pwinty\AvailableForDownload::images()
	 * @used-by \Mangoit\MediaclipHub\Controller\Index\OrderStatusUpdateEndpoint::ev()
	 * @return Event
	 */
	static function s() {return dfcf(function() {return new self(df_json_decode(file_get_contents(
		'php://input'
	)));});}
}