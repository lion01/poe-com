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
 * Product List Form Field class for the Poecom component
 */
class JFormFieldProductList extends JFormFieldList{
    /**
    * The field type.
    *
    * @var		string
    */
    protected $type = 'ProductList';

    /**
    * Method to get a list of products for a list input.
    *
    * @return	array		An array of JHtml options.
    */
    protected function getOptions(){
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('id,name');
        $query->from('#__poe_product');
        $query->order('name');
        $db->setQuery((string)$query);
        $products = $db->loadObjectList();
        $options = array();
        $options[] = array('value' => 0, 'text' => '--Select Product--');
        if ($products){
            foreach($products as $prd){
                $options[] = JHtml::_('select.option', $prd->id, $prd->name );
            }
        }

        return $options;
    }
}
