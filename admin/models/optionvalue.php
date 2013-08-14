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
 * Product Option Value Model
 */
class PoecomModelOptionValue extends JModelAdmin{
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
    public function getTable($type = 'OptionValue', $prefix = 'PoecomTable', $config = array()){
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
        $form = $this->loadForm('com_poecom.optionvalue', 'optionvalue', array('control' => 'jform', 'load_data' => $loadData));
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
        return JURI::root(true).'/administrator/components/com_poecom/models/forms/optionvalue.js';
    }
    
	
    /**
    * Method to get the data that should be injected in the form.
    *
    * @return	mixed	The data for the form.
    * @since	1.6
    */
    protected function loadFormData(){
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_poecom.optionvalue.edit.data', array());
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
	public function getItem($pk = null)
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
		$table = $this->getTable();

		if ($pk > 0)
		{
			// Attempt to load the row.
			$return = $table->load($pk);

			// Check for a table object error.
			if ($return === false && $table->getError())
			{
				$this->setError($table->getError());
				return false;
			}
		}

		// Convert to the JObject before adding other data.
		$properties = $table->getProperties(1);
		$item = JArrayHelper::toObject($properties, 'JObject');

		if(!empty($item->dim_modifiers)){
                    $dim_mods = json_decode($item->dim_modifiers);
                    
                    if(!empty($dim_mods)){
                        $item->length_modifier = $dim_mods->length_modifier;
                        $item->length_modifier_value = $dim_mods->length_modifier_value;
                        $item->length_modifier_uom = $dim_mods->length_modifier_uom;
                        
                        $item->width_modifier = $dim_mods->width_modifier;
                        $item->width_modifier_value = $dim_mods->width_modifier_value;
                        $item->width_modifier_uom = $dim_mods->width_modifier_uom;
                        
                        $item->height_modifier = $dim_mods->height_modifier;
                        $item->height_modifier_value = $dim_mods->height_modifier_value;
                        $item->height_modifier_uom = $dim_mods->height_modifier_uom;
                    }
                    $wgt_mods = json_decode($item->wgt_modifier);
                    
                    if(!empty($wgt_mods)){
                        $item->weight_modifier = $wgt_mods->weight_modifier;
                        $item->weight_modifier_value = $wgt_mods->weight_modifier_value;
                        $item->weight_modifier_uom = $wgt_mods->weight_modifier_uom;
                    }
                }

		return $item;
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
            
            if(empty($data['option_id'])){
                return false;
            }

		// Initialise variables;
		$dispatcher = JDispatcher::getInstance();
		$table = $this->getTable();
		$key = $table->getKeyName();
		$pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
		$isNew = true;

		// Include the content plugins for the on save events.
		//JPluginHelper::importPlugin('content');
                
                //Prepare dimensional and weight modifiers
                $dim_mods = array();
                $wgt_mods = array();
                
                if($data['length_modifier'] != 'N'){
                    $dim_mods['length_modifier'] = $data['length_modifier'];
                    $dim_mods['length_modifier_uom'] = $data['length_modifier_uom'];
                    $dim_mods['length_modifier_value'] = $data['length_modifier_value'];
                }
                if($data['width_modifier'] != 'N'){
                    $dim_mods['width_modifier'] = $data['width_modifier'];
                    $dim_mods['width_modifier_uom'] = $data['width_modifier_uom'];
                    $dim_mods['width_modifier_value'] = $data['width_modifier_value'];
                }
                if($data['height_modifier'] != 'N'){
                    $dim_mods['height_modifier'] = $data['height_modifier'];
                    $dim_mods['height_modifier_uom'] = $data['height_modifier_uom'];
                    $dim_mods['height_modifier_value'] = $data['height_modifier_value'];
                }
                if(!empty($dim_mods)){
                    $data['dim_modifiers'] = json_encode($dim_mods);
                }else{
                    $data['dim_modifiers'] = '';
                }
                
                
                if($data['weight_modifier'] != 'N'){
                    $wgt_mods['weight_modifier'] = $data['weight_modifier'];
                    $wgt_mods['weight_modifier_uom'] = $data['weight_modifier_uom'];
                    $wgt_mods['weight_modifier_value'] = $data['weight_modifier_value'];
                }
                if(!empty($wgt_mods)){
                    $data['wgt_modifier'] = json_encode($wgt_mods);
                }else{
                    $data['wgt_modifier'] = '';
                }
                

		// Allow an exception to be thrown.
		try
		{
			// Load the row if saving an existing record.
			if ($pk > 0)
			{
				$table->load($pk);
				$isNew = false;
			}

			// Bind the data.
			if (!$table->bind($data))
			{
				$this->setError($table->getError());
				return false;
			}

			// Prepare the row for saving
			$this->prepareTable($table);

			// Check the data.
			if (!$table->check())
			{
				$this->setError($table->getError());
				return false;
			}

			// Trigger the onContentBeforeSave event.
		/*	$result = $dispatcher->trigger($this->event_before_save, array($this->option . '.' . $this->name, &$table, $isNew));
			if (in_array(false, $result, true))
			{
				$this->setError($table->getError());
				return false;
			}
*/
			// Store the data.
			if (!$table->store())
			{
				$this->setError($table->getError());
				return false;
			}

			// Clean the cache.
			$this->cleanCache();

			// Trigger the onContentAfterSave event.
			//$dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, &$table, $isNew));
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		$pkName = $table->getKeyName();

		if (isset($table->$pkName))
		{
			$this->setState($this->getName() . '.id', $table->$pkName);
		}
		$this->setState($this->getName() . '.new', $isNew);

		return true;
	}
        
        /**
         * Delete option values linked to an option
         * @param int $id Option Id
         * @return boolean True on success
         */
        public function deleteByOptionId($id = 0){
            if(empty($id)){
                return false;
            }
            
            //get rows linked to option
            $db = $this->getDbo();
            $q = $db->getQuery(true);
            $q->select('id');
            $q->from('#__poe_option_value');
            $q->where('option_id=' . (int) $id);
            $db->setQuery($q);

            $ids = $db->loadResultArray();
            
            if($ids){
                if(!$this->delete($ids)){
                    return false;
                }else{
                    return true;
                }
            }
            
            //no records found
            return true;
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

           $condition[] = 'option_id = '. $this->_db->Quote($table->option_id);

           return $condition;
   }
}
