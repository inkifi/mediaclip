<?php
namespace Inkifi\Mediaclip\API\Entity\Order\Item;
/**
 * 2019-03-04
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
 * @used-by \Inkifi\Mediaclip\API\Facade\Order\Item::files()
 */
final class File extends \Df\API\Document {}