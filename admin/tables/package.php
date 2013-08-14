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
jimport('joomla.database.table');
 
/**
 * Package Table class
 * 
 * This table does not use 'params' therfore method typically seeting 'params' values are
 * over ridden
 */
class PoecomTablePackage extends JTable{
    /**
    * Constructor
    *
    * @param object Database connector object
    */
    function __construct(&$db){
        parent::__construct('#__poe_shipping_pkg', 'id', $db);
    }
    
    /**
    * Overloaded bind function
    *
    * @param       array           named array
    * @return      null|string     null is operation was satisfactory, otherwise returns an error
    * @see JTable:bind
    * @since 1.5
    */
    public function bind($array, $ignore = ''){
        return parent::bind($array, $ignore);
    }
 
    /**
    * Overloaded load function
    *
    * @param       int $pk primary key
    * @param       boolean $reset reset data
    * @return      boolean
    * @see JTable:load
    */
    public function load($pk = null, $reset = true){
        if (parent::load($pk, $reset)){
            return true;
        }else{
            return false;
        }
    }
    
    
    /**
    * Method to compute the default name of the asset.
    * The default name is in the form `table_name.id`
    * where id is the value of the primary key of the table.
    *
    * @return	string
    * @since	1.6
    */
    protected function _getAssetName(){
        $k = $this->_tbl_key;
        return 'com_poecom.name.'.(int) $this->$k;
    }
    
     /**
     * Get the parent asset id for the record
     * 
     * @param JTable $table Not used
     * @param int $id Not used
     * @return int $asset->id;
     */
    protected function _getAssetParentId($table = null, $id = null){
        $asset = JTable::getInstance('Asset');
        $asset->loadByName('com_poecom');
        return $asset->id;
    }
}
