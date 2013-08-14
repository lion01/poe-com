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
 * Form Rule class for the POE-com Product Admin
 * 
 * 
 * Note: THIS IS NOT COMPLETE - Testing
 * 
 */
class JFormRuleProduct extends JFormRule{
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
        // Initialize variables.
        $field	= (string) $element['name'];
        
        $default_qty = $input->get($field);
        
        /**
         * This $_POST variables are available via $input->get()
         * This rule compares two fields
         */
        $max_qty = $input->get('max_qty');
  
        // Check that a validation field is set.
        if (!$field) {
            return new JException(JText::sprintf('JLIB_FORM_INVALID_FORM_RULE', get_class($this)));
        }

        // Check that a valid JForm object is given for retrieving the validation field value.
        if (!($form instanceof JForm)) {
            return new JException(JText::sprintf('JLIB_FORM_INVALID_FORM_OBJECT', get_class($this)));
        }

        // Test the two values against each other.
        if ($default_qty <= $max_qty && $default_qty > 0) {
            return true;
        }

    }
}
