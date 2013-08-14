<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Registration view class for Users.
 *
 * @package		Joomla.Site
 * @subpackage	com_users
 * @since		1.6
 */
class PoecomViewRegistration extends JView{
    
    /**
     * Create select html for a country's regions
     * 
     * output HTML for AJAX response
     */
    function getRegions(){
        $app = JFactory::getApplication();
        $jinput = $app->input;
        
        $country_id = $jinput->get('country_id', null, 'int');
        $regions = array();
        
        $model = $this->getModel('Countries');
        
        $list = $model->getRegions($country_id);
        
        if($list){
            $regions = $list;
        }
       
        $html = JHTML::_('select.genericList', $regions, "jform[region_id]", 'class="required" aria-required="true" required="required" aria-invalid="false"', 'value', 'text', 0, 'jform_region_id'  );
    
        echo $html;
    }   
}

