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
 * ProductTabs List Model
 */
class PoecomModelProductTabs extends JModelList{
    
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
        if (!empty($layout)) {
            $this->context .= '.'.$layout;
        }
        
        // Set state in parent for pagination
        parent::populateState();

        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        
        $published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published');
        $this->setState('filter.published', $published);
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
    
        $q->select('pt.*, p.name product_name');
        $q->from('#__poe_product_tab pt');
        $q->innerJoin('#__poe_product p ON p.id=pt.product_id');
        
        // Filter by published state
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $q->where('pt.published = '.(int) $published);
        }
        
        // Filter by search in name.
        $search = trim($this->getState('filter.search'));

        if(!empty($search)) {
            if(stripos($search, 'id:') === 0) {
                $q->where('pt.id = '.(int) trim(substr($search, 3)) );
            }else if(stripos($search, 'pt.label:') === 0) {
                $search = $db->Quote('%'.$db->getEscaped(substr($search, 5), true).'%');
                $q->where('(pt.label LIKE '.trim($search).')');
            }else{
                $search = $db->Quote('%'.$db->getEscaped( trim(substr($search, 5) ), true).'%');
                $q->where('(pt.label LIKE '.$search.')');
            }
        }
       
        $q->order('pt.product_id,pt.ordering');

        return $q;
    }
    
    /**
     * Get list of ProductTabs for a Product
     * 
     * @param int $product_id
     * 
     * @return type 
     */
 /*   public function getProductTabsList($product_id = 0){
	$tabs = array();
	$tabs[] = array('value' => 0, 'text' => '--Show All--');
	
	$db = $this->getDBO();
	
	$q = $db->getQuery(true);
        $q->select('id, label');
        $q->from('#__poe_product_tab');
        $q->order('ordering');
        
        $db->setQuery($q);

        if( ($result = $db->loadObjectList()) ){
            foreach($result as $r){
		$tabs[] = array('value' => $r->id, 'text' => $r->label);
	    }
        }
	
	return $tabs;
    }*/
    
    /**
     * Publish / Un-Publish
     * @param int $cids Record Ids
     * @param int $value 0 or 1
     * @return boolean
     */
    public function publish($cids=0, $value=0 ){
        if($cids){
            $model = JModel::getInstance('ProductTab', 'PoecomModel');
            
            foreach($cids as $id){
              
                $record = $model->getItem($id);
              
                if($record){
                    $record->published = $value;
                    
                    $data = JArrayHelper::fromObject($record);
                    //remove params
                    unset($data['params']);

                    if(!$model->save($data)){
                        return false;
                    }else{
                        return true;
                    }
                }
            }
        }else{
            return false;
        } 
    }
}
