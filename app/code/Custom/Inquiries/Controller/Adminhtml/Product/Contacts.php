<?php

namespace Custom\Inquiries\Controller\Adminhtml\Product;

class Contacts extends \Magento\Backend\App\Action
{
    protected $resultPageFactory = false;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        //Call page factory to render layout and page content
        $resultPage = $this->resultPageFactory->create();

        //Set the menu which will be active for this page
        $resultPage->setActiveMenu('Custom_Inquiries::custom_product_inquiries');

        //Set the header title of grid
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Contact Requests'));

        //Add bread crumb
        $resultPage->addBreadcrumb(__('Custom'), __('Custom'));
        $resultPage->addBreadcrumb(__('Manage Contact Requests'), __('Manage Contact Requests'));

        return $resultPage;
    }

    /*
     * Check permission via ACL resource
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Custom_Inquiries::inquiries');
    }
}
