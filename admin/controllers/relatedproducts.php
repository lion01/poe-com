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
 * Product Related Products Controller
 */
class PoecomControllerRelatedProducts extends JControllerAdmin {

    /**
     * Proxy for JController getModel.
     * 
     * Required to get JModelAdmin model in order to execute tasks not includes in
     * JModelList model, such as delete() from list view.
     * 
     * @since	1.6
     */
    public function getModel($name = 'RelatedProducts', $prefix = 'PoecomModel') {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }

    /**
    * Delete relatedproduct(s)
    *
    * @return  void
    *
    * @since   11.1
    */
    public function delete(){
        // Check for request forgeries
        JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

        // Get items to remove from the request.
        $app = JFactory::getApplication();
        $jinput = $app->input;
        $cids = $jinput->get('cid', array(), 'ARRAY');

        if (empty($cids)){
            $app->enqueueMessage(JText::_('COM_POECOM_NO_ITEM_SELECTED'), 'error');
        }else{
            // Get the model.
            $model = $this->getModel('RelatedProduct');

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

        $this->setRedirect('index.php?option=' . $this->option . '&view=' . $this->view_list);
    }
}
