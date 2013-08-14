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
 * Products helper.
 */
abstract class ProductsHelper {

    /**
     * Configure the Linkbar.
     */
    public static function addSubmenu($submenu) {

        JSubMenuHelper::addEntry(JText::_('COM_POECOM_SUBMENU_POECOM'), 'index.php?option=com_poecom', $submenu == 'poecom');
        JSubMenuHelper::addEntry(JText::_('COM_POECOM_SUBMENU_CATEGORIES'), 'index.php?option=com_categories&view=categories&subview=products&extension=com_poecom', $submenu == 'categories');
        JSubMenuHelper::addEntry(JText::_('COM_POECOM_SUBMENU_IMAGES'), 'index.php?option=com_poecom&view=images', $submenu == 'images');
        JSubMenuHelper::addEntry(JText::_('COM_POECOM_SUBMENU_TABS'), 'index.php?option=com_poecom&view=producttabs', $submenu == 'producttabs');
        JSubMenuHelper::addEntry(JText::_('COM_POECOM_SUBMENU_OPTIONS'), 'index.php?option=com_poecom&view=options', $submenu == 'options');
        JSubMenuHelper::addEntry(JText::_('COM_POECOM_SUBMENU_OPTION_VALUES'), 'index.php?option=com_poecom&view=optionvalues', $submenu == 'optionvalues');
        // set some global property
        $document = JFactory::getDocument();
        $document->addStyleDeclaration('.icon-48-products {background-image: url(../media/com_poecom/images/icon-48-products.png);}');

        if ($submenu == 'categories') {
            $document->setTitle(JText::_('COM_POECOM_ADMINISTRATION_CATEGORIES'));
        }
    }

    /**
     * Get the actions
     */
    public static function getActions($productId = 0) {
        jimport('joomla.access.access');
        $user = JFactory::getUser();
        $result = new JObject;

        if (empty($productId)) {
            $assetName = 'com_poecom';
        } else {
            $assetName = 'com_poecom.name.' . (int) $productId;
        }

        $actions = JAccess::getActions('com_poecom', 'component');

        foreach ($actions as $action) {
            $result->set($action->name, $user->authorise($action->name, $assetName));
        }

        return $result;
    }

}