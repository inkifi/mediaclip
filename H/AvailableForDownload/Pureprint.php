<?php
namespace Inkifi\Mediaclip\H\AvailableForDownload;
use Inkifi\Mediaclip\API\Entity\Order\Item as mOI;
use Inkifi\Mediaclip\API\Entity\Project;
use Inkifi\Mediaclip\Event as Ev;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Customer;
use Magento\Eav\Api\AttributeSetRepositoryInterface as IAttributeSetRepository;
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
		$array = [];
		foreach (ikf_api_oi($o->getId()) as $mOI) { /** @var mOI $mOI */
			$this->pOI($mOI);			
		}
		if (!empty($array)) {
			self::zl()->info(json_encode($array));
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
			$array['shipments'] = [[
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
			]];
			// 2018-08-16 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
			// "Replace the «/home/canvaspr/dev2.inkifi.com/html/ftp_json25june/»
			// hardcoded filesystem path with a dynamics one":
			// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/3
			$contents = json_encode($array, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT); /** @var string $contents */
			$file = "{$o->getIncrementId()}.json"; /** @var string $file */
			self::writeLocal($oi, $mP['product_label'], $file, $contents);
			self::writeRemote($file, $contents);
		}
	}

	/**
	 * 2018-11-02 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * «Generate JSON data for photo-books»: https://www.upwork.com/ab/f/contracts/21011549
	 * https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/9
	 * @used-by _p()
	 * @param string|null $v
	 * @param string $m
	 * @return string
	 */
    private function code($v, $m) {return $v ?: (
    	'gifting' === ($m = strtolower($m)) ? 'gift' : ('print' === $m ? 'prints-set-01' : 'photobook-jacket')
	);}

    /**
	 * 2018-11-02
	 * @used-by _p()
     * @param int $pid
     * @return String $mediaclip_module media clip produc type Photobook | Gifting | Print
     */
    private function mediaclipModuleName($pid) {
        $product = df_new_om(Product::class)->load($pid);
        $attributeSet = df_new_om(IAttributeSetRepository::class);
        $attributeSetRepository = $attributeSet->get($product->getAttributeSetId());
        $mediaclip_module = $attributeSetRepository->getAttributeSetName();
        return $mediaclip_module;
    }

	/**
	 * 2019-02-27
	 * @param mOI $mOI
	 * @used-by _p()
	 */
    private function pOI(mOI $mOI) {
		$project = $mOI->project(); /** @var Project $project */
		$oi = $mOI->oi(); /** @var OI $oi */
		$module = $this->mediaclipModuleName($oi->getProductId());
		$mP = $project->mProduct(); /** @var mP $mP */
		if ($mP->sendJson()) {
			$array['destination']['name'] = 'pureprint';
			$array['orderData']['sourceOrderId'] = $o->getId();
			$linesDetails = mc_h()->getMediaClipOrderLinesDetails($mOI->id());
				if (count($linesDetails->files)) {
					/**
					* 2018-11-02 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
					* «Generate JSON data for photo-books»
					* https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/9
					* 2018-11-03
					* An example of $linesDetails->files
					*	[
					*		{
					*			"id": "photobook-jacket",
					*			"productId": "$(package:inkifi/photobooks)/products/hard-cover-gray-210x210mm-70",
					*			"plu": "INKIFI-HCB210-M-70",
					*			"quantity": 1,
					*			"url": "https://renderstouse.blob.core.windows.net/0c25168e-eda3-41d4-b266-8259566d2507/dust.pdf?sv=2018-03-28&sr=c&sig=XzCB%2B2CWlpqNFqVf6CnoVr8ICDGufTexaNqyzxMDUx8%3D&st=2018-11-02T19%3A36%3A41Z&se=2018-12-02T19%3A38%3A41Z&sp=r",
					*			"order": 0
					*		},
					*		{
					*			"id": "photobook-pages",
					*			"productId": "$(package:inkifi/photobooks)/products/hard-cover-gray-210x210mm-70",
					*			"plu": "INKIFI-HCB210-M-70",
					*			"quantity": 1,
					*			"url": "https://renderstouse.blob.core.windows.net/0c25168e-eda3-41d4-b266-8259566d2507/0d0e8542-db8d-475b-95bb-33156dc6551a_0c25168e-eda3-41d4-b266-8259566d2507.pdf?sv=2018-03-28&sr=c&sig=maMnPG2XIrQuLC3mArAgf3YKrM6EzFwNMggwApqMTeo%3D&st=2018-11-02T19%3A36%3A43Z&se=2018-12-02T19%3A38%3A43Z&sp=r",
					*			"order": 1
					*		}
					*	]
					*/
					$array['orderData']['items'][] = [
						'sku' => $mP['plu']
						,'sourceItemId' => $mOI->id()
						,'components' => array_values(df_map($linesDetails->files, function($f) use($module, $mP) {return [
						'code' => $mP['json_code'] ?: $this->code(dfo($f, 'id'), $module)
						,'fetch' => true
						,'path' => $f->url
						];}))
						,'quantity' => $mP->includeQuantityInJson() ? (int)$oi->getQtyOrdered() : 1
					];
				}
		}
	}

	/**
	 * 2019-02-24
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload::_p()
	 */
	static function p() {$i = new self; /** @var self $i */ $i->_p();}

	/**
	 * 2019-02-27
	 * @used-by _p()
	 * @param OI $oi
	 * @param string $product
	 * @param string $file
	 * @param string $contents
	 */
	private static function writeLocal(OI $oi, $product, $file, $contents) {
		$o = $oi->getOrder(); /** @var O $o */
		$dir = df_cc_path(
			BP, 'ftp_json', date('d-m-Y', strtotime($o->getCreatedAt()))
			,$o->getIncrementId(), $oi->getId(), $product
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