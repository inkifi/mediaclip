<?php
use Magento\Sales\Model\Order\Item as OI;
use Mangoit\MediaclipHub\Model\Mediaclip;
/**
 * 2019-01-31
 * @used-by \Inkifi\Consolidation\Processor::pids()
 * @used-by \Mangoit\MediaclipHub\Observer\CheckoutSuccess::post()
 * @param OI $oi
 * @return string|null
 */
function ikf_oi_pid(OI $oi) {return $oi['mediaclip_project_id'] ?:
	dfa(df_eta(df_find(function(array $a) {return
		'Project' === dfa($a, 'label')
	;}, df_eta($oi->getProductOptionByCode('options')))), 'value')
;}

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
function ikf_project_details($id) {
	$m = df_new_om(Mediaclip::class); /** @var Mediaclip $m */
	$m->load($id, 'project_id');
	return df_json_decode($m['project_details']);
}