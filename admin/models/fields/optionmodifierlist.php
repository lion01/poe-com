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
 * Option Detail List Form Field class for the Poecom component
 */
class JFormFieldOptionModifierList extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'OptionModifierList';
 
	/**
	 * Method to get a list of product option modifiers for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function getOptions() 
	{
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
		$query->select('symbol,text');
		$query->from('#__poe_option_modifier');
        $query->where('published=1');
        $query->order('id');
        
		$db->setQuery((string)$query);
		$modifiers = $db->loadObjectList();
		$options = array();
        
		if ($modifiers)
		{
			foreach($modifiers as $mod) 
			{
				$options[] = JHtml::_('select.option', $mod->symbol, $mod->text );
			}
		}

		return $options;
	}
}
