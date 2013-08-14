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
 * Product Image Model
 */
class PoecomModelImage extends JModelAdmin {
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
    public function getTable($type = 'Image', $prefix = 'PoecomTable', $config = array()) {
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
        $form = $this->loadForm('com_poecom.image', 'image', array('control' => 'jform', 'load_data' => $loadData));
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
        return JURI::root(true) . '/administrator/components/com_poecom/models/forms/image.js';
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
    protected function loadFormData() {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_poecom.image.edit.data', array());
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

        return $item;
    }

    /**
     * Override ethod to save the form data.
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success, False on error.
     *
     * @since   11.1
     */
    public function save($data) {
        //Sanitize
         $data = $this->filterInput($data);
         //Validate
         if(!$this->validateData($data)){
             return false;
         }
        
        // Initialise variables;
        $table = $this->getTable();
        $key = $table->getKeyName();
        $pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
        $isNew = true;
        
        // Allow an exception to be thrown.
        try {
            // Load the row if saving an existing record.
            if ($pk > 0) {
                $table->load($pk);
                $isNew = false;
            }

            // Bind the data.
            if (!$table->bind($data)) {
                $this->setError($table->getError());
                return false;
            }

            // Prepare the row for saving
            $this->prepareTable($table);

            // Check the data.
            if (!$table->check()) {
                $this->setError($table->getError());
                return false;
            }

            // Store the data.
            if (!$table->store()) {
                $this->setError($table->getError());
                return false;
            }

            // Clean the cache.
            $this->cleanCache();
        } catch (Exception $e) {
            $this->setError($e->getMessage());

            return false;
        }

        $pkName = $table->getKeyName();

        if (isset($table->$pkName)) {
            $this->setState($this->getName() . '.id', $table->$pkName);
        }
        $this->setState($this->getName() . '.new', $isNew);

        return true;
    }
    
    /**
     * Clean the data before insert/update
     * @param array $data User input
     * @return array $data Clean data
     */
    public function filterInput($data = array()){
        if($data){
            $filter = JFilterInput::getInstance();
            foreach($data as $k => $v){
                $data[$k] = $filter->clean($v);
            }
        }

        return $data;
    }
    
    /**
     * Validate data before insert/update
     * @param array $data User input
     * @return boolean
     */
    private function validateData($data = array()){
        if(!$data){
            return false;
        }
        //check for not allowed characters
        foreach($data as $k => $v){
            $pos = strpos($v, '"');
            if($pos){
                $this->setError(JText::_('COM_SF_INVALID_CHAR'). '" ) '.$v);
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Delete images for a product
     * 
     * @param int $product_id
     * 
     * @return booelan True on success
     */
    public function deleteByProductId($product_id = 0) {
        if ($product_id > 0) {
            $db = JFactory::getDbo();
            $q = $db->getQuery(true);
            $q->select('id');
            $q->from('#__poe_product_image');
            $q->where('product_id=' . (int) $product_id);
            $db->setQuery($q);

            if (($cids = $db->loadResultArray())) {
                if ((!$this->delete($cids))) {
                    return false;
                }
            } else {
                //no options found
                return true;
            }
        } else {
            $this->setError('Product Id: ' . $product_id . ' not valid');
            return false;
        }
        return true;
    }
}
