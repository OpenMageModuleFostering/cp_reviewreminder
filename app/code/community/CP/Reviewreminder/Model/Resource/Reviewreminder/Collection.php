<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Commerce Pundit Technologies
 * @package     CP_Reviewreminder
 * @copyright   Copyright (c) 2016 Commerce Pundit Technologies. (http://www.commercepundit.com)    
* @author   <<Ravi Soni>>    
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CP_Reviewreminder_Model_Resource_Reviewreminder_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Define collection model
     *
     */
    protected function _construct()
    {
       $this->_init('cp_reviewreminder/reviewreminder');
    }
    
    /**
     * Get customer name
     */
    public function addCustomerNameToSelect() 
    {
        $customerEntityType = Mage::getModel('eav/config')->getEntityType('customer');//@return  Mage_Eav_Model_Entity_Type
        $customerEntityTypeId = $customerEntityType->getEntityTypeId();
        $customerEntityTable = $this->getTable($customerEntityType->getEntityTable()); // Retreive entity table name
        
        $firstNameAttribute = Mage::getModel('eav/entity_attribute')->loadByCode($customerEntityTypeId, 'firstname');
        $lastNameAttribute = Mage::getModel('eav/entity_attribute')->loadByCode($customerEntityTypeId, 'lastname');
        $custmerEntityVarchar1 = $customerEntityTable. '_' . $firstNameAttribute->getBackendType();
        $custmerEntityVarchar2 = $customerEntityTable. '_' . $lastNameAttribute->getBackendType();
        
        $this->getSelect()
        ->join(array('cust_ent1' => $custmerEntityVarchar1), 'cust_ent1.entity_id=main_table.customer_id', array('firstname' => 'value'))
        ->where('cust_ent1.attribute_id=' . $firstNameAttribute->getAttributeId()) 
        ->join(array('cust_ent2' => $custmerEntityVarchar2), 'cust_ent2.entity_id=main_table.customer_id', array('lastname' => 'value'))
        ->where('cust_ent2.attribute_id=' . $lastNameAttribute->getAttributeId()) 
        ->columns(new Zend_Db_Expr("CONCAT(`cust_ent1`.`value`, ' ',`cust_ent2`.`value`) AS customer_name"));
        return $this;
    }
    
    /**
     * Get product name
     */
    public function addProductNameToSelect() 
    {
        //@return entity type object  Mage_Eav_Model_Entity_Type
        $productEntityType = Mage::getModel('eav/config')->getEntityType('catalog_product');
        //get entity type id
        $productEntityTypeId = $productEntityType->getEntityTypeId();
        $productEntityTable = $this->getTable($productEntityType->getEntityTable()); // Retreive entity table name
        
        $productName = Mage::getModel('eav/entity_attribute')->loadByCode($productEntityType, 'name');
        $productNameAttributeId = $productName->getAttributeId();
        $productEntityTableByType = $productEntityTable. '_' . $productName->getBackendType();
        
        $this->getSelect()
        ->join(array('pet' => $productEntityTableByType), 'pet.entity_id=main_table.product_id', array('product_name' => 'value'))
        ->where('pet.attribute_id=' . $productNameAttributeId); 
        return $this;
    }
    
    /**
     * Add order status in collection
     */
    public function addOrderStatusAndDateToSelect() 
    {
        $this->getSelect()
        ->join(array('sot' => $this->getTable('sales/order')), 'sot.entity_id=main_table.order_id', 
                array('order_status' => 'status', 'order_date' => 'created_at' ));        
        return $this;
    }
}