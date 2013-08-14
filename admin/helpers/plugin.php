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
 * Plugin helper.
 */
class PluginHelper {

    public function __construct() {
        
    }

    public function getPluginParams($name = '', $folder = '') {
        $params = array();

        $db = JFactory::getDBO();
        $q = $db->getQuery(true);
        $q->select('params');
        $q->from('#__extensions');
        $q->where('folder="' . (string) $folder . '"');
        $q->where('element="' . (string) $name . '"');

        $db->setQuery($q);

        if ($result = $db->loadResult()) {
            $params = json_decode($result);
        }

        return $params;
    }

}