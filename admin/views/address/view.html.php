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
**/

jimport('joomla.application.component.view');

/**
 * Address view class
 *
 * @package		com_poecom
 * @since		2.0
 */
class PoecomViewAddress extends JView{

    protected $form;

    /**
    * Method to display the view.
    *
    * @param	string	The template file to include
    * @since	2.5.1
    */
    public function display($tpl = null){

        $app = JFactory::getApplication();
        $jinput = $app->input;

        $jsess = JFactory::getSession();
        $shipping = $jsess->get('shipping',null,'poecom');

        $updated = $jinput->get('updated', 0, 'cmd');

        if($updated == 1){
            $shipping->address_update = 1;

            $jsess->set('shipping', $shipping, 'poecom');

            $this->assignRef('updated', $updated);
        }else{

            $shipping->address_update = 0;
            $jsess->set('shipping', $shipping, 'poecom');
        }

        // Get the request vars
        $address_type = $jinput->get('address_type', '', 'cmd');
        $address_id = $jinput->get('address_id', 0, 'cmd');

        // set id for getItem()
        $model = $this->getModel('Address');
        $model->setState('address.id', $address_id);

        // Get form with data from getItem($pk) bound to form
        $this->form = $model->getForm('', true, $address_type);

        // ST may not have juser_id yet
        $juser = JFactory::getUser();
        $jform_user_id = $this->form->getField('juser_id')->value;

        if($jform_user_id == "" && $juser->id != 1){
            $this->form->setValue('juser_id', '', $juser->id );
        }

        // Assign the script
        $script = $this->get('Script');

        $this->assignRef('script', $script);

        //$this->setDocument();

        parent::display($tpl);
    }

    /**
    * Set Document Properties
    *
    * @return void
    */
    protected function setDocument(){
        //$document = JFactory::getDocument();
    }
}
