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
jimport('joomla.application.component.modelitem');
 
/**
 * Product Options Model
 */
class PoecomModelCurrencies extends JModel
{
    /**
	 * @var object item
	 */
	protected $item;
    
    protected $items;
    
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
	public function getTable($type = 'Currency', $prefix = 'PoecomTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}
    
   	/**
	 * Get Products
	 * @return array $locations Array of locations objects
     */
	public function getItems($currency_id = 0) 
	{ 
		if (!isset($this->items)) 
		{
			$q = $this->_db->getQuery(true);
            $q->select('*');
			$q->from('#__poe_currency');
            if($currency_id > 0){
                $q->where('id='.$currency_id);
            }
            $q->order('name');
			 
            $this->_db->setQuery($q);
                
			if (!$this->items = $this->_db->loadObjectList()){
				$this->setError($this->_db->getError());
			}else{
                // Merge application and individual object params
                $params = new JRegistry;
                $idx = 0;
                
                foreach($this->items as $item){
                    if(isset($item->params)){
    				// Load the JSON string
    				$params->loadJSON($item->params);
    				$this->items[$idx]->params = $params;
     
    				// Merge global params with item params
    				$params = clone $this->getState('params');
    				$params->merge($this->items[$idx]->params);
    				$this->items[$idx]->params = $params;
                    
                    $idx++;
                    }
                }
				
			}
		}
		return $this->items;
	}
}
