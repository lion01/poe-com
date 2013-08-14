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
 * Poecom component helper.
 */
abstract class PoecomHelper {

    /**
     * Configure the Linkbar.
     */
    public static function addSubmenu($submenu) {
        /*  no submenus on main admin page */
        JSubMenuHelper::addEntry(JText::_('COM_POECOM_SUBMENU_POECOM'), 'index.php?option=com_poecom', $submenu == 'poecom');
        $subview = JRequest::getVar('subview', '');

        if (strlen($subview)) {
            switch ($subview) {
                case 'products':
                    JSubMenuHelper::addEntry(JText::_('COM_POECOM_SUBMENU_PRODUCTS'), 'index.php?option=com_poecom&view=products', $submenu == 'products');
                    break;
                default:
                    break;
            }
        }

        JSubMenuHelper::addEntry(JText::_('COM_POECOM_SUBMENU_CATEGORIES'), 'index.php?option=com_categories&view=categories&extension=com_poecom', $submenu == 'categories');
        // set some global property
        $document = JFactory::getDocument();
        $document->addStyleDeclaration('.icon-48-poecom {background-image: url(../media/com_poecom/images/poecom-tree-48.png);}');
        if ($submenu == 'categories') {
            $document->setTitle(JText::_('COM_POECOM_ADMINISTRATION_CATEGORIES'));
        }
    }

    /**
     * Get the actions
     */
    public static function getActions() {

        //TODO: Add actions
        jimport('joomla.access.access');
        $user = JFactory::getUser();
        $result = new JObject;

        $assetName = 'com_poecom';

        $actions = JAccess::getActions('com_poecom', 'component');

        foreach ($actions as $action) {
            $result->set($action->name, $user->authorise($action->name, $assetName));
        }

        return $result;
    }

}