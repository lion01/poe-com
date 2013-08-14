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
 * Coupons List Model
 */
class PoecomModelCoupons extends JModelList{
    
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
        if ( ($layout = JRequest::getVar('layout')) ) {
                $this->context .= '.'.$layout;
        }

        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        
        $promotion_id = $this->getUserStateFromRequest($this->context.'.filter.type', 'filter_promotion');
        $this->setState('filter.promotion', $promotion_id);

        $status_id = $this->getUserStateFromRequest($this->context.'.filter.staus', 'filter_status');
        $this->setState('filter.status', $status_id);

        // List state information.
        parent::populateState('c.id', 'asc');
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
        $id	.= ':'.$this->getState('filter.promotion');
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
                'c.id, c.promotion_id,c.status_id'
            )
        );
        $q->select('c.*,p.name promotion, u.name username, s.name status');
        $q->from('#__poe_coupon c');
        $q->innerJoin('#__poe_promotion p ON p.id=c.promotion_id');
	$q->leftJoin('#__users u ON u.id=c.user_id');
        $q->innerJoin('#__poe_coupon_status s ON s.id=c.status_id');
        
        // Filter by promotion type
        $promotion_id = $this->getState('filter.promotion');
        if (is_numeric($promotion_id) && $promotion_id > 0) {
            $q->where('c.promtoion_id = '.(int) $promotion_id);
        }
        
        // Filter by promotion discount type
        $status_id = $this->getState('filter.status');
        if (is_numeric($status_id) && $status_id > 0) {
            $q->where('c.status_id = '.(int) $status_id);
        }
        
        // Filter by search in name.
        $search = trim($this->getState('filter.search'));

        if(!empty($search)) {
            if(stripos($search, 'id:') === 0) {
                $q->where('c.id = '.(int) trim(substr($search, 3)) );
            }else if(stripos($search, 'code:') === 0) {
                $search = $db->Quote('%'.$db->getEscaped(substr($search, 4), true).'%');
                $q->where('(c.code LIKE '.trim($search).')');
            }else if(stripos($search, 'promo:') === 0) {
                $search = $db->Quote('%'.$db->getEscaped(substr($search, 4), true).'%');
                $q->where('(p.name LIKE '.trim($search).')');
            }else{
                $search = $db->Quote('%'.$db->getEscaped( trim(substr($search, 3) ), true).'%');
                $q->where('(c.id LIKE '.$search.')');
            }
        }
       
        $q->order('p.name,c.sequence_number');
   
        return $q;
    }
    
    /**
     * Get Coupon Status List
     * 
     * @return type 
     */
    public function getCouponStatusList(){
	$status = array();
	$status[] = array('value' => 0, 'text' => '--Show All--');
	
	$db = $this->getDBO();
	
	$q = $db->getQuery(true);
        $q->select('id, name');
        $q->from('#__poe_coupon_status');
        
        $db->setQuery($q);

        if( ($result = $db->loadObjectList()) ){
            foreach($result as $r){
		$status[] = array('value' => $r->id, 'text' => $r->name);
	    }
        }
	
	return $status;
    }
}
