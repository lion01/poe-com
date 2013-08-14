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
jimport('joomla.application.component.modelitem');

/**
 * Product Model
 */
class PoecomModelProduct extends JModelItem {

    /**
     * @var object item
     */
    protected $item;
    protected $items;

    /**
     * Method to auto-populate the model state.
     *
     * This method should only be called once per instantiation and is designed
     * to be called on the first call to the getState() method unless the model
     * configuration flag to ignore the request is set.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @return	void
     * @since	1.6
     */
    protected function populateState() {
        $app = JFactory::getApplication();
        $jinput = $app->input;
        // Get the product id
        $product_id = $jinput->get('id', null, 'int');
        $cart_change_idx = $jinput->get('changeitemidx', null, 'int');
        $this->setState('cart.changeitemidx', $cart_change_idx);

        // product id in requests originating from menus and list pages
        // chnageitemidx only originates from cart item update request
        if ($product_id > 0) {
            $id = $product_id;
            $this->setState('product.serial_options', null);
            $this->setState('cart.update', false);
        } else if ($cart_change_idx > -1) {
            $jsess = JFactory::getSession();
            $cart = $jsess->get('cart', null, 'poecom');

            if ($cart['items']) {
                $id = $cart['items'][$cart_change_idx]->product_id;
                $this->setState('product.serial_options', $cart['items'][$cart_change_idx]->serial_options);
                $this->setState('cart.update', true);
            }
        }

        $this->setState('product.id', $id);


        // Load the parameters.
        $params = $app->getParams();
        $this->setState('params', $params);
        parent::populateState();
    }

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param	type	The table type to instantiate
     * @param	string	A prefix for the table class name. Optional.
     * @param	array	Configuration array for model. Optional.
     * @return	JTable	A database object
     * @since	1.6
     */
    public function getTable($type = 'Product', $prefix = 'PoecomTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }
    
    /**
     * Get Product detail including options
     * @param type $id
     * @return type 
     */
    public function getProductDetail($id = 0){
        $db = JFactory::getDbo();
        $q =  $db->getQuery(true);
        $q->select('p.name, p.sku, p.type, p.price, p.list_description, p.max_qty');
        $q->from('#__poe_product p');
        $q->where('p.id='.(int)$id);
        $db->setQuery($q);
        
        if(($product = $db->loadObject())){
         
            //get product properties
            $q = $db->getQuery(true);
            $q->select('op.*, ov.id, ov.option_label');
            $q->from('#__poe_option op');
            $q->innerJoin('#__poe_option_value ov ON ov.option_id=op.id');
            $q->where('op.product_id='.(int)$id);
            $q->where('op.published=1');
            $q->where('op.option_type_id=5');
            $q->order('op.ordering');
            
            $db->setQuery($q);
            
            if(($properties = $db->loadObjectList())){
                $product->properties = $properties;
            }else{
                $product->properties = '';
            }
            
            //get options
            $q = $db->getQuery(true);
            $q->select('op.*');
            $q->from('#__poe_option op');
            $q->where('op.product_id='.(int)$id);
            $q->where('op.published=1');
            $q->where('op.option_type_id!=5');
            $q->order('op.ordering');
            
            $db->setQuery($q);
            
            if(($options = $db->loadObjectList())){
                $idx = 0;
                foreach($options as $op){
                    //get vlaues
                    $q = $db->getQuery(true);
                    $q->select('ov.*');
                    $q->from('#__poe_option_value ov');
                    $q->where('ov.option_id='.(int)$op->id);
                    $q->where('ov.published=1');
                    $q->order('ov.ordering');
                    
                    $db->setQuery($q);
                    
                    $options[$idx]->values = $db->loadObjectList();
                    $idx++;
                }
                $product->options = $options;
                
            }else{
                $product->options = '';
            }
            //no selection made by user
            $product->selected_options = '';
        }
        
        return $product;
    }
    /**
     * Get the product
     * @return object The message to be displayed to the user
     */
    public function getItem() {
        if (!isset($this->item)) {
            $id = $this->getState('product.id');

            $this->_db->setQuery($this->_db->getQuery(true)
                            ->select('*')
                            ->from('#__poe_product')
                            ->where('id=' . (int) $id));

            if (!$this->item = $this->_db->loadObject()) {
                $this->setError($this->_db->getError());
            } else {
                // Get tabs
                $tabs[] = array('label' => $this->item->tablabel, 'content' => $this->item->description);

                $db = $this->getDbo();
                $q = $db->getQuery(true);
                $q->select('*');
                $q->from('#__poe_product_tab');
                $q->where('product_id='.(int)$this->item->id);
                $q->where('published=1');
                $q->order('ordering');
                
                $db->setQuery($q);
                
                if (($product_tabs = $db->loadObjectList())) {
                    
                    foreach ($product_tabs as $t) {
                        $tabs[] = array('label' => $t->label, 'content' => $t->content);
                    }
                }

                $this->item->tabs = $tabs;
                
                //get product images
                $q = $this->_db->getQuery(true);
                $q->select('*');
                $q->from('#__poe_product_image');
                $q->where('product_id='.(int) $this->item->id);
                $q->where('type=1');
                $q->order('sort_order');
                $this->_db->setQuery($q);
                
                $this->item->main_images = $this->_db->loadObjectList();
            }
        }

        return $this->item;
    }

    /**
     * Get Products
     * @return array $products Array of product objects
     */
    public function getItems() {
        if (!isset($this->items)) {
            $this->_db->setQuery($this->_db->getQuery(true)
                            ->from('#__poe_product as prd')
                            ->leftJoin('#__categories as c ON prd.catid=c.id')
                            ->select('prd.*, prd.params, c.title as category'));
            if (!$this->item = $this->_db->loadObjectList()) {
                $this->setError($this->_db->getError());
            }
        }
        return $this->items;
    }

    /**
     * Get Product Shipping Info
     * e.g. length, width, height, weight
     * 
     * @param int $product_id
     * 
     * @return object/boolean false Object of shipping info for a product
     */
    public function getShippingInfo($product_id = 0) {
        $db = $this->getDbo();
        $q = $db->getQuery(true);
        $q->select('id,name,sku,weight,weightuom,length,width,height,dimuom');
        $q->from('#__poe_product');
        $q->where('id=' . $db->Quote($product_id));

        $db->setQuery($q);
        $shipping_info = $db->loadObject();
        
        if($shipping_info){
            //get adjustments
            
        }
        
        return $shipping_info;
    }
    
    /**
     * Get Product List Description
     * 
     * @param int $id Product Id
     * @return string $list_desc
     */
    public function getListDescription($id = 0){
        $db = $this->getDbo();
        $q = $db->getQuery(true);
        $q->select('list_description');
        $q->from('#__poe_product');
        $q->where('id=' . (int)($id));

        $db->setQuery($q);
        $list_desc = $db->loadResult();
       
        return $list_desc;
    }
}
