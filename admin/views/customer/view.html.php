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
 * Customer View
 */
class PoecomViewCustomer extends JView{
    /**
    * View form
    *
    * @var form
    */
    protected $form = null;
    
    /**
    * Customer display method
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
            $form->setValue('btid', '', $item->user_bt->id);
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
            $form->setValue('stid', '', $item->user_st->id);
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
        JLoader::register('CustomerHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/customer.php');
        JRequest::setVar('hidemainmenu', true);
      
        $isNew = $this->item->id == 0;
        $canDo = CustomerHelper::getActions($this->item->id);
        
        // Set title and icon class
        JToolBarHelper::title($isNew ? JText::_('COM_POECOM_CUSTOMER_NEW') : JText::_('COM_POECOM_CUSTOMER_EDIT'), 'customer');

        JToolBarHelper::cancel('customer.cancel', 'JTOOLBAR_CLOSE');
    }
    
    /**
    * Method to set up the document properties
    *
    * @return void
    */
    protected function setDocument(){
        $document = JFactory::getDocument();
        $document->addStyleDeclaration('.icon-48-customer {background-image: url(../media/com_poecom/images/icon-48-customers.png);}');
        
        $document->addScript(JURI::root() . "/administrator/components/com_poecom/views/customer/submitbutton.js");
        JText::script('COM_POECOM_INPUT_ERROR_UNACCEPTABLE');
    }
}
