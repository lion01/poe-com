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
jimport('joomla.application.component.modeladmin');

/**
 * Product Model
 */
class PoecomModelProduct extends JModelAdmin {

    /**
     * Method override to check if you can edit an existing record.
     *
     * @param	array	$data	An array of input data.
     * @param	string	$key	The name of the key for the primary key.
     *
     * @return	boolean
     * @since	1.6
     */
    protected function allowEdit($data = array(), $key = 'id') {
        // Check specific edit permission then general edit permission.
        return JFactory::getUser()->authorise('core.edit', 'com_poecom.name.' . ((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
    }

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param	type	The table type to instantiate
     * @param	string	A prefix for the table class name. Optional.
     * @param	array	Configuration array for model. Optional.
     * @return	JTable	A database object
     * @since	1.6
     */
    public function getTable($type = 'Product', $prefix = 'PoecomTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param	array	$data		Data for the form.
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
     * @return	mixed	A JForm object on success, false on failure
     * @since	1.6
     */
    public function getForm($data = array(), $loadData = true) {
        // Get the form.
        $form = $this->loadForm('com_poecom.product', 'product', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;
    }

    /**
     * Method to get the script that have to be included on the form
     *
     * @return string	Script files
     */
    public function getScript() {
        return JURI::root(true) . '/administrator/components/com_poecom/models/forms/product.js';
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
    protected function loadFormData() {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_poecom.product.edit.data', array());
        if (empty($data)) {
            $data = $this->getItem();
        }
        return $data;
    }

    /**
     * Method to get a single record.
     *
     * @param   integer  $pk  The id of the primary key.
     *
     * @return  mixed    Object on success, false on failure.
     * @since   11.1
     */
    public function getItem($pk = null) {
        // Initialise variables.
        $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
        $table = $this->getTable();

        if ($pk > 0) {
            // Attempt to load the row.
            $return = $table->load($pk);

            // Check for a table object error.
            if ($return === false && $table->getError()) {
                $this->setError($table->getError());
                return false;
            }
        }

        // Convert to the JObject before adding other data.
        $properties = $table->getProperties(1);
        $item = JArrayHelper::toObject($properties, 'JObject');

        // Convert the metadata field to an array.
        $registry = new JRegistry;
        $registry->loadString($item->metadata);
        $item->metadata = $registry->toArray();

        // Convert tax_exempt_ids to an array
        $item->tax_exempt_ids = json_decode($item->tax_exempt_ids);

        return $item;
    }

    /**
     * Check if alias exists
     * @param string $alias
     * @return boolean True = found
     */
    private function aliasExists($alias = '', $product_id = 0) {

        $db = $this->getDbo();
        $q = $db->getQuery(true);
        $q->select("COUNT(" . $db->nameQuote('id') . ")");
        $q->from('#__poe_product');
        $q->where('alias=' . $db->Quote($alias));
        if ($product_id > 0) {
            $q->where('id!=' . (int) $product_id);
        }

        $db->setQuery($q);

        $result = $db->loadResult();

        if ($result >= 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Over ride method that calls parent::save and on success
     * updates the product categories cross reference table
     * 
     * @param array $data Form values to store
     * 
     * @return boolean True = product data stored
     * Note: Category Xref fails silently
     */
    public function save($data) {

        //prepare alias
        if (strlen($data['alias']) == 0) {
            $this->setError(JText::_('COM_POECOM_ALIAS_REQ_ERROR'));
            return false;
        } else {
            $alias = str_replace(" ", '-', strtolower($data['alias']));

            //must be unique
            if ($this->aliasExists($alias, $data['id'])) {
                $this->setError(JText::_('COM_POECOM_ALIAS_UNIQUE_ERROR'));
                return false;
            } else {
                $data['alias'] = $alias;
            }
        }

        $data['metadata'] = json_encode($data['metadata']);
        $data['tax_exempt_ids'] = json_encode($data['tax_exempt_ids']);
        $data['tabs'] = json_encode($data['tabs']);
        
        if(!empty($data['price'])){
            //remove characters
            $price = str_replace(array(",","$"), "", $data['price']);
            $data['price'] = $price;
        }
        // Save the product item and then update categories
        if (parent::save($data)) {

            // get the last insertid for new records
            if ($data['id'] == 0 || !strlen($data['id'])) {
                $db = JFactory::getDBO();
                $id = $db->insertid();
            } else {
                $id = $data['id'];
            }

            // Get the existing product categories
            $db = $this->getDbo();
            $query = $db->getQuery(true);
            $query->select('category_id');
            $query->from('#__poe_product_category_xref');
            $query->where("product_id=" . $id);

            $db->setQuery((string) $query);

            if (!$categories = $db->loadResultArray()) {
                $categories = array();
            }

            // categories to add
            $add_array = array_diff($data['catid'], $categories);

            // categories to delete
            $delete_array = array_diff($categories, $data['catid']);

            // Add the categories
            if ($add_array) {
                $dataobj = new JObject;
                $dataobj->id = NULL;
                $dataobj->product_id = $id;

                foreach ($add_array as $catid) {
                    $dataobj->category_id = $catid;

                    $db->insertObject('#__poe_product_category_xref', $dataobj);
                }
            }

            // Remove categories
            if ($delete_array) {
                foreach ($delete_array as $catid) {
                    $q = $db->getQuery(true);
                    $q->delete('#__poe_product_category_xref');
                    $q->where(array("product_id=" . $id, "category_id=" . $catid));
                    $db->setQuery($q);
                    $db->query();
                }
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * Method to test whether a record can be deleted.
     *
     * @param   object  $record  A record object.
     * @return  boolean  True if allowed to delete the record.
     *
     * @since   11.1
     */
    protected function canDelete($record) {
        $user = JFactory::getUser();

        //check user authroization
        if (!$user->authorise('core.delete', $this->option)) {
            $this->setError('not authorized');
            return false;
        }

        //check if product used in order lines
        $orderModel = JModel::getInstance('OrderLine', 'PoecomModel');

        if ($orderModel->productUsed($record->id)) {
            return false;
        }

        //check if product used in RFQ
        $rfqModel = JModel::getInstance('RequestLine', 'PoecomModel');

        if ($rfqModel->productUsed($record->id)) {
            return false;
        }
        return true;
    }
    
    /**
     * Delete Product
     * 
     * Deletes dependencies before deleting product record
     * 
     * @param array $cids List of Product ids to delete
     */
    public function delete(&$cids){
        if($cids){
            //get table
            $table = $this->getTable();
            
            foreach($cids as $cid){
                //Orders - if present delete not allowed
                $orderLineModel = JModel::getInstance('OrderLine', 'PoecomModel');
                
                if(!$orderLineModel->validateProductDelete($cid)){
                    $this->setError(JText::_('COM_POECOM_PRODUCT_ORDER_LINE_EXISTS').' : ' .$orderLineModel->getError());
                    return false;
                }
                
                //RFQ - if present delete not allowed
                $requestLineModel = JModel::getInstance('RequestLine', 'PoecomModel');
                
                if(!$requestLineModel->validateProductDelete($cid)){
                    $this->setError(JText::_('COM_POECOM_PRODUCT_REQUEST_LINE_EXISTS').' : ' .$requestLineModel->getError());
                    return false;
                }
                
                //Related Product - if present delete not allowed
                $relatedModel = JModel::getInstance('RelatedProduct', 'PoecomModel');
                
                if(!$relatedModel->validateProductDelete($cid)){
                    $this->setError(JText::_('COM_POECOM_PRODUCT_RELATED_EXISTS').' : ' .$relatedModel->getError());
                    return false;
                }
                
                //Flyer Block - if present delete not allowed
                $flyerModel = JModel::getInstance('FlyerBlock', 'PoecomModel');
                
                if(!$flyerModel->validateProductDelete($cid)){
                    $this->setError(JText::_('COM_POECOM_PRODUCT_FLYER_BLOCK_EXISTS').' : ' .$flyerModel->getError());
                    return false;
                }
                
                //Options
                $optionModel = JModel::getInstance('Option', 'PoecomModel');
               
                if(!$optionModel->deleteProductOptions($cid)){
                    $this->setError(JText::_('COM_POECOM_PRODUCT_OPTION_DELETE_ERROR').' : ' .$optionModel->getError());
                    return false;
                }
              
                //Tabs
                $tabModel = JModel::getInstance('ProductTab', 'PoecomModel');
                
                if(!$tabModel->deleteByProductId($cid)){
                    $this->setError(JText::_('COM_POECOM_PRODUCT_TAB_DELETE_ERROR'). ' : '. $tabModel->getError());
                    return false;
                }
               
                //Images
                $imageModel = JModel::getInstance('Image', 'PoecomModel');
                
                if(!$imageModel->deleteByProductId($cid)){
                    $this->setError(JText::_('COM_POECOM_PRODUCT_IMAGE_DELETE_ERROR'). ' : '. $imageModel->getError());
                    return false;
                }
              
                
                //remove category xref
                $xrefModel = JModel::getInstance('ProductCategoryXref', 'PoecomModel');

                if(!$xrefModel->deleteByProductId($cid)){
                    $this->setError(JText::_('COM_POECOM_XREF_NOT_DELETED'));
                    return false;
                }
                
                //Product
                if (!$table->delete($cid)){
                    $this->setError($table->getError());
                    return false;
                }
            }
            
            // Clear the component's cache
            $this->cleanCache();
            
            return true;
        }else{
            $this->setError(JText::_('COM_POECOM_NO_ITEM_SELECTED'));
            return false;
        }
    }
    
    /**
     * Get Product detail including options
     * @param type $id
     * @return type 
     */
    public function getProductDetail($id = 0){
        $db = JFactory::getDbo();
        $q =  $db->getQuery(true);
        $q->select('p.name, p.sku, p.type, p.price, p.list_description');
        $q->from('#__poe_product p');
        $q->where('p.id='.(int)$id);
        $db->setQuery($q);
        
        if(($product = $db->loadObject())){
         
            //get product properties
            $q = $db->getQuery(true);
            $q->select('op.*, ov.id option_value_id, ov.option_label');
            $q->from('#__poe_option op');
            $q->innerJoin('#__poe_option_value ov ON ov.option_id=op.id');
            $q->where('op.product_id='.(int)$id);
            $q->where('op.published=1');
            $q->where('op.option_type_id=5');
            $q->order('op.ordering');
       
            $db->setQuery($q);
            
            if(($properties = $db->loadObjectList())){
                $product->properties = $properties;
            }else{
                $product->properties = '';
            }
            
            //get options
            $q = $db->getQuery(true);
            $q->select('op.*');
            $q->from('#__poe_option op');
            $q->where('op.product_id='.(int)$id);
            $q->where('op.published=1');
            $q->where('op.option_type_id!=5');
            $q->order('op.ordering');
            
            $db->setQuery($q);
            
            if(($options = $db->loadObjectList())){
                $idx = 0;
                foreach($options as $op){
                    //get vlaues
                    $q = $db->getQuery(true);
                    $q->select('ov.*');
                    $q->from('#__poe_option_value ov');
                    $q->where('ov.option_id='.(int)$op->id);
                    $q->where('ov.published=1');
                    $q->order('ov.ordering');
                    
                    $db->setQuery($q);
                    
                    $options[$idx]->values = $db->loadObjectList();
                    $idx++;
                }
                $product->options = $options;
                
            }else{
                $product->options = '';
            }
            //no selection made by user
            $product->selected_options = '';
        }
        
        return $product;
    }
}
