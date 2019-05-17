<?php
use Inkifi\Mediaclip\API\Entity\Project;
use Magento\Sales\Model\Order\Item as OI;
use Mangoit\MediaclipHub\Model\Mediaclip;
/**
 * 2019-01-31
 * @used-by \Inkifi\Consolidation\Processor::pids()
 * @used-by \Inkifi\Mediaclip\Event::oi()
 * @used-by \Mangoit\MediaclipHub\Observer\CheckoutSuccess::post()
 * @param OI $oi
 * @return string|null
 */
function ikf_oi_pid(OI $oi) {return $oi['mediaclip_project_id'] ?:
	dfa(df_eta(df_find(function(array $a) {return
		'Project' === dfa($a, 'label')
	;}, df_eta($oi->getProductOptionByCode('options')))), 'value')
;}
