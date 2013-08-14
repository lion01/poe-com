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
 * Order Status Model
 */
class PoecomModelOrderStatus extends JModel{
    /**
    * @var object item
    */
    protected $status_list;

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
    protected function populateState(){
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
    public function getTable($type = 'OrderStatus', $prefix = 'PoecomTable', $config = array()){
        return JTable::getInstance($type, $prefix, $config);
    }
    
    /**
     * Get Order Status List
     * 
     * @return array $status_list
     */
    public function getList(){
        $this->status_list = array();
        $this->status_list[] = array('value' => 0, 'text' => '--Show All--');
        
        $q = $this->_db->getQuery(true);
        $q->select('id value, name text');
        $q->from('#__poe_order_status');
        $q->order('sort_order');
       
        $this->_db->setQuery($q);
            
        if($status_list = $this->_db->loadObjectList()){
            $this->status_list = array_merge($this->status_list, $status_list);
        }
        
        return $this->status_list;
        
    }
}
