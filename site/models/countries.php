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
jimport('joomla.application.component.model');
 
/**
 * Tax Model
 */
class PoecomModelCountries extends JModel
{
    /**
	 * @var object item
	 */
	protected $regions;

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
	protected function populateState() 
	{
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
	public function getTable($type = 'Countries', $prefix = 'PoecomTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}
    
    /**
     * Get Tax Rate for Country and Region
     * 
     * @param string $country_code Two character country code
     * @param string $region_code Two character code for province or state
     * 
     * @return array $taxes Array of tax rates
     */
    public function getRegions($country_id = 0){
        $this->regions = array();
        
        $q = $this->_db->getQuery(true);
        $q->select('id value, name text');
		$q->from('#__geodata_region');
        $q->where('country_id='.$country_id);
       
        $this->_db->setQuery($q);
            
		if($regions = $this->_db->loadObjectList()){
		      $this->regions = $regions;
		}
        
        return $this->regions;
    }
}
