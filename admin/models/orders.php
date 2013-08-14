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
 * Order List Model
 */
class PoecomModelOrders extends JModelList{
    
    /**
    * Method to auto-populate the model state.
    *
    * Note. Calling getState in this method will result in recursion.
    *
    * @return	void
    * @since	1.6
    */
    protected function populateState($ordering = null, $direction = null){
        // Initialise variables.
        $jinput = JFactory::getApplication()->input;
        

        // Adjust the context to support modal layouts.
        $layout = $jinput->get('layout', '', 'CMD');
        if ($layout) {
            $this->context .= '.'.$layout;
        }
        
        // Set state information.
        parent::populateState();

        //Set filters
        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $status_id = $this->getUserStateFromRequest($this->context.'.filter.status', 'filter_status');
        $this->setState('filter.status', $status_id);
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
        $id	.= ':'.$this->getState('filter.status');

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
       
        $q->select('o.*, rfq.number rfq_number, os.name status, ju.name username');
        $q->from('#__poe_order o');
        $q->leftJoin('#__poe_request rfq ON rfq.id=o.rfq_id');
        $q->innerJoin('#__poe_order_status os ON os.id=o.status_id');
        $q->innerJoin('#__users ju ON ju.id=o.juser_id');
        
        // Filter by published state
        $status_id = $this->getState('filter.status');
        if (is_numeric($status_id) && $status_id > 0) {
            $q->where('o.status_id = ' . (int) $status_id);
        }
        
        // Filter by search
        // Expecting order id, username, rfq number, payment id
        $search = trim($this->getState('filter.search'));

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $q->where('o.id = '.(int) trim(substr($search, 3)));
            }else if(stripos($search, 'customer:') === 0) {
                $search = $db->Quote('%'.$db->getEscaped(trim(substr($search, 9)), true).'%');
                $q->where('(ju.name LIKE '.$search.')');
            }else if (stripos($search, 'rfq:') === 0) {
                $search = $db->Quote('%'.$db->getEscaped(trim(substr($search, 4)), true).'%');
                $q->where('(rfq.number LIKE '.$search.')');
            }else{
                $search = $db->Quote('%'.$db->getEscaped(substr($search, 3), true).'%');
                $q->where('(o.id LIKE '.$search.')');
            }
        }
        
        $q->order('o.order_date, o.juser_id');
 
        return $q;
    }
}