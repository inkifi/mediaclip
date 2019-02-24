<?php
namespace Inkifi\Mediaclip\H;
use Inkifi\Mediaclip\Event as Ev;
// 2019-02-24
final class Logger {
	/**
	 * 2018-11-02
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload::_p()
	 * @used-by \Mangoit\MediaclipHub\Controller\Index\OrderStatusUpdateEndpoint::execute()
	 * @param mixed $d
	 */
    static function l($d) {$oidI = Ev::s()->oidI(); df_report(
    	"OrderStatusUpdate/{$oidI}.log", is_string($d) ? $d : df_json_encode($d), true
	);}
}