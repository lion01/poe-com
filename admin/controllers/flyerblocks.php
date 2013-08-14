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
jimport('joomla.application.component.controlleradmin');

/**
 * Flyer Blocks Controller
 */
class PoecomControllerFlyerBlocks extends JControllerAdmin {

    /**
     * Proxy for JController getModel.
     * 
     * Required to get JModelAdmin model in order to execute tasks not includes in
     * JModelList model, such as delete() from list view.
     * 
     * @since	1.6
     */
    public function getModel($name = 'FlyerBlock', $prefix = 'PoecomModel') {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }

}
