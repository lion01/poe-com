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
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Option List Form Field class for the Poecom component
 */
class JFormFieldOptionList extends JFormFieldList {

    /**
     * The field type.
     *
     * @var		string
     */
    protected $type = 'OptionList';

    /**
     * Method to get a list of options for a list input.
     *
     * @return	array		An array of JHtml options.
     */
    protected function getOptions() {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('op.id,op.name,p.name as product_name');
        $query->from('#__poe_option op');
        $query->innerJoin('#__poe_product p ON p.id=op.product_id');
        $query->order('p.name, op.name');
        $db->setQuery((string) $query);
        $options_list = $db->loadObjectList();
        $options = array();
        $options[] = array('value' => 0, 'text' => '--None--');
        if ($options_list) {
            foreach ($options_list as $op) {
                $options[] = JHtml::_('select.option', $op->id, $op->product_name . " : " . $op->name);
            }
        }

        return $options;
    }

}
