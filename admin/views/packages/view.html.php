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
 * Packages List View
 */
class PoecomViewPackages extends JView{
    
    protected $items;
    protected $pagination;
    protected $state;
    protected $script;

    /**
    * PayTransactions view display method
    * @return void
    */
    function display($tpl = null){
	
        // Get data from the model and assign to view
        $this->items = $this->get('Items');
        $this->pagination = $pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->script = $this->get('Script');
        // Check for errors.
        if (count($errors = $this->get('Errors'))){
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }

        // Set the toolbar
        $this->addToolBar();

        // Set document attributes
        $this->setDocument();

        // Display the template
        parent::display($tpl);
    }
    
    /**
    * Setting the toolbar
    */
    protected function addToolBar(){
        JLoader::register('PackagesHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/packages.php');
        
        PackagesHelper::addSubmenu('packages');
		
        $canDo = PackagesHelper::getActions();
        
        // Set title and icon class
        JToolBarHelper::title(JText::_('COM_POECOM_PKG_LIST_TITLE'), 'package');
        
        if ($canDo->get('core.create')){
                JToolBarHelper::addNew('package.add', 'JTOOLBAR_NEW');
        }
        if ($canDo->get('core.edit')){
                JToolBarHelper::editList('package.edit', 'JTOOLBAR_EDIT');
        }
        if ($canDo->get('core.delete')){
                JToolBarHelper::deleteList('', 'packages.delete', 'JTOOLBAR_DELETE');
        }
    }
    
    private function setDocument(){
	JText::script('COM_POECOM_SELECT_ONE_MSG');
	JText::script('COM_POECOM_SELECT_MAX_ONE_MSG');
    }
    
    private function setModalDocument(){
	$doc = JFactory::getDocument();
	$doc->addStyleSheet(JURI::root(true).'/administrator/components/com_poecom/assets/css/poecom.css');
	
	JText::script('COM_POECOM_NO_USERS_SELECTED_MSG');
    }
	
}
