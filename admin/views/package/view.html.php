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
 * Package View
 */
class PoecomViewPackage extends JView{
    /**
    * View form
    *
    * @var form
    */
    protected $form = null;
    
    /**
    * Package display method
    * @return void
    */
    public function display($tpl = null){
        $form = $this->get('Form');
        $item = $this->get('Item');

        // Check for errors.
        if (count($errors = $this->get('Errors'))){
                JError::raiseError(500, implode('<br />', $errors));
                return false;
        }
        // Assign the Data
        $this->form = $form;
        $this->item = $item;

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
        JLoader::register('PackageHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/package.php');
        JRequest::setVar('hidemainmenu', true);
      
        $isNew = $this->item->id == 0;
        $canDo = PackageHelper::getActions($this->item->id);
        
        // Set title and icon class
        JToolBarHelper::title($isNew ? JText::_('COM_POECOM_PACKAGE_NEW') : JText::_('COM_POECOM_PACKAGE_EDIT'), 'package');
        
        // Build the actions for new and existing records.
        if ($isNew){
            // For new records, check the create permission.
            if ($canDo->get('core.create')){
                
                JToolBarHelper::apply('package.apply', 'JTOOLBAR_APPLY');
                JToolBarHelper::save('package.save', 'JTOOLBAR_SAVE');
          
            }
            JToolBarHelper::cancel('package.cancel', 'JTOOLBAR_CANCEL');
        }else{
            if ($canDo->get('core.edit')){
                // We can save the new record
                JToolBarHelper::apply('package.apply', 'JTOOLBAR_APPLY');
                JToolBarHelper::save('package.save', 'JTOOLBAR_SAVE');
            }
    
            JToolBarHelper::cancel('package.cancel', 'JTOOLBAR_CLOSE');
        }
    }
    
    /**
    * Method to set up the document properties
    *
    * @return void
    */
    protected function setDocument(){
        $document = JFactory::getDocument();
        $document->addStyleDeclaration('.icon-48-package {background-image: url(../media/com_poecom/images/icon-48-packages.png);}');
        
        $document->addScript(JURI::root() . "/administrator/components/com_poecom/views/package/submitbutton.js");
        JText::script('COM_POECOM_INPUT_ERROR_UNACCEPTABLE');
    }
}
