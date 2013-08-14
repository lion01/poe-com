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
jimport('joomla.application.component.modeladmin');
 
/**
 * Product Categroy XREF Model
 */
class PoecomModelProductCategoryXref extends JModelAdmin{
    /**
    * Method override to check if you can edit an existing record.
    *
    * @param	array	$data	An array of input data.
    * @param	string	$key	The name of the key for the primary key.
    *
    * @return	boolean
    * @since	1.6
    */
    protected function allowEdit($data = array(), $key = 'id'){
        // Check specific edit permission then general edit permission.
        return JFactory::getUser()->authorise('core.edit', 'com_poecom.name.'.((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
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
    public function getTable($type = 'ProductCategoryXref', $prefix = 'PoecomTable', $config = array()){
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
    public function getForm($data = array(), $loadData = true){
        // Get the form.
        $form = $this->loadForm('com_poecom.product_category_xref', 'product_category_xref', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)){
            return false;
        }
        return $form;
    }
    
    /**
    * Method to get the script that have to be included on the form
    *
    * @return string	Script files
    */
    public function getScript(){
        return JURI::root(true).'/administrator/components/com_poecom/models/forms/product.category.xref.js';
    }
    
	
    /**
    * Method to get the data that should be injected in the form.
    *
    * @return	mixed	The data for the form.
    * @since	1.6
    */
    protected function loadFormData(){
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_poecom.product_category_xref.edit.data', array());
        if (empty($data)){
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
    public function getItem($pk = null){
        // Initialise variables.
        $pk		= (!empty($pk)) ? $pk : (int) $this->getState($this->getName().'.id');
        $table	= $this->getTable();

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

        if (property_exists($item, 'params')) {
                $registry = new JRegistry;
                $registry->loadString($item->params);
                $item->params = $registry->toArray();
        }

        return $item;
    }
    
    /**
     * Method to get all categories for a product
     * 
     */
    public function getProductCategories($product_id = 0){
        $db = $this->getDbo();
        $query = $db->getQuery(true);
		$query->select('category_id');
		$query->from('#__poe_product_category_xref');
		$query->where("product_id=".$product_id);
		
        $db->setQuery((string)$query);
	
        if(!$categories = $db->loadResultArray()){
            $categtories = array();
        }
        
        return $categories;
    }
    /**
     * Delete xref's where product id found
     * 
     * @param int $product_id
     * @return boolean
     */
    public function deleteByProductId($product_id = 0){
        $db = $this->getDbo();
        $q = $db->getQuery(true);
        $q->select('id');
        $q->from('#__poe_product_category_xref');
        $q->where('product_id='.(int)$product_id);
        
        $db->setQuery($q);
       
        if(($xref_ids = $db->loadResultArray())){
            return $this->delete($xref_ids);
        }else{
            return false;
        }
    }
}
