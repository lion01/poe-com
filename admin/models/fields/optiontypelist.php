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
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
 
/**
 * Option Type List Form Field class for the Poecom component
 */
class JFormFieldOptionTypeList extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'OptionTypeList';
 
	/**
	 * Method to get a list of products for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function getOptions() 
	{
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
		$query->select('id,name');
		$query->from('#__poe_option_type');
        $query->order('name');
		$db->setQuery((string)$query);
		$types = $db->loadObjectList();
		$options = array();
        // Force a choice
        //$options[] = array('id' => 0, 'text' => '--Select Option Type--');
		if ($types)
		{
			foreach($types as $type) 
			{
				$options[] = JHtml::_('select.option', $type->id, $type->name );
			}
		}

		return $options;
	}
}
