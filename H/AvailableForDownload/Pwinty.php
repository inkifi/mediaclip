<?php
namespace Inkifi\Mediaclip\H\AvailableForDownload;
use Inkifi\Mediaclip\Event as Ev;
use Magento\Customer\Model\Customer;
use Magento\Framework\App\Config\ScopeConfigInterface as IScopeConfig;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Item as OI;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as OIC;
use Magento\Store\Model\StoreManagerInterface as IStoreManager;
use Mangoit\MediaclipHub\Model\Mediaclip;
use Mangoit\MediaclipHub\Model\Orders as mOrder;
use Mangoit\MediaclipHub\Model\Product as mP;
use Mangoit\MediaclipHub\Setup\UpgradeSchema as Schema;
use pwinty\PhpPwinty;
// 2019-02-24
final class Pwinty {
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
		//set order item status to 1 as response of each line item receives
		/** @var OIC $oiC */
		$oiC = df_oic()->addFieldToFilter('mediaclip_project_id', ['eq' => $ev['projectId']]);
		foreach ($oiC as $oi) { /** @var OI $oi */
			$oi[Schema::OI__ITEM_DOWNLOAD_STATUS] = 1;
			$oi->save();
		}
		// check if all items are downloaded
		// 2018-08-16 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		// «Modify orders numeration for Mediaclip»
		// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/1
		if (!df_find(
			df_oic()->addFieldToFilter('order_id', ['eq' => $ev->oidI()])
			,function(OI $oi) {return !$oi[Schema::OI__ITEM_DOWNLOAD_STATUS];}
		)) {
			$merchantId = df_o(IScopeConfig::class)->getValue('api/pwinty_api_auth/merchant_id');
			$apiKey = df_o(IScopeConfig::class)->getValue('api/pwinty_api_auth/pwinty_api_key');
			$pwinty = new PhpPwinty([
				'api' => 'sandbox' //production
				,'apiKey' => $apiKey
				,'merchantId' => $merchantId
			]);
			// 2018-08-16 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
			// «Modify orders numeration for Mediaclip»
			// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/1
			$o = df_order($ev->oidI()); /** @var O $o */
			$mOrderDetails = mc_h()->getMediaClipOrders($o->getId());
			foreach ($mOrderDetails->lines as $lines) {
				$projectDetails[] = ikf_m_project_details($lines->projectId);
			}
			$orderDirDate = mc_h()->createOrderDirectoryDate($o->getCreatedAt());
			$imageArray = [];
			$catalogue = $pwinty->getCatalogue('GB', 'Pro');
			foreach ($projectDetails as $value) {
				$oiC->clear()->getSelect()->reset('where');
				$salesOrderItem = $oiC->addFieldToFilter('mediaclip_project_id', ['eq' => $value['projectId']]);
				//get images from downloaded folder
				foreach ($salesOrderItem as $newvalue) {
					$orderItemID = $newvalue->getData('item_id');
				}
				$dir = df_o(DirectoryList::class);
				$base = $dir->getRoot();
				/** @var array(string => mixed) $mP */
				$mP = df_new_om(mP::class)->load($value['items'][0]['plu'], 'plu')->getData();
				$pwintyProduct = $mP['pwinty_product_name'];
				$frameColour = $mP['frame_colour'];
				$filesUploadPath = df_cc_path(
					$base, 'mediaclip_orders', $orderDirDate, 'pwinty'
					,$o->getIncrementId(), $orderItemID, $mP['product_label']
				);
				$imgPath = explode('html/', $filesUploadPath);
				$storeManager = df_o(IStoreManager::class);
				$store = $storeManager->getStore();
				$baseUrl = $store->getBaseUrl();
				if ($filesUploadPath != '') {
					$quantity = 0 ;
					foreach (new \DirectoryIterator($filesUploadPath) as $key => $fileInfo) {
						if ($fileInfo->isDot() || $fileInfo->isDir())
						   continue;
						if ($fileInfo->isFile() && $fileInfo->getExtension() != 'csv') {
							$img = $baseUrl.$imgPath[1].'/'.$fileInfo->getFilename();
							$imgAttribute = [];
							$imgAttribute['url'] = $img;
							$imgAttribute['sizing'] = "ShrinkToFit";
							$imgAttribute['priceToUser'] = "0";
							$imgAttribute['copies'] = $quantity+1;
							$imgAttribute['type'] = $pwintyProduct;
							foreach ($catalogue['items'] as $value) {
								//check if product has frame attribute
								if ($value['name'] == $pwintyProduct) {
									if($frameColour != "" && !empty($value['attributes'])) {
										$imgAttribute['attributes'][$value['attributes'][0]['name']] = strtolower($frameColour);
									}
								}
							}
							$imageArray[$orderItemID] = $imgAttribute;
							$quantity++;
						}
					}
				}
			}
			$imageArray = array_values($imageArray);
			$address = $o->getShippingAddress();
			$postcode = $address->getPostcode();
			$countryCode = $address->getCountryId();
			$region = $address->getRegion();
			if ($address->getCompany() != ''){
				$street1 = $address->getCompany().','.$address->getStreet()[0];
			} else {
				$street1 = $address->getStreet()[0];
			}
			if (isset($address->getStreet()[1])) {
				$street2 = $address->getStreet()[1];
			} else{
				$street2 = '';
			}
			$city = $address->getCity();
			$customerId = $o->getCustomerId();
			$customer = df_new_om(Customer::class)->load($customerId);
			$name = $customer['firstname'].' '.$customer['lastname'];
			$email = $customer['email'];
			$pOrder = $pwinty->createOrder(// create order to pwinty
				$name,          //name
				$email,         //email address
				$street1,    //address1
				$street2,    //address 2
				$city,          //town
				$region,        //state
				$postcode,      //postcode or zip
				'GB',            //country code
				$countryCode,    //destination code
				true,            //tracked shipping
				"InvoiceMe",     //payment method
				"Pro"            //quality
			);
			$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pwinty_orders_status.log');
			$logger = new \Zend\Log\Logger();
			$logger->addWriter($writer);
			$logger->info($pOrder);
			$pwintyOrderId = $pOrder['id'];
			//save pwinty id to custom table
			$mOrderModel = df_new_om(mOrder::class);
			$mOrderModelCollection = $mOrderModel->getCollection();
			$mOrder = $mOrderModelCollection
				->addFieldToFilter('magento_order_id', ['eq' => $ev->oidE()]);
			foreach ($mOrder as $key => $value) {
				$value->setPwintyOrderId($pwintyOrderId);
				$value->save();
			}
			$photos =  $pwinty->addPhotos( //add photos to order
				$pwintyOrderId, //order id
				$imageArray
			);
			$logger->info($photos);
			$getOrderStatus = $pwinty->getOrderStatus(// check order status
				$pwintyOrderId              //orderid
				 //status
			);
			$logger->info($getOrderStatus);
			if ($getOrderStatus['isValid'] == 1) {// submit order if no error
				$pwinty->updateOrderStatus(
					$pwintyOrderId,              //orderid
					"Submitted"         //status
				);
			} else {
				$logger->info('order is not submitted');
			}
		}
	}

	/**
	 * 2019-02-24
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload::_p()
	 */
	static function p() {$i = new self; /** @var self $i */ $i->_p();}
}