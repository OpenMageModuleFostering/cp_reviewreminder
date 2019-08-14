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
$installer = $this;
/**
 * Prepare database for install
 */
$installer->startSetup();

/**
 * Create table 'cp_review_reminder'
 */
$table = $installer->getConnection()
        ->newTable($installer->getTable('cp_reviewreminder/reviewreminder'))
         ->addColumn('reminder_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Reminder Id')
        ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Order id')
		 ->addColumn('order_incid', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Order increment id')
        ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Customer Id')
        ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Product Id')
        ->addColumn('is_reminder_sent', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is reminder sent')
        ->addColumn('is_review_added', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is review added')
        ->addColumn('reminder_count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Reminder count')
        ->addColumn('sent_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'default'   => NULL,
        ), 'Reminder sent time')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'default'   => NULL,
        ), 'Creation Time')
        ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'default'   => NULL,
        ), 'Update Time')
        ->setComment('CP reviewreminder Table');
$installer->getConnection()->createTable($table);
$installer->endSetup();
