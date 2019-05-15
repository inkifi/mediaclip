<?php
namespace Inkifi\Mediaclip;
use Magento\Catalog\Model\Product as P;
// 2019-05-15
// «Make the Mediaclip's «Get Price» endpoint compatible with the Magento 2 multistore mode»
// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/13
final class Price {
	/**
	 * 2019-05-15
	 * @used-by \Mangoit\MediaclipHub\Controller\Index\AddToCart::execute()
	 * @used-by \Mangoit\MediaclipHub\Controller\Index\GetPriceEndpoint::execute()
	 * @param P $product
	 * @param $mediaclip_obj
	 * @param $quantity
	 * @param bool $setToSession [optional]
	 * @return float|int
	 */
	static function get(P $product, $mediaclip_obj, $quantity, $setToSession = false) {
		$product_id = $product->getId();
		$product_price = $product->getPrice();
		$custom_option_price = 0;
		$additionalPrice = 0;
		if (isset($mediaclip_obj['properties']['option_details']) && !empty($mediaclip_obj['properties']['option_details'])) {
			$option_details = json_decode($mediaclip_obj['properties']['option_details']);

			$product_options = $product->getOptions();

			foreach ($option_details as $oid => $ovalue) {
				if ($product_options) {

					foreach ($product_options as $pvalue) {
						 //print_r($pvalue->getData('option_id'));
					 //print_r($pvalue);
						if ($oid == $pvalue->getData('option_id')) {
							$optionValues = $pvalue->getValues();
							if ($optionValues) {

								foreach ($optionValues as $_value) {
									if ($ovalue == $_value->getData('option_type_id')) {
										$custom_option_price = $custom_option_price + $_value->getPrice();
									}
								}
							}
						}
					}
				}
			}
		}
		foreach ($mediaclip_obj['items'] as $key => $value) {
			if (isset($value['photobookData']) && isset($value['photobookData']['additionalSheetCount'])) {
				if ($value['photobookData']['additionalSheetCount'] > 0) {
					$additionalPriceAmount = self::additional($product);
					$additional = $value['photobookData']['additionalSheetCount'];
					$additionalPrice = $additionalPrice + ($additionalPriceAmount * (int)$additional);
				}
			}
		}

		if ($product->getMediaclipMinimumPrintsAllow()) {
			$min_allow = $product->getMediaclipMinimumPrintsCount();
			$add_price = $product->getMediaclipExtraPrintsPrice();
			if ($min_allow != '' && is_numeric($min_allow) && $add_price != '' && is_numeric($add_price)) {
				foreach ($mediaclip_obj['items'] as $mediaclipItem) {
					if ($mediaclipItem['properties']['storeProductId'] == $product_id) {
						$designer_prints = $mediaclipItem['quantity'];
						if ($designer_prints > $min_allow) {
							$diff = $designer_prints - $min_allow;
							$additionalPrice = $diff*$add_price;
						} else if ($designer_prints < $min_allow) {
							$diff = $min_allow - $designer_prints;
							df_customer_session()->setCanAddMoreMediaclipPrintsPrompt(1);
							$add_more_prompt['diff'] = $diff;
							$add_more_prompt['project_id'] = $mediaclip_obj['projectId'];
							$add_more_prompt['product_id'] = $product_id;
							df_customer_session()->setAddMoreMediaclipPrints($add_more_prompt);
						}
					}
				}
			}
		}
		$price = $product_price + $custom_option_price + $additionalPrice;
		$price = $price*$quantity;
		if ($setToSession) {
			df_customer_session()->setCustomPriceObserver($price);
		}
		return $price;
	}

	/**
	 * 2019-05-15
	 * @used-by get()
	 * @param P $product
	 * @return int
	 */
	private static function additional(P $product){
		if ($product->getMediaClipExtrasheetamt() && is_numeric($product->getMediaClipExtrasheetamt())) {
			$additionalAmount = $product->getMediaClipExtrasheetamt();
			return $additionalAmount;
		}
		return 0;
	}
}