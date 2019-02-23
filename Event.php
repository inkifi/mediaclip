<?php
namespace Inkifi\Mediaclip;
/**
 * 2018-08-16
 * 2019-02-24
 * A response looks like:
 * 	{
 *		"id": "3ea5265e-46cf-42cd-97a8-1c292169e006",
 *		"order": {
 *			"storeData": {
 *				"orderId": "40826"
 *			}
 *		},
 *		"storeData": {
 *			"lineNumber": 1,
 *			"productId": "79772"
 *		},
 *		"projectId": "4a9a1d14-0807-42ab-9a03-e2d54d9b8d12",
 *		"status": {
 *			"value": "AvailableForDownload",
 *			"effectiveDateUtc": "2018-08-15T22:51:43.5408397Z"
 *		}
 *	}
 */
final class Event extends \Df\API\Document {
	/**
	 * 2019-02-24
	 * @used-by oidI()
	 * @used-by \Mangoit\MediaclipHub\Controller\Index\OrderStatusUpdateEndpoint::pAvailableForDownload()
	 * @used-by \Mangoit\MediaclipHub\Controller\Index\OrderStatusUpdateEndpoint::pShipped()
	 * @return string
	 */
	function oidE() {return dfc($this, function() {return $this['order/storeData/orderId'];});}

	/**
	 * 2019-02-24
	 * @used-by \Mangoit\MediaclipHub\Controller\Index\OrderStatusUpdateEndpoint::l()
	 * @used-by \Mangoit\MediaclipHub\Controller\Index\OrderStatusUpdateEndpoint::pAvailableForDownload()
	 * @used-by \Mangoit\MediaclipHub\Controller\Index\OrderStatusUpdateEndpoint::pShipped()
	 * @return string
	 */
	function oidI() {return dfc($this, function() {return ikf_eti($this->oidE());});}

	/**
	 * 2019-02-24
	 * @used-by \Mangoit\MediaclipHub\Controller\Index\OrderStatusUpdateEndpoint::ev()
	 * @return Event
	 */
	static function s() {return dfcf(function() {return new self(df_json_decode(file_get_contents(
		'php://input'
	)));});}
}