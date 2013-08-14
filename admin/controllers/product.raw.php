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
 
/**
 * Product RAW Controller
 */
class PoecomControllerProduct extends JController{

    public function getOptions(){
        $html = '';
        $no_options = '<div id="options">'.JText::_('COM_POECOM_OPTIONSET_NO_OPTIONS').'</div>';
        $jinput = JFactory::getApplication()->input;
        
        $product_id = $jinput->get('product_id', 0, 'INT');
        
        if(!empty($product_id)){
            $optionModel = JModel::getInstance('Option', 'PoecomModel');
            $product_options = $optionModel->getOptionsByProductId($product_id);
            
            
            if($product_options){
                $option_types = $optionModel->getOptionTypes();
                $price_controls = $optionModel->getPriceControls();
                $uoms = $optionModel->getUOMs();
                $details  = $optionModel->getDetails();
                
                $idx = 1;
                $html = '<div id="options">
                    <ul id="optionset_option_list-head">
                    <li>Name</li><li>Option Sku</li><li>Option Type</li><li>UOM</li><li>Price Control</li><li>CSS Class</li><li>Detail Id</li><li>Description</li>
                    </ul>
<ul id="optionset_option_list">';
                foreach($product_options as $op){
                    $html .= '<li>';
                    $html .= '<input type="checkbox" name="optionset[options]" id="option'.$idx.'" />';
                    $html .= '<input type="text" id="option_name'.$idx.'" value="'.$op->name.'">';
                    $html .= '<input type="text" id="option_sku'.$idx.'" value="'.$op->option_sku.'">';
                    $html .= JHTML::_('select.genericList', $option_types, 'option_type_id'.$idx, '', 'value', 'text', $op->option_type_id);
                    $html .= JHTML::_('select.genericList', $uoms, 'oum_id'.$idx, '', 'value', 'text', $op->uom_id);
                    $html .= JHTML::_('select.genericList', $price_controls, 'price_control_id'.$idx, '', 'value', 'text', $op->price_control_id);
                    $html .= '<input type="text" id="option_class'.$idx.'" value="'.$op->class.'">';
                    $html .= JHTML::_('select.genericList', $details, 'detail_id'.$idx, '', 'value', 'text', $op->detail_id);
                    $html .= '<textarea  id="description'.$idx.'" cols="60" rows="3">'.$op->description.'</textarea>';
                    $html .= '</li>';
                    $idx++;
                }
                
                $html .= '</ul>
                    <div id="optionset_option_list_buttons">
                    <button type="button" id="addOption" onclick="addOption()" >Add</button>
                    <button type="button" id="editOption" onclick="editOption()" >Edit</button>
                    <button type="button" id="deleteOption" onclick="deleteOption()" >Delete</button>
                    </div>
                    </div>';
            }else{
                $html = $no_options;
                $html .= '<div id="optionset_option_list_buttons">
                    <button type="button" id="addOption" onclick="addOption()" >Add</button>
                    </div></div>';
            }
        }else{
            $html = $no_options;
             $html .= '<div id="optionset_option_list_buttons">
                    <button type="button" id="addOption" onclick="addOption()" >Add</button>
                    </div></div>';
        }
        
        echo $html;
    }
    /**
     * Add option to option set
     */
    public function add(){
        $response = new JObject();
        $response->error = 0;
        $response->msg = JText::_('COM_POECOM_OPTION_SAVED');
        
        $jinput = JFactory::getApplication()->input;
        $option = new JObject();
        $optionset_id = $jinput->get('optionset_id', 0, 'INT');
        
        $option->name = $jinput->get('name', '', 'STRING');
        $option->option_sku = $jinput->get('option_sku', '', 'STRING');
        $option->option_type_id = $jinput->get('option_type_id', 0, 'INT');
        $option->price_control_id = $jinput->get('price_control_id', 0, 'INT');
        $option->class = $jinput->get('option_class', '', 'STRING');
        $option->uom_id = $jinput->get('uom_id', 0, 'INT');
        $option->detail_id = $jinput->get('detail_id', 0, 'INT');
        $option->description = $jinput->get('description', '', 'STRING');
        
        $option_idx = $jinput->get('option_idx', -1, 'INT');
        
        //create dom_element
        $optionModel = JModel::getInstance('Option', 'PoecomModel');
        
        $option->dom_element = $optionModel->createDomElement($option->name);
        
        //get existing options
        $optionSetModel = JModel::getInstance('OptionSet', 'PoecomModel');
        $data = JArrayHelper::fromObject($optionSetModel->getItem($optionset_id));
        $options = $optionSetModel->getOptions($optionset_id);
        
        if($option_idx > -1 && !empty($options[$option_idx])){
            //updating
            $options[$option_idx]->name = $option->name;
            $options[$option_idx]->option_sku = $option->option_sku;
            $options[$option_idx]->option_type_id = $option->option_type_id;
            $options[$option_idx]->price_control_id = $option->price_control_id;
            $options[$option_idx]->class = $option->class;
            $options[$option_idx]->uom_id = $option->uom_id;
            $options[$option_idx]->detail_id = $option->detail_id;
            $options[$option_idx]->description = $option->description;
        }else{
            // adding
            // set sort order       
            if($options){
                $option->sort_order = count($options)+1; 
            }else{
                $option->sort_order = 1;
                $options = array();
            }

            //add new option 
            $options[] = $option;
        }
        
        //set options
        $data['json_optionset'] = json_encode($options);
        
        //save
        if(!$optionSetModel->save($data)){
            $response->error = 1;
            $response->msg = JText::_('COM_POECOM_OPTION_NOT_SAVED'). ' : ' .$optionSetModel->getError();
        }
        
        $json_response = json_encode($response);
        
        echo $json_response;
    }
    
