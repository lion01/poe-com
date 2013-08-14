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
 * Flyer Section Model
 */
class PoecomModelFlyerSection extends JModelAdmin {

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
    public function getTable($type = 'FlyerSection', $prefix = 'PoecomTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }
    
   /**
    * Method to get the script that have to be included on the form
    *
    * @return string	Script files
    */
    public function getScript(){
        
        $file = JPATH_ADMINISTRATOR.'/components/com_poecom/models/forms/flyersection.js';
 
        if(JFile::exists($file)){
            $js = JURI::root(true).'/administrator/components/com_poecom/models/forms/flyersection.js';
        }else{
            $js = '';
        }
        
        return $js;
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
        $form = $this->loadForm('com_poecom.flyersection', 'flyersection', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState('com_poecom.flyersection.edit.data', array());
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
     * Get number of rows in the table
     * @return int $count
     */
    protected function getRowCount(){
        $count = 0;
        
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->select('COUNT(id)');
        $q->from('#__poe_flyer_section');
        $db->setQuery($q);

        if(($result = $db->loadResult())){
            $count = $result;
        }
        
        return $count;
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

           $condition[] = 'flyer_id = '. $this->_db->Quote($table->flyer_id);

           return $condition;
   }
    
    /**
     * Reset the record ordering value 
     * 
     * Used when edit form has sortlist field type that allows changing ordering
     * Similiar to JModelAdmin->reorder, but doesn't need delta value
     * 
     * @param int $id Id of record initiating the ordering change
     * @param mixed $group Group identifier for a ordering set
     * @param int $pos Current ordering value
     * @param int $ordering New ordering value
     * @return true on success
     */
    public function resetSort($id = 0, $group = '', $pos = 0, $ordering = 0){
        
        if(empty($id) || empty($ordering)){
            return false;
        }
        
        //set move direction
        if($pos < $ordering){
            //move row down in sort order
            $move = 'down';
        }else{
            //move row up in sort order
            $move = 'up';
        }
        
        // get list of ids an sort number
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->select('id,ordering');
        $q->from('#__poe_flyer_section');
        if(!empty($group)){
            $q->where('flyer_id='.$db->quote($group));
        }
        $db->setQuery($q);

        if(($rows = $db->loadObjectList())){
            if($move === 'down'){
                //move id record down and other records up
                for($i = 0; $i <= $ordering; $i++){
                    if($rows[$i]->id != $id){
                       
                        $q = $db->getQuery(true);
                        $q->update('#__poe_flyer_section');
                        $q->set('ordering='.$db->quote($rows[$i]->ordering-1));
                        $q->where('id=' . $rows[$i]->id);
                        $db->setQuery($q);

                        if(!$db->query()){
                            return false;
                        }
                    }
                }
            }else{
                //move record up and other records down
                for($i = $pos-1; $i >= 0; $i--){
                    if($rows[$i]->id != $id){
                        $q = $db->getQuery(true);
                        $q->update('#__poe_flyer_section');
                        $q->set('ordering = '.$db->quote($rows[$i]->ordering+1));
                        $q->where('id=' . $rows[$i]->id);
                        $db->setQuery($q);

                        if(!$db->query()){
                            return false;
                        }
                    }
                }
            }
        }
        
        return true;
        
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
            //Note: It maybe possible to remove ordering code and use JModelAdmin->reorder
            //This code does not need to determine delta
            if(isset($data['ordering'])){
                $last_row = $this->getRowCount();
                
                if($data['ordering'] == 0 ){
                    //make this record last
                    if(empty($pk)){
                        $data['ordering'] = $last_row + 1;
                    }else{
                        $data['ordering'] = $last_row;
                    }

                }

                if(!empty($table->id)  && $last_row > 0 ){
                    //re-set all sorts
                    $this->resetSort($pk,$data['flyer_id'], $table->ordering, $data['ordering']);
                }
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
     * Get option set options
     * @param int $id Option Set Id
     * @return array Array of Objects
     */
    public function getOptions($id = 0){
        $set = $this->getItem($id);

        if(!empty($set->json_optionset)){
            $options = json_decode($set->json_optionset);
        }else{
            $options = array();
        }

        return $options;
    }
    /**
     * Delete option set option
     * 
     * @param int $flyer_id
     * @param array $option_ids
     * @return boolean True on success
     */
    public function deleteOptions($flyer_id = 0 , $option_ids = array()){
        $flyer = $this->getItem($flyer_id);
        
        if($flyer){
            if(!empty($flyer->json_optionset)){
                $options = json_decode($flyer->json_optionset);
                
                foreach($option_ids as $id){
                    //remove option
                    unset($options[$id]);
                }
                
                $flyer->json_optionset = json_encode($options);
                
                if(!$this->save(JArrayHelper::fromObject($flyer))){
                    return false;
                }
            }else{
                $this->setError('No options found');
                return false;
            }
        }else{
            $this->setError('No option set found');
            return false;
        }
        
        return true;
    }
}
