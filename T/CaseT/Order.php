<?php
namespace Inkifi\Mediaclip\T\CaseT;
use Inkifi\Mediaclip\API\Facade\Order as F;
// 2019-02-26
final class Order extends \Inkifi\Mediaclip\T\CaseT {
	/** 2019-02-26 */
	function t00() {}

	/**
	 * @test 2019-02-26
	 * A response:
	 * {
	 *		"dateCreatedUtc": "2019-02-20T14:00:19.9430000",
	 *		"id": "3bdaf74f-2dc3-433f-a0f4-3f9a4cde9061",
	 *		"lines": [
	 *			{
	 *				"id": "3e22fb02-abd8-44fe-9ea3-b3b42866ede6",
	 *				"projectId": "8cd1f396-d465-403d-8126-c1a1cccde5de",
	 *				"projectVersionId": 1272602,
	 *				"status": {
	 *					"effectiveDateUtc": "2019-02-20T14:00:39.1770000",
	 *					"value": "AvailableForDownload"
	 *				},
	 *				"storeData": {
	 *					"lineNumber": 1,
	 *					"productId": "79772"
	 *				}
	 *			}
	 *		],
	 *		"storeData": {
	 *			"orderId": "60055",
	 *			"userId": "73906"
	 *		},
	 *		"storeId": "inkifi",
	 *		"userId": "64e95b58-e317-4b04-8a33-1434de6a388d"
	 *	}
	 */
	function t01() {echo df_json_encode(F::s()->get(60055)->a());}
}