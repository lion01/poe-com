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
jimport('joomla.application.component.controller');

class PoecomControllerCustomer extends JController{
    
    public function updateUser(){
        
        $response = new JObject();
        $repsonse->error = 0;
        $response->msg = JText::_('COM_POECOM_USER_UPDATED');
        
        $app = JFactory::getApplication();
        $jinput = $app->input;
        
        // check the token
 /*       $jtoken = $jinput->get('jtoken_name', null, 'string');
        $jtoken_val = $jinput->get('jtoken_value', null, 'int');
        
        $token = JUtility::getToken();
        
        // Check that the token is in a valid format.
        if ($jtoken != $token || $jtoken_val !== 1) {
            JError::raiseError(403, JText::_('JINVALID_TOKEN'));
            return false;
        }*/
        
        //get update vars
        $id = $jinput->get('id', 0, 'INT');
        $name = $jinput->get('name', '', 'STRING');
        $email = $jinput->get('email', '', 'STRING');
        
        $userModel = JModel::getInstance('Customer', 'PoecomModel');
        
        if(!empty($id)){
            $values = array();
            if(!empty($name)){
                $values['name'] = $name;
            }
            if(!empty($email)){
                $values['email'] = $email;
            }
         
            if(!$userModel->update($id, $values)){
                $repsonse->error = 1;
                $response->msg = JText::_('COM_POECOM_USER_NOT_UPDATED'). ' : '. $userModel->getError();
            }
        }
        
        $json_response = json_encode($response);
        
        echo $json_response;     
    }
    
    
    public function updateAddress(){
        
        $response = new JObject();
        $repsonse->error = 0;
        $response->msg = JText::_('COM_POECOM_USER_UPDATED');
        
        $app = JFactory::getApplication();
        $jinput = $app->input;
        
        // check the token
 /*       $jtoken = $jinput->get('jtoken_name', null, 'string');
        $jtoken_val = $jinput->get('jtoken_value', null, 'int');
        
        $token = JUtility::getToken();
        
        // Check that the token is in a valid format.
        if ($jtoken != $token || $jtoken_val !== 1) {
            JError::raiseError(403, JText::_('JINVALID_TOKEN'));
            return false;
        }*/
        
        //get update vars
        $id = $jinput->get('id', 0, 'INT');
        $fname = $jinput->get('fname', '', 'STRING');
        $lname = $jinput->get('lname', '', 'STRING');
        $street1 = $jinput->get('street1', '', 'STRING');
        $street2 = $jinput->get('street2', '', 'STRING');
        $city = $jinput->get('city', '', 'STRING');
        $country_id = $jinput->get('country_id', 0, 'INT');
        $region_id = $jinput->get('region_id', 0, 'INT');
        $postal_code = $jinput->get('postal_code', '', 'STRING');
        $telephone = $jinput->get('telephone', '', 'STRING');
        
        
        $addressModel = JModel::getInstance('Address', 'PoecomModel');
        
        if(!empty($id)){
            $values = array();
            if(!empty($fname)){
                $values['fname'] = $fname;
            }
            if(!empty($lname)){
                $values['lname'] = $lname;
            }
            if(!empty($street1)){
                $values['street1'] = $street1;
            }
            //can be empty
            $values['street2'] = $street2;
            
            if(!empty($city)){
                $values['city'] = $city;
            }
            if(!empty($country_id)){
                $values['country_id'] = $country_id;
            }
            if(!empty($region_id)){
                $values['region_id'] = $region_id;
            }
            if(!empty($postal_code)){
                $values['postal_code'] = $postal_code;
            }
            if(!empty($telephone)){
                $values['telephone'] = $telephone;
            }
            if(!$addressModel->update($id, $values)){
                $repsonse->error = 1;
                $response->msg = JText::_('COM_POECOM_USER_NOT_UPDATED'). ' : '. $addressModel->getError();
            }
        }
        
        $json_response = json_encode($response);
        
        echo $json_response;     
    }
}
