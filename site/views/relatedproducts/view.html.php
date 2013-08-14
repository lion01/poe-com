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
 * */
jimport('joomla.application.component.view');

/**
 * HTML View class for Related Products
 */
class PoecomViewRelatedProducts extends JView {

    // Overwriting JView display method
    public function display($tpl = null) {
        $jsess = JFactory::getSession();
        $cart = $jsess->get('cart', array(), 'poecom');
      
        $jinput = JFactory::getApplication()->input;
        $rpModel = $this->getModel('RelatedProducts');
        $rp = array();
        
        $related_group_id = $jinput->get('related_group_id', 0, 'INT');
        $product_id = $jinput->get('product_id', 0, 'INT');

        if($related_group_id > 0 && $product_id > 0){
            $rp = $rpModel->getRelatedProductsByGroup($related_group_id , $product_id );
        }else if ($product_id > 0){
            $rp = $rpModel->getRelatedProducts($product_id );
        }
        
        if($cart['currency'][0]){
            $currency_symbol = $cart['currency'][0]->symbol;
        }else{
            $currency_symbol = "&#36;";
        }
        
        $script = JURI::root().'components/com_poecom/models/forms/related.products.js';
        
        $this->assignRef('rp', $rp);
        $this->assignRef('currency_symbol', $currency_symbol);
        $this->assignRef('script', $script);
        

        // Assign data to the view
        $this->prepareDocument();

        // Display the view
        parent::display($tpl);
    }

    /**
     * Prepares the document.
     *
     * @since	1.6
     */
    protected function prepareDocument() {
        //set dialog strings
        JText::script('COM_POECOM_ITEM_ADDED');
        JText::script('COM_POECOM_ITEM_NOT_ADDED');
        JText::script('COM_POECOM_CHECKOUT');
        JText::script('COM_POECOM_ADD_MORE');
        JText::script('COM_POECOM_CLOSE');
    }
}

