<?php
namespace Inkifi\Mediaclip\API\Entity;
use Mangoit\MediaclipHub\Model\Product as mP;
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
 * @used-by ikf_api_oi()
 */
final class Project extends \Df\API\Document {
	/**
	 * 2019-02-26
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::_p()
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pwinty::_p()
	 * @return string «f113e39c-ccc9-4dec-bc38-a5825493647e»
	 */
	function id() {return $this['projectId'];}
}