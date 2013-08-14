<?php

defined('_JEXEC') or die('Restricted access');
/**
 * POE-com - Site - Shipping Model Class
 * 
 * @package com_poecom
 * @author Micah Fletcher
 * @copyright 2010 - 2012 (C) Extensible Point Solutions Inc. All Right Reserved.
 * @license GNU GPLv3, http://www.gnu.org/licenses/gpl.html
 *
 * @since 2.0 - 3/11/2012 10:25:34 PM
 *
 * http://www.exps.ca
 * */
jimport('joomla.application.component.modeladmin');

/**
 * Product Model
 */
class PoecomModelShipping extends JModelAdmin {

    /**
     * Method override to check if you can edit an existing record.
     *
     * @param	array	$data	An array of input data.
     * @param	string	$key	The name of the key for the primary key.
     *
     * @return	boolean
     * @since	1.6
     */
    protected function allowEdit($data = array(), $key = 'id') {
        // Check specific edit permission then general edit permission.
        return JFactory::getUser()->authorise('core.edit', 'com_poecom.name.' . ((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
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
    public function getTable($type = 'Shipment', $prefix = 'PoecomTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param	array	$data		Data for the form.
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
     * @return	mixed	A JForm object on success, false on failure
     * @since	1.6
     */
    public function getForm($data = array(), $loadData = true) {
        // Get the form.
        $form = $this->loadForm('com_poecom.shipping', 'shipping', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;
    }

    /**
     * Method to get the script that have to be included on the form
     *
     * @return string	Script files
     */
    public function getScript() {
        return 'components/com_poecom/models/forms/shipping.js';
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
    protected function loadFormData() {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_poecom.user.edit.data', array());
        if (empty($data)) {
            $data = $this->getItem();
        }
        return $data;
    }

    /**
     * Method to get a single record.
     *
     * @param   integer  $pk  The id of the primary key.
     *
     * @return  mixed    Object on success, false on failure.
     * @since   11.1
     */
    public function getItem($pk = null) {
        // Initialise variables.
        $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
        $table = $this->getTable();

        if ($pk > 0) {
            // Attempt to load the row.
            $return = $table->load($pk);

            // Check for a table object error.
            if ($return === false && $table->getError()) {
                $this->setError($table->getError());
                return false;
            }
        }

        // Convert to the JObject before adding other data.
        $properties = $table->getProperties(1);
        $item = JArrayHelper::toObject($properties, 'JObject');

        // Convert tax_exempt_ids to an array
        $item->tax_exempt_ids = json_decode($item->tax_exempt_ids);
        return $item;
    }

    public function getShippingMethods() {
        // Get list of payment methods
        $q = $this->_db->getQuery(true);
        $q->select('*');
        $q->from('#__poe_shipping_method');
        $q->where('sm_enabled=1');
        $q->order('sort_order');

        $this->_db->setQuery($q);

        if(($ship_methods = $this->_db->loadObjectList())){
            $lang = JFactory::getDocument()->language;
            $idx = 0;
            $language = JFactory::getLanguage();
            foreach($ship_methods as $sm){
                //load language
                JFactory::getLanguage()->load('plg_poecomship_'.$sm->plugin, 'plugins/poecomship/freeship/', $language->getTag(), true);
                //set image
                $ship_methods[$idx]->logo = $sm->logo . $lang.'-'.$sm->plugin.'-logo.png';
                $idx++;
            }
        }

        return $ship_methods;
    }

    /**
     * Get Shipping Method Rates
     * 
     * @param array $cart Current cart values
     * @param array $taxes Current tax rates
     * @param string $plugin Shipping method plugin name
     * 
     * @return array $rates Array of rate objects
     */
    public function getRates($cart, $taxes, $plugin) {
        $rates = array();

        // Prepare data format for plugin
        if ($cart['items']) {

            // Register UOM conversion class
            //JLoader::register('UOMHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/uomhelper.php');

            $getUOM = 'get' . $plugin . 'UOM';
            $getRates = 'get' . $plugin . 'Rates';

            // Register plugin
            $dispatcher = JDispatcher::getInstance();
            JPluginHelper::importPlugin('poecomship', $plugin);
            $dispatcher->register($getUOM, 'plgPoecomShip' . $plugin);
            $dispatcher->register($getRates, 'plgPoecomShip' . $plugin);

            // Get the carrier API UOM for conversions (if neccessary)
            $response = $dispatcher->trigger($getUOM);

            if ($response) {
                $api_uom = $response[0];
            } else {
                $api_uom = array();
            }

            $model = JModel::getInstance('product', 'PoecomModel');

            // Get shipment items
            $items = array();

            foreach ($cart['items'] as $itm) {
                // get shipping info
                if ($itm->type == 1) { // phyical product
                    $item = $model->getShippingInfo($itm->product_id);

                    if ($item) {
                        if($itm->ship_modifiers != ''){
                            if($itm->ship_modifiers->weight_modifier != 'N'){
                                //adjust item weight
                                $item->weight = $this->adjustItemShipAspect($item->weight,$item->weightuom,
                                        $itm->ship_modifiers->weight_modifier_uom, $itm->ship_modifiers->weight_modifier,
                                        $itm->ship_modifiers->weight_modifier_value );
                            }

                            if($itm->ship_modifiers->length_modifier != 'N'){
                                //adjust item length
                                $item->length = $this->adjustItemShipAspect($item->length,$item->dimuom,
                                        $itm->ship_modifiers->length_modifier_uom, $itm->ship_modifiers->length_modifier,
                                        $itm->ship_modifiers->length_modifier_value );
                            }

                            if($itm->ship_modifiers->width_modifier != 'N'){
                                //adjust item width
                                $item->width = $this->adjustItemShipAspect($item->width,$item->dimuom,
                                        $itm->ship_modifiers->width_modifier_uom, $itm->ship_modifiers->width_modifier,
                                        $itm->ship_modifiers->width_modifier_value );
                            }

                            if($itm->ship_modifiers->height_modifier != 'N'){
                                //adjust item height
                                $item->height = $this->adjustItemShipAspect($item->height,$item->dimuom,
                                        $itm->ship_modifiers->height_modifier_uom, $itm->ship_modifiers->height_modifier,
                                        $itm->ship_modifiers->height_modifier_value );
                            }
                        }
                       
                        // Check weight uom
                        if ($api_uom && $api_uom['weight_uom_id'] != $item->weightuom) {
                            $convert = new UOMHelper();
                            //Convert the weight to API weight uom
                            $item->ship_weight = $convert->getConversion($item->weightuom, $api_uom['weight_uom_id'], $item->weight);
                        } else {
                            $item->ship_weight = $item->weight;
                        }

                        // Check dimension uom
                        if ($api_uom && $api_uom['dim_uom_id'] != $item->dimuom) {
                            $convert = new UOMHelper();
                            //Convert the dimensions to API dimension uom
                            $item->ship_length = $convert->getConversion($item->dimuom, $api_uom['dim_uom_id'], $item->length);
                            $item->ship_width = $convert->getConversion($item->dimuom, $api_uom['dim_uom_id'], $item->width);
                            $item->ship_height = $convert->getConversion($item->dimuom, $api_uom['dim_uom_id'], $item->height);
                        } else {
                            $item->ship_length = $item->length;
                            $item->ship_width = $item->width;
                            $item->ship_height = $item->height;
                        }
                        $item->name = $itm->product_name;
                        $item->sku = $itm->product_sku;
                        $item->quantity = $itm->quantity;
                        $item->price = $itm->price;

                        $items[] = $item;
                    } else {
                        $rates[] = array('error' => '1', 'msg' => JText::_('COM_POECOM_NO_SHIP_INFO'), 'product_id' => $itm->product_id);

                        return $rates;
                    }
                }
            }

            if ($items) {
                // Prepare data for shipping rate request
                $params = JComponentHelper::getParams('com_poecom');
                $shop_bill_id = $params->get('billinglocation');
                $shop_ship_id = $params->get('shipfromlocation');

                if ($shop_ship_id > 0) {
                    $location_id = $shop_ship_id;
                } else if ($shop_bill_id > 0) {
                    $location_id = $shop_bill_id;
                } else {
                    $location_id = 0;
                }

                if ($location_id > 0) {
                    // Get the origin
                    $model = JModel::getInstance('Location', 'PoecomModel');
                    $origin = $model->getItem($location_id);
                } else {
                    $origin = new JObject();
                }

                // Get the destination
                if ($cart['user_st']) {
                    $destination = $cart['user_st'];
                } else {
                    $destination = $cart['user_bt'];
                }

                // Set tax rate
                $tax_rate = 0;

                if ($taxes) {

                    foreach ($taxes as $tax) {
                        $tax_rate += floatval($tax->rate);
                    }
                }
                //total order weight - in API UOM
                $order_weight = 0;
                foreach($items as $itm){
                    $order_weight += $itm->ship_weight * $itm->quantity;
                }

                $shipment = new JObject();
                $shipment->order_subtotal = $cart['subtotal'];
                $shipment->tax_rate = $tax_rate;
                $shipment->origin = $origin;
                $shipment->destination = $destination;
                $shipment->items = $items;
                $shipment->order_weight = $order_weight;


                $data = array('shipment' => $shipment);

                // Send request to carrirer API plugin
                $rates = $dispatcher->trigger($getRates, $data);

                if ($rates) {
                    $rates = $rates[0];
                }
            } else {
                // no physical items
                $rates[] = array('noitems' => 1, 'rate' => 0);
            }
        }

        return $rates;
    }

    /**
     * 
     * @param float $aspect_value E.G. Item weight
     * @param int $uom1 Item aspect value UOM
     * @param int $uom2 Modifier UOM
     * @param string $modifier Expression either + or -
     * @param float $value Modifier value
     * @return float $value Modified value
     */
    private function adjustItemShipAspect($aspect_value, $uom1, $uom2, $modifier, $value){
        $convert = new UOMHelper();
        //convert option modifier to base unit
        if($uom1 != $uom2){
            //from - to
            $converted_value = $convert->getConversion($uom2,$uom1, floatval($value));
        }else{
            $converted_value = floatval($value);
        }
        $value = floatval($aspect_value);
        switch($modifier){
            case "+":
                //add
                $value += $converted_value;
                break;
            case "-":
                //subtract
                $value -= $converted_value;
                break;
        }
        
        return $value;
    }
}
