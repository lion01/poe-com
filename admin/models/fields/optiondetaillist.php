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
 * Option Detail List Form Field class for the Poecom component
 */
class JFormFieldOptionDetailList extends JFormFieldList {

    /**
     * The field type.
     */
    protected $type = 'OptionDetailList';

    /**
     * Method to get a list of option details for a list input.
     *
     * @return	array		An array of JHtml options.
     */
    protected function getOptions() {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('id,name');
        $query->from('#__poe_detail');
        $query->order('name');
        $db->setQuery((string) $query);
        $details = $db->loadObjectList();
        $options = array();
        $options[] = array('value' => 0, 'text' => '--None--');
        if ($details) {
            foreach ($details as $dt) {
                $options[] = JHtml::_('select.option', $dt->id, $dt->name);
            }
        }

        return $options;
    }

}
