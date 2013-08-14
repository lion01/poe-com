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
 * RFQ's helper.
 */
abstract class RequestsHelper {

    /**
     * Configure the Linkbar.
     */
    public static function addSubmenu($submenu) {
        JSubMenuHelper::addEntry(JText::_('COM_POECOM_SUBMENU_POECOM'), 'index.php?option=com_poecom', $submenu == 'poecom');
        JSubMenuHelper::addEntry(JText::_('COM_POECOM_SUBMENU_ORDERS'), 'index.php?option=com_poecom&view=orders', $submenu == 'orders');
        JSubMenuHelper::addEntry(JText::_('COM_POECOM_SUBMENU_PAY_TRANSACTIONS'), 'index.php?option=com_poecom&view=paytransactions', $submenu == 'paytransactions');

        // Set icon
        $document = JFactory::getDocument();
        $document->addStyleDeclaration('.icon-48-requests {background-image: url(../media/com_poecom/images/icon-48-requests.png);}');
    }

    /**
     * Get the actions
     */
    public static function getActions($requestId = 0) {
        jimport('joomla.access.access');
        $user = JFactory::getUser();
        $result = new JObject;

        if (empty($requestId)) {
            $assetName = 'com_poecom';
        } else {
            $assetName = 'com_poecom.name.' . (int) $requestId;
        }

        $actions = JAccess::getActions('com_poecom', 'component');

        foreach ($actions as $action) {
            $result->set($action->name, $user->authorise($action->name, $assetName));
        }

        return $result;
    }

}