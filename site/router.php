<?php defined('_JEXEC') or die('Restricted access');

/**
 * POE-com - Site - Router
 * 
 * @package com_poecom
 * @author Micah Fletcher
 * @copyright 2010 - 2012 (C) Extensible Point Solutions Inc. All Right Reserved.
 * @license GNU GPLv3, http://www.gnu.org/licenses/gpl.html
 *
 * @since 2.0 - 3/11/2012 9:49:31 PM
 *
 * http://www.exps.ca
 * */
function poecomBuildRoute(&$query) {
    
    $segments = array();
    
    if (isset($query['changeitemidx'])) {
        //item change from cart
        //format index.php?option=com_poecom&view=product&changeitemidx=cartIndex&token=1
        //where cartIndex is int and token is a random string value
        $key = explode("=",JUtility::getToken());
        
        $segments[] = $query['view'];
        $segments[] = $query['id'];
        $segments[] = $query['changeitemidx'];
        $segments[] = $query[$key[0]];
        unset($query['view']);
        unset($query['id']);
        unset($query['changeitemidx']);
        unset($query[$key[0]]);
    }else{
        if (isset($query['view'])) {
            $segments[] = $query['view'];
            unset($query['view']);
        }
        if (isset($query['id'])) {
            $segments[] = $query['id'];
            unset($query['id']);
        }
        if (isset($query['skiplogin'])) {
            $segments[] = $query['skiplogin'];
            unset($query['skiplogin']);
        }
    }
    return $segments;
}

function poecomParseRoute($segments) {
    
    $vars = array();

    switch ($segments[0]) {
        case 'flyer':
            $vars['view'] = 'flyer';
            $vars['flyerid'] = $segments[1];
            break;
        case 'account':
            $vars['view'] = 'account';
            break;
        case 'login':
            $vars['view'] = 'login';
            break;
        case 'cart':
            $vars['view'] = 'cart';
            $vars['skiplogin'] = isset($segments[1])?$segments[1]:0;
            break;
        case 'products':
            $vars['view'] = 'category';
            $id = explode(':', $segments[1]);
            $vars['id'] = (int) $id[0];
            break;
        case 'product':
            $vars['view'] = 'product';
           
            if(array_key_exists('changeitemidx', $segments)){
                $vars['id'] = $segments[1];
                $vars['changeitemidx'] = $segments[2];
                $key = explode("=",JUtility::getToken());
              
                $vars[(string)$key[0]] = $segments[3];
            }else{
                $id = explode(':', $segments[1]);
                $vars['id'] = (int) $id[0];
            }
            
            break;
        case 'productcategory':
            $vars['view'] = 'productcategory';
            $id = explode(':', $segments[1]);
            $vars['id'] = (int) $id[0];
            break;
    }
    return $vars;
}
