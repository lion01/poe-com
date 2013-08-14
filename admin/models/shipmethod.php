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
 * ShipMethod Model
 */
class PoecomModelShipMethod extends JModelAdmin{
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
    public function getTable($type = 'ShipMethod', $prefix = 'PoecomTable', $config = array()){
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
        $form = $this->loadForm('com_poecom.shipmethod', 'shipmethod', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)){
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
    protected function loadFormData(){
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_poecom.shipmethod.edit.data', array());
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
        
        // For edit check if plugin is enabled and override form
        if($item->id > 0){
            // Get plugin status
            $plugin_enabled = JPluginHelper::isEnabled('poecomship', $item->plugin);
            
            if(!$plugin_enabled && $item->sm_enabled == 1){
                $item->sm_enabled = 0;
                $update_enabled = true;
            }else if($plugin_enabled && $item->sm_enabled == 0 ){
                $item->sm_enabled = 1;
                $update_enabled = true;
            }else{
                $update_enabled = false;
            }
            
            if($update_enabled){
                $data = JArrayHelper::fromObject($item);
                //no params in table
                unset($data['params']);
                $this->save($data);
            }
        }

        return $item;
    }
    
    public function save($data){
        
        // checkbox not is $data when unchecked
        if(!strlen($data['sm_default'])){
            $data['sm_default'] = 0;
        }else{
            $update_default = true;
        }
        
        if(!strlen($data['sm_enabled'])){
            $data['sm_enabled'] = 0;
        }
        
        
        // Save the product item and then update categories
        if(parent::save($data)){
            $db = JFactory::getDBO();
            
            if($update_default){
                // set all other method's default to "0"
                // get the last insertid for new records
                if($data['id'] == 0 || !strlen($data['id'])){
                    $id = $db->insertid();
                }else{
                    $id = $data['id'];
                }
                
                $q = $db->getQuery(true);
                $q->update('#__poe_shipping_method')
                ->set('sm_default=0')
                ->where('id!='.(int)$id);
                
                $db->setQuery($q);
                
                if(!$db->query()){
                    $app = JFactory::getApplication();
                    
                    $app->enqueueMessage('COM_POECOM_SHIPMETHOD_DEFAULT_ERROR', 'error');
                }
            }    
            // check plugin status, enable as needed
            $plugin_enabled = JPluginHelper::isEnabled('poecomship', $data['plugin']);
            
            if(($plugin_enabled && $data['sm_enabled'] == '0') ||
                    !$plugin_enabled && $data['sm_enabled'] == '1' ){
                $update_plugin = true;
            }else{
                $update_plugin = false;
            }

            if($update_plugin){
                //update plugin
                $q = $db->getQuery(true);
                $q->update('#__extensions')
                ->set('enabled='.$db->Quote($data['sm_enabled']))
                ->where('type='.$db->Quote('plugin'))
                ->where('folder='.$db->Quote('poecomship'))
                ->where('element='.$db->Quote($data['plugin']));
              
                $db->setQuery($q);
               
               if(!$db->query()){
                    $app = JFactory::getApplication();

                    $app->enqueueMessage('COM_POECOM_SHIPMETHOD_PLUGIN_ENABLE_ERROR', 'error');
                }
            }
          
            return true;
        }else{
            return false;
        }
    }
}
