<?php
namespace Inkifi\Mediaclip\T\CaseT;
use Inkifi\Mediaclip\API\Entity\Order\Item as mOI;
use Inkifi\Mediaclip\API\Entity\Project;
use Magento\Catalog\Model\Product as P;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Item as OI;
use Mangoit\MediaclipHub\Model\Product as mP;
// 2019-03-04
final class Product extends \Inkifi\Mediaclip\T\CaseT {
	/** 2019-03-04 */
	function t00() {}

	/** @test 2019-03-04 */
	function t01() {
		$o = df_order(60055); /** @var O $o */
		$oi = df_first($o->getItems()); /** @var OI $oi */
		$p = df_product($oi); /** @var P $p */
		echo df_json_encode($p['mediaclip_print_product']);
	}
}