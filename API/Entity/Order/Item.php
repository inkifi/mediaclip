<?php
namespace Inkifi\Mediaclip\API\Entity\Order;
use Inkifi\Mediaclip\API\Entity\Order\Item\File;
use Inkifi\Mediaclip\API\Facade\Order\Item as fOI;
use Magento\Catalog\Model\Product as P;
use Magento\Sales\Model\Order\Item as OI;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as OIC;
use Magento\Store\Model\Store;
use Mangoit\MediaclipHub\Model\Product as mP;
/**
 * 2019-02-26
 * A data item:
 *	{
 *		"id": "3e22fb02-abd8-44fe-9ea3-b3b42866ede6",
 *		"projectId": "8cd1f396-d465-403d-8126-c1a1cccde5de",
 *		"projectVersionId": 1272602,
 *		"status": {
 *			"effectiveDateUtc": "2019-02-20T14:00:39.1770000",
 *			"value": "AvailableForDownload"
 *		},
 *		"storeData": {
 *			"lineNumber": 1,
 *			"productId": "79772"
 *		}
 *	}
 * @used-by ikf_api_oi()
 */
final class Item extends \Df\Core\O {
	/**
	 * 2019-03-04
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::pOI()
	 * @used-by \Inkifi\Mediaclip\T\CaseT\Order\Item::t01()
	 * @used-by \Inkifi\Pwinty\AvailableForDownload::images()
	 * @return File[]
	 */
	function files() {return dfc($this, function() {
		$f = new fOI($this); /** @var fOI $f */
		return $f->files();
	});}

	/**
	 * 2019-02-26
	 * @used-by \Inkifi\Mediaclip\API\Facade\Order\Item::path()
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::pOI()
	 * @return string «f113e39c-ccc9-4dec-bc38-a5825493647e»
	 */
	function id() {return $this['id'];}

	/**
	 * 2019-03-04
	 * A result:
	 * {
	 *		"created_at": "2017-09-21 04:54:52",
	 *		"dust_jacket_popup": null,
	 *		"frame_colour": null,
	 *		"ftp_json": "1",
	 *		"id": "8",
	 *		"include_quantity_in_json": "0",
	 *		"json_code": null,
	 *		"module": "3",
 	 *		"plu": "INKIFI-VP",
 	 *		"product_id": "$(package:inkifi/prints)/products/vintage-polaroids",
	 *		"product_label": "Vintage Prints Web",
	 *		"product_theme": null,
	 *		"pwinty_product_name": null,
	 *		"updated_at": "2018-03-16 06:06:24"
	 *	}
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::pOI()
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::writeLocal()
	 * @used-by \Inkifi\Pwinty\AvailableForDownload::images()
	 * @return mP
	 */
	function mProduct() {return dfc($this, function() {
		$r = df_new_om(mP::class); /** @var mP $r */
		$p = $this->product(); /** @var P $p */
		$r->loadByPlu(
			df_first(array_filter(array_map(function(array $o) {return df_first(df_fetch_col(
				'catalog_product_option_type_value', 'sku', 'option_type_id', $o['option_value']
			));}, $this->oi()->getProductOptionByCode('options'))))
			?: $p[implode('_', ['mediaclip', $p['mediaclip_module'], 'product'])]
		);
		df_assert($r->getId());
		return $r;
	});}

	/**
	 * 2019-02-27
	 * `return df_oi($this->projectId(), 'mediaclip_project_id')` does not work correctly here
	 * due to a stupid software architecture implemented by a previous (Indian) developer:
	 * the `sales_order_item` table can contain multiple records
	 * with the same `mediaclip_project_id` value:
	 * 		SELECT mediaclip_project_id, COUNT(*) c
	 * 		FROM sales_order_item
	 * 		GROUP BY mediaclip_project_id
	 * 		HAVING c > 1
	 * 		ORDER BY created_at DESC;
	 * Such records have distinct `order_id` values
	 * and they belong to repetitive order placement attempts.
	 * @used-by ikf_api_oi()
	 * @used-by mProduct()
	 * @used-by store()
	 * @used-by \Inkifi\Mediaclip\API\Facade\Order\Item::path()
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::pOI()
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::writeLocal()
	 * @return OI
	 */
	function oi() {return $this->oic()->getLastItem();}

	/**
	 * 2019-02-26
	 * @used-by oic()
	 * @return string	«8cd1f396-d465-403d-8126-c1a1cccde5de»
	 */
	function projectId() {return $this['projectId'];}

	/**
	 * 2019-03-04
	 * @used-by \Inkifi\Mediaclip\API\Facade\Order\Item::__construct()
	 * @return Store
	 */
	function store() {return $this->oi()->getStore();}

	/**
	 * 2019-02-27
	 * @used-by oi()
	 * @return OIC
	 */
	private function oic() {return dfc($this, function() {return df_oic()->addFieldToFilter(
		'mediaclip_project_id', ['eq' => $this->projectId()]
	);});}

	/**
	 * 2019-02-26
	 * @used-by mProduct()
	 * @return P
	 */
	private function product() {return dfc($this, function() {return df_product(
		(int)$this['storeData/productId']
	);});}
}