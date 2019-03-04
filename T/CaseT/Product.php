<?php
namespace Inkifi\Mediaclip\T\CaseT;
use Inkifi\Mediaclip\API\Entity\Order\Item as mOI;
use Inkifi\Mediaclip\API\Entity\Project;
use Mangoit\MediaclipHub\Model\Product as mP;
// 2019-03-04
final class Product extends \Inkifi\Mediaclip\T\CaseT {
	/** 2019-03-04 */
	function t00() {}

	/**
	 * @test 2019-03-04
	 */
	function t01() {
		$mOI = ikf_api_oi(60055)[0]; /** @var mOI $mOI */
		$project = $mOI->project(); /** @var Project $project */
		$mP = $project->mProduct(); /** @var mP $mP */
		echo df_json_encode($mP->getData());
	}
}