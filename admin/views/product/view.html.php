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
 * Product View
 */
class PoecomViewProduct extends JView{
    /**
    * View form
    *
    * @var		form
    */
    protected $form = null;
    
    /**
    * display method of Product view
    * @return void
    */
    public function display($tpl = null){
        $form = $this->get('Form');
    	$item = $this->get('Item');
        $script = $this->get('Script');
        
        $cat_model = $this->getModel('ProductCategoryXref');
        $item->catids = $cat_model->getProductCategories($item->id);
      
        // Check for errors.
        if(count($errors = $this->get('Errors'))){
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }
 
        //get defaults
        if(!$item->dimuom){
            //empty form
            $params = JComponentHelper::getParams('com_poecom');
            $dim_uom = $params->get('lengthuom');
            $wgt_uom = $params->get('weightuom');
            $form->setValue('dimuom', '', $dim_uom);
            $form->setValue('weightuom', '', $wgt_uom);
        }

        // Assign the Data
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
        JLoader::register('ProductHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/product.php');
        JRequest::setVar('hidemainmenu', true);
        
        $isNew = $this->item->id == 0;
        $canDo = ProductHelper::getActions($this->item->id);
        
        // Set title and assign icon class
        JToolBarHelper::title($isNew ? JText::_('COM_POECOM_MANAGER_PRODUCT_NEW') : JText::_('COM_POECOM_MANAGER_PRODUCT_EDIT'), 'product');
        
        // Built the actions for new and existing records.
        if ($isNew){
            // For new records, check the create permission.
            if ($canDo->get('core.create')){
                JToolBarHelper::apply('product.apply', 'JTOOLBAR_APPLY');
                JToolBarHelper::save('product.save', 'JTOOLBAR_SAVE');
                JToolBarHelper::custom('product.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
            }
            
            JToolBarHelper::cancel('product.cancel', 'JTOOLBAR_CANCEL');
        }else{
            if ($canDo->get('core.edit')){
                // We can save the new record
                JToolBarHelper::apply('product.apply', 'JTOOLBAR_APPLY');
                JToolBarHelper::save('product.save', 'JTOOLBAR_SAVE');

                // We can save this record, but check the create permission to see if we can return to make a new one.
                if ($canDo->get('core.create')){
                    JToolBarHelper::custom('product.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
                }
            }
           
            if ($canDo->get('core.create')){
                JToolBarHelper::custom('product.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
            }
            
            JToolBarHelper::cancel('product.cancel', 'JTOOLBAR_CLOSE');
        }
    }
    
    /**
    * Method to set up the document properties
    *
    * @return void
    */
    protected function setDocument(){
        $document = JFactory::getDocument();
        $document->addStyleDeclaration('.icon-48-product {background-image: url(../media/com_poecom/images/icon-48-products.png);}');
        $document->addScript(JURI::root() . "/administrator/components/com_poecom/views/product/submitbutton.js");
        JText::script('COM_POECOM_PRODUCT_ERROR_UNACCEPTABLE');
        JText::script('COM_POECOM_PRODUCT_ERROR_DEFAULT_QTY');
    }
}
