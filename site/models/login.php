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
jimport('joomla.application.component.model');
 
/**
 * Login Model
 */
class PoecomModelLogin extends JModel{


    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param	type	The table type to instantiate
     * @param	string	A prefix for the table class name. Optional.
     * @param	array	Configuration array for model. Optional.
     * @return	JTable	A database object
     * @since	1.6
     */
    public function getTable($type = 'PoeUser', $prefix = 'PoecomTable', $config = array()) 
    {
            return JTable::getInstance($type, $prefix, $config);
    }


    /**
     * Method to get the script that have to be included on the form
     *
     * @return string	Script files
     */
    public function getScript() 
    {
            return JURI::root(true).'/components/com_poecom/models/forms/login.js';
    }
    
    /**
     * Clear sessions
     * 
     * This function is used to delete other sessions when user credentials are
     * used to login from more than one browser. The last login sessions is retained
     * 
     * @param string $session_id
     * @param int $user_id
     */
    public function clearSessions($session_id, $user_id){
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->select('session_id');
        $q->from('#__session');
        $q->where('userid=' . (int) $user_id);
        $q->where('session_id!='.$db->Quote($session_id));
        $q->where('client_id=0'); //do not clear admin
        $db->setQuery($q);

        if($sessions = $db->loadResultArray()){
            foreach($sessions as $s){
                $q = $db->getQuery(true);
                
                $q->delete('#__session');
                $q->where('session_id='.$db->Quote($s));
                $db->setQuery($q);
                
                $db->query();
            }
        }
        
        return true;
    }
	
}
