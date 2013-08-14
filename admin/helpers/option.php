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
 * Product Option helper.
 */
abstract class OptionHelper {

    /**
     * Get the actions
     */
    public static function getActions($optionId = 0) {
        jimport('joomla.access.access');
        $user = JFactory::getUser();
        $result = new JObject;

        if (empty($optionId)) {
            $assetName = 'com_poecom';
        } else {
            $assetName = 'com_poecom.name.' . (int) $optionId;
        }

        $actions = JAccess::getActions('com_poecom', 'component');

        foreach ($actions as $action) {
            $result->set($action->name, $user->authorise($action->name, $assetName));
        }

        return $result;
    }

}