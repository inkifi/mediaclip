<?php
use Zend\Log\Logger as L;
use Zend\Log\Writer\Stream as W;
/**
 * 2019-02-24 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload::_p()
 * @param string $n
 * @return L
 */
function ikf_logger($n) {
	$r = new L; /** @var L $r */
	$r->addWriter(new W(BP . "/var/log/$n.log"));
	return $r;
}