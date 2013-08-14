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
 * ProductTabs List View
 */
class PoecomViewProductTabs extends JView{
    
    protected $items;
    protected $pagination;
    protected $state;

    /**
    * Display view
    * @return void
    */
    function display($tpl = null){
	
        // Get data from the model and assign to view
        $this->items = $this->get('Items');
        $this->pagination = $pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))){
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }

        // Set enabled filter
        $published_list = array();
        $published_list[] = array('value' => 'ALL', 'text' => JText::_('COM_POECOM_S_PUB_ALL'));
        $published_list[] = array('value' => 1, 'text' => JText::_('COM_POECOM_S_PUB_YES'));
        $published_list[] = array('value' => 0, 'text' => JText::_('COM_POECOM_S_PUB_NO'));

	$this->assignRef('published_list', $published_list);

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
        JLoader::register('ProductTabsHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/producttabs.php');
        
        ProductTabsHelper::addSubmenu('producttabs');
		
        $canDo = ProductTabsHelper::getActions();
        
        // Set title and icon class
        JToolBarHelper::title(JText::_('COM_POECOM_TAB_LIST_TITLE'), 'producttab');
        
        if ($canDo->get('core.create')){
                JToolBarHelper::addNew('producttab.add', 'JTOOLBAR_NEW');
        }
        if ($canDo->get('core.edit')){
                JToolBarHelper::editList('producttab.edit', 'JTOOLBAR_EDIT');
                JToolBarHelper::divider();
                JToolBarHelper::publishList('producttabs.publish', JText::_('JTOOLBAR_PUBLISH'),true);
                JToolBarHelper::unpublishList('producttabs.unpublish', JText::_('JTOOLBAR_UNPUBLISH'),true);
                JToolBarHelper::divider();
        }
        if ($canDo->get('core.delete')){
                JToolBarHelper::deleteList('', 'producttabs.delete', 'JTOOLBAR_DELETE');
        }
        JToolBarHelper::divider();
        JToolBarHelper::help('producttabs', true, null, 'com_poecom');
    }
    
    private function setDocument(){
	JText::script('COM_POECOM_SELECT_ONE_MSG');
	JText::script('COM_POECOM_SELECT_MAX_ONE_MSG');
    }
    /*
    private function setModalDocument(){
	$doc = JFactory::getDocument();
	$doc->addStyleSheet(JURI::root(true).'/administrator/components/com_poecom/assets/css/poecom.css');
	
	JText::script('COM_POECOM_NO_USERS_SELECTED_MSG');
    }
	*/
}
