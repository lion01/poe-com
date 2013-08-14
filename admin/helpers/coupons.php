<?php

defined('_JEXEC') or die;
/**
 * Product Options E-commerce Extension
 * 
 * @author Micah Fletcher
 * @copyright 2011 - 2012 Extensible Point Solutions Inc. All Right Reserved
 * @license GNU GPL version 3, http://www.gnu.org/copyleft/gpl.html
 * @link http://www.exps.ca
 * @version 2.5.0
 * @since 2.5
 * */

/**
 * Coupons Helper.
 */
abstract class CouponsHelper {

    /**
     * Configure the Linkbar.
     */
    public static function addSubmenu($submenu) {
        JSubMenuHelper::addEntry(JText::_('COM_POECOM_SUBMENU_POECOM'), 'index.php?option=com_poecom', $submenu == 'poecom');
        JSubMenuHelper::addEntry(JText::_('COM_POECOM_SUBMENU_REQUESTS'), 'index.php?option=com_poecom&view=requests', $submenu == 'requests');
        JSubMenuHelper::addEntry(JText::_('COM_POECOM_SUBMENU_ORDERS'), 'index.php?option=com_poecom&view=orders', $submenu == 'orders');
        JSubMenuHelper::addEntry(JText::_('COM_POECOM_SUBMENU_PROMOTIONS'), 'index.php?option=com_poecom&view=promotions', $submenu == 'promotions');

        // Set icon
        $document = JFactory::getDocument();
        $document->addStyleDeclaration('.icon-48-coupon {background-image: url(../media/com_poecom/images/icon-48-coupons.png);}');
    }

    /**
     * Get the actions
     */
    public static function getActions($couponId = 0) {
        jimport('joomla.access.access');
        $user = JFactory::getUser();
        $result = new JObject;

        if (empty($couponId)) {
            $assetName = 'com_poecom';
        } else {
            $assetName = 'com_poecom.name.' . (int) $couponId;
        }

        $actions = JAccess::getActions('com_poecom', 'component');

        foreach ($actions as $action) {
            $result->set($action->name, $user->authorise($action->name, $assetName));
        }

        return $result;
    }

}