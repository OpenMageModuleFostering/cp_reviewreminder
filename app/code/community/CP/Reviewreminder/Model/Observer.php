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
class CP_Reviewreminder_Model_Observer
{
    public function addReviewReminderInformation($observer)
    {
        //check is extension enabled
         if (!Mage::helper('cp_reviewreminder')->isExtensionEnabled()) {
             return;
         }
         
        $event = $observer->getEvent();
        $orderIds = $event->getOrderIds();
        if(!empty($orderIds) && is_array($orderIds)){
            foreach ($orderIds as $orderId) {
                //check order id
                if(empty($orderId)){
                    continue;
                }
                
                //get order information
                $order = Mage::getModel('sales/order')->load($orderId);
                $customerId = $order->getCustomerId();
                //Mage::log("cid=".$customerId);
                //check customer id
                if(empty($customerId)){
                    continue;
                }
                    
                $currentTimestamp = time();

                //product ids from order ids
                $items = $order->getAllVisibleItems();
                //Mage::log($items);
                $productId = array();
                if(!empty($items) && is_array($items)){
                    foreach ($items as $item) {
                        $productIds[] = $item->getProductId();
                    }
                }
                // Mage::log($productIds);
                    
                //Save data
                if(!empty($productIds) && is_array($productIds)){

                    $transactionSave = Mage::getModel('core/resource_transaction');

                    foreach ($productIds as $productId) {
                        //Check is reminder exist
                        if(Mage::Helper('cp_reviewreminder')->isReminderExist($productId, $customerId)){
                            continue;
                        }

                        //Check is review already added by customer
                        if(Mage::Helper('cp_reviewreminder')->isReviewAlreadyAdded($productId, $customerId)){
                            continue;
                        }

                        //add reminder
                        $reviewreminder = Mage::getModel('cp_reviewreminder/reviewreminder');
						$reviewreminder->setOrderIncid($order->getIncrementId());
						$reviewreminder->setOrderId($orderId);
                        $reviewreminder->setCustomerId($customerId);
                        $reviewreminder->setProductId($productId);
                        $reviewreminder->setCreatedAt($currentTimestamp);

                        $transactionSave->addObject($reviewreminder);
                        break;
                    }
                    $transactionSave->save();
                }
            }
        }
    }
    
    /**
     * Update reminder after review added
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function reviewSaveAfter(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();
        $review = $event->getObject();
        $productId = $review->getEntityPkValue();
        $cutomerId = $review->getCustomerId();
        
        if(empty($productId) || empty($cutomerId)){
            return false;
        }
        
        //Check is reminder exist
        $isRecordExist = Mage::Helper('cp_reviewreminder')->isReminderExist($productId, $cutomerId);
        //update review flag
        if($isRecordExist){
            $collection = Mage::getModel('cp_reviewreminder/reviewreminder')->getCollection()
                ->addFieldToFilter('customer_id', $cutomerId)
                ->addFieldToFilter('product_id', $productId);
        
            if($collection->count() > 0){
                foreach($collection as $reminder){
                    $reminder->setIsReviewAdded(1);
                    $reminder->save();
                }
            }
        }
    }
    
    /**
     * Cron job method to send product review reminder
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function sendReviewReminder(Mage_Cron_Model_Schedule $schedule)
    {
        //check is extension enabled
         if (!Mage::helper('cp_reviewreminder')->isExtensionEnabled()) {
             return;
         }
         
        //get all records to send reminder
        $collection = Mage::getModel('cp_reviewreminder/reviewreminder')->getCollection()
            ->addFieldToFilter('is_review_added', 0)
			->addFieldToFilter('reminder_count', array('lt' => Mage::getStoreConfig('review_reminder/general_settings/number_of_times')));
		
        if($collection->count() > 0){
            foreach ($collection as $reminder){
                $customerId = $reminder->getCustomerId();
                if(empty($customerId)){
                    continue;
                }
                
                //Check config settings
                if(!Mage::Helper('cp_reviewreminder')->isMatchAllConfigSettings($reminder)){
                    continue;
                }
                
                //send reminder mail
                $isMailSent = Mage::Helper('cp_reviewreminder/mail')->sendReminderEmail($reminder);
                
                //update reminder record
                if($isMailSent){
                    $reminder->setIsReminderSent(1);
                    $reminderCount = $reminder->getReminderCount();
					
                    //Increment reminder count by 1
                    $reminderCount = $reminderCount + 1;
					$reminder->setReminderCount($reminderCount);
                    $currentTimestamp = time();
                    $reminder->setSentAt($currentTimestamp);
                    $reminder->setUpdatedAt($currentTimestamp);
                    $reminder->save();
                }
            }
        }
    }
}
