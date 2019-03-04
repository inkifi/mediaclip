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

	/**
	 * 2019-02-27
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
	 * @return mP
	 */
	function mProduct() {return dfc($this, function() {
		$r = df_new_om(mP::class); /** @var mP $r */
		return $r->load($this['items'][0]['plu'], 'plu');
	});}
}