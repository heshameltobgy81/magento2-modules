<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Custom\Inquiries\Controller\Contact;

use Exception;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Custom\Inquiries\Model\Contact;
use Zend_Validate;

class Post extends Action
{
    protected $_modelContactForm;
    /**
     * @var ResultInterface
     */
    protected $_resultRedirect;
    protected $productObj;
    /**
     * @var Session
     */
    protected $_customerSession;
    protected $resultJsonFactory;
    protected $resourceConnection;
    /**
     * @var  TransportBuilder
     */
    private $_transportBuilder;
    private $_scopeConfig;
    /**
     * @var DateTime
     */
    private $timeZone;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        Context $context,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopConfig,
        Contact $_modelContactForm,
        Session $customerSession,
        ResourceConnection $resourceConnection,
        JsonFactory $resultJsonFactory,
        Timezone $timeZone,
        StoreManagerInterface $storeManager
    ) {
        $this->timeZone = $timeZone;
        $this->resourceConnection = $resourceConnection;
        $this->_customerSession = $customerSession;
        $this->_modelContactForm = $_modelContactForm;
        $this->_transportBuilder = $transportBuilder;
        $this->_scopeConfig = $scopConfig;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     * @throws Exception
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
  
        $result = $this->resultJsonFactory->create();
        if (!$post) {
            if ($this->getRequest()->isXmlHttpRequest()) {
                return $result->setData(['message' => 'No data received.', 'error' => true]);
            }
            return $this->_redirect($this->_redirect->getRefererUrl());
        }

        try {
            $postObject = new DataObject();
            $postObject->setData($post);

            $error = false;

            $customerFName = $post['fullname'] ?? trim($post['fullname']);
            $customerName = $customerFName;
            if (isset($post['telephone'])) {
                $phone = $post['telephone'] ?? trim($post['telephone']);
            } else {
                $phone = '';
            }
            $mobile = $post['mobile'] ?? trim($post['mobile']);
            if (isset($post['fax'])) {
                $fax = $post['fax'] ?? trim($post['fax']);
            } else {
                $fax = '';
            }
            if (isset($post['position'])) {
                $position = $post['position'] ?? trim($post['position']);
            } else {
                $position = '';
            }
            if (isset($post['address'])) {
                $address = $post['address'] ?? trim($post['address']);
            } else {
                $address = '';
            }
            $message = $post['comment'] ?? addslashes(trim($post['comment']));
            $customerEmail = $post['email'] ?? trim($post['email']);
            $company = $post['company'] ?? trim($post['company']);
        
            if (isset($post['find_us'])) {
                $findUs = $post['find_us'] ?? trim($post['find_us']);
            } else {
                $findUs = '';
            }

            if (isset($post['your_industry'])) {
                $yourIndustry = $post['your_industry'] ?? trim($post['your_industry']);
            } else {
                $yourIndustry = '';
            }

            $store = $post['store_name'] ?? trim($post['store_name']);
            $form = $post['form'] ?? trim($post['form']);

            if (isset($post['enquiry_from'])) {
                $enquiryFrom = $post['enquiry_from'] ?? trim($post['enquiry_from']);
            } else {
                $enquiryFrom = '';
            }


            if (!Zend_Validate::is($customerName, 'NotEmpty')) {
                $error = true;
            }
            if (!Zend_Validate::is($customerEmail, 'EmailAddress')) {
                $error = true;
            }
            if (!Zend_Validate::is($message, 'NotEmpty')) {
                $error = true;
            }
            if (!Zend_Validate::is($company, 'NotEmpty')) {
                $error = true;
            }
            if (!Zend_Validate::is($mobile, 'NotEmpty')) {
                $error = true;
            }
            if ($error) {
                if ($this->getRequest()->isXmlHttpRequest()) {
                    return $result->setData(['message' => 'Must fill all required fields.', 'error' => true]);
                }
                $this->_customerSession->setCustomerFormData($this->getRequest()->getPostValue());
                $this->messageManager->addErrorMessage(__($error));
                return $this->_redirect($this->_redirect->getRefererUrl());
            }

            $data = [
                "name"          => $customerName,
                "telephone"     => $phone,
                "mobile"        => $mobile,
                "fax"           => $fax,
                "company"       => $company,
                "position"      => $position,
                "address"       => $address,
                "email"         => $customerEmail,
                "find_us"       => $findUs,
                "your_industry" => $yourIndustry,
                "store"         => $store,
                "form"          => $form,
                "enquiry_from"  => $enquiryFrom,
                "added_date"    => $this->timeZone->date()->format('Y-m-d H:i:s')
            ];
            $this->_modelContactForm->setData($data);

            try {
                $this->_modelContactForm->save();
            } catch (Exception $e) {
                if ($this->getRequest()->isXmlHttpRequest()) {
                    return $result->setData(['message' => $e->getMessage(), 'error' => true]);
                }
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            $storeId = $this->storeManager->getStore()->getId();

            // Send Email to Customer
            $emailTemplateVariables = [];
            $emailTemplateVariables['name'] = $customerName;

            $senderName = $this->_scopeConfig->getValue('trans_email/ident_support/name', ScopeInterface::SCOPE_STORE);
            $senderEmail = $this->_scopeConfig->getValue('trans_email/ident_support/email', ScopeInterface::SCOPE_STORE);

            $email = $customerEmail;
            $postObject = new DataObject();
            $postObject->setData($emailTemplateVariables);

            $sender = ['name' => $senderName, 'email' => $senderEmail];

            $transport = $this->_transportBuilder->setTemplateIdentifier('customer_thankyou_template_inquiry')
                ->setTemplateOptions([
                    'area'  => Area::AREA_FRONTEND,
                    'store' => $storeId
                ])
                ->setTemplateVars(['data' => $postObject])
                ->setFrom($sender)
                ->addTo($email)
                ->setReplyTo($senderEmail)
                ->getTransport();


            try {
                $transport->sendMessage();
            } catch (Exception $e) {
                if ($this->getRequest()->isXmlHttpRequest()) {
                    return $result->setData(['message' => $e->getMessage(), 'error' => true]);
                }
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            $html = '';
            $emailData = [];
            $emailData['Full Name'] = $customerName;
            if (isset($post['company'])) {
                $emailData['Company/Organization'] = trim($post['company']);
            }
            $emailData['E-mail adress'] = $customerEmail;
            if (isset($phone)) {
                $emailData['Phone'] = $phone;
            }
            $emailData['Mobile'] = $mobile;
            if (isset($fax)) {
                $emailData['fax'] = $fax;
            }
            if (isset($address)) {
                $emailData['address'] = $address;
            }
            if (isset($position)) {
                $emailData['position'] = $position;
            }
            $emailData['Message'] = $message;
            if (isset($post['find_us']) && !empty($post['find_us'])) {
                $emailData['How did you find about us'] = $post['find_us'] ?? trim($post['find_us']);
            }
            if (isset($post['your_industry']) && !empty($post['your_industry'])) {
                $emailData['Your Industry'] = $post['your_industry'] ?? trim($post['your_industry']);
            }
            foreach ($emailData as $key => $value) {
                $html .= "<tr>";

                $html .= "<td width='30%' style='padding: 5px'>";
                $html .= $key;
                $html .= "</td>";

                $html .= "<td width='70%' style='padding: 5px'>";
                $html .= $value;
                $html .= "</td>";

                $html .= "</tr>";
            }

            $emailTemplateVariables = [];
            $emailTemplateVariables['product_inquiry'] = $html;
            $emailTemplateVariables['name'] = $customerName;

            $email = $this->_scopeConfig->getValue('trans_email/ident_support/email', ScopeInterface::SCOPE_STORE);
            $postObject = new DataObject();
            $postObject->setData($emailTemplateVariables);

            $transport1 = $this->_transportBuilder->setTemplateIdentifier('admin_data_template_inquiry')
                ->setTemplateOptions(['area'  => Area::AREA_FRONTEND,
                                      'store' => $storeId])
                ->setTemplateVars(['data' => $postObject])
                ->setFrom($sender)
                ->addTo($email)
                ->setReplyTo($senderEmail)
                ->getTransport();

            try {
                $transport1->sendMessage();
            } catch (Exception $e) {
                if ($this->getRequest()->isXmlHttpRequest()) {
                    return $result->setData(['message' => $e->getMessage(), 'error' => true]);
                }
                $this->messageManager->addErrorMessage($e->getMessage());
            }
            if ($this->getRequest()->isXmlHttpRequest()) {
                return $result->setData(['message' => 'Thank you for your submission, we will be in contact soon.', 'error' => false]);
            }
            $this->messageManager->addSuccessMessage(
                __('Thank you for your submission, we will be in contact soon.')
            );
            return $this->resultRedirectFactory->create()->setPath($this->_redirect->getRefererUrl());
        } catch (Exception $e) {
            if ($this->getRequest()->isXmlHttpRequest()) {
                return $result->setData(['message' => 'We can\'t process your request right now. Sorry, that\'s all we know.', 'error' => true]);
            }
            $this->messageManager->addErrorMessage(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            return $this->resultRedirectFactory->create()->setPath($this->_redirect->getRefererUrl());
        }
    }
}
