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
 * Options Values List Model
 */
class PoecomModelOptionValues extends JModelList {

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @return	void
     * @since	1.6
     */
    protected function populateState($ordering = null, $direction = null) {
        // Initialise variables.
        $jinput = JFactory::getApplication()->input;

        // Adjust the context to support modal layouts.
        $layout = $jinput->get('layout');
        if (!empty($layout)) {
            $this->context .= '.' . $layout;
        }

        // List state information.
        parent::populateState();

        //Filters
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);

        $categoryId = $this->getUserStateFromRequest($this->context . '.filter.product_id', 'filter_product_id');
        $this->setState('filter.product_id', $categoryId);
        
        $order = $this->getUserStateFromRequest($this->context.'.filter_order', 'filter_order', 'ordering');
        $this->setState('list.ordering', $order);
        
        $dir = $this->getUserStateFromRequest($this->context.'.filter_order_Dir', 'filter_order_Dir', 'ASC');
        $this->setState('list.direction', $dir);
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
        $id .= ':' . $this->getState('filter.product_id');

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
        $q->select('opv.*, op.name as option_name, p.name as product_name');
        $q->from('#__poe_option_value as opv');
        $q->innerJoin('#__poe_option as op ON op.id=opv.option_id');
        $q->innerJoin('#__poe_product as p ON p.id=op.product_id');

        // Filter by published state
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $q->where('opv.published = ' . (int) $published);
        } else if ($published === '') {
            $q->where('(opv.published = 0 OR opv.published = 1)');
        }

        // Filter by a single or group of products.
        $productId = $this->getState('filter.product_id');
        if (is_numeric($productId)) {
            $q->where('p.id = ' . (int) $productId);
        } else if (is_array($productId)) {
            JArrayHelper::toInteger($productId);
            $productId = implode(',', $productId);
            $q->where('p.id IN (' . $productId . ')');
        }

        // Filter by search in name.
        $search = trim($this->getState('filter.search'));

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $q->where('opv.id = ' . (int) trim(substr($search, 3)));
            } else if (stripos($search, 'name:') === 0) {
                $search = $db->Quote('%' . $db->getEscaped(substr($search, 7), true) . '%');
                $q->where('(op.name LIKE ' . $search . ')');
            } else if (stripos($search, 'sku:') === 0) {
                $search = $db->Quote('%' . $db->getEscaped(substr($search, 7), true) . '%');
                $q->where('(opv.option_value_sku LIKE ' . $search . ')');
            } else {
                $search = $db->Quote('%' . $db->getEscaped(substr($search, 7), true) . '%');
                $q->where('(p.name LIKE ' . $search . ')');
            }
        }
        $dir = $this->getState('list.direction');
        $q->order('p.name '.$dir.',op.ordering '.$dir.', opv.ordering '.$dir);

        return $q;
    }
}
