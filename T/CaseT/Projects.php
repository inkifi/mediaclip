<?php
namespace Inkifi\Mediaclip\T\CaseT;
use Inkifi\Mediaclip\API\Facade\User as F;
use Magento\Sales\Model\Order as O;
// 2019-01-11
final class Projects extends \Inkifi\Mediaclip\T\CaseT {
	/** @test 2019-01-11 */
	function t00() {}

	/** 2019-01-11 */
	function t01() {
		$o = df_order(50759); /** @var O $o */
		$f = new F(65963, $o->getStore());
		echo df_json_encode($f->projects());
	}

	/** 2019-01-11 */
	function t02() {
		$o = df_order(50759); /** @var O $o */
		$f = new F('c03eaad5-9cd9-55d4-eb6c-e55112388530', $o->getStore());
		echo df_json_encode($f->projects());
	}
}