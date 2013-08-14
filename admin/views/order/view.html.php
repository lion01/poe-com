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
 * Order Option View
 */
class PoecomViewOrder extends JView{
    /**
    * View form
    *
    * @var form
    */
    protected $form = null;
    
    /**
    * Order display method
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
        
        if(!empty($item->user_bt)){
            $form->setValue('fname', '', $item->user_bt->fname);
            $form->setValue('lname', '', $item->user_bt->lname);
            $form->setValue('street1', '', $item->user_bt->street1);
            $form->setValue('street2', '', $item->user_bt->street2);
            $form->setValue('city', '', $item->user_bt->city);
            $form->setValue('country_id', '', $item->user_bt->country_id);
            $form->setValue('region_id', '', array($item->user_bt->country_id,$item->user_bt->region_id));
            $form->setValue('postal_code', '', $item->user_bt->postal_code);
            $form->setValue('telephone', '', $item->user_bt->telephone);
        }
        //set ST
        if(!empty($item->user_st)){
            
            $form->setValue('stbt_same', '', 0);
            $form->setValue('st_lname', '', $item->user_st->lname);
            $form->setValue('st_street1', '', $item->user_st->street1);
            $form->setValue('st_street2', '', $item->user_st->street2);
            $form->setValue('st_city', '', $item->user_st->city);
            $form->setValue('st_country_id', '', $item->user_st->country_id);
            $form->setValue('st_region_id', '', array($item->user_st->country_id,$item->user_st->region_id));
            $form->setValue('st_postal_code', '', $item->user_st->postal_code);
            $form->setValue('st_telephone', '', $item->user_st->telephone);
        }else{
            $form->setValue('stbt_same', '', 1);
        }
        //set carrier
        if(!empty($item->carrier)){
            $form->setValue('carrier', '', $item->carrier->carrier);
            $form->setValue('carrier_logo', '', $item->carrier->carrier_logo);
            $form->setValue('eta', '', $item->carrier->eta);
            $form->setValue('service', '', $item->carrier->service);
        }

        // Assign the Data
        $this->form = $form;
        $this->item = $item;
        
        // assign script
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
        JLoader::register('OrderHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/order.php');
        JRequest::setVar('hidemainmenu', true);
      
        $isNew = $this->item->id == 0;
        $canDo = OrderHelper::getActions($this->item->id);
        
        // Set title and assign icon class
        JToolBarHelper::title($isNew ? JText::_('COM_POECOM_ORDER_NEW') : JText::_('COM_POECOM_ORDER_EDIT'), 'order');
        
        // Build the actions for new and existing records.
        if ($isNew){
            // For new records, check the create permission.
            if ($canDo->get('core.create')){
                JToolBarHelper::apply('order.apply', 'JTOOLBAR_APPLY');
                JToolBarHelper::save('order.save', 'JTOOLBAR_SAVE');
                JToolBarHelper::custom('order.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
            }
            JToolBarHelper::cancel('order.cancel', 'JTOOLBAR_CANCEL');
        }else{
            if ($canDo->get('core.edit')){
                // We can save the new record
                JToolBarHelper::apply('order.apply', 'JTOOLBAR_APPLY');
                JToolBarHelper::save('order.save', 'JTOOLBAR_SAVE');

                /* TODO: order save and new
                // We can save this record, but check the create permission to see if we can return to make a new one.
                if ($canDo->get('core.create')){
                        JToolBarHelper::custom('order.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
                } */
            }
            /* TODO: copy order
            if ($canDo->get('core.create')){
                    JToolBarHelper::custom('order.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
            } */
            JToolBarHelper::cancel('order.cancel', 'JTOOLBAR_CLOSE');
        }
    }
    
    /**
    * Method to set up the document properties
    *
    * @return void
    */
    protected function setDocument(){
        $document = JFactory::getDocument();
        $document->addStyleDeclaration('.icon-48-order {background-image: url(../media/com_poecom/images/icon-48-orders.png);}');
        $document->addStyleSheet(JURI::root(true) . "/administrator/components/com_poecom/views/order/order.css");
    
        $document->addScript(JURI::root() . "/administrator/components/com_poecom/views/order/submitbutton.js");
        JText::script('COM_POECOM_INPUT_ERROR_UNACCEPTABLE');
    }
}
