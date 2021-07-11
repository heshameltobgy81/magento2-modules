# magento2-modules

This project consists of the below 5 modules:
1. Custom_AddToCart
2. Custom_CategoryBottomDescription
3. Custom_CmsPage
4. Custom_Inquiries
5. Custom_Newsletter

In order to upload the above modules, you need to go to your Magento root directory, then go inside your app/code directory and upload the above modules.  

Please use the command line to run the below command if you would like to install all the above modules:

You can use either of the below ways to enable the above module on a Magento project:
```
php bin/magento module:status --disabled | grep custom -i | xargs php bin/magento module:enable
```
or

```
php bin/magento module:enable Custom_AddToCart Custom_CategoryBottomDescription Custom_CmsPage Custom_Inquiries Custom_Newsletter

```
