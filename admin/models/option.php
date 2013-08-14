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
 * Product Option Model
 */
class PoecomModelOption extends JModelAdmin {

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
    public function getTable($type = 'Option', $prefix = 'PoecomTable', $config = array()) {
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
        $form = $this->loadForm('com_poecom.option', 'option', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
    protected function loadFormData() {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_poecom.option.edit.data', array());
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
    /*
     * Get options for a product
     * 
     * @param int $product_id
     * 
     * @return array Array of options
     */
    public function getOptionsByProductId($product_id = 0){
        $db = $this->getDbo();
        $q = $db->getQuery(true);
        $q->select('*');
        $q->from('#__poe_option');
        $q->where('product_id=' . (int) $product_id);
        $q->order('ordering');
        $db->setQuery($q);

        $options = $db->loadObjectList();
        
        return $options;
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
        
        // Create dom_element
        $data['dom_element'] = $this->createDomElement($data['name']);
        // Initialise variables;
        $dispatcher = JDispatcher::getInstance();
        $table = $this->getTable();
        $key = $table->getKeyName();
        $pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
        $isNew = true;

        // Include the content plugins for the on save events.
        JPluginHelper::importPlugin('content');

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

            // Trigger the onContentBeforeSave event.
            $result = $dispatcher->trigger($this->event_before_save, array($this->option . '.' . $this->name, &$table, $isNew));
            if (in_array(false, $result, true)) {
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

            // Trigger the onContentAfterSave event.
            $dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, &$table, $isNew));
        } catch (Exception $e) {
            $this->setError($e->getMessage());

            return false;
        }

        $pkName = $table->getKeyName();

        if (isset($table->$pkName)) {
            $this->setState($this->getName() . '.id', $table->$pkName);
            //update dom_element
            $dom_element = $data['dom_element'] . '_' . $table->$pkName;

            $db = JFactory::getDbo();
            $q = $db->getQuery(true);
            $q->update('#__poe_option');
            $q->set('dom_element=' . $db->Quote($dom_element));
            $q->where('id=' . (int) $table->$pkName);
            $db->setQuery($q);
            $db->query();
        }
        $this->setState($this->getName() . '.new', $isNew);

        return true;
    }

    /**
     * Create DOM Element for Option 
     * 
     * Option names can contain characters that cause problem in DOM Element name or id
     * (spaces, brackets, ...). This code strips those characters
     *
     * @param string $name Option Name
     * 
     * @return string $dom_element Document Object Model Element name
     */
    public function createDomElement($name) {
        $dom_name = '';
        $chars = array(" ", "(", ")", "[", "]", ".", ",");

        if (strlen($name)) {
            $name = strtolower($name);

            for ($i = 0; $i < strlen($name); $i++) {

                if (!in_array($name[$i], $chars)) {
                    $dom_name .= $name[$i];
                }
            }
        }

        return $dom_name;
    }

    /**
     * Get option types
     * 
     * @return array of Type Objects
     */
    public function getOptionTypes() {
        $db = $this->getDbo();
        $q = $db->getQuery(true);
        $q->select('id value, name text');
        $q->from('#__poe_option_type');
        $q->order('id');

        $db->setQuery($q);

        $option_types = $db->loadObjectList();

        return $option_types;
    }

    public function getPriceControls() {
        // Initialize variables.
        $options_list = array();

        $options_list[] = JHtml::_('select.option', 1, 'Price Modifier');
        $options_list[] = JHtml::_('select.option', 2, 'Skip');

        return $options_list;
    }

    public function getUOMs() {
        $uom_list = array();
        $uom_list[] = array('value' => 0, 'text' => '--Select--');
        $db = $this->getDbo();
        $q = $db->getQuery(true);
        $q->select('id value, name text');
        $q->from('#__poe_uom');
        $q->order('id');

        $db->setQuery($q);

        if (($uoms = $db->loadObjectList())) {
            $uom_list = array_merge($uom_list, $uoms);
        }

        return $uom_list;
    }

    public function getDetails() {
        $detail_list = array();
        $detail_list[] = array('value' => 0, 'text' => '--Select--');
        $db = $this->getDbo();
        $q = $db->getQuery(true);
        $q->select('id value, name text');
        $q->from('#__poe_detail');
        $q->order('id');

        $db->setQuery($q);

        if (($details = $db->loadObjectList())) {
            $detail_list = array_merge($detail_list, $details);
        }

        return $detail_list;
    }

    /**
     * Import an option
     * 
     * @param int $product_id
     * @param object $option
     * @param int $sort
     * @return boolean True on success
     */
    public function importOption($product_id = 0, $option = null, $sort = 0) {
        if ($product_id == 0 || empty($option)) {
            $this->setError('Product id or option value empty');
            return false;
        }

        $data = JArrayHelper::fromObject($option);
        $data['id'] = '';
        $data['product_id'] = $product_id;
        $data['ordering'] = $sort;
        $data['published'] = 1;

        if (!$this->save($data)) {
            return false;
        }

        return true;
    }
    
    /**
     * Get next sort number
     * @param int $product_id
     * 
     * return int $sort_order
     */
    public function getNextSortNumber($product_id){
        $db = $this->getDbo();
        $q = $db->getQuery(true);
        $q->select('MAX(ordering)');
        $q->from('#__poe_option');
        $q->where('product_id=' . (int) $product_id);
        $db->setQuery($q);

        if(($result = $db->loadResult())){
            $sort_order = $result + 1;
        }else{
            $sort_order = 1;
        }
        
        return $sort_order;
    }

    /**
     * Delete options for a product
     * 
     * @param int $product_id
     * 
     * @return booelan True on success
     */
    public function deleteProductOptions($product_id = 0) {
        if ($product_id > 0) {
            $db = JFactory::getDbo();
            $q = $db->getQuery(true);
            $q->select('id');
            $q->from('#__poe_option');
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

    /**
     * Method to delete one or more records.
     *
     * @param   array  &$cids  An array of record primary keys.
     *
     * @return  boolean  True if successful, false if an error occurs.
     *
     * @since   11.1
     */
    public function delete(&$cids) {
        // Initialise variables.
        $cids = (array) $cids;
        $table = $this->getTable();
        if($cids){
            // Iterate the items to delete each one.
            foreach ($cids as $i => $cid) {

                if ($table->load($cid)) {

                    if ($this->canDelete($table)) {
                        //Option Values
                        $optionValueModel = JModel::getInstance('OptionValue', 'PoecomModel');

                        if(!$optionValueModel->deleteByOptionId($cid)){
                            $this->setError(JText::_('COM_POECOM_PRODUCT_OPTION_DELETE_ERROR').' : ' .$optionModel->getError());
                            return false;
                        }

                        //Option
                        if (!$table->delete($cid)) {
                            $this->setError($table->getError());
                            return false;
                        }
                    } else {

                        // Prune items that you can't change.
                        unset($cids[$i]);
                        $error = $this->getError();
                        if ($error) {
                            JError::raiseWarning(500, $error);
                            return false;
                        } else {
                            JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));
                            return false;
                        }
                    }
                } else {
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
    * A protected method to get a set of ordering conditions.
    *
    * @param   JTable  $table  A JTable object.
    *
    * @return  array  An array of conditions to add to ordering queries.
    *
    * @since   11.1
    */
   protected function getReorderConditions($table){
           $condition = array();

           $condition[] = 'product_id = '. $this->_db->Quote($table->product_id);

           return $condition;
   }
}
