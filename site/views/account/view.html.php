<?php
defined('_JEXEC') or die('Restricted access');
/**
 * POE-com - Site - Adress View Class
 * 
 * @package com_poecom
 * @author Micah Fletcher
 * @copyright 2010 - 2012 (C) Extensible Point Solutions Inc. All Right Reserved.
 * @license GNU GPLv3, http://www.gnu.org/licenses/gpl.html
 *
 * @since 2.0 - 3/11/2012 4:16:52 PM
 *
 * http://www.exps.ca
 * */
jimport('joomla.application.component.view');

/**
 * Account view class
 *
 * @package		com_poecom
 * @since		2.0
 */
class PoecomViewAccount extends JView {

    protected $form;

    /**
     * Method to display the view.
     *
     * @param	string	The template file to include
     * @since	2.5.1
     */
    public function display($tpl = null) {

        //$app = JFactory::getApplication();
        //$jinput = $app->input;

        // Assign data to the view
        $item = $this->get('Item');
        $form = $this->get('Form');
        //$state = $this->get('State');

        //$this->params = $this->state->get('params');
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {

            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }
    
        if($item->bt){
            //set billing form fields
            $form->setValue('btid','', $item->bt->id);
            $form->setValue('fname','', $item->bt->fname);
            $form->setValue('lname','', $item->bt->lname);
            $form->setValue('street1','', $item->bt->street1);
            $form->setValue('street2','', $item->bt->street2);
            $form->setValue('city','', $item->bt->city);
            $form->setValue('region_id','', $item->bt->region_id);
            $form->setValue('country_id','', $item->bt->country_id);
            $form->setValue('postal_code','', $item->bt->postal_code);
            $form->setValue('telephone','', $item->bt->telephone);
        }
        
        if($item->st){
            $form->setValue('stbt_same','', 0);
            //set shipping form fields
            $form->setValue('stid','', $item->st->id);
            $form->setValue('stfname','', $item->st->fname);
            $form->setValue('stlname','', $item->st->lname);
            $form->setValue('ststreet1','', $item->st->street1);
            $form->setValue('ststreet2','', $item->st->street2);
            $form->setValue('stcity','', $item->st->city);
            $form->setValue('stregion_id','', $item->st->region_id);
            $form->setValue('stcountry_id','', $item->st->country_id);
            $form->setValue('stpostal_code','', $item->st->postal_code);
            $form->setValue('sttelephone','', $item->st->telephone);
        }else{
            //ship to same as billing
            $form->setValue('stid','', 0);
            $form->setValue('stbt_same','', 1);
        }
        
        $this->form = $form;
        
        //assign requests
        $this->assignRef('requests', $item->requests);
        
        //assign orders
        $this->assignRef('orders', $item->orders);
        
        // asign user
        $user = JFactory::getUser();
        $this->assignRef('juser_id', $user->id);
        
        // Assign the script
        $script = $this->get('Script');
        $this->assignRef('script', $script);

        parent::display($tpl);
    }
}
