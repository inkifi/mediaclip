<?php
namespace Inkifi\Mediaclip\API;
// 2019-01-12
/** @used-by \Inkifi\Mediaclip\API\Client::responseValidatorC() */
final class Validator extends \Df\API\Response\Validator {
	/**
	 * 2019-01-12 https://doc.mediacliphub.com/pages/Api/errorHandling.html
	 * @override
	 * @see \Df\API\Exception::long()
	 * @used-by valid()
	 * @used-by \Df\API\Client::_p()
	 * @return string|null
	 */
	function long() {return $this->r('message');}

	/**
	 * 2019-01-12
	 * @override
	 * @see \Df\API\Response\Validator::valid()
	 * @used-by \Df\API\Response\Validator::validate()
	 * @return bool
	 */
	function valid() {return !$this->long();}
}