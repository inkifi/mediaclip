A custom integration between [inkifi.com](https://inkifi.com) and [mediaclip.ca](https://www.mediaclip.ca).  

## How to install
```
bin/magento maintenance:enable
rm -rf composer.lock
composer clear-cache
composer require inkifi/mediaclip:*
bin/magento setup:upgrade
rm -rf var/di var/generation generated/code && bin/magento setup:di:compile
rm -rf pub/static/* && bin/magento setup:static-content:deploy -f en_US en_GB --area adminhtml --theme Magento/backend && bin/magento setup:static-content:deploy -f en_US en_GB --area frontend --theme Infortis/ultimo
bin/magento maintenance:disable
bin/magento cache:enable
```

## How to upgrade
```
bin/magento maintenance:enable
rm -rf composer.lock
composer clear-cache
composer update inkifi/mediaclip
bin/magento setup:upgrade
rm -rf var/di var/generation generated/code && bin/magento setup:di:compile
rm -rf pub/static/* && bin/magento setup:static-content:deploy -f en_US en_GB --area adminhtml --theme Magento/backend && bin/magento setup:static-content:deploy -f en_US en_GB --area frontend --theme Infortis/ultimo
bin/magento maintenance:disable
bin/magento cache:enable
```

If you have problems with these commands, please check the [detailed instruction](https://mage2.pro/t/263).