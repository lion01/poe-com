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

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_poecom')) 
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Set some global property
$document = JFactory::getDocument();

$css = JURI::base(true).'/components/com_poecom/assets/css/poecom.css';
$document->addStyleSheet($css);

$js = "jQuery(function(){
        jQuery('body').append('<div style=\"width: 400px; margin-left:auto; margin-right:auto;text-align:center;\"><div>Extension Developed by <a href=\"http://www.exps.ca\" target=\"_blank\">Extensible Point Solutions Inc.</a></div><div>Copyright 2011 - 2013 - All Rights Reserved.</div></div>');
    });";
$document->addScriptDeclaration($js);

// import joomla controller library
jimport('joomla.application.component.controller');

require_once( JPATH_COMPONENT.DS.'controller.php' );

// Get an instance of the controller prefixed by Poecom
// If task in dot notation, subcontroller from  ../controllers/ used and task variable set. 
// If no task master controller used and view loaded
$controller = JController::getInstance('Poecom');

$jinput = JFactory::getApplication()->input;
$task = $jinput->get('task', '', 'CMD');

// Perform the Request task
$controller->execute($task);

// Redirect if set by the controller
$controller->redirect();
