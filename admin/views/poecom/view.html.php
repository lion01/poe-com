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
jimport('joomla.application.component.view');
 
/**
 * Poecom View
 */
class PoecomViewPoecom extends JView{
    /**
    * Poecom view display method
    * @return void
    */
    function display($tpl = null){
    // Set the toolbar
    $this->addToolBar();

    // Display the template
    parent::display($tpl);

    // Set the document
    $this->setDocument();
}
    
    /**
    * Setting the toolbar
    */
    protected function addToolBar(){
        // Set the submenu
        JLoader::register('PoecomHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/poecom.php');
	
        $canDo = PoecomHelper::getActions();
        
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->select('s.version_id');
        $q->from('#__schemas s');
        $q->innerJoin('#__extensions e ON e.extension_id=s.extension_id');
        $q->where('e.name="com_poecom"');
        $db->setQuery($q);

        $version = $db->loadResult();
        
        JToolBarHelper::title(JText::_('COM_POECOM_MANAGER_HOME').' - ' .$version, 'poecom');

        if ($canDo->get('core.admin')) 
        {
                JToolBarHelper::preferences('com_poecom');
        }
    }
    
    /**
    * Method to set up the document properties
    *
    * @return void
    */
    protected function setDocument(){
        $document = JFactory::getDocument();
        $document->addStyleDeclaration('.icon-48-poecom {background-image: url(../media/com_poecom/images/icon-48-poecom.png);}');
    }
}
