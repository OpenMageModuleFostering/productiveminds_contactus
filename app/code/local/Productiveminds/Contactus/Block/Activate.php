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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Messages block
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Productiveminds_Contactus_Block_Activate extends Mage_Core_Block_Template
{
    public function activateMessage() {
    	$activateMessage = Mage::getSingleton('core/session', array('name' => 'frontend'))->getActivateMessage();    	
    	Mage::getSingleton('core/session', array('name' => 'frontend'))->unsActivateMessage('');
    	
		return $activateMessage;
    }
    
    public function useFormKey() {
    	if (version_compare(Mage::helper('productivemindscore')->getInstalledMagentoVersion(), '1.8.0.0') >= 0) {
    		return true;
    	}
    	return false;
    }
}
