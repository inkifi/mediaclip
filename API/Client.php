<?php
namespace Inkifi\Mediaclip\API;
use Inkifi\Mediaclip\Settings as S;
use Zend_Http_Client as C;
// 2019-01-11
final class Client extends \Df\API\Client {
	/**
	 * 2019-01-12
	 * @override
	 * @see \Df\API\Client::_construct()
	 * @used-by \Df\API\Client::__construct()
	 */
	protected function _construct() {parent::_construct(); $this->reqJson();  $this->resJson();}

	/**
	 * 2019-01-12 https://doc.mediacliphub.com/pages/Api/authorization.html
	 * @override
	 * @see \Df\API\Client::headers()
	 * @used-by \Df\API\Client::__construct()
	 * @used-by \Df\API\Client::_p()
	 * @return array(string => string)
	 */
	protected function headers() {$s = $this->s(); return
		['Authorization' => df_cc_s('HubApi', base64_encode(implode(':', [$s->id(), $s->key()])))]
		// 2019-02-26 Mediaclip does not allow the `Content-Type` header in `GET` requests.
		+ (C::GET === $this->method() ? [] : ['Content-Type' => 'application/json'])
	;}

	/**
	 * 2019-01-11
	 * @override
	 * @see \Df\API\Client::responseValidatorC()
	 * @used-by \Df\API\Client::p()
	 * @return string
	 */
	protected function responseValidatorC() {return \Inkifi\Mediaclip\API\Validator::class;}

	/**
	 * 2019-01-11
	 * @override
	 * @see \Df\API\Client::urlBase()
	 * @used-by \Df\API\Client::__construct()
	 * @used-by \Df\API\Client::url()
	 * @return string
	 */
	protected function urlBase() {return "https://api.mediacliphub.com/stores/{$this->s()->id()}";}

	/**          
	 * 2019-01-12
	 * @used-by headers()
	 * @used-by urlBase()
	 * @return S
	 */
	private function s() {return dfc($this, function() {return S::s($this->store());});}
}