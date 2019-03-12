<?php
namespace Inkifi\Mediaclip\H\AvailableForDownload;
use Inkifi\Mediaclip\API\Entity\Order\Item as mOI;
use Inkifi\Mediaclip\API\Entity\Order\Item\File as F;
use Inkifi\Mediaclip\Event as Ev;
use Magento\Customer\Model\Customer;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Item as OI;
use Mangoit\MediaclipHub\Model\Product as mP;
use Zend\Log\Logger as zL;
// 2019-02-24
final class Pureprint {
	/**
	 * 2019-02-24
	 * @used-by p()
	 */
	private function __construct() {}

	/**
	 * 2019-02-24
	 * @used-by p()
	 */
	private function _p() {
		$ev = Ev::s(); /** @var Ev $ev */
		// 2018-08-16 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		// «Modify orders numeration for Mediaclip»
		// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/1
		$o = $ev->o(); /** @var O $o */
		/**
		 * 2019-03-13
		 * 1) I have ported this algorithm from the previous implementation,
		 * but it seems to be wrong,
		 * because the current `AvailableForDownload` event is related to a particular order item only,
		 * not to all items of the current order:
		 * «Mediaclip sends separate notifications for each order item.
		 * It is a "per order line" update, meaning that if your order contains three lines,
		 * you will receive an update message for each individual line.»
		 * https://doc.mediacliphub.com/pages/Store%20endpoints/statusUpdateEndpoint.html
		 * 2) In a right implementation we would use @see \Inkifi\Mediaclip\Event::oi()
		 * 3) The problem is only exists for Pureprint.
		 * It does not exist for Pwinty because the Pwinty's handler
		 * uses the @see \Mangoit\MediaclipHub\Setup\UpgradeSchema::OI__ITEM_DOWNLOAD_STATUS flag
		 * and it passes an order to Pwinty only when ALL order items have the flag set.
		 */
		$mItems = ikf_api_oi($o->getId()); /** @var mOI[] $mItems */
		if ($items = df_map($mItems, function(mOI $mOI) {return $this->pOI($mOI);})) {
			/** @var array(array(string => mixed)) $items */
			$d = [
				'destination' => ['name' => 'pureprint']
				,'orderData' => ['items' => $items, 'sourceOrderId' => $o->getId()]
				,'shipments' => [$this->pShipment($o)]
			]; /** @var array(string => array(mixed)) $d */
			self::zl()->info(json_encode($d));
			// 2018-08-16 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
			// "Replace the «/home/canvaspr/dev2.inkifi.com/html/ftp_json25june/»
			// hardcoded filesystem path with a dynamics one":
			// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/3
			$contents = json_encode($d, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT); /** @var string $contents */
			$file = "{$o->getIncrementId()}.json"; /** @var string $file */
			self::writeLocal($mItems, $file, $contents);
			self::writeRemote($file, $contents);
		}
	}

	/**
	 * 2018-11-02 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * «Generate JSON data for photo-books»: https://www.upwork.com/ab/f/contracts/21011549
	 * https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/9
	 * @used-by pOI()
	 * @param F $f
	 * @param string $m
	 * @return string
	 */
    private function code(F $f, $m) {return $f->id() ?: (
    	'gifting' === ($m = strtolower($m)) ? 'gift' : ('print' === $m ? 'prints-set-01' : 'photobook-jacket')
	);}

