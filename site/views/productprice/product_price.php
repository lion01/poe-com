<?php
defined('_JEXEC') or die('Restricted Access');
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

//jimport('joomla.application.component.model');
/**
 * Product Detail
 */
class ProductPrice{
    public $product_id = 0;
    public $product;
    public $selected_options = array();
    public $options = array();
    public $use_QP = false;
    public $use_OQP = false;
    public $use_LP = false;
    public $use_AP = false;
    public $use_VP = false;
    public $order_qty = 1;
    public $option_qty = array();
    public $image_groups = array();
    public $images = array();
    public $tabs = array();
    
    public function __construct($product_id = 0, $quantity = 1, $tax_rate = 1){
        $this->product = new JObject();
        $this->product_id = $product_id;
        $this->quantity = $quantity;
        $this->tax_rate = $tax_rate; 
    }
    
    public function initialize($selected_options = null){
        
        if($this->product_id > 0){
            $this->setProduct(); // price config will be field in product
          //  $this->setPriceConfig();
            $this->setSelectedOptions($selected_options);
            // May not need to do this
            $this->setProductOptions();
            $this->setOptionValues();
            
            
        }else{
            return false;
        } 
    }
    
    /**
     * Error Method for DB queries
     * 
     */
    private function setError($db_error){
        // TODO: db error handling (if needed)
        
        
        return true;
    }
   
    /**
     * Get Product Base Price Detail
     * 
     * @param int $product_id Product ID
     * 
     * @return object/boolean $product Product obejct or false
     */
    protected function setProduct(){
        $db = JFactory::getDBO();
        $db->setQuery($db->getQuery(true)
                ->select('price,default_qty,max_qty,price_config')
                ->from('#__poe_product')
                ->where('id=' . (int)$this->product_id));
			
        if (!$this->product = $db->loadObject()){
                $this->setError($db->getError());
        }else{
            $tmp = explode(",", $this->product->price_config);
            
            if($tmp){
                $this->product->price_config = $tmp;
            }else{
                $this->product->price_config = array();
            }
            
            $this->setPriceConfig();
        }
    }

    /**
     * Price Calculation control
     * 
     * Determines which pricing options are used. 
     * Reduces unnessary function calls.
     * 
     * Sets control for class instance
     * 
     */
    private function setPriceConfig(){
        
        // Either FALSE or valid array
        if(!$this->product->price_config){
            $price_config = array("0","0","0","0","0");
        }else{
            $price_config = $this->product->price_config;
        }
       
        $this->use_QP = $price_config[0] == '1'? true : false;
        $this->use_OQP = $price_config[1] == '1'? true : false;
        $this->use_LP = $price_config[2] == '1'? true : false;
        $this->use_AP = $price_config[3] == '1'? true : false;
        $this->use_VP = $price_config[4] == '1'? true : false;
      
    }
    
    
    /**
     * Set the Selected Options 
     * 
     * Options selected in the form
     * 
     * @param string $selected_options Formated string
     * 
     */
    private function setSelectedOptions($selected_options = ''){
        if(strlen($selected_options)){
            
            $tmp = explode("&", $selected_options);
            
            if($tmp){
                foreach($tmp as $op){
                    $option = new JObject;
                    $tmp_val = explode("=", $op);
                    $option->dom_element = $tmp_val[0];
                    $option->value = $tmp_val[1];
                    
                    $this->selected_options[] = $option;
                    
                }
            }
        }
    }
    
    
    /**
     * Set product Options and Values
     * 
     * @param int $product_id
     * 
     * @return object/boolean Options object 
     */
    protected function setProductOptions(){
        
        if($this->product_id == 0){
            return false;
        }else{
            // Get option that affect price e.g. price_control_id = 1
            $db = JFactory::getDBO();
            $db->setQuery($db->getQuery(true)
                    ->select('id, dom_element, option_type_id, price_control_id, uom_id')
                    ->from('#__poe_option')
                    ->where('product_id=' . (int)$this->product_id, 'published=1')
                    ->order('ordering'));
    			
            if ($this->options = $db->loadObjectList()){
    	
                $idx = 0;
                // Get the selected value and modifiers
                foreach($this->options as $op){
                    $db->setQuery($db->getQuery(true)
                        ->select('id,option_value,modifier,modifier_value,option_id')
        				->from('#__poe_option_value')
        				->where('option_id=' . (int)$op->id, 'published=1')
                        ->order('ordering'));
                    if ($values = $db->loadObjectList()){
                        // Assign values to options array
                        $this->options[$idx]->values = $values;	
            		}else if(strlen($db->getErrorMsg())){
                        $this->setError($db->getErrorMsg());
            		}
                    $idx++;
                }
    		}else if(strlen($db->getErrorMsg()) ){
                $this->setError($db->getErrorMsg());
    		}
        }
    }
    
