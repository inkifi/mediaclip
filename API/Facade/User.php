<?php
namespace Inkifi\Mediaclip\API\Facade;
use Df\API\Operation as Op;
use Df\Core\Exception as DFE;
use Magento\Store\Model\Store;
// 2019-01-12
/** @method static User s()  */
final class User extends \Df\API\Facade {
	/**
	 * 2019-01-12
	 * @override
	 * @see \Df\API\Facade::__construct()
	 * @used-by \Inkifi\Consolidation\Processor::f()
	 * @param string $customerId
	 * @param Store|string|int|null $s [optional]
	 */
	function __construct($customerId, $s = null) {$this->_customerId = $customerId; parent::__construct($s);}

	/**
	 * 2019-01-12
	 * @used-by \Inkifi\Consolidation\Processor::consolidate()
	 * @param string $guid
	 * @return Op
	 * @throws DFE
	 */
	function consolidate($guid) {return $this->post(
		['storeData' => ['anonymousUserId' => $guid]], 'consolidation'
	);}


	/**
	 * 2019-01-12
	 * @used-by \Inkifi\Mediaclip\T\CaseT\Projects::t01()
	 * @return array(array(string => string))
	 * @throws DFE
	 */
	function projects() {$m = __FUNCTION__; return df_try(function() use($m) {return
		array_column($this->p([], null, $m, $this->opts()->silent(true))->a($m), 'id')
	;}, []);}

	/**
	 * 2019-01-12
	 * @override
	 * @see \Df\API\Facade::path()
	 * @used-by \Df\API\Facade::p()
	 * @param int|string|null $id
	 * @param string|null $suffix
	 * @return string
	 */
	protected function path($id, $suffix) {return "users/{$this->_customerId}/$suffix";}

	/**
	 * 2019-01-12
	 * @used-by __construct()
	 * @used-by path()
	 * @var string
	 */
	private $_customerId;
}