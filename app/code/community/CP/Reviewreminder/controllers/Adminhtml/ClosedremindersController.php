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
class CP_Reviewreminder_Adminhtml_ClosedremindersController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Pre dispatch action that allows to redirect to no route page in case of 
     * disabled extension through admin panel
     */
    public function preDispatch()
    {
        parent::preDispatch();
        
        if (!Mage::helper('cp_reviewreminder')->isExtensionEnabled()) {
            $this->setFlag('', 'no-dispatch', true);
            $this->_redirect('noRoute');
        }        
    }
    
    /**
     * Init actions
     *
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->_title($this->__('Review Reminder'));
        
        $this->loadLayout()
            ->_setActiveMenu('catalog/review_reminder')
            ->_addBreadcrumb(Mage::helper('cp_reviewreminder')->__('Review Reminder')
                    , Mage::helper('cp_reviewreminder')->__('Review Reminder'));
        return $this;
    }
    
    /**
     * Index action method
     */
    public function indexAction() 
    {
        $this->_initAction();
        $this->renderLayout();
    }
    
    /**
     * Multiple reminder deletion
     *
     */
    public function massDeleteAction()
    {
        //Get reminder ids from selected checkbox
        $reminderIds = $this->getRequest()->getParam('reminderIds');
        
        if (!is_array($reminderIds)) {
             Mage::getSingleton('adminhtml/session')->addError($this->__('Please select reminder(s).'));
        } else {
            if (!empty($reminderIds)) {
                try {
                    foreach ($reminderIds as $reminderId) {
                        $reminder = Mage::getSingleton('cp_reviewreminder/reviewreminder')->load($reminderId);
                        //delete record
                        $reminder->delete();
                    }
                     Mage::getSingleton('adminhtml/session')->addSuccess(
                        $this->__('Total of %d record(s) have been deleted.', count($reminderIds))
                    );
                } catch (Exception $e) {
                     Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/');
    }
}