     /**
     * Set Option Values
     * 
     * This function will typically be called from cart update to set the
     * selected options before calculateProductPrice()
     * 
     * @param string $serial_options Serialized array of selected options
     * 
     * @return boolean True = Update, False = Failed
     */
    public function setOptionValues($serial_options = ''){
        // recreate array(array(name,value), array(name,value) ) 
        // May go back to this approach
       // $option_values = unserialize($serial_options);
        
        $option_values = $this->selected_options;
       
        if(!empty($this->options) && !empty($option_values)){
            
            $idx = 0;
            foreach($this->options as $op){
                $found_option = false;
                // Find $op->values index that matches $opv[1] 
                // Start by finding option name match
                foreach($option_values as $opv){
                 
                    if($op->dom_element == $opv->dom_element && !$found_option){
                        $found_option = true;
                        // option name = selection name
                        $val_idx = 0;
                        foreach($op->values as $val){
                         
                            if($op->option_type_id == 1 && $val->option_value == $opv->value){
                                // Option type Select
                                // option_value = selection 
                                $this->options[$idx]->selected = $val_idx;
                            }elseif($op->option_type_id == 2 || $op->option_type_id == 3 ){
                                // Option Type inputsize or inputqty
                                
                                // values index is always "0" set input_value
                                $this->options[$idx]->selected = 0;
                                
                                // Check constraints and adjust selection
                                $input_value = $this->checkInputConstraints($op->id, $opv->value);
                                
                                $this->options[$idx]->input_value = $input_value;
                            }
                            $val_idx++;
                        }
                    }
                }
                
                $idx++;                                
            }
        }else if(!empty($this->options)){
            // Set default values base on sort_order and constraints
            $idx=0;
            foreach($this->options as $op){
                //if($op->option_type_id == 1 && $val->id == $opv->value){
                if($op->option_type_id == 1 ){
                    // Option type Select
                    // option_value = selection 
                    $this->options[$idx]->selected = 0;
                }elseif($op->option_type_id == 2 || $op->option_type_id == 3 ){
                    // Option Type inputsize or inputqty
                    // values index is always "0" set input_value
                    $this->options[$idx]->selected = 0;
                    
                    // Check constraints and adjust selection
                    $input_value = $this->checkInputConstraints($op->id, '');
                    
                    $this->options[$idx]->input_value = $input_value;
                }
                $idx++;
            }
        }
        return true; 
    }
    

    
    /**
     * Control function For Price Calculation
     * 
     * Important: This method returns the price per unit, totaling should
     * handled separately.
     * 
     * @param float $product_tax_rate Tax rate to apply to the calculated price
     * 
     * @return float $formatted price Price to fixed number of decminal places
     * 
     */
    public function calculateProductPrice($product_tax_rate = 1){
        
        $price = $this->product->price;
        
        // Step 1 - Get base price by quantity ordered
        if($this->use_QP){
            $price += $this->getBasePriceByQty();
        }
        
        // Step 2 - Apply modifiers for base price by option qty (input options)
        if($this->use_OQP){
            $price += $this->getOptionQtyModifiers();
        }
        
        // Step 3 - Apply base price by price level
        if($this->use_LP){
            $price += $this->getBasePriceByLevel();
        }
        
        // Step 4 - Apply base price by area
        if($this->use_AP){
            $price += $this->getBasePriceByArea();
        }
        
        // Step 5 - Apply base price by volume
        if($this->use_VP){
            $price += $this->getBasePriceByVolume();
        }
        
        // Step 6 - Apply option modifiers
        $price += $this->getOptionModifiers();
        
        if( $product_tax_rate > 1 ) {
            
            $price = round( ($price * $product_tax_rate), 2 );
        }

        $formatted_price = number_format($price, 2);
        
        return $formatted_price;
    }
    
