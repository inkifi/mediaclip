<?php
use Magento\Catalog\Model\Product as P;
use Mangoit\MediaclipHub\Model\ResourceModel\Product\Collection as mPC;
/**
 * 2019-09-04
 * @used-by \Mangoit\MediaclipHub\Controller\Cart\Add::execute()
 * @used-by app/design/frontend/Infortis/ultimo/Magento_Catalog/templates/product/view/addtocart.phtml
 * @param P $p
 * @return bool
 */
function ikf_is_mediaclip_product(P $p) {return 4 != $p->getAttributeSetId() && 'Map' !== df_att_set_name($p);}

/**
 * 2019-03-04
 * @used-by \Mangoit\MediaclipHub\Controller\Product\Edit::getMediaclipProductData()
 * @used-by \Mangoit\MediaclipHub\Helper\Data::getMediaClipProductName()
 * @used-by \Mangoit\MediaclipHub\Model\Product::bySku()
 * @return mPC
 */
function ikf_product_c() {return df_new_om(mPC::class);}