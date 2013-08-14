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
 * FlyerBlocksList Model
 */
class PoecomModelFlyerBlocks extends JModelList{
    
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
        
        // Set filter states
        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
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
        $id	.= ':'.$this->getState('filter.published');

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
        
        $q->select('fb.*, p.name product_name');
        $q->from('#__poe_flyer_block as fb');
        $q->leftJoin('#__poe_product p ON p.id=fb.product_id');

        // Filter by published state
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
                $q->where('fb.published = ' . (int) $published);
        }else if ($published === '') {
                $q->where('(fb.published = 0 OR fb.published = 1)');
        }


        // Filter by search in name.
        $search = trim($this->getState('filter.search'));

        if (!empty($search)) {
                if (stripos($search, 'id:') === 0) {
                        $q->where('fb.id = '.(int) substr($search, 3));
                }
                /*
                else if (stripos($search, 'section:') === 0) {
                        $search = $db->Quote('%'.$db->getEscaped(substr($search, 6), true).'%');
                        $q->where('(fb.title LIKE '.$search.')');
                }else{
                        $search = $db->Quote('%'.$db->getEscaped($search, true).'%');
                        $q->where('(f.title LIKE '.$search.')');
                } */
        }

        $q->order('fb.id');

        return $q;
    }
    
    /**
     * Method to get an array of data items.
     *
     * @return  mixed  An array of data items on success, false on failure.
     *
     * @since   11.1
     */
    public function getItems(){
            // Get a storage key.
            $store = $this->getStoreId();

            // Try to load the data from internal storage.
            if (isset($this->cache[$store]))
            {
                    return $this->cache[$store];
            }

            // Load the list items.
            $query = $this->_getListQuery();
            $items = $this->_getList($query, $this->getStart(), $this->getState('list.limit'));

            // Check for a database error.
            if ($this->_db->getErrorNum())
            {
                    $this->setError($this->_db->getErrorMsg());
                    return false;
            }

            // Add the items to the internal cache.
            $this->cache[$store] = $items;

            return $this->cache[$store];
    }
}