    /**
     * Get Base Price by Quantity
     * 
     * Pirce levels by quantity ordered, default quantity is 1
     *
     * @return float $price Product Base Price
     */
    public function getBasePriceByQty(){
        
        $db = JFactory::getDBO();
        
        // Get Base Price by Order Quantity
        $q = "SELECT price FROM ".$db->nameQuote('#__poe_base_price_qty').
            " WHERE product_id=".$db->Quote($this->product_id)." AND published=1".
            " AND from_qty <= ".$db->Quote($this->order_qty).
            " AND (to_qty >= ".$db->Quote($this->order_qty)." || to_qty=0)".
            " AND qty_type=".$db->Quote('order');
   
        $db->setQuery($q);
        
        if(!$price = $db->loadResult()){
           $price = 0; 
        };
        return $price;       
    }
    
    /**
     * Get Price Modifiers for Option Quantity
     * 
     * Price levels by options ordered. This function should only be
     * called when price by option quantity set, which is controlled by price_config
     * when prices are entered.
     * 
     * Prices added here override prices entered in the option modifier
     *
     * @return float $price Product Base Price
     */
    public function getOptionQtyModifiers(){
        $price = 0;
        if(!empty($this->options)){
            $db = JFactory::getDBO();
            $idx = 0;
            
            // find options of type inputqty
            foreach($this->options as $op){
                if($op->option_type_id == 2){//'inputqty'
                    $q = "SELECT ".$db->nameQuote('price'). " FROM ".$db->nameQuote('#__poe_base_price_qty').
                        " WHERE ".$db->nameQuote('product_id')."=".$db->quote($this->product_id).
                        " AND ".$db->nameQuote('product_option')."=".$db->quote($op->id).
                        " AND from_qty <= ".$db->Quote($op->input_value).
                        " AND (to_qty >= ".$db->Quote($op->input_value)." || to_qty=0)";
                    $db->setQuery($q);
                    
                    if($option_price = $db->loadResult()){
                        // Add to overirde array to bypass option modifier
                        $this->option_qty[$idx] = $op->id;
                        $idx++;
                        
                        $price += floatval($option_price * $op->input_value);
                    }
                }
                
                
            }
        }
        return $price;
    }
    
    /**
     * Get Base Price by Level
     * 
     * This function should only be called when price levels exist based
     * on price_config setting.
     * 
     * Levels are based on a grid where price is set "upto" x and y dimensions.
     * Levels should be set to match appicable constraints.
     * 
     * @return float $price Prices for level entered
     */
    public function getBasePriceByLevel(){
        $price = 0;
        if(!empty($this->options)){
            $db = JFactory::getDBO();
            
            $limit_x = 0;
            $limit_y = 0;
            $found_level = false;
            
            // Get the price levels and store in array
            $q = "SELECT pl.*, xref.limit_x_option_id opt_x, xref.limit_y_option_id opt_y FROM ".$db->nameQuote('#__poe_base_price_level'). " pl".
                " INNER JOIN ".$db->nameQuote('#__poe_base_price_xref'). " xref ON ".$db->nameQuote('xref.product_id')."=".$db->nameQuote('pl.product_id').
                " AND ".$db->nameQuote('xref.type')."=".$db->quote('level').
                " WHERE ".$db->nameQuote('pl.product_id')."=".$db->quote($this->product_id).
                " AND ".$db->nameQuote('pl.published')."=".$db->quote('1').
                " ORDER BY ".$db->nameQuote('pl.limit_x');
            $db->setQuery($q);
            
            $levels = $db->loadObjectList();

            if($levels){
                // Get values entered for limit_x and limit_y
                foreach($this->options as $op){
                    foreach($levels as $lev){
                        if($op->id == $lev->opt_x){
                            // Found limit x
                            $limit_x = $op->input_value; // should be type inputsize
                        }
                        
                        if($op->id == $lev->opt_y){
                            // Found limit x
                            $limit_y = $op->input_value; // should be type inputsize
                        }
                    }
                }
                
                // Find the price level
                foreach($levels as $lev){
                    if(!$found_level && $lev->limit_x >= $limit_x && $lev->limit_y >= $limit_y){
                        $price += floatval($lev->price);
                        
                        $found_level = true;
                    }
                }
            }
        }
        return $price;
    }
    
