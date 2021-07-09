<?php
 
namespace Custom\Inquiries\Model\ResourceModel\Contact;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'contact_id';

    protected function _construct()
    {
        $this->_init(
            'Custom\Inquiries\Model\Contact',
            'Custom\Inquiries\Model\ResourceModel\Contact'
        );
    }
}
