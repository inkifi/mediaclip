<?php
namespace Inkifi\Mediaclip\Settings;
use Magento\Framework\App\ScopeInterface as S;
use Magento\Store\Model\Store;
// 2019-03-13
/** @method static Pwinty s() */
final class Pwinty extends \Df\Config\Settings {
	/**
	 * 2019-03-13 «Merchant ID»
	 * @param null|string|int|S|Store|array(string, int) $s [optional]
	 * @return string
	 */
	function id($s = null) {return $this->v('merchant_id', $s);}

	/**
	 * 2019-03-13 «API KEY»
	 * @param null|string|int|S|Store|array(string, int) $s [optional]
	 * @return string
	 */
	function key($s = null) {return $this->v('pwinty_api_key', $s);}

	/**
	 * 2019-03-13
	 * @override
	 * @see \Df\Config\Settings::prefix()
	 * @used-by \Df\Config\Settings::v()
	 * @return string
	 */
	protected function prefix() {return 'api/pwinty_api_auth';}
}