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
 * Order Lines List Model
 */
class PoecomModelOrderLines extends JModelList{
    
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
        if (($layout = $jinput->get('layout','default', 'string'))) {
            $this->context .= '.'.$layout;
        }
        
        // set parent state
        parent::populateState();

        //set filters
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
    * Extended to prepare selected options
    * 
    * Gets an array of objects from the results of database query.
    *
    * @param   string   $query       The query.
    * @param   integer  $limitstart  Offset.
    * @param   integer  $limit       The number of records.
    *
    * @return  array  An array of results.
    *
    * @since   11.1
    */
    protected function _getList($query, $limitstart = 0, $limit = 0){
            $this->_db->setQuery($query, $limitstart, $limit);
            $result = $this->_db->loadObjectList();
            
            if($result){
                $idx = 0;
                $productModel = JModel::getInstance('Product', 'PoecomModel');
                foreach($result as $line){
                    //get product detail
                    $result[$idx]->product_detail = $productModel->getProductDetail($line->product_id);
                    
                    
                    if(strlen($line->selected_options)){
                        $tmp = json_decode($line->selected_options);
                        
                        if(tmp){
                            $result[$idx]->selected_options = $tmp;
                        }
                    }
                    
                    $idx++;
                }
            }

            return $result;
    }
    
    /**
    * Extended to add order detail
    * Method to build an SQL query to load the list data.
    *
    * @return	string	An SQL query
    */
    protected function getListQuery(){
        // Create a new query object.		
        $db = JFactory::getDBO();
        $q = $db->getQuery(true);
        $q->select('ol.*, o.order_date,p.sku, os.name status, ju.name username');
        $q->from('#__poe_order_line ol');
        $q->innerJoin('#__poe_order o ON o.id=ol.order_id');
        $q->innerJoin('#__poe_product p ON p.id=ol.product_id');
        $q->leftJoin('#__poe_request rfq ON rfq.id=o.rfq_id');
        $q->innerJoin('#__poe_order_status os ON os.id=o.status_id');
        $q->innerJoin('#__users ju ON ju.id=o.juser_id');
        
        // Filter by published state
        $status_id = $this->getState('filter.status');
        if (is_numeric($status_id) && $status_id > 0) {
            $q->where('o.status_id = ' . (int) $status_id);
        }
        
        $order_id = $this->getState('filter.order');
        if (is_numeric($order_id) && $order_id > 0) {
            $q->where('ol.order_id = ' . (int) $order_id);
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
        
        $q->order('ol.order_id, ol.id');
    
        return $q;
    }
}