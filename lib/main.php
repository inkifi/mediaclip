<?php
use Inkifi\Mediaclip\Settings as S;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Item as OI;
use Magento\Store\Model\Store;
/**
 * 2018-08-16
 * «Modify orders numeration for Mediaclip»
 * https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/1
 * @used-by \Mangoit\MediaclipHub\Controller\Index\OrderStatusUpdateEndpoint::execute()
 * @used-by \Mangoit\MediaclipHub\Controller\Index\PwintyOrderStatusUpdate::execute()
 * @param string $v
 * @return string
 */
function ikf_eti($v) {return df_last(explode('-', $v));}

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

/**
 * 2019-01-31
 * @used-by \Inkifi\Consolidation\Processor::pids()
 * @used-by \Mangoit\MediaclipHub\Observer\CheckoutSuccess::post()
 * @param OI $oi
 * @return string|null
 */
function ikf_oi_pid(OI $oi) {return $oi['mediaclip_project_id'] ?:
	dfa(df_eta(df_find(function(array $a) {return
		'Project' === dfa($a, 'label')
	;}, df_eta($oi->getProductOptionByCode('options')))), 'value')
;}
