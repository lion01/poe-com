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
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Form Field class for CoutryList Regions
 * 
 * Note: $this->value is an array with two elements array(country_id, region_id)
 * This is used when the field loads to set the appropriate rgions for a country which is set 
 * with countrylist field type.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldRegionList extends JFormFieldList{
    /**
    * The form field type.
    *
    * @var    string
    * @since  11.1
    */
    protected $type = 'RegionList';
    
    protected function getParam( $key ) {
        // retreive one param
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->select('params');
        $q->from('#__extensions');
        $q->where("name='com_poecom'");
        
        $db->setQuery((string)$q);
        // returns True, False or NULL
        $params = json_decode( $db->loadResult(), true );
        if($params){
            $param = strlen($params[$key])?$params[$key]: '';
        }else{
            $param = '';
        }
        
        return $param;
    }
    
    /**
    * Over Method to get the field input markup.
    *
    * @return  string  The field input markup.
    * @since   11.1
    */
    protected function getInput(){
        // Initialize variables.
        $html = array();
        $attr = '';

        // Initialize some field attributes.
        $attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';

        // To avoid user's confusion, readonly="true" should imply disabled="true".
        if ( (string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
                $attr .= ' disabled="disabled"';
        }

        $attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
        $attr .= $this->multiple ? ' multiple="multiple"' : '';

        // Initialize JavaScript field attributes.
        $attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

        // Get the field options.
        $options = (array) $this->getOptions();
        // Create a read-only list (no name) with a hidden input to store the value.
        if ((string) $this->element['readonly'] == 'true') {
                $html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value[1], $this->id);
                $html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value[1].'"/>';
        }
        // Create a regular list.
        else {
            if(isset($this->value[1])){
                $value = $this->value[1];
            }else{
                $value = 0;
            }
                $html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $value, $this->id);
        }

        return implode($html);
    }

    /**
    * Over ride method to get the field options for UOM mass units
    *
    * @return  array  The field option objects.
    * @since   11.1
    */
    protected function getOptions(){
        // $value[0] = country code, $value[1] = region code
        // set explictly in view
        if($this->value){
            $country_id = $this->value[0];
        }else{
            $country_id = $this->element['countryid'];
        }
        
        // Initialize variables.
        $options = array();
        
        $db = JFactory::getDBO();
        $q = $db->getQuery(true);
        $q->select('id as value,name as text' );
        $q->from('#__geodata_region');
        
        if(strlen($country_id)){
            $q->where("country_id='".$country_id."'");
        }
		
        $q->order('name');

        $db->setQuery((string)$q);
        
        if(($regions = $db->loadObjectList())){
            $options = $regions;
        }
        $options_list = array();
        $options_list[] = JHtml::_('select.option', 0, '--Select--');
        
        foreach($options as $op){
            array_push($options_list, $op);
        }
        
        return $options_list;
    }
}
