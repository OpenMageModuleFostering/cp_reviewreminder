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
class CP_Reviewreminder_Block_Adminhtml_Addreminder_Renderer_CustomerName extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render custome name
     * @param object $row order data object
     * @return string
     */ 
    public function render(Varien_Object $row)
    {
        $firstName = $row->getData('customer_firstname');
        $lastName = $row->getData('customer_lastname');
        if (!empty($firstName) || !empty($lastName)) {
            
            if (!empty($lastName)) {
                return $firstName . ' ' . $lastName;
            } else {
                return $firstName;
            }
        }
    }
}
?>
