<?php

defined('_JEXEC') or die('Restricted access');

/**
 * POE-com - Admin - Unit of Measure Helper Class
 * 
 * @package com_poecom
 * @author Micah Fletcher
 * @copyright 2010 - 2012 (C) Extensible Point Solutions Inc. All Right Reserved.
 * @license GNU GPLv3, http://www.gnu.org/licenses/gpl.html
 *
 * @since 2.0 - 3/14/2012 10:22:07 AM
 *
 * http://www.exps.ca
 * */
class UOMHelper {

    function __construct() {
        //empty
    }

    /**
     * Conversion value from one uom to another
     * 
     * @param int $uom1 Current UOM - convert from
     * @param int $uom2 Desired UOM - convert to
     * @param float $value Current value in Current UOM
     * 
     * @return float $converted_value 
     */
    public function getConversion($uom1 = 0, $uom2 = 0, $value = 0) {
        $db = JFactory::getDbo();

        // get conversion factor
        $q = $db->getQuery(true);
        $q->select('factor');
        $q->from('#__poe_uom_conversion');
        $q->where('uom_1=' . $uom1 . ' AND uom_2=' . $uom2);

        $db->setQuery($q);

        if ($result = $db->loadResult()) {

            $factor = floatval($result);
        } else {
            // try inverse
            $q = $db->getQuery(true);
            $q->select('factor');
            $q->from('#__poe_uom_conversion');
            $q->where('uom_1=' . $uom2 . ' AND uom_2=' . $uom1);

            $db->setQuery($q);

            if ($result = $db->loadResult()) {

                $factor = floatval(1 / $result);
            } else {
                return false;
            }
        }
        $converted_value = $value * $factor;

        return $converted_value;
    }

}

?>