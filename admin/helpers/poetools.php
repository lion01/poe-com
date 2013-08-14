<?php
defined('_JEXEC') or die('Restricted access');
/**
 * POE-com - Admin - Tools Class
 * 
 * @package com_poecom
 * @author Micah Fletcher
 * @copyright 2010 - 2012 (C) Extensible Point Solutions Inc. All Right Reserved.
 * @license GNU GPLv3, http://www.gnu.org/licenses/gpl.html
 *
 * @since 2.0 - 3/14/2012 10:22:07 AM
 *
 * http://www.exps.ca
 * */
/**
 * Class for utility functions used by various components
 */
class PoeTools {

    function __construct() {
        //empty
    }
    
    /**
     * Get list of countries with ID and Name
     * @param array $filter List is country ids to filter list
     * @param boolean $include Filter method include = true means list only includes countries in filter 
     * @return array List of country objects
     */
    public function getCountryList($filter = array(), $include = true){
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->select('id, name');
        $q->from('#__poe_country');
        if($filter && is_array($filter)){
            if($include){
                $q->where('id IN ('.$filter.')');
            }else{
                $q->where('id NOT IN ('.$filter.')');
            }
        }
        $q->order('name');
       
        $db->setQuery($q);

        $countries = $db->loadObjectList();
        
        return $countries;
    }
    
    /**
     * Get list of country regions  with ID and Name
     * 
     * @param int $country_id
     * @param array $filter List is region ids to filter list
     * @param boolean $include Filter method include = true means list only includes regions in filter 
     * @return array List of country objects
     */
    public function getCountryRegionList($country_id = 0, $filter = array(), $include = true){
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->select('id, name');
        $q->from('#__poe_region');
        if($filter && is_array($filter)){
            if($include){
                $q->where('id IN ('.$filter.')');
            }else{
                $q->where('id NOT IN ('.$filter.')');
            }
        }
        $q->order('name');
       
        $db->setQuery($q);

        $regions = $db->loadObjectList();
        
        return $regions;
    }
    
    /**
     * Create publish state list
     * 
     * Either an array list or html select
     * 
     * @param boolean $html Return html or list
     * @param string $name Name for select
     * @param int $value Default value
     * @return mixed array $pub_list or string $html
     */
     public function getPublishedList($html = true, $name = '', $value){
        $pub_list = array();
        $pub_list[] = array('value' => 'ALL', 'text' => JText::_('COM_POECOM_S_PUB_ALL'));
        $pub_list[] = array('value' => 0, 'text' => JText::_('COM_POECOM_S_PUB_NO'));
        $pub_list[] = array('value' => 1, 'text' => JText::_('COM_POECOM_S_PUB_YES'));
        
        
        if($html){
            $html = JHtml::_('select.genericList', $pub_list,$name,'class="inputbox" onchange="this.form.submit()"', 'value', 'text', $value);
            
            return $html;
        }else{
            return $pub_list;
        }
    } 
    
    /**
     * Create enabled state list
     * 
     * Either an array list or html select
     * 
     * @param boolean $html Return html or list
     * @param string $name Name for select
     * @param int $value Default value
     * @return mixed array $pub_list or string $html
     */
     public function getEnabledList($html = true, $name = '', $value){
        $enable_list = array();
        $enable_list[] = array('value' => 'ALL', 'text' => JText::_('COM_POECOM_S_ENABLED_ALL'));
        $enable_list[] = array('value' => 0, 'text' => JText::_('COM_POECOM_S_ENABLED_NO'));
        $enable_list[] = array('value' => 1, 'text' => JText::_('COM_POECOM_S_ENABLED_YES'));
        
        
        if($html){
            $html = JHtml::_('select.genericList', $enable_list,$name,'class="inputbox" onchange="this.form.submit()"', 'value', 'text', $value);
            
            return $html;
        }else{
            return $enable_list;
        }
    } 
    
    /**
     * JS Code snippet for country / region select lists
     * @param string $country_el Country select name
     * @param type $region_el Region select name
     * @return string $html Select
     */
    public function jsUpdateRegion($country_el = '', $region_el = ''){
        
        if(!strlen($country_el)){
            $country_el = 'jform_country_id';
        }
        
       $html = "<script> function updateRegions(){
            var countryID;
            var addressType = 'BT'; //required by updateRegions()
            countryID = jQuery('#".$country_el."').val();
            if(countryID > 0){
                jQuery.ajax({
                    type: 'POST',
                    url: 'index.php?option=com_poecom&view=address&task=address.getRegions&format=raw',
                    data: {country_id : countryID, address_type : addressType, region_el : '".$region_el."' },
                    dataType: 'html',
                    success: function(html, textStatus){";
       if(!strlen($region_el)){
           $html .= "jQuery('#jform_region_id').replaceWith(html); ";
       }else{
           $html .= "jQuery('#".$region_el."').replaceWith(html); ";
       }
                        
       $html .= "   },
                    error: ''
               });  
            }
        }</script>";
       
       return $html;

    }
}

?>