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
 * Payment Transaction List Model
 */
class PoecomModelPayTransactions extends JModelList{
    
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
        
        $type_id = $this->getUserStateFromRequest($this->context.'.filter.type', 'filter_type');
        $this->setState('filter.type', $type_id);

        $status_id = $this->getUserStateFromRequest($this->context.'.filter.status', 'filter_status');
        $this->setState('filter.status', $status_id);

        // List state information.
        parent::populateState('pt.transaction_number', 'asc');
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
        $id	.= ':'.$this->getState('filter.type');
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
        // Select the required fields from the table.
        $q->select(
            $this->getState(
                'list.select',
                'pt.id, pt.transaction_number, pt.type_id, pt.status_id'
            )
        );
        $q->select('pt.*,ptype.name type_name, ps.name payment_status, rfq.number rfq_number, pm.name pay_method_name');
        $q->from('#__poe_payment_transaction pt');
        $q->innerJoin('#__poe_payment_transaction_type ptype ON ptype.id=pt.type_id');
        $q->innerJoin('#__poe_payment_status ps ON ps.id=pt.status_id');
        $q->leftJoin('#__poe_request rfq ON rfq.id=pt.rfq_id');
        $q->leftJoin('#__poe_payment_method pm ON pm.id=pt.pay_method_id');
        
        // Filter by transaction type
        $type_id = $this->getState('filter.type');
        if (is_numeric($type_id) && $type_id > 0) {
            $q->where('pt.type_id = '.(int) $type_id);
        }
        
        // Filter by transaction status
        $status_id = $this->getState('filter.status');
        if (is_numeric($status_id) && $status_id > 0) {
            $q->where('pt.status_id = '.(int) $status_id);
        }
        
        // Filter by search in name.
        $search = trim($this->getState('filter.search'));

        if(!empty($search)) {
            if(stripos($search, 'id:') === 0) {
                $q->where('pt.id = '.(int) trim(substr($search, 3)) );
            }else if(stripos($search, 'txn:') === 0) {
                $search = $db->Quote('%'.$db->getEscaped(substr($search, 4), true).'%');
                $q->where('(pt.transaction_number LIKE '.trim($search).')');
            }else if(stripos($search, 'order:') === 0) {
                $search = $db->Quote('%'.$db->getEscaped(trim(substr($search, 6) ), true).'%');
                $q->where('(pt.order_id LIKE '.$search.')');
            }else if(stripos($search, 'method:') === 0) {
                $search = $db->Quote('%'.$db->getEscaped( trim(substr($search, 7) ), true).'%');
                $q->where('(pm.name LIKE '.$search.')');
            }else{
                $search = $db->Quote('%'.$db->getEscaped( trim(substr($search, 3) ), true).'%');
                $q->where('(pt.id LIKE '.$search.')');
            }
        }
        
        $q->order('pt.transaction_number');
    
        return $q;
    }
}
