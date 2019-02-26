<?php
use Zend\Log\Logger as L;
use Zend\Log\Writer\Stream as W;
/**
 * 2019-02-24 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload::_p()
 * @used-by \Mangoit\MediaclipHub\Controller\Index\OneflowResponse::execute()
 * @used-by \Mangoit\MediaclipHub\Controller\Product\Edit::execute()
 * @used-by \Mangoit\MediaclipHub\Helper\Data::GetTokenForEndUser()
 * @used-by
 * @used-by
 * @used-by
 * @param string $n
 * @return L
 */
function ikf_logger($n) {
	$r = new L; /** @var L $r */
	$r->addWriter(new W(BP . "/var/log/$n.log"));
	return $r;
}