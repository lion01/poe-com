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
 * Product Related Groups Controller
 */
class PoecomControllerRelatedGroups extends JControllerAdmin {

    /**
     * Proxy for JController getModel.
     * 
     * Required to get JModelAdmin model in order to execute tasks not includes in
     * JModelList model, such as delete() from list view.
     * 
     * @since	1.6
     */
    public function getModel($name = 'RelatedGroups', $prefix = 'PoecomModel') {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }
    
   /**
    * Delete Related Product Group(s)
    * 
    * Only groups with no product relations can be deleted
    *
    * @return  void
    *
    * @since   11.1
    */
    public function delete(){
        // Check for request forgeries
        JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

        // Get items to remove from the request.
        $jinput = JFactory::getApplication()->input;
        $cids = $jinput->get('cid', array(), 'ARRAY');

        if (!is_array($cids) || count($cids) < 1){
            JError::raiseWarning(500, JText::_('COM_POECOM_NO_ITEM_SELECTED'));
        }else{
            // Get the model.
            $model = $this->getModel('RelatedGroup');

            // Make sure the item ids are integers
            jimport('joomla.utilities.arrayhelper');
            JArrayHelper::toInteger($cids);
            
            // Remove the items.
           if ($model->delete($cids)){
                $this->setMessage(JText::plural('COM_POECOM_N_ITEMS_DELETED', count($cids)), 'info');
            }else{
                $this->setMessage($model->getError());
            } 
        }

        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
    }
}
