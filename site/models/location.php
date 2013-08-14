<?php

defined('_JEXEC') or die('Restricted access');
/**
 * POE-com - Site - Location Model Class
 * 
 * @package com_poecom
 * @author Micah Fletcher
 * @copyright 2010 - 2012 (C) Extensible Point Solutions Inc. All Right Reserved.
 * @license GNU GPLv3, http://www.gnu.org/licenses/gpl.html
 *
 * @since 2.0 - 3/11/2012 10:21:57 PM
 *
 * http://www.exps.ca
 * */
jimport('joomla.application.component.modelitem');

/**
 * Location Model
 */
class PoecomModelLocation extends JModel {

    /**
     * @var object item
     */
    protected $location;

    /**
     * Method to auto-populate the model state.
     *
     * This method should only be called once per instantiation and is designed
     * to be called on the first call to the getState() method unless the model
     * configuration flag to ignore the request is set.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @return	void
     * @since	1.6
     */
    protected function populateState() {
        $app = JFactory::getApplication();

        // Load the application parameters.
        $params = $app->getParams();
        $this->setState('params', $params);

        parent::populateState();
    }

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param	type	The table type to instantiate
     * @param	string	A prefix for the table class name. Optional.
     * @param	array	Configuration array for model. Optional.
     * @return	JTable	A database object
     * @since	1.6
     */
    public function getTable($type = 'Location', $prefix = 'PoecomTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }
    /**
     * Get Location
     * 
     * @param int $id Location ID
     * @return object $location
     */
    public function getItem($id = 0){
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->select('l.*, c.name country_name, c.code2 countryCode, r.name region_name');
        $q->from('#__poe_location l');
        $q->innerJoin('#__geodata_country c ON c.id=l.country_id');
        $q->innerJoin('#__geodata_region r ON r.id=l.region_id');
        $q->where('l.id=' . (int) $id);
        $db->setQuery($q);

        $location = $db->loadObject();
       
        return $location;
    }
}
