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
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the Product detail
 */
class PoecomViewProduct extends JView{
    
    protected $item;
    protected $state;
    
    function display($tpl = null){
        
        $params = JComponentHelper::getParams('com_poecom');
        $cart_itemid = $params->get('cartitemid', 0);
        $useHTTPS = $params->get('usehttps', 0);
       
        $this->assignRef('cart_itemid', $cart_itemid);
        $this->assignRef('useHTTPS', $useHTTPS);
        
        $this->state = $this->get('State');
        $this->params = $this->state->get('params');
        $currency_id = $this->params->get('base_currency');
        
        $model = $this->getModel('Currencies');
       
        $base_currency = $model->getItems($currency_id);
        
        // Assign data to the view
        $this->item = $this->get('Item');
        
        $serial_options = '';
        $properties = array();
        $json_properties = '';
        
        if($this->item){
            $model = $this->getModel('Options');
            $options = $model->getItems($this->item->id);
            
            if($options){
                //set the proprties
                foreach($options as $op){
                    if($op->option_type_id == 5){
                        $properties[] = array($op->name => $op->values[0]->option_label);
                    }
                }
                
                $serial_options = urldecode($this->state->get('product.serial_options'));
                
                $selected = array();
                
                if(strlen($serial_options)){
                    $tmp = explode("&", $serial_options);
                    
                    if($tmp){
                        foreach($tmp as $t){
                            $opt = explode("=", $t);
                            $value = strlen($opt[1])?$opt[1]: '';
                           
                            array_push($selected, $value); 
                        }
                    }
                    
                    if($selected){
                        // Set option values
                        $idx = 0;
                        foreach($options as $opt){
                            // Note: Serialized options will be in same order
                            $options[$idx]->selected_value = $selected[$idx];

                            $idx++;
                        }
                    }
                }
                
                $this->item->options = $options;
            }else{
                $this->item->options = array();
            }
          
            //encode properties
            if($properties){
                $json_properties = htmlentities(json_encode($properties), ENT_QUOTES );
            }else{
                $json_properties = '';
            }
            
            $this->item->currency = $base_currency;
            
            $product_price = new ProductPrice($this->item->id, $this->item->default_qty, 1);
           
            $product_price->initialize($serial_options);
            $this->item->price = $product_price->calculateProductPrice();
           
            if(!empty($this->item->show_related)){
            
                //get related products
                $rpModel = $this->getModel('RelatedProducts');

                if(!empty($this->item->related_group_id)){
                    //get related products by group
                    $related_products = $rpModel->getRelatedProductsByGroup($this->item->related_group_id, $this->item->id);
                }else{
                    //get default related products
                    $related_products = $rpModel->getRelatedProducts($this->item->id);
                }
            }else{
                $related_products = '';
            }
            $this->assignRef('related_products', $related_products);
            $this->assignRef('serial_options', $serial_options);
        }else{
            //no item - assume hacker trying direct access via inputed url
            $default_page = $params->get('defaultpage', 0);
            
            if($default_page > 0){
                $url = 'index.php?Itemid='.$default_page;
            }else{
                $url = 'index.php';
            }
            $app = JFactory::getApplication();
            $app->redirect(JRoute::_($url));
        }
        
        $jsess = JFactory::getSession();
        $cart = $jsess->get('cart', null, 'poecom');
       
        $geodata = $jsess->get('geodata', array(), 'browser_geodata');
  
        if(empty($geodata) || $geodata['block_order']){
          $app = JFactory::getApplication();
          $app->enqueueMessage(JText::_('COM_POECOM_COUNTRY_NOT_ALLOWED_ERROR'), 'info');
          $block_order = true;
          
          $rfq_email = $params->get('rfqemail', '');
        }else{
          $block_order = false;
          $rfq_email = '';
        }
        
        $this->assignRef('block_order', $block_order);
        $this->assignRef('rfqemail', $rfq_email);
        
        // Set change idx to remove fr0m cart when it loads
        // Do not remove here in case user reloads page on a change request
        if($this->state->get('cart.update')){
            $change_item_idx = $this->state->get('cart.changeitemidx');
        }else{
            $change_item_idx = -1;
        }
        $this->assignRef('json_properties', $json_properties);
        $this->assignRef('change_item_idx',$change_item_idx);
        
        $cart['lastpage'] = 'product';
       
        $jsess->set('cart', $cart, 'poecom');
        
        $this->assignRef('cart', $cart);
        
        $this->prepareDocument();
        
        // Display the view
        parent::display($tpl);
    }
    
    /**
     * Prepares the document metadata
     * 
     * Only meta data from the product record is used, which overirde default 
     * behaviour to use meta data from category, article, menu item
    */
    protected function prepareDocument(){
        //set the page browser title
        $doc = JFactory::getDocument();
        if(strlen($this->item->page_title)){
            $doc->setTitle($this->item->page_title);
        }
        //set the meta data
        if(strlen($this->item->metadesc)){
            $doc->setMetaData('description', $this->item->metadesc);
        }
        if(strlen($this->item->metakey)){
            $doc->setMetaData('keywords', $this->item->metakey);
        }
        
        //Add additional meta tag if a value has been set
        $mdata = json_decode($this->item->metadata);
        
        if($mdata){
            foreach ($mdata as $k => $v){
                if ($v){
                    $doc->setMetadata($k, $v);
                }
            }
        }
    }
}

