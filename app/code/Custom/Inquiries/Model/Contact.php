<?php
namespace Custom\Inquiries\Model;

use Magento\Framework\Model\AbstractModel;

class Contact extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Custom\Inquiries\Model\ResourceModel\Contact');
    }
}