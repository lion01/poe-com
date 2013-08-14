<?php
defined('_JEXEC') or die('Restricted access');
/**
 * POE-com - Site - Payment View
 * 
 * @package com_poecom
 * @author Micah Fletcher
 * @copyright 2010 - 2012 (C) Extensible Point Solutions Inc. All Right Reserved.
 * @license GNU GPLv3, http://www.gnu.org/licenses/gpl.html
 *
 * @since 2.0 - 3/11/2012 4:24:14 PM
 *
 * http://www.exps.ca
**/

jimport('joomla.application.component.view');

/**
 * Payment view class
 *
 * @package		Joomla.Site
 * @subpackage	com_users
 * @since		1.6
 */
class PoecomViewPayment extends JView {

    protected $data;
    protected $form;
    protected $params;
    protected $state;

    /**
     * Method to display the view.
     *
     * @param	string	The template file to include
     * @since	1.6
     */
    public function display($tpl = null) {

        $app = JFactory::getApplication();

        $jsess = JFactory::getSession();
        $cart = $jsess->get('cart', null, 'poecom');

        if ($cart) {
            switch ($this->pay_method->type) {
                case '1': // credit card on site
                    // Get the view data.
                    $this->data = $this->get('Data');

                    $this->form = $this->get('Form');

                    $this->state = $this->get('State');

                    $this->params = $this->state->get('params');

                    $script = $this->get('Script');

                    // Check for errors.
                    if (count($errors = $this->get('Errors'))) {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                    }

                    $this->script = $script;
                    break;
                case '2': // external form
                    $plugin = $this->pay_method->plugin;

                    // Format data array for plugin
                    $data = array('cart' => $cart);

                    if (strlen($plugin)) {
                        $enabled = JPluginHelper::isEnabled('poecom', $plugin);

                        if ($enabled) {
                            // Fire sendRequest to open extneral form
                            $dispatcher = JDispatcher::getInstance();
                            JPluginHelper::importPlugin('poecom', $plugin);
                            $dispatcher->register('sendRequest', 'plgPoecom' . $plugin);
                            $result = $dispatcher->trigger('sendRequest', $data);

                            $this->assignRef('url', urlencode($result[0]));
                        } else {
                            $app->enqueueMessage(JText::_('COM_POECOM_PLUGIN_NOT_ENABLED_ERROR'));
                        }
                    }

                    break;
                case '3': // contract form
                    break;
                default:
                    break;
            }
        } else {
            $app->enqueueMessage(JText::_('COM_POECOM_ERROR_CART_EMPTY'), 'error');
        }

        parent::display($tpl);
    }
}
