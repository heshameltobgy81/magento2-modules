<?php

namespace Custom\Inquiries\Block;

use Magento\Customer\Block\Form\Register;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url;
use Magento\Directory\Helper\Data;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\View\Element\Template\Context;
use Magento\Newsletter\Model\Config;

class Contact extends Register
{
    public function __construct(
        Context $context,
        Data $directoryHelper,
        EncoderInterface $jsonEncoder,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        CollectionFactory $countryCollectionFactory,
        Manager $moduleManager,
        Session $customerSession,
        Url $customerUrl,
        Http $request,
        array $data = [], Config $newsLetterConfig = null)
    {
        $this->_request = $request;
        parent::__construct($context, $directoryHelper, $jsonEncoder, $configCacheType, $regionCollectionFactory, $countryCollectionFactory, $moduleManager, $customerSession, $customerUrl, $data, $newsLetterConfig);
    }

    /**
     * Returns action url for contact form
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('inquiries/contact/post', ['_secure' => true]);
    }

    public function getFromtype()
    {
        if ($this->getRequest()->getFullActionName() == 'cms_index_index') {
            return $value = 'Home';
        } else if ($this->getRequest()->getFullActionName() == 'catalog_product_view') {
            return $value = 'Product Inquiry';
        } else {
            return $value = 'Contact';
        }
    }
}
