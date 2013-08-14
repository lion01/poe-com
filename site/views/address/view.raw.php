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
     * Create select html for a country's regions
     * 
     * output HTML for AJAX response
     */
    function getRegions(){
        $app = JFactory::getApplication();
        $jinput = $app->input;
        
        $country_id = $jinput->get('country_id', null, 'int');
        $type = $jinput->get('type', '', 'string');
        $regions = array();
        
        $name = $type.'region_id';
        
        $model = $this->getModel('Countries');
        
        $list = $model->getRegions($country_id);
        
        if($list){
            $regions = $list;
        }
       
        $html = JHTML::_('select.genericList', $regions, "jform[".$name."]", 'class="required" aria-required="true" required="required" aria-invalid="false"', 'value', 'text', 0, 'jform_'.$name  );
    
        echo $html;
    }
}

