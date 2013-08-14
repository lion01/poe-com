<?php
defined('_JEXEC') or die('Restricted access');
/**
 * Product Options E-commerce Extension
 * 
 * @author Micah Fletcher
 * @copyright 2011 - 2012 Extensible Point Solutions Inc. All Right Reserved
 * @license GNU GPL version 3, http://www.gnu.org/copyleft/gpl.html
 * @link http://www.exps.ca
 * @version 2.5.0
 * @since 2.5
**/ 
jimport('joomla.application.component.modellist');
/**
 * Locations List Model
 */
class PoecomModelLocations extends JModelList{
    
    /**
    * Method to auto-populate the model state.
    *
    * Note. Calling getState in this method will result in recursion.
    *
    * @return	void
    * @since	1.6
    */
    protected function populateState($ordering = null, $direction = null){
        $jinput = JFactory::getApplication()->input;
        // Adjust the context to support modal layouts.
        if ( ($layout = $jinput->get('layout', 'default')) ) {
                $this->context .= '.'.$layout;
        }
        
         // Set state and pagination
        parent::populateState();

        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        
        $enabled = $this->getUserStateFromRequest($this->context.'.filter.enabled', 'filter_enabled');
        $this->setState('filter.enabled', $enabled);
    }
    
    /**
    * Method to get the script that have to be included on the form
    *
    * @return string	Script files
    */
    public function getScript(){
        return JURI::root(true).'/administrator/components/com_poecom/models/forms/locations.js';
    }

    /**
    * Method to get a store id based on model configuration state.
    *
    * This is necessary because the model is used by the component and
    * different modules that might need different sets of data or different
    * ordering requirements.
    *
    * @param	string		$id	A prefix for the store id.
    *
    * @return	string		A store id.
    * @since	1.6
    */
    protected function getStoreId($id = ''){
        // Compile the store id.
        $id	.= ':'.$this->getState('filter.search');

        return parent::getStoreId($id);
    }
    
    /**
    * Method to build an SQL query to load the list data.
    *
    * @return	string	An SQL query
    */
    protected function getListQuery(){
        // Create a new query object.		
        $db = JFactory::getDBO();
        $q = $db->getQuery(true);
    
        $q->select('l.*, c.name country_name, r.name region_name');
        $q->from('#__poe_location l');
        $q->innerJoin('#__geodata_country c ON c.id=l.country_id');
        $q->innerJoin('#__geodata_region r ON r.id=l.region_id');
        
        // Filter by location type
        $enabled = $this->getState('filter.enabled');
        if (is_numeric($enabled)) {
            $q->where('l.enabled = '.(int) $enabled);
        }
        
        // Filter by search in name.
        $search = trim($this->getState('filter.search'));

        if(!empty($search)) {
            if(stripos($search, 'id:') === 0) {
                $q->where('l.id = '.(int) trim(substr($search, 3)) );
            }else if(stripos($search, 'l.name:') === 0) {
                $search = $db->Quote('%'.$db->getEscaped(substr($search, 4), true).'%');
                $q->where('(l.name LIKE '.trim($search).')');
            }else{
                $search = $db->Quote('%'.$db->getEscaped( trim(substr($search, 3) ), true).'%');
                $q->where('(l.id LIKE '.$search.')');
            }
        }
       
        $q->order('l.name');

        return $q;
    }
    
    /**
     * Get list of Locations
     * 
     * @return type 
     */
    public function getLocationsList(){
	$locations = array();
	$locations[] = array('value' => 0, 'text' => '--Show All--');
	
	$db = $this->getDBO();
	
	$q = $db->getQuery(true);
        $q->select('id, name');
        $q->from('#__poe_location');
        
        $db->setQuery($q);

        if( ($result = $db->loadObjectList()) ){
            foreach($result as $r){
		$locations[] = array('value' => $r->id, 'text' => $r->name);
	    }
        }
	
	return $locations;
    }
}
