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
 * RFQ List Model
 */
class PoecomModelRequests extends JModelList{
    
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
        $app = JFactory::getApplication();
        $session = JFactory::getSession();

        // Adjust the context to support modal layouts.
        if ($layout = JRequest::getVar('layout')) {
                $this->context .= '.'.$layout;
        }

        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $status = $this->getUserStateFromRequest($this->context.'.filter.status_id', 'filter_status_id', '');
        $this->setState('filter.status_id', $status);

        // List state information.
        parent::populateState('rfq.number', 'asc');
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
        $id	.= ':'.$this->getState('filter.status_id');

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
        // Select the required fields from the table.
        $q->select(
            $this->getState(
                    'list.select',
                    'rfq.id, rfq.number, rfq.status_id'
            )
        );
        $q->select('rfq.*, ju.name username, stat.name statusname');
        $q->from('#__poe_request rfq');
        $q->innerJoin('#__poe_request_status stat ON stat.id=rfq.status_id');
        $q->innerJoin('#__users ju ON ju.id=rfq.juser_id');
        
        // Filter by status state
        $statusId = $this->getState('filter.status_id');
        if (is_numeric($statusId) && $statusId > 0) {
            $q->where('rfq.status_id = ' . (int) $statusId);
        }/*else if ($status === '') {
            $q->where('(rfq.status = 0 OR rfq.status = 1)');
        } */
        
        
        // Filter by search in name.
        $search = trim($this->getState('filter.search'));

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $q->where('rfq.id = '.(int) substr($search, 3));
            }else if (stripos($search, 'number:') === 0) {
                $search = $db->Quote('%'.$db->getEscaped(substr($search, 7), true).'%');
                $q->where('(rfq.number LIKE '.$search.')');
            }else if (stripos($search, 'user:') === 0) {
                $search = $db->Quote('%'.$db->getEscaped(substr($search, 7), true).'%');
                $q->where('(ju.name LIKE '.$search.')');
            }else if (stripos($search, 'order:') === 0) {
                $search = $db->Quote('%'.$db->getEscaped(substr($search, 7), true).'%');
                $q->where('(rfq.order_id LIKE '.$search.')');
            }else{
                $search = $db->Quote('%'.$db->getEscaped(substr($search, 7), true).'%');
                $q->where('(rfq.number LIKE '.$search.')');
            }
        }
        
        $q->order('rfq.number');
    
        return $q;
    }
}
