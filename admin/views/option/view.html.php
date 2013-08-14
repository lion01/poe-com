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
 * Product Option View
 */
class PoecomViewOption extends JView{
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
        
        
        $layout = $this->getLayout();
        
        if($layout == 'optionsetmodal'){
            //optionset modal
            $jinput = JFactory::getApplication()->input;
            $optionset_id = $jinput->get('optionset_id', 0, 'INT');
            $option_idx = $jinput->get('option_idx', -1, 'INT');
           
            $optionSetModel = JModel::getInstance('OptionSet', 'PoecomModel');
            $options = $optionSetModel->getOptions($optionset_id);
            
            if(!empty($options[$option_idx])){
                $set = $options[$option_idx];
                //set values
                $form->setValue('name', '', $set->name);
                $form->setValue('option_sku', '', $set->option_sku);
                $form->setValue('option_type_id', '', $set->option_type_id);
                $form->setValue('uom_id', '', $set->uom_id);
                $form->setValue('price_control_id', '', $set->price_control_id);
                $form->setValue('class', '', $set->class);
                $form->setValue('detail_id', '', $set->detail_id);
                $form->setValue('description', '', $set->description);
            }
            
            $this->assignRef('optionset_id', $optionset_id);
            $this->assignRef('option_idx', $option_idx);
        }else if($layout == 'productoptionmodal'){
            //product option modal
            $jinput = JFactory::getApplication()->input;
            $product_id = $jinput->get('product_id', 0, 'INT');
            $option_idx = $jinput->get('option_idx', -1, 'INT');
           
            $optionModel = JModel::getInstance('Option', 'PoecomModel');
            $options = $optionModel->getOptionsByProductId($product_id);
            
            if(!empty($options[$option_idx])){
                $set = $options[$option_idx];
                //set values
                $form->setValue('name', '', $set->name);
                $form->setValue('option_sku', '', $set->option_sku);
                $form->setValue('option_type_id', '', $set->option_type_id);
                $form->setValue('uom_id', '', $set->uom_id);
                $form->setValue('price_control_id', '', $set->price_control_id);
                $form->setValue('class', '', $set->class);
                $form->setValue('detail_id', '', $set->detail_id);
                $form->setValue('description', '', $set->description);
                $form->setValue('sort_order', '', $set->sort_order);
                
                $this->assignRef('option_id', $set->id);
            }
            
            $this->assignRef('product_id', $product_id);
            $this->assignRef('option_idx', $option_idx);
        }
        
        // Assign the Data
        $this->form = $form;
        $this->item = $item;
        
        // Set the toolbar
        $this->addToolBar();
        
        // Display the template
        parent::display($tpl);
        
        // Set the document after display to include submitbutton.js correctly
        $this->setDocument();
    }
 
    /**
    * Setting the toolbar
    */
    protected function addToolBar(){
        JLoader::register('OptionHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/option.php');
        JRequest::setVar('hidemainmenu', true);
        
        $isNew = $this->item->id == 0;
        $canDo = OptionHelper::getActions($this->item->id);
        JToolBarHelper::title($isNew ? JText::_('COM_POECOM_MANAGER_OPTION_NEW') : JText::_('COM_POECOM_MANAGER_OPTION_EDIT'), 'option');
        // Built the actions for new and existing records.
        if ($isNew){
            // For new records, check the create permission.
            if ($canDo->get('core.create')){
                JToolBarHelper::apply('option.apply', 'JTOOLBAR_APPLY');
                JToolBarHelper::save('option.save', 'JTOOLBAR_SAVE');
                JToolBarHelper::custom('option.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
            }
            JToolBarHelper::cancel('option.cancel', 'JTOOLBAR_CANCEL');
        }else{
            if ($canDo->get('core.edit')){
                // We can save the new record
                JToolBarHelper::apply('option.apply', 'JTOOLBAR_APPLY');
                JToolBarHelper::save('option.save', 'JTOOLBAR_SAVE');

                // We can save this record, but check the create permission to see if we can return to make a new one.
                if ($canDo->get('core.create')){
                    JToolBarHelper::custom('option.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
                }
            }
            if ($canDo->get('core.create')){
                JToolBarHelper::custom('option.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
            }
            JToolBarHelper::cancel('option.cancel', 'JTOOLBAR_CLOSE');
        }
    }
    
    /**
    * Method to set up the document properties
    *
    * @return void
    */
    protected function setDocument(){
        $document = JFactory::getDocument();
        $document->addStyleDeclaration('.icon-48-option {background-image: url(../media/com_poecom/images/icon-48-options.png);}');
        $document->addScript(JURI::root() . "/administrator/components/com_poecom/views/option/submitbutton.js");
        JText::script('COM_POECOM_OPTION_ERROR_UNACCEPTABLE');
    }
}
