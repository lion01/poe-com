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
jimport('joomla.application.component.controlleradmin');
 
/**
 * Packages Controller
 */
class PoecomControllerPackages extends JControllerAdmin{
 
    /**
    * Proxy for JController getModel.
    * 
    * Required to get JModelAdmin model in order to execute tasks not includes in
    * JModelList model, such as delete() from list view.
    * 
    * @since	1.6
    */
    public function getModel($name = 'Packages', $prefix = 'PoecomModel'){
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }
    
    
    /**
    * Delete order(s)
    * 
    * Only order with status open, invoiced, or canceled can be deleted
    * When an order is deleted dependencies are reset, eg payment order_id 
    *
    * @return  void
    *
    * @since   11.1
    */
    public function delete(){
        // Check for request forgeries
        JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

        // Get items to remove from the request.
        $cids = JRequest::getVar('cid', array(), '', 'array');

        if (!is_array($cids) || count($cids) < 1){
            JError::raiseWarning(500, JText::_('COM_POECOM_NO_ITEM_SELECTED'));
        }else{
            // Get the model.
            $model = $this->getModel('Package');

            // Make sure the item ids are integers
            jimport('joomla.utilities.arrayhelper');
            JArrayHelper::toInteger($cids);
            
            // Remove the items.
           if ($model->delete($cids)){
                $this->setMessage(JText::plural('COM_POECOM_N_ITEMS_DELETED', count($cids)));
            }else{
                $this->setMessage($model->getError());
            }
        }

        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
    }
}
