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
class JFormFieldUomList extends JFormFieldList {

    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    protected $type = 'UomList';

    protected function getParam($key) {
        // retreive one param
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('params');
        $query->from('#__extensions');
        $query->where("name='com_poecom'");

        $db->setQuery((string) $query);
        // returns True, False or NULL
        $params = json_decode($db->loadResult(), true);
        if ($params && !empty($key)) {
            $param = strlen($params[$key]) ? $params[$key] : '';
        } else {
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
    protected function getInput() {
        // Get the default UOM for typefilter
        // filter type
        $type = $this->element['typefilter'];
        switch ($type) {
            case 'mass':
                $type_param = 'weightuom';
                break;
            case 'length':
                $type_param = 'lengthuom';
                break;
            default:
                $type_param = '';
                break;
        }

        $param = $this->getParam($type_param);

        //$default_uom = strlen($param) ? $param : '';

        // Initialize variables.
        $html = array();
        $attr = '';

        // Initialize some field attributes.
        $attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';

        // To avoid user's confusion, readonly="true" should imply disabled="true".
        if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
            $attr .= ' disabled="disabled"';
        }

        $attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
        $attr .= $this->multiple ? ' multiple="multiple"' : '';

        // Initialize JavaScript field attributes.
        $attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

        // Get the field options.
        $options = (array) $this->getOptions();

        // Create a read-only list (no name) with a hidden input to store the value.
        if ((string) $this->element['readonly'] == 'true') {
            $html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
            $html[] = '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '"/>';
        }
        // Create a regular list.
        else {
            $html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
        }

        return implode($html);
    }

    /**
     * Over ride method to get the field options for UOM mass units
     *
     * @return  array  The field option objects.
     * @since   11.1
     */
    protected function getOptions() {
        // filter type
        $type = $this->element['typefilter'];

        // Initialize variables.
        $options = array();
        $options_list = array();

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('id as value,name as text, type');
        $query->from('#__poe_uom');
        if (strlen($type)) {
            $query->where("type='" . $type . "'");
        }

        $query->order('system,type,name');

        $db->setQuery((string) $query);

        if (($result = $db->loadObjectList())) {
            $options = $result;
        }

        $options_list[] = JHtml::_('select.option', 0, '--None--');

        foreach ($options as $op) {
            array_push($options_list, $op);
        }
        return $options_list;
    }

}