	/**
	 * 2019-02-27
	 * @used-by pOI()
	 * @param mOI $mOI
	 * @return array(string => mixed)
	 */
    private function pOI(mOI $mOI) {
    	$r = []; /** @var array(string => mixed) $r */
		$mP = $mOI->mProduct(); /** @var mP $mP */
		if ($mP->sendJson() && ($files = $mOI->files())) { /** @var F[] $files */
			/**
			 * 2018-11-02 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
			 * «Generate JSON data for photo-books»
			 * https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/9
			 * 2019-03-12
			 * Data item examples:
			 * 1)
			 *		{
			 *			"id": "photobook-jacket",
			 *			"productId": "$(package:inkifi/photobooks)/products/hard-cover-gray-210x210mm-70",
			 *			"plu": "INKIFI-HCB210-M-70",
			 *			"quantity": 1,
			 *			"url": "https://renderstouse.blob.core.windows.net/0c25168e-eda3-41d4-b266-8259566d2507/dust.pdf?sv=2018-03-28&sr=c&sig=XzCB%2B2CWlpqNFqVf6CnoVr8ICDGufTexaNqyzxMDUx8%3D&st=2018-11-02T19%3A36%3A41Z&se=2018-12-02T19%3A38%3A41Z&sp=r",
			 *			"order": 0
			 *		}
			 * 2)
			 *		{
			 *			"id": "photobook-pages",
			 *			"productId": "$(package:inkifi/photobooks)/products/hard-cover-gray-210x210mm-70",
			 *			"plu": "INKIFI-HCB210-M-70",
			 *			"quantity": 1,
			 *			"url": "https://renderstouse.blob.core.windows.net/0c25168e-eda3-41d4-b266-8259566d2507/0d0e8542-db8d-475b-95bb-33156dc6551a_0c25168e-eda3-41d4-b266-8259566d2507.pdf?sv=2018-03-28&sr=c&sig=maMnPG2XIrQuLC3mArAgf3YKrM6EzFwNMggwApqMTeo%3D&st=2018-11-02T19%3A36%3A43Z&se=2018-12-02T19%3A38%3A43Z&sp=r",
			 *			"order": 1
			 *		}
			*/
			$oi = $mOI->oi(); /** @var OI $oi */
			/** @var string $mod */
			$mod = df_attribute_set(df_product($oi->getProductId()))->getAttributeSetName();
			$r = [
				'sku' => $mP->plu()
				,'sourceItemId' => $mOI->id()
				,'components' => array_values(df_map($files, function(F $f) use($mod, $mP) {return [
					'code' => $mP->jsonCode() ?: $this->code($f, $mod)
					,'fetch' => true
					,'path' => $f->url()
				];}))
				,'quantity' => $mP->includeQuantityInJson() ? (int)$oi->getQtyOrdered() : 1
			];
		}
		return $r;
	}

	/**
	 * 2019-03-12
	 * @used-by _p()
	 * @param O $o
	 * @return array(string => mixed)
	 */
	function pShipment(O $o) {
		$shippingMethod = $o->getShippingMethod();
		$address = $o->getShippingAddress();
		$postcode = $address->getPostcode();
		$countryCode = $address->getCountryId();
		$region = $address->getRegion();
		$telephone = $address->getTelephone();
		if ($address->getCompany() != '') {
			$street1 = $address->getCompany() . ',' . $address->getStreet()[0];
		}
		else {
			$street1 = $address->getStreet()[0];
		}
		if (isset($address->getStreet()[1])) {
			$street2 = $address->getStreet()[1];
		}
		else {
			$street2 = '';
		}
		$city = $address->getCity();
		$customerId = $o->getCustomerId();
		$customer = df_new_om(Customer::class)->load($customerId);
		$name = $address->getFirstname().' '.$address->getLastname();
		$email = $customer['email'];
		return [
		   'shipTo' => [
				'name' => $name
				,'address1'=> $street1
				,'address2' => $street2
				,'town' => $city
				,'postcode' => $postcode
				,'isoCountry' => $countryCode
				,'state' => $region
				,'email' => $email
				,'phone' => $telephone
		   ],
		   'carrier' => ['alias' => $shippingMethod]
		];
	}

	/**
	 * 2019-02-24
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload::_p()
	 */
	static function p() {$i = new self; /** @var self $i */ $i->_p();}

	/**
	 * 2019-02-27
	 * @used-by _p()
	 * @param mOI[] $mItems
	 * @param string $file
	 * @param string $contents
	 */
	private static function writeLocal(array $mItems, $file, $contents) {
		$mOI = df_first($mItems); /** @var mOI $mOI */
		$oi = $mOI->oi(); /** @var OI $oi */
		$o = $oi->getOrder(); /** @var O $o */
		$dir = df_cc_path(
			BP, 'ftp_json', date('d-m-Y', strtotime($o->getCreatedAt()))
			,$o->getIncrementId(), $oi->getId(), $mOI->mProduct()->label()
		); /** @var string $dir */
		df_file()->mkdir($dir);
		file_put_contents("$dir/$file", $contents);
	}

	/**
	 * 2019-02-27
	 * @used-by _p()
	 * @param string $file
	 * @param string $contents
	 */
	private static function writeRemote($file, $contents) {
		// 2018-08-20 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		// «FTP upload to ftp.pureprint.com has stopped working»
		// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/6
		df_sftp()->open([
			'host' => 'ftp.pureprint.com'
			,'username' => 'Inkifi'
			,'password' => 'Summ3rD4ys!'
		]);
		df_sftp()->write("/Inkifi/$file", $contents);
		df_sftp()->close();
	}

	/**
	 * 2019-02-27
	 * @used-by _p()
	 * @used-by pOI()
	 * @return zL
	 */
	private static function zl() {return dfcf(function() {return ikf_logger('json_status');});}
}