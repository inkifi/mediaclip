<?php
namespace Inkifi\Mediaclip\API\Facade;
use Magento\Store\Model\Store;
/**
 * 2019-02-26
 * @used-by ikf_api_oi()
 * @method static Order s()
 */
final class Order extends \Df\API\Facade {
	/**
	 * 2019-02-26
	 * @override
	 * @see \Df\API\Facade::storeByP()
	 * @used-by \Df\API\Facade::p()
	 * @param int|string $p
	 * @return Store|null
	 */
	protected function storeByP($p) {return df_order($p)->getStore();}
}