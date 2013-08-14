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
 
jimport('joomla.form.formrule');
 
/**
 * Form Rule class for the ShipMethod Admin
 * Note: THIS IS NOT COMPLETE - Testing
 * 
 */
class JFormRuleShipMethod extends JFormRule{
    /**
    * Method to test if two values are equal. To use this rule, the form
    * XML needs a validate attribute of equals and a field attribute
    * that is equal to the field to test against.
    *
    * @param   object  $element	The JXMLElement object representing the <field /> tag for the
    * 								form field object.
    * @param   mixed   $value		The form field value to validate.
    * @param   string  $group		The field name group control value. This acts as as an array
    * 								container for the field. For example if the field has name="foo"
    * 								and the group value is set to "bar" then the full field name
    * 								would end up being "bar[foo]".
    * @param   object  $input		An optional JRegistry object with the entire data set to validate
    * 								against the entire form.
    * @param   object  $form		The form object for which the field is being tested.
    *
    * @return  boolean  True if the value is valid, false otherwise.
    *
    * @since   11.1
    * @throws  JException on invalid rule.
    */
    public function test(& $element, $value, $group = null, & $input = null, & $form = null){
        // see product rule

        return true;
    }
}