    /**
     * Get Base Price by Area
     * 
     * The price returned is a price per square unit, where unit is determined by the option inputsize
     * type values for x , y dimensions. The square unit is calculated, which is then multipled by the price
     * for a sq unit range. 
     * 
     * @return float $price Prices for calculated area limit_x X limit_y
     */
    public function getBasePriceByArea(){
        $price = 0;
        if(!empty($this->options)){
            $db = JFactory::getDBO();
            
            $limit_x = 0;
            $limit_y = 0;
            $area = 0;
            $found_level = false;
            
            // Get the limit_x and limit_y ids
            $q = "SELECT " .$db->nameQuote('id').",".$db->nameQuote('limit_x_option_id')." opt_x,".$db->nameQuote('limit_y_option_id'). " opt_y".
                " FROM ".$db->nameQuote('#__poe_base_price_xref').
                " WHERE ".$db->nameQuote('product_id')."=".$db->quote($this->product_id).
                " AND ".$db->nameQuote('type')."=".$db->quote('area');
            
            $db->setQuery($q);
            
            $limits = $db->loadObject();
         
            if($limits){
                // Get the input values
                 foreach($this->options as $op){
                    
                    if($op->id == $limits->opt_x){
                        // Found limit x
                        $limit_x = $op->input_value; // should be type inputsize
                        
                        // set input uom
                        if(!isset($limit_uom_id)){
                            $limit_uom_id = $op->uom_id;
                        }
                    }
                        
                    if($op->id == $limits->opt_y){
                        // Found limit x
                        $limit_y = $op->input_value; // should be type inputsize
                    }
                 }
                 
                 // Get the pricing UOM from first price
                 $q = "SELECT bp.price_uom_id, uom.length_uom_id  FROM ".$db->nameQuote('#__poe_base_price_area'). 'bp'.
                        " INNER JOIN ".$db->nameQuote('#__poe_uom'). 'uom ON '.$db->nameQuote('bp.price_uom_id')."=".$db->nameQuote('uom.id').
                        " WHERE ".$db->nameQuote('bp.product_id')."=".$db->quote($this->product_id).
                        " AND ".$db->nameQuote('bp.xref_id')."=".$db->quote($limits->id).
                        " AND ".$db->nameQuote('bp.published')."=".$db->quote('1').
                        " AND ".$db->nameQuote('bp.from_qty')."=".$db->quote('1');
                
                 $db->setQuery($q);
                 
                 $price_uom = $db->loadObject();
                 
                 $factor = 1;
            
                 if($price_uom->length_uom_id != $limit_uom_id){
                    // Get conversion factor
                    $q = "SELECT ".$db->nameQuote('factor'). " FROM ".$db->nameQuote('#__poe_uom_conversion').
                        " WHERE ".$db->nameQuote('uom_1')."=".$price_uom->length_uom_id." AND ".$db->nameQuote('uom_2')."=".$limit_uom_id;
  
                    $db->setQuery($q);
                    
                    if(!$factor = floatval($db->loadResult())){
                        $factor = 1;
                    }
                 }
              
                 // Factor = value to convert 1 unit of uom_1 to uom_2
                 if($factor > 1){
                    $factor = floatval(1/$factor);
                 }
                 
                 $area = ($limit_x * $factor) * ($limit_y * $factor);
                 
                 if($area > 0 && $area < 1){
                    $area = 1;
                 }
                 
                 if($area > 0){
                    // Get the price for calculated area
                    $q = "SELECT price FROM ".$db->nameQuote('#__poe_base_price_area').
                        " WHERE ".$db->nameQuote('product_id')."=".$db->quote($this->product_id).
                        " AND ".$db->nameQuote('xref_id')."=".$db->quote($limits->id).
                        " AND ".$db->nameQuote('published')."=".$db->quote('1').
                        " AND ".$db->nameQuote('from_qty')." <= ".$db->Quote($area).
                        " AND (".$db->nameQuote('to_qty')." >= ".$db->Quote($area)." || ".$db->nameQuote('to_qty')."=0)";
                   
                    $db->setQuery($q);
                    
                    if($result = $db->loadResult()){
                        $price =  floatval($result) * $area;
                    }
                }
            }
        }
        return $price;
    }
    
    
    /**
     * Get Base Price by Volume
     * 
     * The price returned is a price per cubic unit, where unit is determined by the option inputsize
     * type values for x , y, z dimensions. The unit volume is calculated, which is then multipled by the price
     * for a cubic unit range. 
     * 
     * @return float $price Prices for calculated volume limit_x, limit_y, limit_z
     */
    public function getBasePriceByVolume(){
        $price = 0;
        if(!empty($this->options)){
            $db = JFactory::getDBO();
            $control_option_id = 0;
            $option_value = '';
            $limit_x = 0;
            $limit_y = 0;
            $limit_y = 0;
            $volume = 0;
            $found_level = false;
            $xref = array();
            $idx = 0;
            
            // Find the base price by volume
            foreach($this->options as $opt){
                // Only check select options
                if($opt->option_type_id == 1){ //'select'
                    // Check base price exists for specif option/value pair
                    $q = "SELECT * FROM ".$db->nameQuote('#__poe_base_price_xref').
                        " WHERE ".$db->nameQuote('product_id')."=".$db->Quote($this->product_id).
                        " AND ".$db->nameQuote('type')."=".$db->Quote('volume').
                        " AND ".$db->nameQuote('option_id')."=".$db->Quote($opt->id).
                        " AND ".$db->nameQuote('option_value')."=".$db->Quote($opt->values[$opt->selected]->option_value);
                    $db->setQuery($q);
                    
                    if($db->loadObject()){
                        $xref[$idx] = $db->loadObject();
                        $idx++;
                    }
                    
                }
            }
            
            // Check for xref where option / value pair not specified
            if(empty($xref)){
                $q = "SELECT * FROM ".$db->nameQuote('#__poe_base_price_xref').
                        " WHERE ".$db->nameQuote('product_id')."=".$db->Quote($this->product_id).
                        " AND ".$db->nameQuote('type')."=".$db->Quote('volume').
                        " AND ".$db->nameQuote('option_id')."=".$db->Quote(0);
                    $db->setQuery($q);
                    
                    if($db->loadObject()){
                        $xref[$idx] = $db->loadObject();
                        $idx++;
                    }
            }
            
            if($xref){
                // Only calculate volume price if there is an xref
                foreach($xref as $x){
                    // Get the base price foreach xref
                    // Calculate volume
                    $length = 0;
                    $width = 0;
                    $height = 0;
                    $uom_id = '';
                    $ui_uom_id = 0;
                    
                    foreach($this->options as $opt){
                        //TODO: make uomselect option type, in the mean time...
                        if($opt->dom_element == 'uom'){
                           // echo 'found uom '. $opt->values[$opt->selected]->option_value;
                            $ui_uom_id = $opt->values[$opt->selected]->option_value;
                        }
                        
                        if($opt->option_type_id == 3){ //'inputsize'
                            if($opt->id == $x->limit_x_option_id){
                                //echo 'found length';
                                $length = $opt->input_value;
                                
                                $uom_id = $opt->uom_id;
                            }elseif($opt->id == $x->limit_y_option_id){
                                $width = $opt->input_value;
                            }elseif($opt->id == $x->limit_z_option_id){
                                $height = $opt->input_value;
                            }
                        }
                        
                    }
                    
                    // Set UI uom override if found
                    if($ui_uom_id > 0){
                        $uom_id = $ui_uom_id;
                    }
                    
                    $volume = $length * $width * $height;
                   
                    // Find base price level
                    $q = "SELECT bp.*, uom.length_uom_id  FROM ".$db->nameQuote('#__poe_base_price_volume'). 'bp'.
                        " INNER JOIN ".$db->nameQuote('#__poe_uom'). 'uom ON '.$db->nameQuote('bp.price_uom_id')."=".$db->nameQuote('uom.id').
                        " WHERE ".$db->nameQuote('bp.product_id')."=".$db->quote($this->product_id).
                        " AND ".$db->nameQuote('bp.xref_id')."=".$db->quote($x->id).
                        " AND ".$db->nameQuote('bp.published')."=".$db->quote('1');
                
                     $db->setQuery($q);
                     
                     if($base_prices = $db->loadObjectList()){
                        // check for base unit conversion
                        foreach($base_prices as $bp){
                            
                            if($bp->length_uom_id != $uom_id){
                                // convert volume
                                // Get conversion factor
                                $q = "SELECT ".$db->nameQuote('factor'). " FROM ".$db->nameQuote('#__poe_uom_conversion').
                                    " WHERE ".$db->nameQuote('uom_1')."=".$uom_id." AND ".$db->nameQuote('uom_2')."=".$bp->length_uom_id;
              
                                $db->setQuery($q);
                                
                                if(!$factor = floatval($db->loadResult())){
                                    $factor = 1;
                                }
                              
                                $volume = ($length * $factor) * ($width * $factor) * ($height * $factor);
                            }
                            
                            // check from/to range
                            if($volume >= $bp->from_qty && ($volume <= $bp->to_qty || $bp->to_qty == 0)){
                                $price += $volume * $bp->price;
                                break;
                            }
                        }
                     }
                }
            }
        }
        return $price;
    }
    
