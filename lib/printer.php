<?php
use Inkifi\Mediaclip\Printer;
use Magento\Catalog\Model\Product as P;
use Magento\Sales\Model\Order\Item as OI;
use Mangoit\MediaclipHub\Setup\UpgradeSchema as Schema;
/**
 * 2019-03-13
 * @used-by \Inkifi\Mediaclip\Event::isPwinty()
 * @param P|OI $p
 * @return bool
 */
function ikf_product_is_pwinty($p) {return Printer::PWINTY === ikf_product_printer($p);}

/**
 * 2019-03-13
 * The `mediaclip_upload_folder` attribute is absent in the `Default` attribute set
 * and presents on in the specialized attribute sets: `Gifting`, `Photobook`, and `Print`.
 * @used-by ikf_api_oi()
 * @used-by ikf_product_is_pwinty()
 * @used-by \Inkifi\Mediaclip\Event::type()
 * @param P|OI $p
 * @return string|null
 */
function ikf_product_printer($p) {return df_product($p)[Schema::P__UPLOAD_FOLDER];}