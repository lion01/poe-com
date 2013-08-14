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
JFormHelper::loadFieldClass('category');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldProductCategoryList extends JFormFieldCategory {

    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    public $type = 'ProductCategoryList';

    /**
     * @var    array  Cached array of the category items.
     */
    protected static $items = array();

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
        if ($params) {
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
     * Over ride method to get Product Categories base on root category parameter
     * 
     * Uses the category path field to filter out non product categories
     *
     * @return  array  The field option objects.
     * @since   1.7
     */
    protected function getOptions() {

        // Get the root category
        $root_cat = $this->getParam('productrootcatid');

        // Initialise variables.
        $options = array();
        $extension = $this->element['extension'] ? (string) $this->element['extension'] : (string) $this->element['scope'];
        $published = (string) $this->element['published'];

        // Load the product categories for an extension
        if (!empty($extension)) {

            // Get the path for the root product category
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select('path');
            $query->from('#__categories');
            $query->where(array("extension='" . $extension . "'", "id='" . $root_cat . "'"));

            $db->setQuery((string) $query);

            // Set path
            if (!$path = $db->loadResult()) {
                $path = '';
            }

            // Get all extension product categories
            $query = $db->getQuery(true);
            $query->select('id as value, title as text, lft, level, parent_id, path');
            $query->from('#__categories');
            $query->where(array("extension='" . $extension . "'", "published='" . $published . "'"));
            $query->order('lft');

            $db->setQuery((string) $query);

            if ( ($options = $db->loadObjectList()) ) {
                $idx = 0;
                foreach ($options as $op) {
                    // Add dashes for sub category display
                    $repeat = ( $op->level - 1 >= 0 ) ? $op->level - 1 : 0;
                    $options[$idx]->text = str_repeat('- ', $repeat) . $op->text;

                    // extract start of path equal to $path length
                    $path_op = substr($op->path, 0, strlen($path));

                    // unset categories that do not start with $path
                    if ($path_op != $path) {
                        unset($options[$idx]);
                    }
                    $idx++;
                }
            }

            // Verify permissions.  If the action attribute is set, then we scan the options.
            if( ($action = (string) $this->element['action']) ) {

                // Get the current user object.
                $user = JFactory::getUser();

                foreach ($options as $i => $option) {
                    // To take save or create in a category you need to have create rights for that category
                    // unless the item is already in that category.
                    // Unset the option if the user isn't authorised for it. In this field assets are always categories.
                    if ($user->authorise('core.create', $extension . '.category.' . $option->value) != true) {
                        unset($options[$i]);
                    }
                }
            }

            if (isset($this->element['show_root'])) {
                array_unshift($options, JHtml::_('select.option', '0', JText::_('JGLOBAL_ROOT')));
            }
        } else {
            JError::raiseWarning(500, JText::_('JLIB_FORM_ERROR_FIELDS_CATEGORY_ERROR_EXTENSION_EMPTY'));
        }

        return $options;
    }

}
