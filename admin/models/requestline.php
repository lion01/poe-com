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
 * Request Model
 */
class PoecomModelRequestLine extends JModelAdmin{
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
    public function getTable($type = 'RequestLine', $prefix = 'PoecomTable', $config = array()){
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
        $form = $this->loadForm('com_poecom.order', 'requestline', array('control' => 'jform', 'load_data' => $loadData));
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
        return JURI::root(true).'/administrator/components/com_poecom/models/forms/requestline.js';
    }
    
	
    /**
    * Method to get the data that should be injected in the form.
    *
    * @return	mixed	The data for the form.
    * @since	1.6
    */
    protected function loadFormData(){
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_poecom.requestline.edit.data', array());
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
        $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName().'.id');
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

        return $item;
    }
    
    /**
     * Over ride method that calls parent::save 
     * 
     * Included for possible extension
     * 
     * @param array $data Form values to store
     * 
     * @return boolean True = order data stored
     */
    public function save($data){
        
        // Save the order item 
        if(!parent::save($data)){
            return false;
        }else{
            return true;
        }
    }
    
    /**
     * Delete Request Line(s)
     * 
     * @param array $cids List of order line ids
     */
    public function delete(&$cids = array()){
        
        if($cids){
            $table = $this->getTable();
            
            foreach($cids as $cid){
                if (!$table->delete($cid)){
                    $this->setError($table->getError());
                    return false;
                }
            } 
        }else{
            JError::raiseWarning(500, JText::_('COM_POECOM_NO_ORDER_LINE_ERROR'));
            return false;
        }
        return true;
    }
    /**
     * Check if product id used in request lines
     * 
     * Delete validation
     * 
     * @param int $product_id
     * @return boolean True if found
     */
    public function productUsed($product_id = 0){
        $db = $this->getDbo();
        $q = $db->getQuery(true);
        $q->select('id');
        $q->from('#__poe_request_line');
        $q->where('id='.(int)$product_id);
        
        $db->setQuery($q, '', 1);
        
        if(($result = $db->loadResult())){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * Check if product id exists in request lines
     * @param int $product_id
     * @return boolean True means not found
     */
    public function validateProductDelete($product_id = 0){
        if(empty($product_id)){
            $this->setError(JText::_('COM_POECOM_NO_PRODUCT'));
            return false;
        }
        
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->select('id');
        $q->from('#__poe_request_line');
        $q->where('product_id=' . (int) $product_id);
        $db->setQuery($q, 0, 1);

        if(($id = $db->loadResult())){
            $this->setError('Request : ' . $id);
            return false;
        }else{
            return true;
        }
    }
}
