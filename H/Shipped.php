<?php
namespace Inkifi\Mediaclip\H;
use Inkifi\Mediaclip\Event as Ev;
use Magento\Catalog\Model\Product;
use Magento\Eav\Api\AttributeSetRepositoryInterface as IAttributeSetRepository;
use Magento\Framework\DB\Transaction;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Item as OI;
// 2019-02-24
final class Shipped {
	/**
	 * 2019-02-24
	 * @used-by \Mangoit\MediaclipHub\Controller\Index\OrderStatusUpdateEndpoint::execute()
	 */
	static function p() {
    	$ev = Ev::s(); /** @var Ev $ev */
		$projectId = $ev['projectId'];
		try {
			// 2018-08-16 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
			// «Modify orders numeration for Mediaclip»
			// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/1
			$o = df_new_om(O::class)->load($ev->oidI()); /** @var O $o */
			$qtys = [];
			foreach ($o->getItemsCollection() as $oi) { /** @var OI $oi */
				if (($oi->getQtyToShip() > 0) && (!$oi->getIsVirtual())) {
					$_productId = $oi->getProductId();
					$_product = df_new_om(Product::class)->load($_productId);
					$attributeSet = df_new_om(IAttributeSetRepository::class);
					$attributeSetRepository = $attributeSet->get($_product->getAttributeSetId());
					$attribute_set_name = $attributeSetRepository->getAttributeSetName();
					if ($attribute_set_name == 'Photobook') {
						if (
							$oi->getMediaclipProjectId() != ''
							&& ($oi->getMediaclipProjectId() == $projectId)
						) {
							$itemId = $oi->getItemId();
							$qtys[$itemId] = $oi->getQtyToShip();
						}
					}
				}
			}
			if (empty($qtys)) {
				$loggers = $ev->oidE()." No item found to make shipment.";
			}
			else {
				/**
				 * 2019-02-24 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
				 * This code seems to be ported from Magento 1 and it should not work in Magento 2
				 * because the `prepareShipment()`
				 * @see \Magento\Sales\Model\Order::prepareShipment() method is absent in Magento 2.
				 */
				$shipment = $o->prepareShipment($qtys);
				$shipment->register();
				$shipment->sendEmail(true)->setEmailSent(true)->save();
				df_new_om(Transaction::class)
					->addObject($shipment)
					->addObject($shipment->getOrder())
					->save()
				;
				$o->setStatus('complete')->save();
				$loggers = $ev->oidE()." Shipment created successfully ".json_decode($qtys);
			}
			$writer = new \Zend\Log\Writer\Stream(
				BP . '/var/log/mediaclip_orders_shipped_dispactched_status.log'
			);
			$logger = new \Zend\Log\Logger();
			$logger->addWriter($writer);
			$logger->info($loggers);
		}
		catch (\Exception $e) {
			// Log Error On Order Comment History
			$o->addStatusHistoryComment('Failed to create shipment - '. $e->getMessage())->save();
			// Error
			$loggers = $ev->oidE()." Failed to create shipment";
			$writer = new \Zend\Log\Writer\Stream(
				BP . '/var/log/mediaclip_orders_shipped_dispactched_status.log'
			);
			$logger = new \Zend\Log\Logger();
			$logger->addWriter($writer);
			$logger->info($loggers);
		}		
	}
}