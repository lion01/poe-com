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
 * RFQ Admin View
 */
class PoecomViewRequest extends JView{
    /**
    * View form
    *
    * @var form
    */
    protected $form = null;
    
    /**
    * Request display method
    * @return void
    */
    public function display($tpl = null){
        $state = $this->get('State');
       
      //  $convert_rfq_id = $state->get('request.id');
        
        if(!empty($convert_rfq_id)){
            $app = JFactory::getApplication();
            $model = $this->getModel();
            if($model->createOrderFromRFQ($convert_rfq_id)){
                $app->enqueueMessage(JText::_('COM_POECOM_RFQ_CONVERTED_ORDER'), 'info');
            }else{
                $app->enqueueMessage(JText::_('COM_POECOM_RFQ_CONVERTED_ORDER_ERROR'), 'error');
                $app->redirect('index.php?option=com_poecom&view=requests');
            }

            $model->setState('request.id',$convert_rfq_id);
        }
        
        $form = $this->get('Form');
        $item = $this->get('Item');
        $script = $this->get('Script');

        // Check for errors.
        if (count($errors = $this->get('Errors'))){
	    JError::raiseError(500, implode('<br />', $errors));
	    return false;
        }
        // Assign the Data
        $this->form = $form;
        $this->item = $item;

        // Get regions
        if(!empty($item->user_bt->country_id)){
            // Get countries
            $requestModel = $this->getModel('Request');
            $countries = $requestModel->getCountries();
            $regions = $requestModel->getRegions($item->user_bt->country_id);
            $this->assignRef('countries', $countries);
            $this->assignRef('regions', $regions);
        }

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
        JLoader::register('RequestHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/request.php');
        JRequest::setVar('hidemainmenu', true);
      
        $isNew = $this->item->id == 0;
        $canDo = RequestHelper::getActions($this->item->id);
        
        // Set title and icon class
        JToolBarHelper::title($isNew ? JText::_('COM_POECOM_REQUEST_NEW') : JText::_('COM_POECOM_REQUEST_EDIT'), 'request');
        
        // Build the actions for new and existing records.
        if ($isNew){
            // For new records, check the create permission.
            if ($canDo->get('core.create')){
           //     JToolBarHelper::apply('request.apply', 'JTOOLBAR_APPLY');
           //     JToolBarHelper::save('request.save', 'JTOOLBAR_SAVE');
           //     JToolBarHelper::custom('request.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
            }
            JToolBarHelper::cancel('request.cancel', 'JTOOLBAR_CANCEL');
        }else{
            if ($canDo->get('core.edit')){
                // We can save the new record
                JToolBarHelper::apply('request.apply', 'JTOOLBAR_APPLY');
                JToolBarHelper::save('request.save', 'JTOOLBAR_SAVE');

                // We can save this record, but check the create permission to see if we can return to make a new one.
                if ($canDo->get('core.create')){
                  // JToolBarHelper::custom('request.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
                }
            }
            if ($canDo->get('core.create')){
                JToolBarHelper::custom('request.generateorder', 'generate-order.png', 'save-copy_f2.png', 'COM_POECOM_RFQ_GEN_ORDER', false);
               //     JToolBarHelper::custom('request.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
            }
            JToolBarHelper::cancel('request.cancel', 'JTOOLBAR_CLOSE');
        }
    }
    
    /**
    * Method to set up the document properties
    *
    * @return void
    */
    protected function setDocument(){
        $document = JFactory::getDocument();
        $document->addStyleDeclaration('.icon-48-request {background-image: url(../media/com_poecom/images/icon-48-requests.png);}');
        $document->addStyleSheet(JURI::root(true) . "/administrator/components/com_poecom/views/request/request.css");
        
        $document->addScript(JURI::root(true) . "/administrator/components/com_poecom/views/request/submitbutton.js");
        JText::script('COM_POECOM_INPUT_ERROR_UNACCEPTABLE');
        
        
    }
}
