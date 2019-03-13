<?php
use Inkifi\Mediaclip\API\Entity\Order\Item as mOI;
use Inkifi\Mediaclip\API\Facade\Order as F;
/**
 * 2019-02-26
 * https://doc.mediacliphub.com/swagger-ui/index.html#!/orders/getordersOrderId_get_0
 * A response:
 *	[
 *		{
 *			"id": "3e22fb02-abd8-44fe-9ea3-b3b42866ede6",
 *			"projectId": "8cd1f396-d465-403d-8126-c1a1cccde5de",
 *			"projectVersionId": 1272602,
 *			"status": {
 *				"effectiveDateUtc": "2019-02-20T14:00:39.1770000",
 *				"value": "AvailableForDownload"
 *			},
 *			"storeData": {
 *				"lineNumber": 1,
 *				"productId": "79772"
 *			}
 *		}
 *	]
 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::_p()
 * @used-by \Inkifi\Pwinty\AvailableForDownload::_p()
 * @used-by \Inkifi\Mediaclip\T\CaseT\Order\Item::t01()
 * @used-by \Mangoit\MediaclipHub\Helper\Data::downloadAndUploadOrderFilesToServer()
 * @param int $id
 * @return mOI[]
 */
function ikf_api_oi($id) {return df_map(F::s()->get($id)['lines'], function(array $i) {return
	new mOI($i)
;});}
