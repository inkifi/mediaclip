<?php
namespace Inkifi\Mediaclip\H\AvailableForDownload;
use Inkifi\Mediaclip\Event as Ev;
use Inkifi\Mediaclip\H\Logger as L;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Customer;
use Magento\Eav\Api\AttributeSetRepositoryInterface as IAttributeSetRepository;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Item as OI;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as OIC;
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
		$zl = ikf_logger('json_status'); /** @var zL $zl */
		// 2018-08-16 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		// «Modify orders numeration for Mediaclip»
		// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/1
		$o = df_order($ev->oidI()); /** @var O $o */
		$mOrderDetails = mc_h()->getMediaClipOrders($o->getEntityId());
		$date = mc_h()->createOrderDirectoryDate($o->getCreatedAt()); /** @var string $date */
		$array = [];
		L::l('mediaclipOrderDetails->lines count: ' . count($mOrderDetails->lines));
		foreach ($mOrderDetails->lines as $lines) {
			L::l('A line:');  L::l($lines);
			$projectDetails = ikf_project_details($lines->projectId);
			L::l('projectDetails:'); L::l($projectDetails);
			$oi = df_oic()->addFieldToFilter('mediaclip_project_id', [
				'eq' => $projectDetails['projectId'
			]])->getLastItem(); /** @var OI $oi */
			$orderQuantity = (int)$oi->getQtyOrdered();
			$module = $this->mediaclipModuleName($oi->getData('product_id'));
			L::l("Module: $module");
			/** @var array(string => mixed) $mP */
			$mP = df_new_om(mP::class)->load($projectDetails['items'][0]['plu'], 'plu')->getData();
			L::l('Mediaclip Product:');  L::l($mP);
			$ftp_json = $mP['ftp_json'];
			$zl->info($ftp_json);
			L::l('Send Json: ' . $ftp_json);
			#@var $includeQuantityInJSON flag to include json
			$includeQuantityInJSON = $mP['include_quantity_in_json'];
			if ($ftp_json == 1) {
				$filesUploadPath =
					BP.'/mediaclip_orders/'.$date.'/ascendia/'
					.$o->getIncrementId().'/'.$oi->getId().'/'
					.$mP['product_label']
				;
				L::l("filesUploadPath: $filesUploadPath");
				$zl->info(json_encode($filesUploadPath));
				$array['destination']['name'] = 'pureprint';
				$array['orderData']['sourceOrderId'] = $mOrderDetails->storeData->orderId;
				$linesDetails = mc_h()->getMediaClipOrderLinesDetails($lines->id);
				L::l('linesDetails->files count: ' . count($linesDetails->files));
if (count($linesDetails->files)) {
L::l('linesDetails->files:');  L::l($linesDetails->files);
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
,'sourceItemId' => $lines->id
,'components' => array_values(df_map($linesDetails->files, function($f) use($module, $mP) {return [
'code' => dfa($mP, 'json_code', $this->code(dfo($f, 'id'), $module)), 'fetch' => true, 'path' => $f->url
];}))
,'quantity' => 1 == $includeQuantityInJSON ? $orderQuantity : 1
];
}
			}
		}
		L::l('array:'); L::l($array);
		if (!empty($array)) {
			$zl->info(json_encode($array));
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
			$filesUploadPath = df_cc_path(
				BP, 'ftp_json', $date, $o->getIncrementId(), $oi->getId(), $mP['product_label']
			);
			L::l("filesUploadPath: $filesUploadPath");
			$zl->info(json_encode($filesUploadPath));
			// 2018-08-20 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
			// «FTP upload to ftp.pureprint.com has stopped working»
			// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/6
			df_sftp()->open([
				'host' => 'ftp.pureprint.com'
				,'username' => 'Inkifi'
				,'password' => 'Summ3rD4ys!'
			]);
			/* Check SKU code here */
			$jsonFileName = $o->getIncrementId().'.json';
			$jsonFile = $filesUploadPath.'/'.$jsonFileName;
			$jsonRemoteFile = '/Inkifi/'.$jsonFileName;
			df_file()->mkdir($filesUploadPath);
			$json_handler = fopen($jsonFile, 'w+');
			//here it will print the array pretty
			fwrite($json_handler, json_encode($array,JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT));
			fclose($json_handler);
			$content = file_get_contents($jsonFile);
			df_sftp()->write($jsonRemoteFile, $content);
			df_sftp()->close();
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
	 * 2019-02-24
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload::_p()
	 */
	static function p() {$i = new self; /** @var self $i */ $i->_p();}
}