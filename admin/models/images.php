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
 * ImagesList Model
 */
class PoecomModelImages extends JModelList{
    
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

        $published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);
        
        //get product id from request for modal
        $productId = $jinput->get('product_id', 0, 'CMD');
        if($productId > 0){
            $this->setState('filter.product_id', $productId);
        }else{
            $productId = $this->getUserStateFromRequest($this->context.'.filter.product_id', 'filter_product_id');
            $this->setState('filter.product_id', $productId);
        }
        
        //get image type id from request for modal
        $typeId = $jinput->get('type_id', 0, 'CMD');
        if($typeId > 0){
            $this->setState('filter.type_id', $typeId);
        }else{
            $typeId = $this->getUserStateFromRequest($this->context.'.filter.type_id', 'filter_type_id');
            $this->setState('filter.type_id', $typeId);
        }
        // List state information.
        parent::populateState('img.name', 'asc');
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
        $id	.= ':'.$this->getState('filter.product_id');
        $id	.= ':'.$this->getState('filter.type_id');

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
        $q->select('img.*, p.name product_name, pit.name type_name');
        $q->from('#__poe_product_image img');
        $q->innerJoin('#__poe_product p ON p.id=img.product_id');
        $q->innerJoin('#__poe_product_image_type pit ON pit.id=img.type');
        

        // Filter by published state
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
                $q->where('img.published = ' . (int) $published);
        }
        else if ($published === '') {
                $q->where('(img.published = 0 OR img.published = 1)');
        }
        
        // Filter by image type
        $typeId = $this->getState('filter.type_id');
        if (is_numeric($typeId)) {
                $q->where('img.type = ' . (int) $typeId);
        }else if ($typeId === '') {
                $q->where('(img.type = 0 OR img.type = 1)');
        }

        // Filter by a single or group of products.
        $productId = $this->getState('filter.product_id');
        if (is_numeric($productId)) {
            $q->where('p.id = '.(int) $productId);
        }else if (is_array($productId)) {
            JArrayHelper::toInteger($productId);
            $productId = implode(',', $productId);
            $q->where('p.id IN ('.$productId.')');
        }

        // Filter by search in name.
        $search = trim($this->getState('filter.search'));

        if (!empty($search)) {
                if (stripos($search, 'id:') === 0) {
                        $q->where('img.id = '.(int) substr($search, 3));
                }
                else if (stripos($search, 'name:') === 0) {
                        $search = $db->Quote('%'.$db->getEscaped(substr($search, 7), true).'%');
                        $q->where('(img.name LIKE '.$search.')');
                }else if (stripos($search, 'sku:') === 0) {
                        $search = $db->Quote('%'.$db->getEscaped(substr($search, 7), true).'%');
                        $q->where('(img.option_type LIKE '.$search.')');
                }else{
                        $search = $db->Quote('%'.$db->getEscaped(substr($search, 7), true).'%');
                        $q->where('(p.name LIKE '.$search.')');
                }
        }

        $q->order('p.name, img.sort_order');

        return $q;
    }
}