    /**
     * Get Option Price Modifiers
     * 
     * Price modifiers for seelct and input options
     * 
     * @return float $price The total value of all modifiers use to adjust base price
     * 
     */
    protected function getOptionModifiers(){
        $price = 0;
        $count_mods = 0;
        $found_equals = false;
        $equals_price = 0;
        
        if(!empty($this->options)){
            foreach($this->options as $op){
               
                if($op->option_type_id == 1 || $op->option_type_id == 2){  
                    // Option type select or inputqty
                    // Set default modifiers for selected options
                    if($op->values[$op->selected]->modifier != 'N' && $op->option_type_id != 3 ){
                        // Not option type inputsize
                        $count_mods++;
                        /**
                         * In this calculation we do not handle "A" (Area)
                         * 
                         * Modifier symbols
                         * N : none
                         * A : Area (inputsize type only)
                         * + : Each (inputqty type only)
                         * = : Equals
                         * + : Add Amount
                         * - : Subtract Amount
                         * +% : Add Percentage
                         * -% : Substract Percentage
                         */
                         switch($op->values[$op->selected]->modifier){
                            case '+':
                                if($op->option_type_id == 2){//'inputqty'
                                    // Check for option qty prices
                                    if(!in_array($op->id, $this->option_qty)){
                                        // values index always zero
                                        $price += floatval($op->values[0]->modifier_value * $op->input_value);
                                    }
                                }else{
                                    $price += floatval($op->values[$op->selected]->modifier_value);
                                }
                                
                                break;
                            case '-':
                                $price -= floatval($op->values[$op->selected]->modifier_value);
                                break;
                            case '=':
                                $found_equals = true;
                                $equals_price = floatval($op->values[$op->selected]->modifier_value);
                                break;
                            case '+%':
                                break;
                            case '-%':
                                break;
                            default:
                                break;
                         }
                    }
                }
            }
        }
        
        if($count_mods == 1 && $found_equals){
            // There is only one price modifier, use equals_price
            $price = $equals_price;
        }
     
        // make sure price is not negative
        $price = $price > 0 ? $price : 0;
        
        return $price;
    }
    
