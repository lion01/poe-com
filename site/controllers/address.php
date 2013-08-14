<?php
defined('_JEXEC') or die('Restricted access');
/**
 * POE-com - Site - Address Controller
 * 
 * @package com_poecom
 * @author Micah Fletcher
 * @copyright 2010 - 2012 (C) Extensible Point Solutions Inc. All Right Reserved.
 * @license GNU GPLv3, http://www.gnu.org/licenses/gpl.html
 *
 * @since 2.0 - 3/11/2012 4:21:36 PM
 *
 * http://www.exps.ca
**/

jimport('joomla.application.component.controllerform');
/**
* Address controller class type JControllerForm
**/
class PoecomControllerAddress extends JControllerForm{
    /**
     * Method to display a view.
     *
     * @param	boolean $cachable Not used
     * @param	array	$urlParams  Not used An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return	JController This object to support chaining.
     * @since	1.7
     */
    public function display($cachable = false, $urlparams = false){
        
        $app = JFactory::getApplication();
        $jinput = $app->input;
        
        // check the token
        $jtoken = $jinput->get('jtoken_name', null, 'string');
        $jtoken_val = $jinput->get('jtoken_value', null, 'int');
        
        $token = JUtility::getToken();
        
        // Check that the token is in a valid format.
        if ($jtoken != $token || $jtoken_val !== 1) {
                JError::raiseError(403, JText::_('JINVALID_TOKEN'));
                return false;
        }

        $document = JFactory::getDocument();
        //get view parameters
        $viewType = $document->getType();
        $viewName = $jinput->get('view', 'Address', 'CMD');
        $viewLayout = $jinput->get('layout', 'default', 'CMD');

        // Set the view              
        $jinput->set('view', $viewName);
        $jinput->set('layout', $viewLayout);

        // Get the view object
        $view = $this->getView($viewName, $viewType, '', array('base_path' => $this->basePath, 'layout' => $viewLayout));

        // Get/Create the default model
        if (($model = $this->getModel($viewName))) {
            // Push the model into the view (as default)
            $view->setModel($model, true);
        }
     
         // Push document object into the view.
        $view->assignRef('document', $document);
        
        $view->display();
        
        return $this;
    }
    
   	/**
	 * Method to save a record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @since   11.1
	 */
	public function save($key = null, $urlVar = null){
            // Check for request forgeries.
            JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

            // Initialise variables.
            $app = JFactory::getApplication();
            $lang = JFactory::getLanguage();
            $model = $this->getModel('Address');
            $table = $model->getTable('address', 'PoecomTable');
            $data = JRequest::getVar('jform', array(), 'post', 'array');
            $checkin = property_exists($table, 'checked_out');
            $context = "$this->option.edit.$this->context";
     
            // Redirect to the list screen.
            $jtoken_name = JUtility::getToken();

            $fail_url = 'index.php?option=com_poecom&task=address.display&view=address&tmpl=component'.
                    '&jtoken_name='.$jtoken_name.'&jtoken_value=1&address_type='.$data['address_type'];

            $success_url = 'index.php?option=com_poecom&task=address.display&view=address&tmpl=component'.
                '&jtoken_name='.$jtoken_name.'&jtoken_value=1&updated=1';

            // Determine the name of the primary key for the data.
            if (empty($key)){
                $key = $table->getKeyName();
            }
        
            $juser = JFactory::getUser();

            if($juser->id != $data['juser_id']){
                // only allowed to edit your own addresses
                $this->setMessage(JText::_('COM_POECOM_NOT_USER_ADDRESS_ERROR'), 'error');

                $this->setRedirect( JRoute::_( $fail_url, false ) );

                return false;
            }

            // Validate the posted data.
            // Sometimes the form needs some posted data, such as for plugins and modules.
            $form = $model->getForm($data, false);

            if (!$form){
                $app->enqueueMessage($model->getError(), 'error');

                return false;
            }

            // Test whether the data is valid.
            $validData = $model->validate($form, $data);

            // Check for validation errors.
            if ($validData === false){

                // Get the validation messages.
                $errors = $model->getErrors();

                // Push up to three validation messages out to the user.
                for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++){
                    if ($errors[$i] instanceof Exception){
                            $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
                    }
                    else{
                            $app->enqueueMessage($errors[$i], 'warning');
                    }
                }

                // Save the data in the session.
                // may not need to do this for address
                $app->setUserState($context . '.data', $data);

                // Redirect back to the edit screen.
                $this->setRedirect(	JRoute::_( $fail_url, false ) );

                return false;
            }
        
            // check if ST address changed to equal BT
            if($validData['address_type'] == 'ST' && $validData['stbt_same'] == '1'){
                // remove ST address
                if(!$model->delete($validData['id'])){
                    // Redirect back to the edit screen.
                    $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_DELETE_FAILED', $model->getError()));
                    $this->setMessage($this->getError(), 'error');

                    $this->setRedirect(	JRoute::_( $fail_url, false ) );

                    return false;
            }
            
        }else if (!$model->save($validData)){
            // Attempt to save the data.
            // Save the data in the session.
            $app->setUserState($context . '.data', $validData);

            // Redirect back to the edit screen.
            $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
            $this->setMessage($this->getError(), 'error');

            $this->setRedirect(	JRoute::_( $fail_url, false ) );

            return false;
            
            // Save succeeded, so check-in the record.
            if ($checkin && $model->checkin($validData[$key]) === false){
                // Save the data in the session.
                $app->setUserState($context . '.data', $validData);

                // Check-in failed, so go back to the record and display a notice.
                $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
                $this->setMessage($this->getError(), 'error');

                $this->setRedirect(	JRoute::_( $fail_url, false ) );

                return false;
            }
        }

	//$this->setMessage(JText::_('COM_POECOM_ADDRESS_UPDATED'));
        
        // Clear the record id and data from the session.
	//	$this->releaseEditId($context, $recordId);
        $app->setUserState($context . '.data', null);

        $this->setRedirect(	JRoute::_( $success_url, false ) );

        return true;
    }
}
