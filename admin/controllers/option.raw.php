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
jimport('joomla.application.component.controllerform');
 
/**
 * Product Option Controller
 */
class PoecomControllerOption extends JController{
    /**
     * Add option from AJAX request
     * 
     * outputs HTML response
     */
    public function add(){
        $response = new JObject();
        $response->error = 0;
        $response->msg = JText::_('COM_POECOM_OPTION_ADDED');
        
        $app = JFactory::getApplication();
        $jinput = $app->input;
        
        $data = array();
        $data['id'] = '';
        $data['product_id'] = $jinput->get('product_id', 0, 'INT');
        $data['name'] = $jinput->get('name', '', 'STRING');
        $data['option_sku'] = $jinput->get('option_sku', '', 'STRING');
        $data['option_type_id'] = $jinput->get('option_type_id', 1, 'INT');
        $data['price_control_id'] = $jinput->get('price_control_id', 1, 'INT');
        $data['class'] = $jinput->get('option_class', '', 'STRING');
        $data['uom_id'] = $jinput->get('uom_id', 0, 'INT');
        $data['detail_id'] = $jinput->get('detail_id', 0, 'INT');
        $data['description'] = $jinput->get('description', '', 'STRING');
        $data['ordering'] = $jinput->get('ordering', 0, 'INT');

        $optionModel = JModel::getInstance('Option', 'PoecomModel');
        
        if(!$optionModel->save($data)){
            $response->error = 1;
            $response->msg = JText::_('COM_POECOM_OPTION_NOT_ADDED');
        }
        
        $json_response = json_encode($response);
        
        echo $json_response;
        
    }
    /*
     * Update option from AJAX request
     * 
     * Outputs HTML response
     */
    public function update(){
        $response = new JObject();
        $response->error = 0;
        $response->msg = JText::_('COM_POECOM_OPTION_UPDATED');
        
        $app = JFactory::getApplication();
        $jinput = $app->input;
        
        $data = array();
        $data['id'] = $jinput->get('option_id', '', 'INT');
        $data['product_id'] = $jinput->get('product_id', 0, 'INT');
        $data['name'] = $jinput->get('name', '', 'STRING');
        $data['option_sku'] = $jinput->get('option_sku', '', 'STRING');
        $data['option_type_id'] = $jinput->get('option_type_id', 1, 'INT');
        $data['price_control_id'] = $jinput->get('price_control_id', 1, 'INT');
        $data['class'] = $jinput->get('option_class', '', 'STRING');
        $data['uom_id'] = $jinput->get('uom_id', 0, 'INT');
        $data['detail_id'] = $jinput->get('detail_id', 0, 'INT');
        $data['description'] = $jinput->get('description', '', 'STRING');
        $data['ordering'] = $jinput->get('ordering', 0, 'INT');
        
        $optionModel = JModel::getInstance('Option', 'PoecomModel');
        
        if(!$optionModel->save($data)){
            $response->error = 1;
            $response->msg = JText::_('COM_POECOM_OPTION_NOT_UPDATED');
        }
        
        $json_response = json_encode($response);
        
        echo $json_response;
        
    }
}
