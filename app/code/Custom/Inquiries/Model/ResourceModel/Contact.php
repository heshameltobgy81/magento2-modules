<?php

namespace Custom\Inquiries\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Contact extends AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('inquiries_contacts', 'contact_id');
    }
}
