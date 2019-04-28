<?php
use Mangoit\MediaclipHub\Model\ResourceModel\Product\Collection as mPC;
/**
 * 2019-03-04
 * @used-by \Mangoit\MediaclipHub\Controller\Product\Edit::getMediaclipProductData()
 * @used-by \Mangoit\MediaclipHub\Helper\Data::getMediaClipProductName()
 * @used-by \Mangoit\MediaclipHub\Model\Product::bySku()
 * @return mPC
 */
function ikf_product_c() {return df_new_om(mPC::class);}