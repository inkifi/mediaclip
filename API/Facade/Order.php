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
	 * @see \Df\API\Facade::storeById()
	 * @used-by \Df\API\Facade::p()
	 * @param int|string $id
	 * @return Store|null
	 */
	protected function storeById($id) {return df_order($id)->getStore();}
}