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
 * ShipmentMethodsList Model
 */
class PoecomModelShipMethods extends JModelList{
    
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

        $enabled = $this->getUserStateFromRequest($this->context.'.filter.enabled', 'filter_enabled', '');
        $this->setState('filter.enabled', $enabled);
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
        // Select the required fields from the table.
        $q->select( $this->getState('list.select','sm.*,ex.enabled,ex.extension_id'));
        $q->from('#__poe_shipping_method AS sm');
        $q->innerJoin('#__extensions ex ON ex.element=sm.plugin');

        // Filter by sm_enabled state
        $enabled = $this->getState('filter.enabled');
        if (is_numeric($enabled)) {
            $q->where('ex.enabled = ' . (int) $enabled);
        }else if ($enabled === '') {
            $q->where('(ex.enabled = 0 OR ex.enabled = 1)');
        }

        // Filter by search in name.
        $search = trim($this->getState('filter.search'));

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $q->where('sm.id = '.(int) substr($search, 3));
            }else{
                $search = $db->Quote('%'.$db->getEscaped($search, true).'%');
                $q->where('(sm.name LIKE '.$search.')');
            }
        }

        $q->order('name');

        return $q;
    }
}