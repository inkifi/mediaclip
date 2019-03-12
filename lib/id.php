<?php
use Inkifi\Mediaclip\Settings as S;
use Magento\Sales\Model\Order as O;
use Magento\Store\Model\Store;
/**
 * 2018-08-16
 * «Modify orders numeration for Mediaclip»
 * https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/1
 * @used-by \Inkifi\Mediaclip\Event::oidI()
 * @used-by \Mangoit\MediaclipHub\Controller\Index\OrderStatusUpdateEndpoint::execute()
 * @used-by \Mangoit\MediaclipHub\Controller\Index\PwintyOrderStatusUpdate::execute()
 * @param string $v
 * @return int
 */
function ikf_eti($v) {return intval(df_last(explode('-', $v)));}

/**
 * 2018-08-16
 * «Modify orders numeration for Mediaclip»
 * https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/1
 * @used-by \Mangoit\MediaclipHub\Controller\Index\MediaclipOrderUpdate::execute()
 * @used-by \Mangoit\MediaclipHub\Observer\CheckoutSuccess::post()
 * @param int|string|O $v
 * @return string
 */
function ikf_ite($v) {return dfcf(function($v) {
	list($v, $s) = $v instanceof O ? [$v->getId(), $v->getStore()] : [$v, null]; /** @var Store|null $s */
	return !df_contains(S::s()->id($s), 'staging') ? $v : "staging-$v";
}, [$v]);}