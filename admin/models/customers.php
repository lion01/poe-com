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
 * Customers List Model
 */
class PoecomModelCustomers extends JModelList{
    
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
        $layout = $jinput->get('layout', '', 'CMD');
        if ( !empty($layout) ) {
                $this->context .= '.'.$layout;
        }
        
        // List state information.
        parent::populateState();
        
        //set filters
        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        
        $published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published');
        $this->setState('filter.published', $published);
    }
    
    /**
    * Method to get the script that have to be included on the form
    *
    * @return string	Script files
    */
    public function getScript(){
        return JURI::root(true).'/administrator/components/com_poecom/models/forms/customers.js';
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
        $id	.= ':'.$this->getState('filter.published');

        return parent::getStoreId($id);
    }
    
    /**
    * Method to build an SQL query to load the list data.
    *
    * @return	string	An SQL query
    */
    protected function getListQuery(){
        
        $params = JComponentHelper::getParams('com_poecom');
        $user_group = $params->get('poegroup', '');
        
        // Create a new query object.		
        $db = JFactory::getDBO();
        $q = $db->getQuery(true);
    
        $q->select('ju.id, ju.name, ju.username, ju.email');
        $q->from('#__users ju');
        $q->innerJoin('#__user_usergroup_map ugm ON ugm.user_id=ju.id');
        $q->where('ugm.group_id='.(int)$user_group);
        
        // Filter by package type
   /*     $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $q->where('t.published = '.(int) $published);
        } */
        
        // Filter by search in name.
        $search = trim($this->getState('filter.search'));

        if(!empty($search)) {
            if(stripos($search, 'id:') === 0) {
                $q->where('ju.id = '.(int) trim(substr($search, 3)) );
            }else if(stripos($search, 'ju.name:') === 0) {
                $search = $db->Quote('%'.$db->getEscaped(substr($search, 4), true).'%');
                $q->where('(ju.name LIKE '.trim($search).')');
            }else{
                $search = $db->Quote('%'.$db->getEscaped( trim(substr($search, 3) ), true).'%');
                $q->where('(ju.id LIKE '.$search.')');
            }
        }
       
        $q->order('ju.name');

        return $q;
    }
    
    /**
     * Get list of Customers
     * 
     * @return type 
     */
    public function getCustomersList(){
	$taxes = array();
	$taxes[] = array('value' => 0, 'text' => '--Show All--');
	
	$db = $this->getDBO();
	
	$q = $db->getQuery(true);
        $q->select('id, name');
        $q->from('#__poe_tax_rate');
        
        $db->setQuery($q);

        if( ($result = $db->loadObjectList()) ){
            foreach($result as $r){
		$taxes[] = array('value' => $r->id, 'text' => $r->name);
	    }
        }
	
	return $taxes;
    }
}
