<?php
namespace Inkifi\Mediaclip\T\CaseT;
use Inkifi\Mediaclip\API\Facade\User as F;
use Magento\Sales\Model\Order as O;
// 2019-01-11
final class Projects extends \Inkifi\Mediaclip\T\CaseT {
	/** 2019-01-11 */
	function t00() {}

	/** @test 2019-01-11 */
	function t01() {
		$o = df_order(50759); /** @var O $o */
		$f = new F(65963, $o->getStore());
		echo df_json_encode($f->projects()->a());
	}
}