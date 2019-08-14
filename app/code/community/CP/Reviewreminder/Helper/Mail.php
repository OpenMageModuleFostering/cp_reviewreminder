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
class CP_Reviewreminder_Helper_Mail extends Mage_Core_Helper_Abstract
{
    const XML_PATH_EMAIL_TEMPLATE   = 'review_reminder/general_settings/email_template';
    const XML_PATH_EMAIL_SENDER     = 'review_reminder/general_settings/sender_email_identity';
    
    /**
     * Send reminder email
     *
     * @param Clarion_ReviewReminder_Model_Reviewreminder $reminder
     * @return boolean
     */
    public function sendReminderEmail($reminder)
    {
        //echo '<pre>'; print_r($reminder); die;
        
        //check is extension enabled
         if (!Mage::helper('cp_reviewreminder')->isExtensionEnabled()) {
             return;
         }
         
        $customerId = $reminder->getCustomerId();
        if(!$customerId){
            return false;
        }
        
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $customerEmail = $customer->getEmail();
        $firstName = $customer->getFirstname();
        $productId = $reminder->getProductId();
        $product = Mage::getModel('catalog/product')->load($productId);
        $productName = $product->getName();
        $categoryId = $this->getProductCategoryId($product);
               
        
        
        //Send multiple product detail in reviewReminder email
        $orderId = $reminder->getorder_id();
        $order = Mage::getModel('sales/order')->load($orderId);
        $order->getAllVisibleItems();
            
        $orderItems = $order->getItemsCollection()
        ->addAttributeToSelect('*')
        ->load();
        
        
        foreach($orderItems as $sItem) 
        {
            
            $proId = $sItem->getProductId();
            $proName = $sItem->getName();
            
            $product = Mage::getModel('catalog/product')->load($proId);
            $proImage = Mage::getModel('catalog/product_media_config')->getMediaUrl( $product->getSmallImage());
            $catids = $this->getProductCategoryId($product);
           
            $reviewUrl = Mage::getBaseUrl()."reviewreminder/index/addReview/id/$proId/category/$catids";
              
            $html .= '<div style="clear:both; margin-bottom:18px; margin-top:12px; overflow:hidden; width:100%;">';
            $html .= '<img style="float:left; margin-right:10px;" src="'.$proImage.'" height="50" width="50" />'." ".$proName;
            $html .= '<br>';
            $html .= '<a href="'.$reviewUrl.'" target="_blank">Write a review</a>';
            $html .= '</div>';
        }
        
        
        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        
        $translate->setTranslateInline(false);
        try {
            
            $mailTemplate = Mage::getModel('core/email_template');
            
            /* @var $mailTemplate Mage_Core_Model_Email_Template */

            //get configured email template
            $template = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, Mage::app()->getStore()->getId());

            $mailSender = Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER, Mage::app()->getStore()->getId());
            
            $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>Mage::app()->getStore()->getId()))
                ->sendTransactional(
                $template,
                $mailSender,
                $customerEmail,
                $firstName,
                array(
                    'firstName' => $firstName,
                    'productId' => $productId,
                    'productDetail' => $html,
                    'categoryId' => $categoryId
                )
            );
            
            if (!$mailTemplate->getSentSuccess()) {
                throw new Exception();
            }
                
            $translate->setTranslateInline(true);
            //Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cp_reviewreminder')->__('Your reminder sent successfully.'));
            //$this->_redirect('*/*/');
            return true;
        } catch (Exception $ex) {
            $translate->setTranslateInline(true);
            //Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cp_reviewreminder')->__('Unable to send reminder. Please, try again later'));
            // $this->_redirect('*/*/');
            return false;
        }
    }
    
    /**
     * Get product category id
     *
     * @param Mage_Catalog_Model_Product $product
     * @return boolean/int categoryId
     */
    public function getProductCategoryId($product) {
        /* @var $product Mage_Catalog_Model_Product */
        if ($product->getId()) {
            $categoryIds = $product->getCategoryIds();
            if (is_array($categoryIds) and count($categoryIds) > 0) {
                $categoryId = (isset($categoryIds[0]) ? $categoryIds[0] : null);
                return $categoryId;
            };
        }
        return false;
    }
}