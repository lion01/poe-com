<?php

defined('_JEXEC') or die('Restricted access');
/**
 * POE-com - Site - Tax Model Class
 * 
 * @package com_poecom
 * @author Micah Fletcher
 * @copyright 2010 - 2012 (C) Extensible Point Solutions Inc. All Right Reserved.
 * @license GNU GPLv3, http://www.gnu.org/licenses/gpl.html
 *
 * @since 2.0 - 3/11/2012 10:21:57 PM
 *
 * http://www.exps.ca
 * */
jimport('joomla.application.component.modelitem');

/**
 * Tax Model
 */
class PoecomModelTax extends JModel {

  /**
   * @var object item
   */
  protected $tax_rate;

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
  public function getTable($type = 'Tax', $prefix = 'PoecomTable', $config = array()) {
    return JTable::getInstance($type, $prefix, $config);
  }

  /**
   * Get Tax Rate for Country and Region
   * 
   * @param string $country_id Two character country code
   * @param string $region_id Two character code for province or state
   * 
   * @return array $taxes Array of tax rates
   */
  public function getTaxRates($country_id = '', $region_id = '') {
    $taxes = array();

    $q = $this->_db->getQuery(true);
    $q->select('t.*, tt.code, tt.name');
    $q->from('#__poe_tax_rate t');
    $q->innerJoin('#__poe_tax_type tt ON tt.id=t.type_id');
    $q->where('t.country_code="' . $country_id . '" AND t.region_code=""');

    $this->_db->setQuery($q);

    if (!$country_tax = $this->_db->loadObject()) {
      $this->setError($this->_db->getError());
    }
    else {
      $taxes[] = $country_tax;
    }
    if(strlen($region_id)){
        $q = $this->_db->getQuery(true);
        $q->select('t.*, tt.code, tt.name');
        $q->from('#__poe_tax_rate t');
        $q->innerJoin('#__poe_tax_type tt ON tt.id=t.type_id');
        $q->where('t.country_id="' . $country_id . '" AND t.region_id="' . $region_id . '"');

        $this->_db->setQuery($q);

        if (!$region_tax = $this->_db->loadObject()) {
        $this->setError($this->_db->getError());
        }
        else {
        $taxes[] = $region_tax;
        }
    }
    return $taxes;
  }

  /**
   * Get Product Tax Rate
   * 
   * @param int $product_id
   * @param array $taxes Array of tax rates
   * 
   * @return float $tax_rate Tax Rate for a Product 
   */
  public function getProductTaxRate($product_id = 0, $taxes = array()) {
    $tax_rate = 0;
    if ($taxes && $product_id > 0) {
      // Check if product tax exempt
      $q = $this->_db->getQuery(true);
      $q->select('tax_exempt_ids');
      $q->from('#__poe_product');
      $q->where('id=' . $product_id);

      $this->_db->setQuery($q);

      if (!$tax_exempt_ids = $this->_db->loadResult()) {
        $this->setError($this->_db->getError());
      }
      else {
        // convert string to array
        $exempt_ids = json_decode($tax_exempt_ids);

        // adjust for exemptions
        foreach ($taxes as $t) {
          if($exempt_ids){
            if (!in_array($t->type_id, $exempt_ids)) {
              $tax_rate += floatval($t->rate);
            }
          }else{
            //no exemptions
            $tax_rate += floatval($t->rate);
          }
          
        }
      }
    }
    return $tax_rate;
  }

}
