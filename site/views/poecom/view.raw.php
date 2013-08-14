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
jimport('joomla.application.component.view');

/**
 * Poecom View
 *
 * @package	Joomla.site
 * @subpackage	com_poecom
 * @since 	2.5
 */
class PoecomViewPoecom extends JView{
        
    /**
    * display method of Product view
    * @return void
    */
    public function display($tpl = null){
	// Display the template
	parent::display($tpl); 
    }
    /**
     * Route urls generated by JS functions
     */
    public function routeJSUrl(){
        $response = new JObject();
        $response->routed = 0;

        $app = JFactory::getApplication();
        $jinput = $app->input;

        $route_url = $jinput->get('routeurl', '', 'string' );

        if(strlen($route_url)){
            //get component params
            $params = JComponentHelper::getParams('com_poecom');
            //put query into array
            $segs = array();
            $vars = explode("&", $route_url);
            if($vars){
                foreach($vars as $v){
                    $segment = explode("=", $v);
                    if($segment){
                         $segs[$segment[0]] = $segment[1];
                    }
                }
            }
            
            //check for defaultpage
            if(isset($segs['defaultpage']) && $segs['defaultpage'] == 1){

                $menu_item = $params->get('defaultpage');
                
                if($menu_item > 0){
                    $route_url = 'index.php?Itemid='.$menu_item;
                }
            }else if(isset($segs['deleteitemidx'])){
                
                $menu_item = $params->get('cartitemid');
                
                if($menu_item > 0){
                    $route_url = 'index.php?Itemid='.$menu_item.'&deleteitemidx='.$segs['deleteitemidx'].'&'.JUtility::getToken().'=1';
                }
            }
            
            

            $url = JRoute::_($route_url);
            $response->routed = 1;
            $response->sefUrl = $url;
        }

        $json_response = json_encode($response);

        echo $json_response;
    }
    
    /**
     * Return full path for relative URL
     * 
     * Support window.location.replace
     */
    public function fullPathJSUrl(){
        $response = new JObject();
        $response->error = 0;

        $app = JFactory::getApplication();
        $jinput = $app->input;

        $relative_url = $jinput->get('relative_url', '', 'string' );
        
        if(empty($relative_url) && !stripos('http', $relative_url)){
            $response->error = 1;
        }else{
            $params = JComponentHelper::getParams('com_poecom');
            $use_https = $params->get('usehttps', '0');
            
            $response->fullpath = JURI::root().$relative_url;
            
            if($use_https === '1'){
                $response->fullpath = str_ireplace('http', 'https', $response->fullpath);
            }
        }

        $json_response = json_encode($response);

        echo $json_response;
    }
}
