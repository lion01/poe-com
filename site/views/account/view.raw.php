<?php

defined('_JEXEC') or die('Restricted access');
/**
 * POE-com - Site - Account Raw View Class
 * 
 * @package com_poecom
 * @author Micah Fletcher
 * @copyright 2010 - 2012 (C) Extensible Point Solutions Inc. All Right Reserved.
 * @license GNU GPLv3, http://www.gnu.org/licenses/gpl.html
 *
 * @since 2.0 - 3/11/2012 3:55:47 PM
 *
 * http://www.exps.ca
 * */
jimport('joomla.application.component.view');

/**
 * Account Raw View Class
 *
 * @package com_poecom
 * @since	2.0
 */
class PoecomViewAccount extends JView {

    /**
     * Update Joomla user profile 
     * 
     * Only name, password and email can be updated
     */
    public function updateProfile() {
        $app = JFactory::getApplication();
        $jinput = $app->input;

        $response = new JObject();
        $response->error = 1;
        $pass_valid = true;

        $id = $jinput->get('juser_id', 0, 'int');
        $email = $jinput->get('email', '', 'string');
        $pass1 = trim($jinput->get('password', '', 'string'));
        $pass2 = trim($jinput->get('password_2', '', 'string'));
        $name = $jinput->get('name', '', 'string');

        if (strlen($pass1) !== 0) {
            //password check length, match and minimum characters
            if (mb_strlen($pass1, 'UTF-8') < 6) {
                $response->msg = JText::_('COM_POECOM_ACCT_PROFILE_UPDATE_PASS_ERROR_1');
                $pass_valid = false;
            } else if ($pass1 != $pass2) {
                $response->msg = JText::_('COM_POECOM_ACCT_PROFILE_UPDATE_PASS_ERROR_2');
                $pass_valid = false;
            } else if (1 !== preg_match('~[0-9]~', $pass1)) {
                $response->msg = JText::_('COM_POECOM_ACCT_PROFILE_UPDATE_PASS_ERROR_3');
                $pass_valid = false;
            } else if (1 !== preg_match('~[A-Z]~', $pass1)) {
                $response->msg = JText::_('COM_POECOM_ACCT_PROFILE_UPDATE_PASS_ERROR_4');
                $pass_valid = false;
            } else if (1 !== preg_match('~[a-z]~', $pass1)) {
                $response->msg = JText::_('COM_POECOM_ACCT_PROFILE_UPDATE_PASS_ERROR_5');
                $pass_valid = false;
            } else {
                //password update
                $salt = JUserHelper::genRandomPassword(32);
                $crypt = JUserHelper::getCryptedPassword(trim($pass1), $salt);
                $password = $crypt . ':' . $salt;
            }
        }

        if ($pass_valid) {
            //either no password update or updated value valid
            $aModel = $this->getModel('Account');

            $user = $aModel->getUser($id);

            if ($user) {
                if (strlen(trim($pass1))) {
                    $user->password = $password;
                }

                if ($user->email != $email && strlen($email)) {
                    $user->email = $email;
                }

                if (strlen($name) && $name != $user->name) {
                    $user->name = $name;
                }

                if (!$aModel->updateUser($user)) {
                    $response->msg = JText::_('COM_POECOM_ACCT_PROFILE_UPDATE_ERROR_2');
                } else {
                    $response->error = 0;
                    $response->msg = JText::_('COM_POECOM_ACCT_PROFILE_UPDATE_OK');
                }
            } else {
                $response->msg = JText::_('COM_POECOM_ACCT_PROFILE_UPDATE_ERROR_1');
            }
        }

        $json_response = json_encode($response);

        echo $json_response;
    }
    /**
     * Update customer address
     * Handles update, insert and delete
     */
    public function updateAddress() {
        $app = JFactory::getApplication();
        $jinput = $app->input;

        $response = new JObject();
        $response->error = 1;

        $juser_id = $jinput->get('juser_id', 0, 'int');

        $user = JFactory::getUser();

        if ($user->id != $juser_id) {
            $response->msg = JText::_('COM_POECOM_NOT_USER');
        } else {
            $id = $jinput->get('id', 0, 'int');
            $stbt_same = $jinput->get('stbt_same', -1, 'int');
            $address_type = $jinput->get('address_type', 'BT', 'string');
            $fname = $jinput->get('fname', '', 'string');
            $lname = $jinput->get('lname', '', 'string');
            $street1 = $jinput->get('street1', '', 'string');
            $street2 = $jinput->get('street2', '', 'string');
            $city = $jinput->get('city', '', 'string');
            $country_id = trim($jinput->get('country_id', 0, 'int'));
            $region_id = trim($jinput->get('region_id', 0, 'int'));
            $postal_code = $jinput->get('postal_code', '', 'string');
            $telephone = $jinput->get('telephone', '', 'string');
            
            $aModel = $this->getModel('Account');
          
            $address = $aModel->getAddress($id);
           
            if($address && $stbt_same == 1 && $address_type == 'ST'){
                //delete the ship to address
                if(!$aModel->deleteAddress($id)){
                   $response->msg = JText::_('COM_POECOM_ACCT_ADDR_DELETE_ERROR'); 
                }else{
                   $response->error = 0;
                   $response->msg = JText::_('COM_POECOM_ACCT_ADDR_DELETE_OK'); 
                }
               
            }else if($address){
                 if(strlen(trim($fname)) > 0){
                    $address->fname = $fname;
                }
                if(strlen(trim($lname)) > 0){
                    $address->lname = $lname;
                }
                if(strlen(trim($street1)) > 0){
                    $address->street1 = $street1;
                }
                if(strlen(trim($street2)) > 0){
                    $address->street2 = $street2;
                }
                if(strlen(trim($city)) > 0){
                    $address->city = $city;
                }
                if($country_id > 0){
                    $address->country_id = $country_id;
                }
                if($region_id  > 0){
                    $address->region_id = $region_id;
                }
                if(strlen(trim($postal_code)) > 0){
                    $address->postal_code = $postal_code;
                }
                if(strlen(trim($telephone)) > 0){
                    $address->telephone = $telephone;
                }
                
                if(!$aModel->updateAddress($address)){
                    $response->msg = JText::_('COM_POECOM_ACCT_ADDR_UPDATE_ERROR');
                } else {
                    $response->error = 0;
                    $response->msg = JText::_('COM_POECOM_ACCT_ADDR_UPDATE_OK');
                }
            }else{
                //creating new
                $data = new JObject();
                $data->id = '';
                $data->juser_id = $juser_id;
                $data->address_type = $address_type;
                $data->fname = $fname;
                $data->lname = $lname;
                $data->street1 = $street1;
                $data->street2 = $street2;
                $data->city = $city;
                $data->region_id = $region_id;
                $data->country_id = $country_id;
                $data->postal_code = $postal_code;
                $data->telephone = $telephone;
                
                if(!$aModel->insertAddress($data)){
                    $response->msg = JText::_('COM_POECOM_ACCT_ADDR_INSERT_ERROR');
                } else {
                    $response->error = 0;
                    $response->msg = JText::_('COM_POECOM_ACCT_ADDR_INSERT_OK');
                }
            }
        }
        
        $json_response = json_encode($response);

        echo $json_response;
    }
}

