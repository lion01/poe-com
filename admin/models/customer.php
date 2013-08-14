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
 * Customer Model
 */
class PoecomModelCustomer extends JModelAdmin {

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
    public function getTable($type = 'Customer', $prefix = 'PoecomTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the script that have to be included on the form
     *
     * @return string	Script files
     */
    public function getScript() {
        return JURI::root(true) . '/administrator/components/com_poecom/models/forms/customer.js';
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
        $form = $this->loadForm('com_poecom.customer', 'customer', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState('com_poecom.customer.edit.data', array());
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

        //add user_bt
        $addressModel = JModel::getInstance('Address', 'PoecomModel');
        if (!empty($item->id)) {
            $item->user_bt = $addressModel->getAddress($item->id, 'BT');
            $item->user_st = $addressModel->getAddress($item->id, 'ST');
        }

        return $item;
    }

    /**
     * Over ride Save method that does not use content plugin
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success, False on error.
     *
     * @since   11.1
     */
    public function save($data) {
        // Initialise variables
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
     * Update user table - not all values can be updated here
     * @param type $id
     * @param type $values
     * @return boolean
     */
    public function update($id = 0, $values = array()) {
        if (!empty($id) && !empty($values)) {
            $db = $this->getDbo();
            $q = $db->getQuery(true);
            $q->update('#__users');
            if (!empty($values['name'])) {
                $q->set('name=' . $db->quote($values['name']));
            }
            if (!empty($values['email'])) {
                $q->set('email=' . $db->quote($values['email']));
            }

            $q->where('id=' . (int) $id);

            $db->setQuery($q);

            if (!$db->query()) {
                return false;
            }
        } else {
            $this->setError('id or values missing');
            return false;
        }

        return true;
    }

    /**
     * Delete Customer(s)
     * 
     * Only customers with no orders or RFQ's can be deleted
     * Deleting customer also deletes Joomla user, BT/ST addresses
     * 
     * @param array $cids Customer ID which are Joomla user ids
     */
    public function delete(&$cids) {
        $app = JFactory::getApplication();

        if (!empty($cids)) {
            //get table
            $table = $this->getTable();
            $orderModel = JModel::getInstance('Order', 'PoecomModel');
            $rfqModel = JModel::getInstance('Request', 'PoecomModel');
            $addressModel = JModel::getInstance('Address', 'PoecomModel');
            
            foreach ($cids as $cid) {
                //check orders
                if (!$orderModel->deleteValidation('juser_id', $cid)) {
                    $app->enqueueMessage(JText::_('COM_POECOM_ORDER_FOUND') . $orderModel->getError(), 'error');
                    return false;
                }

                //check rfq's
                if (!$rfqModel->deleteValidation('juser_id', $cid)) {
                    $app->enqueueMessage(JText::_('COM_POECOM_RQUEST_FOUND') . $rfqModel->getError(), 'error');
                    return false;
                }
                
                //delete addresses
                if(!$addressModel->deleteUserAddresses($cid)){
                    $app->enqueueMessage($addressModel->getError(), 'error');
                    return false;
                }
           
                //delete Joomla user
                if (!$table->delete($cid)) {
                    $this->setError($table->getError());
                    return false;
                }
                
                
                
            }
        } else {
            $app->enqueueMessage(JText::_('COM_POECOM_NO_ITEM_SELECTED'), 'error');
            return false;
        }
        
        return true;
    }

}
