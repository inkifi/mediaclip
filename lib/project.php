<?php
use Mangoit\MediaclipHub\Model\Mediaclip;
/**
 * 2019-02-26
 * A result looks like:
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
 * @param string $id  A string like «4cbe2c38-fa9e-4760-966f-f42f021bef84»
 * @return array(string => mixed)
 */
function ikf_m_project_details($id) {
	$m = df_new_om(Mediaclip::class); /** @var Mediaclip $m */
	$m->load($id, 'project_id');
	return df_json_decode($m['project_details']);
}