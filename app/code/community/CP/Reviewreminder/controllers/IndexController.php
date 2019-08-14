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
class CP_Reviewreminder_IndexController extends Mage_Core_Controller_Front_Action
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
    
    function indexAction()
    {
        Mage::Helper('cp_reviewreminder')->isExtensionEnabled();
        $this->loadLayout();
        $this->renderLayout();
    }
    
    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
    
    /**
     * If customer logined in then redirect page to add review otherwise redirect 
     * page to login and after login redirect to add review.
     *
     */
    
    function addReviewAction()
    {               
        $productId  = (int) $this->getRequest()->getParam('id');
        $categoryId = (int) $this->getRequest()->getParam('category', false);
        
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('review/product/list', array('id' => $productId, 'category' => $categoryId));
        } else {
            $this->_getSession()->setBeforeAuthUrl(Mage::getUrl('review/product/list', 
                    array('id' => $productId, 'category' => $categoryId)));
            $this->_redirect('customer/account/login');
        }
        return;
    }
}