<?php
use Magento\Catalog\Model\Product as P;
use Mangoit\MediaclipHub\Model\Product as mP;
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
 * @used-by \Inkifi\Mediaclip\API\Entity\Order\Item::mProduct()
 * @param P|string $p
 * @return mP
 */
function ikf_product($p) {
	$r = df_new_om(mP::class); /** @var mP $r */
	$r->loadByPlu($p instanceof P ? $p['mediaclip_print_product'] : $p);
	return $r;
}