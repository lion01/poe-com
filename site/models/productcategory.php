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
jimport('joomla.application.component.modelitem');

/**
 * Products Model
 */
class PoecomModelProductCategory extends JModel {

    /**
     * @var object item
     */
    protected $item;
    protected $items;

    /**
     * Method to auto-populate the model state.
     *
     * This method should only be called once per instantiation and is designed
     * to be called on the first call to the getState() method unless the model
     * configuration flag to ignore the request is set.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @return	void
     * @since	1.6
     */
    protected function populateState() {
        $app = JFactory::getApplication();

        // Load the application parameters.
        $params = $app->getParams();
        $this->setState('params', $params);

        parent::populateState();
    }

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param	type	The table type to instantiate
     * @param	string	A prefix for the table class name. Optional.
     * @param	array	Configuration array for model. Optional.
     * @return	JTable	A database object
     * @since	1.6
     */
    public function getTable($type = 'Products', $prefix = 'PoecomTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Get Products
     * @return obejct $items Object with category and products properties
     */
    public function getItems() {
        $items = new JObject();
        $app = JFactory::getApplication();
        $jinput = $app->input;
        // Get the top level category
        $catid = $jinput->get('catid', 0, 'int');
       
        if($catid > 0){
            //get category info
            $db = JFactory::getDbo();
            $q = $db->getQuery(true);
            $q->select('*');
            $q->from('#__categories');
            $q->where('id=' . (int) $catid);
            
            $db->setQuery($q);

            if(( $cat = $db->loadObject()) ){
              
                $items->cat = $cat;
                // Get products
                $q = $db->getQuery(true);
                $q->select('p.*')
                  ->select("CASE WHEN CHAR_LENGTH(".$db->nameQuote('p.alias').
                          ") THEN CONCAT_WS(".$db->Quote(':').", ".$db->nameQuote('p.id').
                          ", ".$db->nameQuote('p.alias').") ELSE ".$db->Quote('p.id')." END as slug");
                $q->from('#__poe_product p');
                $q->innerJoin('#__poe_product_category_xref xref ON xref.product_id=p.id');
                $q->where('xref.category_id=' . (int) $catid);
                $q->order('xref.ordering');
                $db->setQuery($q);

                $items->products = $db->loadObjectList();
                
                //set the params
                 if (property_exists($cat, 'params')) {
                    $registry = new JRegistry;
                    $registry->loadString($cat->params);
                    $items->params = $registry->toArray();
                }
                // Convert the metadata field to an array.
                if (property_exists($cat, 'metadata')) {
                    $registry = new JRegistry;
                    $registry->loadString($cat->metadata);
                    $items->metadata = $registry->toArray();
                }
            }   
        }
      
        return $items;
    }

}
