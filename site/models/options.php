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
 * Product Options Model
 */
class PoecomModelOptions extends JModel {

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

        // Load the application parameters.
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
     * Get Products
     * @return array $locations Array of locations objects
     */
    public function getItems($product_id = 0) {
        if (!isset($this->items)) {
            $q = $this->_db->getQuery(true);
            $q->select('*');
            $q->from('#__poe_option');
            if ($product_id > 0) {
                $q->where('product_id=' . $product_id);
            }
            $q->order('product_id, ordering');

            $this->_db->setQuery($q);

            if (!$this->items = $this->_db->loadObjectList()) {
                $this->setError($this->_db->getError());
            } else {
                if ($product_id > 0) {
                    $idx = 0;
                    foreach ($this->items as $op) {
                        
                        // Get option values
                        $q = $this->_db->getQuery(true);
                        $q->select('*');
                        $q->from('#__poe_option_value');
                        $q->where(array('option_id=' . $op->id, 'published=1'));
                        $q->order('ordering');

                        $this->_db->setQuery($q);

                        if( ($values = $this->_db->loadObjectList() )) {
                            $this->items[$idx]->values = $values;
                            $this->items[$idx]->selected_value = $values[0]->option_value;
                        }

                        $idx++;
                    }
                }
            } 
        }
        
        return $this->items;
    }

    /**
     * Get Option and Option Value display elements
     * 
     * 
     * @return object $option Option display elements
     */
    public function getOptionByDOMElement($product_id = 0, $dom_element = null, $value = 0) {
        if ($product_id > 0 && strlen($dom_element)) {
            // Get option labels

            $q = $this->_db->getQuery(true);
            $q->select('op.id, op.name, op.option_type_id, u.symbol uom');
            $q->from('#__poe_option op');
            $q->leftJoin('#__poe_uom u ON u.id=op.uom_id');
            $q->where('op.product_id=' . $product_id . ' AND op.dom_element="' . $dom_element . '" AND op.published=1');

            $this->_db->setQuery($q);

            if (($option = $this->_db->loadObject())) {

                if ($option->option_type_id == '1') {
                    // Get label for choosen select option
                    $q = $this->_db->getQuery(true);
                    $q->select('option_label');
                    $q->from('#__poe_option_value');
                    $q->where('option_id=' . $option->id . ' AND option_value="' . $value . '"');

                    $this->_db->setQuery($q);

                    if (($option_label = $this->_db->loadResult())) {
                        $option->option_label = $option_label;
                    } else {
                        $option->option_label = '';
                    }
                }
            }

            return $option;
        }
    }
    
    /**
     * Get Shipping weight and dimension modifiers for an item
     * 
     * Modifies object passed to find the total modification for an item
     * 
     * @param string $option_dom_el Option DOM Element which contains option id
     * @param string $option_value
     * @param object $modifiers
     * @return object $modifiers Shipment package modifiers for an item
     */
    public function getShippingModifiers($option_dom_el = '', $option_value = '', $modifiers = ''){
        
        //$option_dom_el in formart name_number where number is option id
        $option = explode("_",$option_dom_el);
        
        if($option){
            $db = JFactory::getDbo();
            $q = $db->getQuery(true);
            $q->select('ov.dim_modifiers,ov.wgt_modifier');
            $q->from('#__poe_option_value ov');
            $q->where('ov.option_id=' . (int) $option[1]);
            $q->where('ov.option_value='.$db->Quote($option_value));
            $db->setQuery($q);

            if( ($obj = $db->loadObject()) ){
                if(!empty($obj->dim_modifiers)){
                    $dim_mods = json_decode($obj->dim_modifiers);
                    
                    if(!empty($dim_mods->length_modifier) && $dim_mods->length_modifier != 'N' ){
                        $modifiers = $this->applyShippingMods($modifiers, 'length_modifier', 'length_modifier_value', $dim_mods->length_modifier, $dim_mods->length_modifier_uom, $modifiers->length_modifier_uom, $dim_mods->length_modifier_value);
                    }
                    if(!empty($dim_mods->width_modifier) && $dim_mods->width_modifier != 'N' ){
                        $modifiers = $this->applyShippingMods($modifiers, 'width_modifier', 'width_modifier_value', $dim_mods->width_modifier, $dim_mods->width_modifier_uom, $modifiers->width_modifier_uom, $dim_mods->width_modifier_value);
                    }
                    if(!empty($dim_mods->height_modifier) && $dim_mods->height_modifier != 'N' ){
                        $modifiers = $this->applyShippingMods($modifiers, 'height_modifier', 'height_modifier_value', $dim_mods->height_modifier, $dim_mods->height_modifier_uom, $modifiers->height_modifier_uom, $dim_mods->height_modifier_value);
                    }
                }
                if(!empty($obj->wgt_modifier)){
                    $wgt_mods = json_decode($obj->wgt_modifier);
                    if(!empty($wgt_mods->weight_modifier) && $wgt_mods->weight_modifier != 'N'){
                        $modifiers = $this->applyShippingMods($modifiers, 'weight_modifier', 'weight_modifier_value', $wgt_mods->weight_modifier, $wgt_mods->weight_modifier_uom, $modifiers->weight_modifier_uom, $wgt_mods->weight_modifier_value);
                    }
                }
            } 
        }
        
        return $modifiers;
    }
    /**
     * Update Shipping modifiers for and item with option values 
     * @param object $modifiers Object with weight and dimension modification
     * @param string $aspect Feature to update
     * @param string $aspect_value 
     * @param string $modifier Expression to apply "+" or "-"
     * @param int $uom1 Base UOM
     * @param int $uom2 Option value UOM
     * @param string $value
     */
    private function applyShippingMods($modifiers,$aspect,$aspect_value, $modifier, $uom2, $uom1, $value){
        $convert = new UOMHelper();
        //convert option modifier to base unit
        if($uom1 != $uom2){
            //from - to
            $converted_value = $convert->getConversion($uom2,$uom1, floatval($value));
        }else{
            $converted_value = floatval($value);
        }
        switch($modifier){
            case "+":
                //add
                $value = floatval($modifiers->$aspect_value) + $converted_value;
                break;
            case "-":
                //subtract
                $value = floatval($modifiers->$aspect_value) - $$converted_value;
                break;
        }
        if($value == 0){
            $modifiers->$aspect = "N";
        }else if($value > 0){
            $modifiers->$aspect = "+";
        }else{
            $modifiers->$aspect = "-";
        }
        $modifiers->$aspect_value = $value;
        
        return $modifiers;
    }

}
