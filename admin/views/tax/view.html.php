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
 * Tax View
 */
class PoecomViewTax extends JView{
    /**
    * View form
    *
    * @var form
    */
    protected $form = null;
    
    /**
    * Tax display method
    * @return void
    */
    public function display($tpl = null){
        $form = $this->get('Form');
        $item = $this->get('Item');
        $script = $this->get('Script');

        // Check for errors.
        if (count($errors = $this->get('Errors'))){
                JError::raiseError(500, implode('<br />', $errors));
                return false;
        }
        
        // Assign the Data
        $form->setValue('country_id', '', $item->country_id);
        $form->setValue('region_id', '', $item->region_id);
        $this->form = $form;
        $this->item = $item;
        $this->assignRef('script', $script); 

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
        JLoader::register('TaxHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/tax.php');
        JRequest::setVar('hidemainmenu', true);
      
        $isNew = $this->item->id == 0;
        $canDo = TaxHelper::getActions($this->item->id);
        
        // Set title and icon class
        JToolBarHelper::title($isNew ? JText::_('COM_POECOM_TAX_NEW') : JText::_('COM_POECOM_TAX_EDIT'), 'tax');
        
        // Build the actions for new and existing records.
        if ($isNew){
            // For new records, check the create permission.
            if ($canDo->get('core.create')){
                
                JToolBarHelper::apply('tax.apply', 'JTOOLBAR_APPLY');
                JToolBarHelper::save('tax.save', 'JTOOLBAR_SAVE');
          
            }
            JToolBarHelper::cancel('tax.cancel', 'JTOOLBAR_CANCEL');
        }else{
            if ($canDo->get('core.edit')){
                // We can save the new record
                JToolBarHelper::apply('tax.apply', 'JTOOLBAR_APPLY');
                JToolBarHelper::save('tax.save', 'JTOOLBAR_SAVE');
            }
    
            JToolBarHelper::cancel('tax.cancel', 'JTOOLBAR_CLOSE');
        }
    }
    
    /**
    * Method to set up the document properties
    *
    * @return void
    */
    protected function setDocument(){
        $document = JFactory::getDocument();
        $document->addStyleDeclaration('.icon-48-tax {background-image: url(../media/com_poecom/images/icon-48-taxes.png);}');
        
        $document->addScript(JURI::root() . "/administrator/components/com_poecom/views/tax/submitbutton.js");
        JText::script('COM_POECOM_INPUT_ERROR_UNACCEPTABLE');
    }
}
