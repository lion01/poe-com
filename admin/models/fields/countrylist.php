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
 * Form Field class for the Joomla Framework.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldCountryList extends JFormFieldList{
    /**
    * The form field type.
    *
    * @var    string
    * @since  11.1
    */
    protected $type = 'CountryList';
    
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
            //can be int, string or array
            $param = $params[$key];
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
        
        if(strlen($this->value)){
            $country_id = $this->value;
        }else{
            $country_id = $this->element['default'];
        }
        
       
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
                $html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $country_id, $this->id);
                $html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
        }else{
            // Create a regular list.
            $html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $country_id, $this->id);
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
            
            $allowed_only = (int)$this->element['allowedonly'];

            $enabled = (int)$this->element['enabled'];
        
            // Initialize variables.
            $options = array();
        
            $db = JFactory::getDBO();
            $q = $db->getQuery(true);
            $q->select('id as value,name as text' );
            $q->from('#__geodata_country');
            if($enabled == 1){
                $q->where("enabled='".$enabled."'");
            }
            if($allowed_only == 1){
                //expecting array
                $allowedCountries = $this->getParam('allowedcountries');
                if($allowedCountries){
                    $q->where("code2 IN ('".implode("','",$allowedCountries)."')");
                }
            }
            $q->order('name');

            $db->setQuery((string)$q);
        
            if(($temp = $db->loadObjectList())){
                $options = $temp;
            }
            $options_list = array();
            $options_list[] = JHtml::_('select.option', 0, '--Select--');
        
		foreach ($this->element->children() as $option) {

                    // Only add <option /> elements.
                    if ($option->getName() != 'option') {
                            continue;
                    }

                    // Create a new option object based on the <option /> element.
                    $tmp = JHtml::_('select.option', (string) $option['value'], JText::alt(trim((string) $option), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)), 'value', 'text', ((string) $option['disabled']=='true'));

                    // Set some option attributes.
                    $tmp->class = (string) $option['class'];

                    // Set some JavaScript option attributes.
                    $tmp->onclick = (string) $option['onclick'];

                    // Add the option object to the result set.
                    $options[] = $tmp;
          
		}
        
        foreach($options as $op){
            array_push($options_list, $op);
        }
		return $options_list;
	}
}
