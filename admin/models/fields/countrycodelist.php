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
class JFormFieldCountryCodeList extends JFormFieldList{
    /**
    * The form field type.
    *
    * @var    string
    * @since  11.1
    */
    protected $type = 'CountryCodeList';
    
    protected function getParam( $key ) {
        // retreive one param
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('params');
        $query->from('#__extensions');
        $query->where("name='com_poecom'");
        
        $db->setQuery((string)$query);
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
        
        //value can be string from default field value or an array from params
        if(is_array($this->value) ){
            $country_ids = $this->value;
        }else if(strlen($this->value) ){
            $ids = explode(',', $this->value);
            $country_ids = $ids;
        }else{
            $country_ids = array();
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
                $html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $country_ids, $this->id);
                $html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
        }else{
            // Create a regular list.
            $html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $country_ids, $this->id);
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
            $enabled = $this->element['enabled'];
        
            // Initialize variables.
            $options = array();
        
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select('code2 as value,name as text' );
            $query->from('#__geodata_country');
            if($enabled == true){
                $query->where("enabled=1");
            }
		
            $query->order('name');
      
            $db->setQuery((string)$query);
        
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