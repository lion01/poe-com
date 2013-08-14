<?php
defined('_JEXEC') or die('Restricted access');
/**
 * POE-com - Site - Order Model
 * 
 * @package com_poecom
 * @author Micah Fletcher
 * @copyright 2010 - 2012 (C) Extensible Point Solutions Inc. All Right Reserved.
 * @license GNU GPLv3, http://www.gnu.org/licenses/gpl.html
 *
 * @since 2.0 - 3/11/2012 4:20:21 PM
 *
 * http://www.exps.ca
**/
jimport('joomla.application.component.modelform');
 
/**
 * Order Line Model
 */
class PoecomModelOrderLine extends JModelForm{
   
    /**
    * Method for getting the model form
    * 
    *
    * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
    * @return	mixed	A JForm object on success, false on failure
    * @since	11.1
    */
    public function getForm($data = array(),$loadData = true){
        

        $form = $this->loadForm('com_poecom.orderline', $orderline_XML, array('control' => 'jform', 'load_data' => $loadData));    

        if (empty($form)){
            return false;
        }
        return $form;
    }
    
    
    /**
    * Method to get the data that should be injected in the form.
    *
    * @return	mixed	The data for the form.
    * @since	11.1
    */
    protected function loadFormData(){
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_poecom.user.edit.data', array());
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
    *
    * @since   11.1
    */
    public function getItem($pk = null){

        // Initialise variables.
        $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');

        $table = $this->getTable('orderline','PoecomTable');

        if ($pk > 0){
            // Attempt to load the row.
            $return = $table->load($pk);

            // Check for a table object error.
            if ($return === false && $table->getError()){
                $this->setError($table->getError());
                return false;
            }
        }

        // Convert to the JObject before adding other data.
        $properties = $table->getProperties(1);
        $item = JArrayHelper::toObject($properties, 'JObject');

        if (property_exists($item, 'params')){
            $registry = new JRegistry;
            $registry->loadString($item->params);
            $item->params = $registry->toArray();
        }

        return $item;
    }
    
    /**
    * Method to get the script that have to be included on the form
    *
    * @return string	Script files
    */
    public function getScript(){
        return 'components/com_poecom/models/forms/orderline.js';
    }
    
    /**
    * Prepare and sanitise the table data prior to saving.
    *
    * @param   JTable  &$table  A reference to a JTable object.
    *
    * @return  void
    *
    * @since   11.1
    */
    protected function prepareTable(&$table){
        // Derived class will provide its own implementation if required.
    }
    
    /**
    * Method to test whether a record can be saved.
    * Only the user can save their address
    *
    * @param   object  $record  A record object.
    *
    * @return  boolean  True if allowed to delete the record. Defaults to the permission for the component.
    *
    * @since   2.0
    */
    protected function canSave($user_id){
        $user = JFactory::getUser();

        if($user->id == $user_id){
            return true;
        }else{
            return false;
        }
    }    
    
    
    /**
    * Method to save the form data.
    *
    * @param   array  $data  The form data.
    *
    * @return  boolean  True on success, False on error.
    *
    * @since   11.1
    */
    public function save($data){
        if(!$this->canSave($data['juser_id'])){
            $this->setError(JText::_('COM_POECOM_USER_MISMATCH_SAVE_ERROR'));
            return false;
        }

        // Initialise variables;
        $table = $this->getTable('orderline', 'PoecomTable');

        $key = $table->getKeyName();

        $pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');

        $isNew = true;

        // Allow an exception to be thrown.
        try{
            // Load the row if saving an existing record.
            if ($pk > 0){
                    $table->load($pk);
                    $isNew = false;
            }else{
                $user = JFactory::getUser();

                if($user->id > 1){
                    // no guest orders
                    $data['juser_id'] = $user->id;
                }else{
                    $this->error(JText::_('COM_POECOM_NOT_LOGGED_IN_USER_FOR_ADDR'));
                    return false;
                }
            }

            // Bind the data.
            if (!$table->bind($data)){
                $this->setError($table->getError());
                return false;
            }

            // Prepare the row for saving
            $this->prepareTable($table);

            // Check the data.
            if (!$table->check()){
                $this->setError($table->getError());
                return false;
            }

            // Store the data.
            if (!$table->store()){
                $this->setError($table->getError());
                return false;
            }

            // Clean the cache.
            $this->cleanCache();
        }catch (Exception $e){
            $this->setError($e->getMessage());

            return false;
        }

        $pkName = $table->getKeyName();

        if (isset($table->$pkName)){
            $this->setState($this->getName() . '.id', $table->$pkName);
        }
        
        $this->setState($this->getName() . '.new', $isNew);

        return true;
    }
    
    /**
    * Method to test whether a record can be deleted.
    * Only the user can delete their address
    *
    * @param   object  $record  A record object.
    *
    * @return  boolean  True if allowed to delete the record. Defaults to the permission for the component.
    *
    * @since   11.1
    */
    protected function canDelete($record){
        $user = JFactory::getUser();

        if($user->id == $record->juser_id){
            return true;
        }else{
            return false;
        }
    }
    
    
    /**
    * Method to delete one or more records.
    *
    * @param   array  &$pks  An array of record primary keys.
    *
    * @return  boolean  True if successful, false if an error occurs.
    *
    * @since   11.1
    */
    public function delete($pk){
        // Initialise variables.
        $table = $this->getTable('orderline', 'PoecomTable');


        if ($table->load($pk)){

            if ($this->canDelete($table)){
                if (!$table->delete($pk)){
                        $this->setError($table->getError());
                        return false;
                }
            }else{
                $error = $this->getError();
                if ($error){
                        JError::raiseWarning(500, $error);
                        return false;
                }else{
                        JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));
                        return false;
                }
            }
        }else{
            $this->setError($table->getError());
            return false;
        }

        // Clear the component's cache
        $this->cleanCache();

        return true;
    }
}
?>