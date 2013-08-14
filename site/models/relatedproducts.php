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
 * Related Products Model
 */
class PoecomModelRelatedProducts extends JModel {
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
     * Get related products for a group excluding one product
     * 
     * @param type $group_id
     * @param type $product_id
     * @return type
     */
    public function getRelatedProductsByGroup($group_id = 0 , $product_id = 0){
        $db = $this->getDbo();
        $q = $db->getQuery(true);
        $q->select('rp.*, p.alias, p.name, p.sku, p.price, p.list_description, p.description,p.thumbimage');
        $q->from('#__poe_related_product rp');
        $q->innerJoin('#__poe_product p ON p.id=rp.product_id');
        $q->where('rp.group_id=' . (int) $group_id);
        $q->where('rp.product_id!='.(int)$product_id);
        $q->where('rp.published=1');
        $q->where('p.published=1');
        $q->order('rp.sort_order');
        $db->setQuery($q);

        $rp = $db->loadObjectList();
        
        if($rp){
            $idx = 0;
            foreach($rp as $r){
                if(empty($r->thumbimage)){
                    $rp[$idx]->thumbimage = 'components/com_poecom/css/images/no-thumb.png';
                }
                $idx++;
            }
        }

        return $rp;
    }
    
    
    /**
     * Get the default related products when product has no related group and
     * component parameter set to show related products. 
     * 
     * 
     * 
     * @param int $product_id Product Id to exclude
     * @return array $rp Array of Related Product Objects
     */
    public function getRelatedProducts($product_id){
        //get random products
        $p = JComponentHelper::getParams('com_poecom');
        $random_count = $p->get('randomprodcount', 3);
        
        $rp = array(); 
        $rp_ids = array();
        
        $db = JFactory::getDbo();
        
        //get 20 product ids
        for($i=0; $i < 20; $i++){
            //get random id
            $q = $db->getQuery(true);
            $q->select('RAND() * MAX(id)');
            $q->from('#__poe_product');

            $db->setQuery($q);

            if(($result = $db->loadResult())){  
                $rp_ids[] = (int)$result;
            }
        }

        if($rp_ids){
            $rp_ids = array_unique($rp_ids);
            //remove unpublished and get $random_count number of products
            $count = 0;
            foreach($rp_ids as $rpid){
                if($count < $random_count && $rpid != $product_id){
                    $q = $db->getQuery(true);
                    $q->select('p.id product_id,p.menu_id, p.name,p.alias, p.sku, p.price, p.list_description,p.thumbimage');
                    $q->from('#__poe_product p');
                    $q->where('p.id='.(int)$rpid);
                    $q->where('p.published=1');
                    $db->setQuery($q);

                    if(($obj = $db->loadObject())){
                        $rp[] = $obj;
                        $count++;
                    }
                }
            }
        }
        
        //set thunmb default where needed
        if($rp){
            $idx = 0;
            foreach($rp as $r){
                if(empty($r->thumbimage)){
                    $rp[$idx]->thumbimage = 'components/com_poecom/css/images/no-thumb.png';
                }
                $idx++;
            }
        }
       
        return $rp;
    }
}
