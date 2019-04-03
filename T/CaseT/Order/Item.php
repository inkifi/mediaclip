<?php
namespace Inkifi\Mediaclip\T\CaseT\Order;
use Inkifi\Mediaclip\API\Entity\Order\Item as mOI;
// 2019-03-04
final class Item extends \Inkifi\Mediaclip\T\CaseT {
	/** @test 2019-03-04 */
	function t00() {}

	/** 2019-03-04 */
	function t01() {
		$mOI = df_first(ikf_api_oi(60055)); /** @var mOI $mOI */
		echo df_json_encode($mOI->files()[0]->a());
	}
}