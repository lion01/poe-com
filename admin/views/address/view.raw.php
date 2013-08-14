<?php
defined('_JEXEC') or die('Restricted access');
/**
 * POE-com - Site - Address Raw View Class
 * 
 * @package com_poecom
 * @author Micah Fletcher
 * @copyright 2010 - 2012 (C) Extensible Point Solutions Inc. All Right Reserved.
 * @license GNU GPLv3, http://www.gnu.org/licenses/gpl.html
 *
 * @since 2.0 - 3/11/2012 3:55:47 PM
 *
 * http://www.exps.ca
**/

jimport('joomla.application.component.view');

/**
 * Address Raw View Class
 *
 * @package com_poecom
 * @since	2.0
 */
class PoecomViewAddress extends JView{
    
    /**
     * AJAX - Update Address in Admin
     * 
     *  
     */
    function updateAddress(){
        $app = JFactory::getApplication();
        $jinput = $app->input;
        
        // Get the address type
        $address_type = $jinput->get('address_type', '', 'cmd');
        $address_id = $jinput->get('address_id', -1, 'int');
        $juser_id = $jinput->get('juser_id', 0, 'int');
        $data_values = $jinput->get('data_values', '', 'string');
        
        $status = 0;
        
        if(strlen($data_values)){
            $data = json_decode($data_values);
            
            if($data){
                if($address_id == 0){
                    //creating new
                    $data->id = '';
                }else{
                    $data->id = $address_id;
                }
                
                $data->address_type = $address_type;
                $data->juser_id = $juser_id;
                
                $dataArray = JArrayHelper::fromObject($data);
                
                $model = $this->getModel('Address');
                if($model->save($dataArray)){
                    $status = 1;
                    
                    if($address == 0){
                        //get the last insertid
                        $address_id = $model->getState('address.id');
                    }
                }
            }
        }
       
        $response = array('status' => $status, 'address_id' => $address_id);
        
        $html = json_encode($response);
        
        echo $html;
        
    }
    
    /**
     * Create select html for a country's regions
     * 
     * output HTML for AJAX response
     */
    function getRegions(){
        $app = JFactory::getApplication();
        $jinput = $app->input;
        
        $country_id = $jinput->get('country_id', null, 'int');
        $address_type = $jinput->get('address_type', 'BT', 'string');
        $region_el = $jinput->get('region_el', '', 'string');
        $regions = array();
        
        $model = $this->getModel('Countries');
        
        $list = $model->getRegions($country_id);
        
        if($list){
            $regions = $list;
        }else{
            $regions = array();
            $regions[] = array('value' => 'ALL', 'text' => JText::_('COM_POECOM_NO_REGIONS'));
        }
        
        if($address_type == 'BT'){
            if(strlen($region_el)){
                $region_DOM_name = $region_DOM_id = $region_el;
            }else{
                $region_DOM_name = 'jform[region_id]';
                $region_DOM_id = 'jform_region_id';
            }
            
            $class = 'poe-btaddress';
        }else{
             if(strlen($region_el)){
                $region_DOM_name = $region_DOM_id = $region_el;
            }else{
                $region_DOM_name = 'jform[st_region_id]';
                $region_DOM_id = 'jform_st_region_id';
            }
            $class = 'poe-staddress';
        }
       
        $html = JHTML::_('select.genericList', $regions, $region_DOM_name, 'class="required '.$class.'" aria-required="true" required="required" aria-invalid="false"', 'value', 'text', 0, $region_DOM_id  );
    
        echo $html;
    }
}

