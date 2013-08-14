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
jimport('joomla.application.component.controller');
$app = JFactory::getApplication();
// check the user state
$user = JFactory::getUser();

$p = JComponentHelper::getParams('com_poecom');
$allowed_countries = $p->get('allowedcountries', array());

$jsess = JFactory::getSession();
$geodata = $jsess->get('geodata', array(), 'browser_geodata');

if($user->guest == 1 && empty($geodata)){
    // Get the browser GEOIP data
    $dispatcher = JDispatcher::getInstance();
    JPluginHelper::importPlugin('system', 'geodata');
    $dispatcher->register('getBrowserGeodata','plgSystemGeodata');
    $result = $dispatcher->trigger('getBrowserGeoData');
    
    //$result is true when geodata plugin sets session geodata array
    if(!$result){
        //could not determine country code
        $user_country_code = 0;
    }else{
        //get geodata from session
        $geodata = $jsess->get('geodata', array(), 'browser_geodata');
    }
}

if(!empty($geodata)){
    $user_country_code = $geodata['countryCode'];
    if(!in_array($user_country_code, $allowed_countries)){
        $geodata['block_order'] = true;
}else{
    $geodata['block_order'] = false;
}
    $jsess->set('geodata', $geodata, 'browser_geodata');
}

//set site url for JS
JText::script('SITEURL', '');

// Register ProductPrice
$path = JPATH_COMPONENT.'/views/productprice/product_price.php';
JLoader::register('ProductPrice',$path, true);

// Register pluginHelper
$path = JPATH_COMPONENT_ADMINISTRATOR.'/helpers/plugin.php';
JLoader::register('PluginHelper',$path, true);

$document = JFactory::getDocument();

//determine CSS file location
$use_tmpl_css = $p->get('usetploverride', 0);

if( $use_tmpl_css === '1'){
    //get stylesheet from active template folder
    $css = JURI::base() . 'templates/' . $app->getTemplate().'/css/poecom.css';
}else{
    $css = JURI::base() . 'components/com_poecom/css/poecom.css';
}

$document->addStyleSheet($css);

//JS router
$js = JURI::base() . 'components/com_poecom/js/poecom.js';
$document->addScript($js);

//redirect to default page
$jinput = $app->input;

if($jinput->get('defaultpage', 0, 'cmd') == 1){
    $params = JComponentHelper::getParams('com_poecom');
    
    $menu_item = $params->get('defaultpage');
 
    $menus = $app->getMenu();
  
    $menu = $menus->getItem($menu_item);
    
    $app->redirect(JRoute::_($menu->link.'&Itemid='.$menu_item) );
}

// Register UOM conversion class
JLoader::register('UOMHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/uomhelper.php');

// Get an instance of the controller
$controller = JController::getInstance('Poecom');

// Get an instance of the controller prefixed by Poecom
// If task in dot notation, subcontroller from  ../controllers/ used and task variable set. 
// If no task master controller used and view loaded
//$controller->execute(JRequest::getCmd('task','display'));
$controller->execute(JRequest::getCmd('task','display'));
 
// Redirect if set by the controller
$controller->redirect();
