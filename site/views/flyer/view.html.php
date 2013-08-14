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
jimport('joomla.application.component.view');

/**
 * Flyer View
 */
class PoecomViewFlyer extends JView {
    
    // Overwriting JView display method
    public function display($tpl = null) {
       
        $params = JComponentHelper::getParams('com_poecom');
        
        $cart_itemid = $params->get('cartitemid', 0);
        $product_itemid = $params->get('productitemid', 0, 'int');
        $useHTTPS = $params->get('usehttps', 0);

        $base_currency_id = $params->get('base_currency', 1);
        $model = $this->getModel('Currencies');
       
        $currency = $model->getItems($base_currency_id);
        
        $item = $this->get('Flyer');
        
        $this->assignRef('cart_itemid', $cart_itemid);
        $this->assignRef('product_itemid', $product_itemid);
        $this->assignRef('useHTTPS', $useHTTPS);
        $this->assignRef('currency', $currency);
        $this->assignRef('item', $item);

        $this->prepareDocument($item);

        // Display the view
        parent::display($tpl);
    }

    /**
     * Set document title and metadata
     *
     * @since	1.6
     */
    protected function prepareDocument($item) {
        $app = JFactory::getApplication();
        $menus = $app->getMenu();
       
        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();
   
        if (!empty($menu)) {
            $menu->params->def('page_heading', $menu->params->get('page_title', $menu->title));
            $title = $menu->params->get('page_title', $menu->title);
        } 
        
        if (empty($title)) {
            $title = $app->getCfg('sitename');
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
        }
        $this->document->setTitle($title);

        if (strlen($item->cat->metadesc)) {
            $this->document->setDescription($item->cat->metadesc);
        }

        if (strlen($item->cat->metakey)) {
            $this->document->setMetadata('keywords', $item->cat->metakey);
        }

        if ($item->metadata['robots']) {
            $this->document->setMetadata('robots', $item->metadata['robots']);
        }
    }

}

