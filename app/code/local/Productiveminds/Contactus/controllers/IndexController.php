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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Admin
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once 'Mage/Contacts/controllers/IndexController.php';
class Productiveminds_Contactus_IndexController extends Mage_Contacts_IndexController
{
    
    const XML_PATH_ENABLED          = 'contactus_sectns/contactus_grps/enabled';
    const XML_PATH_EMAIL_RECIPIENT  = 'contactus_sectns/contactus_grps/email_recipient';
    const XML_PATH_EMAIL_SENDER     = 'contactus_sectns/contactus_grps/email_sender';
    const XML_PATH_EMAIL_TEMPLATE   = 'contactus_sectns/contactus_grps/email_template';
    
    public function preDispatch() {
        parent::preDispatch();
        if( !Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, Mage::app()->getStore()) ) {
            //$this->norouteAction();
        	Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('The Productiveminds Contact us module is disabled. Please enable it (in admin) to continue'));
        }
    }
	
    public function indexAction() {     
        $this->loadLayout();
        
        $currentModule = $this->getRequest()->getModuleName();
        if($currentModule == 'contacts') {
        	$this->getLayout()->getBlock('contactsForm')->setFormAction(Mage::getUrl('*/*/post'));
        } else if($currentModule == 'contactus') {
        	$this->getLayout()->getBlock('contactusForm')->setFormAction(Mage::getUrl('*/*/post'));
        }
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }
	
    public function postAction() {
        $post = $this->getRequest()->getPost();
        if ( $post ) {
        	
        	$is_not_spam = false;
        	if(empty($post['city']) || $post['city'] == '') {
        		$is_not_spam = true;
        	}
        	//  Added by Sola: Now check if user fills the unrequired field
        	if($is_not_spam) {
	            $translate = Mage::getSingleton('core/translate');
	            /* @var $translate Mage_Core_Model_Translate */
	            $translate->setTranslateInline(false);
	            try {
	                $postObject = new Varien_Object();
	                $postObject->setData($post);
	
	                $error = false;
	
	                if (!Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
	                    $error = true;
	                }
	
	                if (!Zend_Validate::is(trim($post['comment']) , 'NotEmpty')) {
	                    $error = true;
	                }
	
	                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
	                    $error = true;
	                }
	
	                if (Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
	                    $error = true;
	                }
	                
	                if ($error) {
	                	throw new Exception();
	                }
	                
	                if (version_compare(Mage::helper('productivemindscore')->getInstalledMagentoVersion(), '1.8.0.0') >= 0) {
	                	if (!$this->_validateFormKey()) {
	                		Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Please refresh the page - invalid security form key.'));
	                		$this->_redirect('*/*/');
	                		return;
	                	}
	                }
		            
	                $mailTemplate = Mage::getModel('core/email_template');
	                
	                $store = Mage::app()->getStore();
	                $senderEmailId = Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER, $store);
	                $recipientEmailId = Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT, $store);
	                $emailTemplate = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $store);
	                
	                if (empty($recipientEmailId) || $recipientEmailId == '') {
	                	Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Email address is missing - see module configuration in Magento admin.'));
	                	$this->_redirect('*/*/');
	                	return;
	                }
	                
	                if (empty($emailTemplate) || $emailTemplate == '') {
	                	Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Email template is missing - see module configuration in Magento admin.'));
	                	$this->_redirect('*/*/');
	                	return;
	                }
	                
	                $recipientEmailId = str_replace(' ', '', $recipientEmailId);
	                $recipientEmailId = explode(',', $recipientEmailId);
	                
	                $mailTemplate->setDesignConfig(array('area' => 'frontend'))
	                ->setReplyTo($post['email'])
	                ->sendTransactional($emailTemplate, $senderEmailId, $recipientEmailId, null, array('data' => $postObject)
	                );
					
	                if (!$mailTemplate->getSentSuccess()) {
	                    throw new Exception();
	                }
					
	                $translate->setTranslateInline(true);
	                
	                $confirmationMessage = Mage::getStoreConfig('contactus_sectns/contactus_grps/confirmation_message', $store);
	                Mage::getSingleton('customer/session')->addSuccess(Mage::helper('contacts')->__($confirmationMessage));
	                $this->_redirect('*/*/');
	                return;
	                
	            } catch (Exception $e) {
	                $translate->setTranslateInline(true);
	                Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Unable to submit your request. Please, try again later'));
	                $this->_redirect('*/*/');
	                return;
	            }
            } else {
            	$this->_redirect('*/*/');
            }

        } else {
            $this->_redirect('*/*/');
        }
    }
}
