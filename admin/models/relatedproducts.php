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
jimport('joomla.application.component.modellist');
/**
 * RelatedProducts List Model
 */
class PoecomModelRelatedProducts extends JModelList{
    
    /**
    * Method to auto-populate the model state.
    *
    * Note. Calling getState in this method will result in recursion.
    *
    * @return	void
    * @since	1.6
    */
    protected function populateState($ordering = null, $direction = null){
        $jinput = JFactory::getApplication()->input;

        // Adjust the context to support modal layouts.
        $layout = $jinput->get('layout', '', 'CMD');
        if (!empty($layout)) {
            $this->context .= '.' . $layout;
        }

        // Set state in parent for pagination
        parent::populateState();
        
        //assign filtering elements
        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);
        
        //get group id from request for modal
        $groupId = $jinput->get('group_id', 0, 'CMD');
        if($groupId > 0){
            $this->setState('filter.group_id', $groupId);
        }else{
            $groupId = $this->getUserStateFromRequest($this->context.'.filter.group_id', 'filter_group_id');
            $this->setState('filter.group_id', $groupId);
        }
    }

    /**
    * Method to get a store id based on model configuration state.
    *
    * This is necessary because the model is used by the component and
    * different modules that might need different sets of data or different
    * ordering requirements.
    *
    * @param	string		$id	A prefix for the store id.
    *
    * @return	string		A store id.
    * @since	1.6
    */
    protected function getStoreId($id = ''){
        // Compile the store id.
        $id	.= ':'.$this->getState('filter.search');
        $id	.= ':'.$this->getState('filter.published');
        $id	.= ':'.$this->getState('filter.group_id');
        
        return parent::getStoreId($id);
    }
    
    /**
    * Method to build an SQL query to load the list data.
    *
    * @return	string	An SQL query
    */
    protected function getListQuery(){
        // Create a new query object.		
        $db = JFactory::getDBO();
        $q = $db->getQuery(true);
        $q->select('rp.*, p.name product_name, grp.name group_name');
        $q->from('#__poe_related_product rp');
        $q->innerJoin('#__poe_product p ON p.id=rp.product_id');
        $q->leftJoin('#__poe_related_product_group grp ON grp.id=rp.group_id');
        

        // Filter by published state
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
                $q->where('rp.published = ' . (int) $published);
        }
        else if ($published === '') {
                $q->where('(rp.published = 0 OR rp.published = 1)');
        }
        
        // Filter by image type
        $groupId = $this->getState('filter.group_id');
        if (is_numeric($groupId)) {
                $q->where('rp.group_id = ' . (int) $groupId);
        }else if ($groupId === '') {
                $q->where('(rp.group_id = 0 OR rp.group_id = 1)');
        }

        // Filter by a single or group of groups.
        $groupId = $this->getState('filter.group_id');
        if (is_numeric($groupId)) {
            $q->where('p.id = '.(int) $groupId);
        }else if (is_array($groupId)) {
            JArrayHelper::toInteger($groupId);
            $groupId = implode(',', $groupId);
            $q->where('p.id IN ('.$groupId.')');
        }

        // Filter by search in name.
        $search = trim($this->getState('filter.search'));

        if (!empty($search)) {
                if (stripos($search, 'id:') === 0) {
                        $q->where('rp.id = '.(int) substr($search, 3));
                }
                else if (stripos($search, 'name:') === 0) {
                        $search = $db->Quote('%'.$db->getEscaped(substr($search, 5), true).'%');
                        $q->where('(p.name LIKE '.$search.')');
                }else if (stripos($search, 'group:') === 0) {
                        $search = $db->Quote('%'.$db->getEscaped(substr($search, 6), true).'%');
                        $q->where('(grp.name LIKE '.$search.')');
                }else{
                        $search = $db->Quote('%'.$db->getEscaped($search, 7).'%');
                        $q->where('(p.name LIKE '.$search.')');
                }
        }

        $q->order('p.name');

        return $q;
    }
    
    	/**
	 * Method to change the published state of one or more records.
	 *
	 * @param   array    &$pks   A list of the primary keys to change.
	 * @param   integer  $value  The value of the published state.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 */
	public function publish(&$pks, $value = 1)
	{
		// Initialise variables.
		$dispatcher = JDispatcher::getInstance();
		$user = JFactory::getUser();
		$table = $this->getTable('PoecomTableRelatedProduct');
		$pks = (array) $pks;

		// Include the content plugins for the change of state event.
		JPluginHelper::importPlugin('content');

		// Access checks.
		foreach ($pks as $i => $pk)
		{
			$table->reset();

			if ($table->load($pk))
			{
				if (!$this->canEditState($table))
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
					return false;
				}
			}
		}

		// Attempt to change the state of the records.
		if (!$table->publish($pks, $value, $user->get('id')))
		{
			$this->setError($table->getError());
			return false;
		}

		$context = $this->option . '.' . $this->name;

		// Trigger the onContentChangeState event.
		$result = $dispatcher->trigger($this->event_change_state, array($context, $pks, $value));

		if (in_array(false, $result, true))
		{
			$this->setError($table->getError());
			return false;
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}
}
