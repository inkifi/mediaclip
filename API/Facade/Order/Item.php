<?php
namespace Inkifi\Mediaclip\API\Facade\Order;
use Df\API\Client as ClientBase;
use Inkifi\Mediaclip\API\Client;
use Inkifi\Mediaclip\API\Entity\Order\Item as mOI;
use Inkifi\Mediaclip\API\Entity\Order\Item\File as F;
/**
 * 2019-03-04
 * @used-by ikf_api_oi()
 * @method static Item s()
 */
final class Item extends \Df\API\Facade {
	/**
	 * 2019-03-04
	 * @override
	 * @see \Df\API\Facade::__construct()
	 * @used-by \Inkifi\Mediaclip\API\Entity\Order\Item::files()
	 * @param mOI $mOI
	 */
	function __construct(mOI $mOI) {$this->_mOI = $mOI; parent::__construct($mOI->store());}

	/**
	 * 2019-03-04
	 * @used-by \Inkifi\Mediaclip\API\Entity\Order\Item::files()
	 * @return F[]
	 */
	function files() {return array_values(df_map(function(array $a) {return
		new F($a)
	;}, $this->get(null)['files']));}

	/**
	 * 2019-03-04
	 * @override
	 * @see \Df\API\Facade::adjustClient()
	 * @used-by \Df\API\Facade::p()
	 * @param Client|ClientBase $c
	 */
	protected function adjustClient(ClientBase $c) {$c->skipStore();}

	/**
	 * 2019-03-04 https://doc.mediacliphub.com/pages/Api/orderLineInformation.html
	 * @override
	 * @see \Df\API\Facade::path()
	 * @used-by \Df\API\Facade::p()
	 * @param int|string|null $id
	 * @param string|null $suffix
	 * @return string
	 */
	protected function path($id, $suffix) {return
		"lines/{$this->_mOI->id()}"
		//"lines/{$this->_mOI->oi()->getId()}"
		//'orders/3bdaf74f-2dc3-433f-a0f4-3f9a4cde9061/'
		//"orders/{$this->_mOI->oi()->getOrderId()}/lines/{$this->_mOI->oi()->getId()}"
		//"orders/{$this->_mOI->oi()->getOrderId()}/lines/{$this->_mOI->id()}"
	;}

	/**
	 * 2019-03-04
	 * @used-by __construct()
	 * @var string
	 */
	private $_mOI;
}