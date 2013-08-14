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
 * FlyerBlock View
 */
class PoecomViewFlyerBlock extends JView{
    /**
    * View form
    *
    * @var form
    */
    protected $form = null;
    
    /**
    * display method of Hello view
    * @return void
    */
    public function display($tpl = null){
        $form = $this->get('Form');
    	$item = $this->get('Item');
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) 
        {
                JError::raiseError(500, implode('<br />', $errors));
                return false;
        }
     
        // Assign the Data
        $this->form = $form;
        $this->item = $item;
        
        if(!empty($item->id)){
            $editmode = true;
        }else{
            $editmode = false;
        }
        $this->assignRef('editmode', $editmode);
 
        // Set the toolbar
        $this->addToolBar();
        
        $script = $this->get('Script');
        $this->assignRef('script', $script);
        
        // Display the template
        parent::display($tpl);
        
        // Set the document after display to include submitbutton.js correctly
        $this->setDocument();
    }
 
    /**
    * Setting the toolbar
    */
    protected function addToolBar(){
        JLoader::register('FlyerBlockHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/flyerblock.php');
        JRequest::setVar('hidemainmenu', true);
        
        $isNew = $this->item->id == 0;
        $canDo = FlyerBlockHelper::getActions($this->item->id);
        JToolBarHelper::title($isNew ? JText::_('COM_POECOM_MANAGER_FLYER_BLOCK_NEW') : JText::_('COM_POECOM_MANAGER_FLYER_BLOCK_EDIT'), 'flyerblock');
        // Built the actions for new and existing records.
        if ($isNew){
            // For new records, check the create permission.
            if ($canDo->get('core.create')){
                JToolBarHelper::apply('flyerblock.apply', 'JTOOLBAR_APPLY');
                JToolBarHelper::save('flyerblock.save', 'JTOOLBAR_SAVE');
                JToolBarHelper::custom('flyerblock.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
            }
            JToolBarHelper::cancel('flyerblock.cancel', 'JTOOLBAR_CANCEL');
        }else{
            if ($canDo->get('core.edit')){
                // We can save the new record
                JToolBarHelper::apply('flyerblock.apply', 'JTOOLBAR_APPLY');
                JToolBarHelper::save('flyerblock.save', 'JTOOLBAR_SAVE');

                // We can save this record, but check the create permission to see if we can return to make a new one.
                if ($canDo->get('core.create')){
                    JToolBarHelper::custom('flyerblock.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
                }
            }
            if ($canDo->get('core.create')){
                JToolBarHelper::custom('flyerblock.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
            }
            JToolBarHelper::cancel('flyerblock.cancel', 'JTOOLBAR_CLOSE');
        }
    }
    
    /**
    * Method to set up the document properties
    *
    * @return void
    */
    protected function setDocument(){
        $document = JFactory::getDocument();
        $document->addStyleDeclaration('.icon-48-flyerblock {background-image: url(../media/com_poecom/images/icon-48-flyers.png);}');
        $document->addScript(JURI::root() . "/administrator/components/com_poecom/views/flyerblock/submitbutton.js");
        JText::script('COM_POECOM_OPTION_ERROR_UNACCEPTABLE');
    }
}
