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
class CP_Reviewreminder_Model_Adminhtml_System_Config_Source_Status
{
    public function toOptionArray($isMultiselect = false)
    {
        $options = array(
            array('value'=>'canceled', 'label'=>Mage::helper('cp_reviewreminder')->__('Canceled')),
            array('value'=>'closed', 'label'=>Mage::helper('cp_reviewreminder')->__('Closed')),
            array('value'=>'complete', 'label'=>Mage::helper('cp_reviewreminder')->__('Complete')),
            array('value'=>'holded', 'label'=>Mage::helper('cp_reviewreminder')->__('On Hold')),
            array('value'=>'pending', 'label'=>Mage::helper('cp_reviewreminder')->__('Pending')),
            array('value'=>'processing', 'label'=>Mage::helper('cp_reviewreminder')->__('Processing')),
        );
        
        /*
        $statuses = Mage::getModel('sales/order_status')->getResourceCollection()->getData();
        $options = array();
        if(!empty($statuses) && is_array($statuses)){
            foreach ($statuses as $status) {
                $options[] = array('value'=>$status['status'], 'label'=>Mage::helper('cp_reviewreminder')->__($status['label']));
            }
        }
        */
        
        if(!$isMultiselect){
 
            array_unshift($options, array('value'=>'', 'label'=>Mage::helper('cp_reviewreminder')->__('--Please Select--')));
 
        }
        return $options;
    }
}