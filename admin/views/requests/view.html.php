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
 * RFQ List View
 */
class PoecomViewRequests extends JView{
    
    protected $items;
    protected $pagination;
    protected $state;

    /**
    * Requests view display method
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
        
        $rfq_status_filter = array();
        $rfq_status_filter[] = array('value' => '0', 'text' => JText::_('COM_POECOM_SELECT_RFQ_STATUS_ALL'));
        $rfq_status_filter[] = array('value' => '1', 'text' => JText::_('COM_POECOM_SELECT_RFQ_STATUS_OPEN'));
        $rfq_status_filter[] = array('value' => '2', 'text' => JText::_('COM_POECOM_SELECT_RFQ_STATUS_ORDERED'));
        $rfq_status_filter[] = array('value' => '3', 'text' => JText::_('COM_POECOM_SELECT_RFQ_STATUS_CLOSED'));
        
        $this->assignRef('rfq_status_filter', $rfq_status_filter);

        // Set the toolbar
        $this->addToolBar();

        // Display the template
        parent::display($tpl);
    }
    
    /**
    * Setting the toolbar
    */
    protected function addToolBar(){
        JLoader::register('RequestsHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/requests.php');
        
        RequestsHelper::addSubmenu('requests');
		
        $canDo = RequestsHelper::getActions();
        
        // Set title and icon class
        JToolBarHelper::title(JText::_('COM_POECOM_REQUEST_LIST_TITLE'), 'requests');
        
        if ($canDo->get('core.create')){
    //            JToolBarHelper::addNew('request.add', 'JTOOLBAR_NEW');
        }
        if ($canDo->get('core.edit')){
                JToolBarHelper::editList('request.edit', 'JTOOLBAR_EDIT');
        }
        if ($canDo->get('core.delete')){
                JToolBarHelper::deleteList('', 'requests.delete', 'JTOOLBAR_DELETE');
        }
        if ($canDo->get('core.admin')){
     //           JToolBarHelper::divider();
     //           JToolBarHelper::preferences('com_poecom');
        }
    }
}
