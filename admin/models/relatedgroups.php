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
 * RelatedGroups List Model
 */
class PoecomModelRelatedGroups extends JModelList{
    
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
        $layout = $jinput->get('layout','default', 'CMD');
        $this->context .= '.'.$layout;
        
        //assign filtering elements
        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $enabled = $this->getUserStateFromRequest($this->context.'.filter.enabled', 'filter_enabled', '');
        $this->setState('filter.enabled', $enabled);
        
        // List state information.
        parent::populateState('grp.name', 'asc');
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
        $id	.= ':'.$this->getState('filter.enabled');

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
        $q->select('grp.*, c.title category_name');
        $q->from('#__poe_related_product_group grp');
        $q->innerJoin('#__categories c ON c.id=grp.category_id');

        // Filter by enabled state
        $enabled = $this->getState('filter.enabled');
        if (is_numeric($enabled)) {
                $q->where('grp.enabled = ' . (int) $enabled);
        }
        else if ($enabled === '') {
                $q->where('(grp.enabled = 0 OR grp.enabled = 1)');
        }

        // Filter by search in name.
        $search = trim($this->getState('filter.search'));

        if (!empty($search)) {
                if (stripos($search, 'id:') === 0) {
                        $q->where('grp.id = '.(int) substr($search, 3));
                }
                else if (stripos($search, 'name:') === 0) {
                        $search = $db->Quote('%'.$db->getEscaped(substr($search, 7), true).'%');
                        $q->where('(grp.name LIKE '.$search.')');
                }else{
                        $search = $db->Quote('%'.$search.'%');
                        $q->where('(p.name LIKE '.$search.')');
                }
        }

        $q->order('grp.name');

        return $q;
    }
}