        /**
     * Create detail link for Product Options
     * 
     * Build JavaScript for <a>
     * 
     * @param int $detail_id Product Option Detail
     * 
     * @return string $html HTML to insert into <a> tag
     */
     protected function createDetailLink($id){
        $db = JFactory::getDBO();
        $q = "SELECT * FROM ".$db->nameQuote('#__poe_detail').
            " WHERE ".$db->nameQuote('id')."=".$db->Quote($id);
        $db->setQuery($q);
        
        $det = $db->loadObject();
            
        if($det){
            $html = '<div>';
            $link = '';
            
            // Check for article id
            if($det->article_id > 0 && !strlen($det->url)){
                // Link to article
                $link = 'index2.php?option=com_content&amp;task=view&amp;id='.$det->article_id;
            }elseif(strlen($det->url)){
                $link = urldecode($det->url);
            }             
           
            // SEF link if needed
        	if( class_exists('jroute')) {
        		$link = JRoute::_($link);
        	} else {
        		$link =  sefRelToAbs( $link );
        	}
        
            switch($det->window_type){
                case 'rokbox':
                    $html .= '<a href="'.$link.'" rel="rokbox['.$det->width.' '.$det->height.']">'.$det->link_label.'</a>';
                    break;
                case 'modal':
                    JHTML::_('behavior.modal');
                   // $html .= '{modal url='.$link.'|width='.$det->width.'|height='.$det->height.'}'.$det->link_label.'{/modal}';
                    $html .= ' <a class="modal" id="modalDetail" rel="{handler: \'iframe\', size: {x: '.$det->width.', y: '.$det->height.'}}" 
                            href="index.php?option=com_productoptions&controller=details&task=displaymodal&tmpl=component&link='.urlencode($link).'" target="_blank" >
                            '.$det->link_label.'</a>';
                    break;
                default:
                    $html .= vmPopupLink( $link,$det->link_label ,$det->width,$det->height, $det->detail_name );
                    break;
            }
            
            $html .='</div>';
            
            return $html;
        }else{
            return false;
        }
     }
     
