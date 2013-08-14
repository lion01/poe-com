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
jimport('joomla.application.component.modelitem');
 
/**
 * Product Categories Model
 */
class PoecomModelProductCategories extends JModel
{
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
	protected function populateState() 
	{
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
	public function getTable($type = 'ProductCategories', $prefix = 'PoecomTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}
    
    /**
     * Get Products
     * @return array $locations Array of locations objects
    */
    public function getItems(){ 
        if (!isset($this->items)){

            // Get the top level category
            $catid = JRequest::getCmd('catid', null);
/*
            JLoader::register('PoecomCategories', JPATH_COMPONENT.'/helpers/category.php');

            // Get all nested categories
            $categories = PoecomCategories::getInstance('Poecom');

            // Load category and children in JCategoryNode object
         //   $catsubs = $categories->get($catid);

            $nodes = $categories->getNodes($catid);
 
*/
            //find categories
            $nodes = $this->getCategories($catid);
            
            if($nodes){
                $idx = 0;

                foreach($nodes as $n){
                    $params = json_decode($n->params);

                    $item = new JObject;
                    $item->category_id = $n->id;
                    $item->category_title = $n->title;
                    $item->category_image = $params->image;
                    $item->category_desc = $n->description;

                    // Get product thumbnails
                    $db = JFactory::getDbo();
                    $q = "SELECT p.* FROM ".$db->nameQuote('#__poe_product'). " p".
                        " INNER JOIN ".$db->nameQuote('#__poe_product_category_xref'). " xref ON xref.product_id=p.id".
                        " WHERE xref.category_id=".$n->id;
                    $db->setQuery($q);

                    $products = $db->loadObjectList();

                    if($products){
                        $item->products = $products;
                    }
                    $this->items[$idx] = $item;

                    $idx++;
                }
            }
        }
        return $this->items;
    }
    
    private function getCategories($catid){
        $cats = array();
        $cats[] = $catid;
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->select('id, parent_id,lft');
        $q->from('#__categories');
        $q->where('extension=' . $db->Quote('com_poecom') );
        $q->where('published=1');
        $q->order('level,lft');
        $db->setQuery($q);
        
        //all categories
       if( $nodes = $db->loadObjectList() ){
     
           $subs = $this->findCategoryChildren($catid, $nodes);
           
           
       }
    }
    
    private function findCategoryChildren($parent_id, $nodes, $subs){
        foreach($nodes as $n){
            if($n->id == $catid){
                $parent_id = $catid;
            }
        }
        return $subs;
    }
}
