<?php
namespace Inkifi\Mediaclip\API\Entity\Order;
use Inkifi\Mediaclip\API\Entity\Project;
use Magento\Catalog\Model\Product as P;
use Magento\Sales\Model\Order\Item as OI;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as OIC;
use Mangoit\MediaclipHub\Model\Mediaclip;
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
final class Item extends \Df\API\Document {
	/**
	 * 2019-02-26
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::_p()
	 * @used-by \Mangoit\MediaclipHub\Helper\Data::downloadAndUploadOrderFilesToServer()
	 * @return string «f113e39c-ccc9-4dec-bc38-a5825493647e»
	 */
	function id() {return $this['id'];}

	/**
	 * 2019-02-26
	 * @used-by \Mangoit\MediaclipHub\Helper\Data::downloadAndUploadOrderFilesToServer()
	 * @return bool
	 */
	function isAvailableForDownload() {return 'AvailableForDownload' === $this['status/value'];}

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
	 * @return OI
	 */
	function oi() {return $this->oic()->getLastItem();}

	/**
	 * 2019-02-27
	 * @used-by oi()
	 * @return OIC
	 */
	function oic() {return dfc($this, function() {return df_oic()->addFieldToFilter(
		'mediaclip_project_id', ['eq' => $this->projectId()]
	);});}

	/**
	 * 2019-02-26
	 * @used-by \Mangoit\MediaclipHub\Helper\Data::downloadAndUploadOrderFilesToServer()
	 * @return P
	 */
	function product() {return dfc($this, function() {return df_product(
		(int)$this['storeData/productId']
	);});}

	/**
	 * 2019-02-26
	 * A data item:
	 * {
	 *		"storeData": {
	 *			"userId": "74312"
	 *		},
	 *		"projectId": "f113e39c-ccc9-4dec-bc38-a5825493647e",
	 *		"properties": {
	 *			"storeProductId": "80314"
	 *		},
	 *		"items": [
	 *			{
	 *				"productId": "$(package:inkifi/us-prints)/products/vintage-polaroids",
	 *				"plu": "US-INKIFI-VP",
	 *				"quantity": 80,
	 *				"properties": {
	 *					"storeProductId": "80314"
	 *				}
	 *			}
	 *		]
	 *	}
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::_p()
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pwinty::_p()
	 * @used-by \Mangoit\MediaclipHub\Helper\Data::downloadAndUploadOrderFilesToServer()
	 * @return Project
	 */
	function project() {return dfc($this, function() {
		$m = df_new_om(Mediaclip::class); /** @var Mediaclip $m */
		$m->load($this->projectId(), 'project_id');
		return new Project(df_json_decode($m['project_details']));
	});}

	/**
	 * 2019-02-26
	 * @used-by oic()
	 * @used-by project()
	 * @return string «8cd1f396-d465-403d-8126-c1a1cccde5de»
	 */
	function projectId() {return $this['projectId'];}
}