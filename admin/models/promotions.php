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
 * Promotions List Model
 */
class PoecomModelPromotions extends JModelList{
    
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
        
        $type_id = $this->getUserStateFromRequest($this->context.'.filter.type', 'filter_type');
        $this->setState('filter.type', $type_id);

        $discount_type_id = $this->getUserStateFromRequest($this->context.'.filter.discount_type', 'filter_discount_type');
        $this->setState('filter.discount_type', $discount_type_id);

        // List state information.
        parent::populateState('p.id', 'asc');
    }
    
    /**
    * Method to get the script that have to be included on the form
    *
    * @return string	Script files
    */
    public function getScript(){
        return JURI::root(true).'/administrator/components/com_poecom/models/forms/promotions.js';
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
        $id	.= ':'.$this->getState('filter.discount_type');

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
                'p.id, p.promotion_type_id'
            )
        );
        $q->select('p.*,ptype.name promotion_type, dtype.name discount_type, damttype.name amount_type');
        $q->from('#__poe_promotion p');
        $q->innerJoin('#__poe_promotion_type ptype ON ptype.id=p.promotion_type_id');
        $q->innerJoin('#__poe_discount_type dtype ON dtype.id=p.discount_type_id');
        $q->innerJoin('#__poe_discount_amount_type damttype ON damttype.id=p.discount_amount_type_id');
        
        // Filter by promotion type
        $type_id = $this->getState('filter.type');
        if (is_numeric($type_id) && $type_id > 0) {
            $q->where('p.promtoion_type_id = '.(int) $type_id);
        }
        
        // Filter by promotion discount type
        $discount_type_id = $this->getState('filter.discount_type');
        if (is_numeric($discount_type_id) && $discount_type_id > 0) {
            $q->where('p.discount_type_id = '.(int) $discount_type_id);
        }
        
        // Filter by search in name.
        $search = trim($this->getState('filter.search'));

        if(!empty($search)) {
            if(stripos($search, 'id:') === 0) {
                $q->where('p.id = '.(int) trim(substr($search, 3)) );
            }else if(stripos($search, 'name:') === 0) {
                $search = $db->Quote('%'.$db->getEscaped(substr($search, 4), true).'%');
                $q->where('(p.name LIKE '.trim($search).')');
            }else{
                $search = $db->Quote('%'.$db->getEscaped( trim(substr($search, 3) ), true).'%');
                $q->where('(p.id LIKE '.$search.')');
            }
        }
       
        $q->order('p.name');
   
        return $q;
    }
    
    /**
     * Get list of Promotions
     * 
     * @return type 
     */
    public function getPromotionsList(){
	$promotions = array();
	$promotions[] = array('value' => 0, 'text' => '--Show All--');
	
	$db = $this->getDBO();
	
	$q = $db->getQuery(true);
        $q->select('id, name');
        $q->from('#__poe_promotion');
        
        $db->setQuery($q);

        if( ($result = $db->loadObjectList()) ){
            foreach($result as $r){
		$promotions[] = array('value' => $r->id, 'text' => $r->name);
	    }
        }
	
	return $promotions;
    }
    
    /**
     * Get list of Promotion Types
     * 
     * @return type 
     */
    public function getPromotionTypeList(){
	$types = array();
	$types[] = array('value' => 0, 'text' => '--Show All--');
	
	$db = $this->getDBO();
	
	$q = $db->getQuery(true);
        $q->select('id, name');
        $q->from('#__poe_promotion_type');
        
        $db->setQuery($q);

        if( ($result = $db->loadObjectList()) ){
            foreach($result as $r){
		$types[] = array('value' => $r->id, 'text' => $r->name);
	    }
        }
	
	return $types;
    }
    
    
    /**
     * Get list of Promotion Discount Types
     * 
     * @return type 
     */
    public function getDiscountTypeList(){
	$types = array();
	$types[] = array('value' => 0, 'text' => '--Show All--');
	
	$db = $this->getDBO();
	
	$q = $db->getQuery(true);
        $q->select('id, name');
        $q->from('#__poe_discount_type');
        
        $db->setQuery($q);

        if( ($result = $db->loadObjectList()) ){
            foreach($result as $r){
		$types[] = array('value' => $r->id, 'text' => $r->name);
	    }
        }
	
	return $types;
    }
    
    /**
     * Get the last sequence number for a numbered coupon series
     * 
     * @param int $promotion_id
     * @return int 
     */
    public function getLastSequence($promotion_id = 0){
	
	$last_sequence = 0;
	
	$db = $this->getDBO();
	
	$q = $db->getQuery(true);
        $q->select('MAX(sequence_number)');
        $q->from('#__poe_coupon');
	$q->where('promotion_id='.(int)$promotion_id);
      
        $db->setQuery($q);

        if( ($result = $db->loadResult()) ){
           $last_sequence = $result;
        }
	
	return $last_sequence;
    }
    
    public function getUserList(){
	$user_list = array();
	
	$params = JComponentHelper::getParams('com_poecom');
	
	$user_group = $params->get('poegroup', 0);
	
	$db = $this->getDBO();
	
	$q = $db->getQuery(true);
        $q->select('id,name');
        $q->from('#__users u');
	$q->innerJoin('#__user_usergroup_map gp ON gp.user_id=u.id');
	$q->where(array('u.block=0', 'gp.group_id='.(int)$user_group) );
	$q->order('name');
       
        $db->setQuery($q);

        if( ($result = $db->loadObjectList()) ){
	    //add option for all
	    $user_list[] = array('id' => 'all', 'name' => '--All Customers--');
	    foreach($result as $user){
		$user_list[] = array('id' => $user->id, 'name' => $user->name);
	    }
        }
	
	return $user_list;
    }
}