     /**
      * Check Option Constraints for Input Value
      * 
      * If entered value is outside constraints the min/max 
      * contraint value is returned.
      * 
      * Note: inputqty and inputsize option types only
      * 
      * @param int $id Option ID
      * @param int $value Input value for option
      * 
      * @return int Either $value or Option mix/max
      */
     public function checkInputConstraints($id, $value){
        if($id > 0 && $value > 0){
            $db = JFactory::getDBO();
            $q = "SELECT * FROM ".$db->nameQuote('#__poe_constraint'). 
            " WHERE ".$db->nameQuote('option_id')."=".$db->Quote($id);
            $db->setQuery($q);
            
            $con = $db->loadObjectList();
          
            if($con){
                foreach($con as $c){
                    $limit = intval($c->limit_value);
                    switch($c->expression_id){
                        case '1': //Equal To
                            if($value < $limit ){
                                $value = $limit;
                            }
                            break;
                        case '2': //Not Equal
                            if($value < $limit ){
                                $value = $limit;
                            }
                            break;
                        case '3': //Min
                            if($value < $limit ){
                                $value = $limit;
                            }
                            break;
                        case '4': //Max
                            if($value > $limit ){
                                $value = $limit;
                            }
                            break;
                        case '5': //Great Than
                            if($value <= $limit ){
                                $value = $limit;
                            }
                            break;
                        case '6': //Less Than
                            if($value >= $limit ){
                                $value = $limit;
                            }
                            break;
                        default:
                            break;
                    }
                    
                }
            }
        }
        return $value;
     }
     
     /** 
      * Get Option Min Constraint Value
      * 
      * @param int $id Option ID
      * 
      * @return int $input_min_value Min value for option
      */
     public function getMinConstraintValue($id = 0){
        if($id > 0){
            $db = JFactory::getDBO();
            $q = "SELECT ".$db->nameQuote('limit_value')." FROM ".$db->nameQuote('#__poe_constraint'). 
            " WHERE ".$db->nameQuote('option_id')."=".$db->Quote($id)." AND ".$db->nameQuote('expression_id')."=".$db->Quote('3');
            $db->setQuery($q);
            
            if(!$input_min_value = $db->loadResult()){
                $input_min_value = 0;
            }
        }else{
            $input_min_value = 0;
        }
        
        return $input_min_value;
     }
    
    /**
      * Create string for option comparision
      * 
      * Used to determine if product configuration already in cart
      * and controls add or update. Pulls options and value from $d POST array
      * 
      * @param array $d Post variables for options
      * 
      * @return string $html String of options and values to display.
      */
     function getOptionsString($d){
        $option_cfg = array();
        
        if(!empty($d) && !empty($this->options)){
            $idx = 0;
            foreach($this->options as $op){
                
                foreach($d as $k=>$v){
                    if($op->name == $k){
                        $option_cfg[$idx] = array($op->name, $v);
                    }
                }
                
                $idx++;
            }
        }
        
        $str = serialize($option_cfg);
        
        return $str;
     }
     
     /**
      * Get Product Tabs
      * 
      */
     public function getProductTabs(){
        return $this->tabs;
     }
    
    /**
     * Set Product Tabs
     * 
     * @param int $product_id Fetch tab content for this product id
     * 
     * @return array $tabs Either -1 (no reault) or array of Product Tabs
     */
    protected function setProductTabs(){
        $db = JFactory::getDBO();
        $q = "SELECT * FROM ".$db->nameQuote('#__poe_tabs').
            " WHERE ".$db->nameQuote('product_id')."=".$db->Quote($this->product_id).
            " AND ".$db->nameQuote('published')."=".$db->Quote(1).
            " ORDER BY ".$db->nameQuote('sort_order');
        
        $db->setQuery($q);
        $this->tabs = $db->loadAssocList();
    }
    