    public function deleteOption(){
        $response = new JObject();
        $response->error = 0;
        $response->msg = JText::_('COM_POECOM_OPTION_DELETED');
        
        $jinput = JFactory::getApplication()->input;
        
        $optionset_id = $jinput->get('optionset_id', 0, 'INT');
        $option_ids =  explode(",",$jinput->get('option_ids', '', 'STRING'));
       
        //get model
        $optionSetModel = JModel::getInstance('OptionSet', 'PoecomModel');
        
        if(!$optionSetModel->deleteOptions($optionset_id, $option_ids)){
            $response->error = 1;
            $response->msg = JText::_('COM_POECOM_OPTION_NOT_DELETED'). ' : ' .$optionSetModel->getError();
        }
        
        $json_response = json_encode($response);
        
        echo $json_response;
    }
    
    /**
     * Copy optionset to a product
     */
    public function generateOptions(){
        $response = new JObject();
        $response->error = 0;
        $response->msg = JText::_('COM_POECOM_OPTIONSET_COPIED');
        
        $jinput = JFactory::getApplication()->input;
        
        $product_id = $jinput->get('product_id', 0, 'INT');
        $id = $jinput->get('id', 0, 'INT');
        $append_options = $jinput->get('append_options', 0, 'INT');
        
        $optionSetModel = JModel::getInstance('OptionSet', 'PoecomModel');
        
        //get option set
        $options = $optionSetModel->getOptions($id);
        if(!empty($options)){
            $optionModel = JModel::getInstance('Option', 'PoecomModel');
        
            if($append_options === 0){
                //remove existing options
                $optionModel->deleteProductOptions($product_id);
                $idx = 1;
            }else{
                $idx = $optionModel->getNextSortNumber($product_id); 
            }

            
            foreach($options as $op){
                if(!$optionModel->importOption($product_id, $op, $idx)){
                    $response->error = 1;
                    $response->msg = JText::_('COM_POECOM_OPTION_NOT_IMPORTED').$optionModel->getError();
                    break;
                }
                $idx++;
            }
        }else{
            $response->error = 1;
            $response->msg = JText::_('COM_POECOM_OPTIONSET_NOT_FOUND');
        }
        
        $json_response = json_encode($response);
        
        echo $json_response;
    }
}
