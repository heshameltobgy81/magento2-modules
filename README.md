# magento2-modules
## Instructions
***This project consists of the below 5 modules:***
1. Custom_AddToCart
2. Custom_CategoryBottomDescription
3. Custom_CmsPage
4. Custom_Inquiries
5. Custom_Newsletter

In order to upload the above modules, you need to go to your Magento root directory, then go inside your app/code directory and upload the above modules.  

Please use the command line to run the below command if you would like to install all the above modules:

You can use any of the below ways to enable the above module on a Magento project:
```
php bin/magento module:status --disabled | grep custom -i | xargs php bin/magento module:enable
```
or

```
php bin/magento module:enable Custom_AddToCart Custom_CategoryBottomDescription Custom_CmsPage Custom_Inquiries Custom_Newsletter

```
Then Run

```
php bin/magento setup:upgrade
```
## Description
**Custom_AddToCart**
This module overrides the default Magento add to cart behaviour on both category and product pages, so when you add to cart any product, a popup appears with the selected product name and image and 2 buttons (Continue Shopping or Go to cart).

**Custom_CategoryBottomDescription**
This module creates a new field on Magento Backend called (Category Bottom Description). If this field has some content, the content will appear at the bottom of the Category page with a read more button if the height of the content is above a certain height.

**Custom_CmsPage**
This module creates a new page (url key= custom-cms-page). Also, it has a carousel slider section added to the top of the page content below the header section.

**Custom_Inquiries**
This module overrides the default Magento Contact form, as new fields are added to the form. You can check the new form by visiting the default Magento contact page.

**Custom_Newsletter**
This module overrides the default Magento Newsletter form, as new name field are added to the form. Also, we added ajax call on form submit.