    /**
     * Get Product Image Groups
     * 
     */
    public function getProductImageGroups(){
        return $this->image_groups;
    }
    
    /**
     * Set Product Image Groups
     * 
     * Note: Specific fields select (in specifc order) to use by JS array
     * 
     * imageGroups[0][0] = '4';             // Group ID
     * imageGroups[0][1] = 'product_image'; // Image Class
     * imageGroups[0][2] = 'prod_image';    // Image DIV ID
     * imageGroups[0][3] = 'nav_prod';      // Navigation Class
     * imageGroups[0][4] = 'prod_nav';      // Navigation DIV ID
     * imageGroups[0][5] = '50px';          // Navigation Left / Right Padding
     * imageGroups[0][6] = '52px';          // Navigation Top / Bottom Padding
     * imageGroups[0][7] = '10';            // Maximum Navigation Images to Display
     * imageGroups[0][8] = 'scrollLeft';    // Transition
     * imageGroups[0][9] = '500px';         // Default Image Width
     * imageGroups[0][10] = '200px';        // Default Image Height
     * imageGroups[0][11] = 'Option Value'; // Product options linked to this group (using "name" not "id")
     *
     * 
     * @param int $product_id
     * 
     * @return array $groups Array of Associate Array containing Image Group properties
     */
    public function setProductImageGroups(){
        $db = $this->getDBO();
        $q = "SELECT ".$db->nameQuote('g.id').",".
                $db->nameQuote('g.image_class').",".
                $db->nameQuote('g.image_div_id').",".
                $db->nameQuote('g.nav_class').",".
                $db->nameQuote('g.nav_div_id').",".
                $db->nameQuote('g.nav_lr_padding').",".
                $db->nameQuote('g.nav_tb_padding').",".
                $db->nameQuote('g.nav_max').",".
                $db->nameQuote('g.transition').",".
                $db->nameQuote('g.width').",".
                $db->nameQuote('g.height').",".
                $db->nameQuote('op.dom_element').
                " FROM ".$db->nameQuote('#__poe_image_group'). " g".
             " INNER JOIN ".$db->nameQuote('#__poe_option')." op ON ".$db->nameQuote('op.id')."=".$db->nameQuote('g.control_option').
             " WHERE ".$db->nameQuote('g.product_id')."=".$db->quote($this->product_id).
             " AND ".$db->nameQuote('g.published')."=".$db->quote('1').
             " ORDER BY ".$db->nameQuote('g.image_class'); //TODO: sort by value needed for zoom icon
        
        $db->setQuery($q);
        
        $this->image_groups = $db->loadAssocList();
    }
    
    /**
     * Get Product Images
     * 
     */
    public function getProductImages(){
        return $this->images;
    }
    
    
    /**
     * Set product images that are linked to attributes for a product_id
     * 
     * Used to create multi-dimensional array like:
     * productImages[0][0] = '4'                                            // Image Group ID
     * productImages[0][1] = 'product/Light_Oak_Mid_Oa_4d6f478ca37fe.jpg';  // Main image
     * productImages[0][2] = 'product/Light_Oak_Mid_Oa_4d6f478cacc69.jpg';  // Enlarge image
     * productImages[0][3] = 'product/nav/pole_light_nav_35x100.jpg';       // Nav image
     * productImages[0][4] = 'Colour|Light;';                               // Control Attributes
     * productImages[0][5] = 'Image Title';                                 // Main image title
     * productImages[0][6] = 'Image Lg Title';                              // Enlarge image title
     * productImages[0][7] = 'Image Nav Title';                             // Nav image title
     * 
     * @param int $product_id 
     * 
     * @return array $product_images List of product image arrays
     */
    protected function setProductImages(){
        $dbo = JFactory::getDBO();
        
        $q = "SELECT pir.group_id, pir.image_file, pir.image_file_lg, pir.image_file_nav, pir.option_link,
        pir.image_title, pir.image_lg_title, pir.image_nav_title, pir.image_lg_caption FROM ".$dbo->nameQuote('#__poe_image_relationship'). " pir".
            " INNER JOIN ".$dbo->nameQuote('#__poe_image_group'). ' pig WHERE pir.group_id=pig.id AND pig.product_id='.$dbo->Quote($this->product_id). " AND pir.published=1";
        $dbo->setQuery($q);
        
        $this->images = $dbo->loadAssocList();
    }
}
?>