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

/**
 * Product select box
 */
class ProductList{
    
    public function __construct(){
        
    }
    
    /**
     * Get options for list
     * 
     * Used to create html.behaviour lists
     * 
     * @return array $options Array of select.option objects
     */
    public function getOptions(){
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
		$query->select('id,name');
		$query->from('#__poe_product');
        $query->order('name');
		$db->setQuery((string)$query);
		$products = $db->loadObjectList();
		$options = array();
        $options[] = array('id' => 0, 'text' => '--Select Product--');
		if ($products)
		{
			foreach($products as $prd) 
			{
				$options[] = JHtml::_('select.option', $prd->id, $prd->name );
			}
		}

		return $options;
    }
    
    /**
     * Create a GenericList for Products
     * 
     * @param string $name Input name and id attributes
     * @param string $atrr Optional select attributes
     * @param string $key  Optional value atrribute name, default is 'value'
     * @param string $text Optional text attribute name, default is 'text'
     * @param string $default Optional default value for options
     * 
     * @return string $select HTML for select input
     */ 
    public function getSelectList($name = 'list', $attr = null, $key = 'vlaue', $text = 'text', $default = null){
        $select = JHtml::_('select.genericlist', $this->getOptions(), $name, $attr, $key, $text, $default);
        
        return $select;
    }
    
}