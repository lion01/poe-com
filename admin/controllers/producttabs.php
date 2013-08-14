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
 * ProductTabs Controller
 */
class PoecomControllerProductTabs extends JControllerAdmin{
 
    /**
    * Proxy for JController getModel.
    * 
    * Required to get JModelAdmin model in order to execute tasks not includes in
    * JModelList model, such as delete() from list view.
    * 
    * @since	1.6
    */
    public function getModel($name = 'ProductTabs', $prefix = 'PoecomModel'){
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }
    
    /**
    * Method to publish a list of items
    *
    * @return  void
    *
    * @since   11.1
    */
    public function publish(){
        // Check for request forgeries
        JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

        // Get items to publish from the request.
        $cid = JRequest::getVar('cid', array(), '', 'array');
        $data = array('publish' => 1, 'unpublish' => 0);
        $task = $this->getTask();
        $value = JArrayHelper::getValue($data, $task, 0, 'int');

        if (empty($cid)){
            JError::raiseWarning(500, JText::_($this->text_prefix . '_NO_ITEM_SELECTED'));
        }else{
            // Get the JModelAdmin model.
            $model = $this->getModel('ProductTab');

            // Make sure the item ids are integers
            JArrayHelper::toInteger($cid);

            // Publish the items.
            if (!$model->publish($cid, $value)){
                JError::raiseWarning(500, $model->getError());
            }else{
                if ($value == 1){
                    $ntext = $this->text_prefix . '_N_ITEMS_PUBLISHED';
                }elseif ($value == 0){
                    $ntext = $this->text_prefix . '_N_ITEMS_UNPUBLISHED';
                }elseif ($value == 2){
                    $ntext = $this->text_prefix . '_N_ITEMS_ARCHIVED';
                }else{
                    $ntext = $this->text_prefix . '_N_ITEMS_TRASHED';
                }
                $this->setMessage(JText::plural($ntext, count($cid)));
            }
        }
        $this->setRedirect(JRoute::_('index.php?option=com_poecom&view=producttabs', false));
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
            $model = $this->getModel('ProductTab');

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
