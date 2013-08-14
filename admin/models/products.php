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
 * */
jimport('joomla.application.component.modellist');

/**
 * ProductsList Model
 */
class PoecomModelProducts extends JModelList {

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @return	void
     * @since	1.6
     */
    protected function populateState($ordering = null, $direction = null) {
        $jinput = JFactory::getApplication()->input;

        // Adjust the context to support modal layouts.
        $layout = $jinput->get('layout', '', 'CMD');
        if (!empty($layout)) {
            $this->context .= '.' . $layout;
        }

        // Set state in parent for pagination
        parent::populateState();

        // Set filter states
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);

        $categoryId = $this->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id');
        if ($categoryId == 0) {
            //set the default category to filter duplicate rows
            //where product is in multiple categories
            $params = JComponentHelper::getParams('com_poecom');

            $categoryId = $params->get('productrootcatid', 0);
        }
        $this->setState('filter.category_id', $categoryId);
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
    protected function getStoreId($id = '') {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.published');
        $id .= ':' . $this->getState('filter.category_id');

        return parent::getStoreId($id);
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return	string	An SQL query
     */
    protected function getListQuery() {
        // Create a new query object.		
        $db = JFactory::getDBO();
        $q = $db->getQuery(true);

        $q->select('p.*, c.path category_name');
        $q->from('#__poe_product p');
        $q->innerJoin('#__poe_product_category_xref xref ON xref.product_id=p.id');
        $q->innerJoin('#__categories c ON c.id=xref.category_id');
        $q->order('xref.ordering');

        // Filter by enabled state
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $q->where('p.published = ' . (int) $published);
        }

        // Filter by a single or group of categories.
        $categoryId = $this->getState('filter.category_id');
        $params = JComponentHelper::getParams('com_poecom');
        $root_cat = $params->get('productrootcatid', 0);
        if (is_numeric($categoryId) && $categoryId > 0 && $categoryId != $root_cat) {
            //get category branch
            $cat_branch = $this->getChildCategories($categoryId);
          
            if($cat_branch){
                $q->where("c.id IN ( " . implode(",", $cat_branch).')');
            }else{
                //this is a parent - first child of root
                $q->where('c.id = ' . (int) $categoryId);
            }
        }

        // Filter by search in name.
        $search = trim($this->getState('filter.search'));

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $q->where('p.id = ' . (int) trim(substr($search, 3)));
            } else if (stripos($search, 'name:') === 0) {
                $search = $db->Quote('%' . $db->getEscaped(trim(substr($search, 5)), true) . '%');
                $q->where('(p.name LIKE ' . $search . ')');
            } else if (stripos($search, 'sku:') === 0) {
                $search = $db->Quote('%' . $db->getEscaped(trim(substr($search, 4)), true) . '%');
                $q->where('(p.sku LIKE ' . $search . ')');
            } else {
                $search = $db->Quote('%' . $db->getEscaped($search, true) . '%');
                $q->where('(p.name LIKE ' . $search . ' OR p.sku LIKE ' . $search . ')');
            }
        }

        $q->order('c.path,p.name');

        return $q;
    }
    /**
     * Get child categories for a category id
     * 
     * @param type $cat_id
     * @return type
     */
    public function getChildCategories($cat_id = 0){
       
        $db = $this->getDbo();
        $q = $db->getQuery(true);
        $q->select('id');
        $q->from('#__categories');
        $q->where('parent_id='.(int)$cat_id);

        $db->setQuery($q);
        
        $child_ids = $db->loadResultArray();
      
        return $child_ids;
    }
}
