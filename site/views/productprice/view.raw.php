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
 * HTML View class for the Poecom Component ProductPrice
 */
class PoecomViewProductPrice extends JView{
    
    public function getProductPrice(){
        $jinput = JFactory::getApplication()->input;
        
        $product_id = $jinput->get('product_id', 0, 'INT');
        $options = $jinput->get('options', '', 'STRING');
        $quantity = $jinput->get('quantity', 1, 'INT');
        $tax_rate = $jinput->get('product_tax_rate', 1, 'INT');
        
        $product_price = new ProductPrice($product_id, $quantity, $tax_rate);
       
        $product_price->initialize($options);
        
        $price = $product_price->calculateProductPrice(); 
     
        echo $price;
    }
}

