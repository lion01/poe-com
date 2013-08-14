<?php

defined('_JEXEC') or die('Restricted access');

/**
 * Script file of POE-com component
 *
 * See doc, http://docs.joomla.org/Managing_Component_Updates_with_Joomla!1.6_-_Part_3
 */
class com_poecomInstallerScript {

    /**
     * method to install the component
     *
     * @return void
     */
    function install($parent) {
        // $parent is the class calling this method
        //   $parent->getParent()->setRedirectURL('index.php?option=com_poecom');
    }

    /**
     * method to uninstall the component
     *
     * @return void
     */
    function uninstall($parent) {
        // $parent is the class calling this method
        echo '<p>' . JText::_('COM_POECOM_UNINSTALL_TEXT') . '</p>';

        //remove POE-com user groups
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->select('id');
        $q->from('#__usergroups');
        $q->where('title=' . $db->Quote('POEcom Users'));

        $db->setQuery($q);

        if (($id = $db->loadResult())) {
            //Delete group mapping
            $q = $db->getQuery(true);
            $q->delete('#__user_usergroup_map');
            $q->where('group_id=' . (int) $id);
            $db->setQuery($q);

            if ($db->query()) {
                echo '<p>' . JText::_('COM_POECOM_UNINSTALL_USER_GROUP_MAP') . '</p>';
                //Delete POEcom User group
                $q = $db->getQuery(true);
                $q->delete('#__usergroups');
                $q->where('id=' . (int) $id);
                $db->setQuery($q);

                if ($db->query()) {
                    echo '<p>' . JText::_('COM_POECOM_UNINSTALL_USER_GROUP') . '</p>';
                } else {
                    echo '<p>' . JText::_('COM_POECOM_UNINSTALL_USER_GROUP_FAILED') . '</p>';
                }
            } else {
                echo '<p>' . JText::_('COM_POECOM_UNINSTALL_USER_GROUP_MAP_FAILED') . '</p>';
            }
        } else {
            echo '<p>' . JText::_('COM_POECOM_UNINSTALL_NO_USER_GROUP_RESULT') . '</p>';
        }

        //remove POE-com Admin user groups
        $q = $db->getQuery(true);
        $q->select('id');
        $q->from('#__usergroups');
        $q->where('title=' . $db->Quote('POEcom Admin'));

        $db->setQuery($q);

        if (($id = $db->loadResult())) {
            //Delete group mapping
            $q = $db->getQuery(true);
            $q->delete('#__user_usergroup_map');
            $q->where('group_id=' . (int) $id);
            $db->setQuery($q);

            if ($db->query()) {
                echo '<p>' . JText::_('COM_POECOM_UNINSTALL_ADMIN_GROUP_MAP') . '</p>';
                //Delete POEcom User group
                $q = $db->getQuery(true);
                $q->delete('#__usergroups');
                $q->where('id=' . (int) $id);
                $db->setQuery($q);

                if ($db->query()) {
                    echo '<p>' . JText::_('COM_POECOM_UNINSTALL_ADMIN_GROUP') . '</p>';
                } else {
                    echo '<p>' . JText::_('COM_POECOM_UNINSTALL_ADMIN_GROUP_FAILED') . '</p>';
                }
            } else {
                echo '<p>' . JText::_('COM_POECOM_UNINSTALL_ADMIN_GROUP_MAP_FAILED') . '</p>';
            }
        } else {
            echo '<p>' . JText::_('COM_POECOM_UNINSTALL_NO_ADMIN_GROUP_RESULT') . '</p>';
        }
    }

    /**
     * method to update the component
     *
     * @return void
     */
    function update($parent) {
        // $parent is the class calling this method
        echo '<p>' . JText::sprintf('COM_POECOM_UPDATE_TEXT', $parent->get('manifest')->version) . '</p>';
    }

    /**
     * method to run before an install/update/uninstall method
     *
     * @return void
     */
    function preflight($type, $parent) {
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)
        echo '<p>' . JText::_('COM_POECOM_PREFLIGHT_' . $type . '_TEXT') . '</p>';
    }

    /**
     * method to run after an install/update/discover_install method
     *
     * Does not run on uninstall
     *
     * @return void
     */
    function postflight($type, $parent) {
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)
        echo '<p>' . JText::_('COM_POECOM_POSTFLIGHT_' . $type . '_TEXT') . '</p>';

        if ($type == 'install') {
            //check for POE-com user group
            $db = JFactory::getDbo();
            $q = $db->getQuery(true);
            $q->select('id');
            $q->from('#__usergroups');
            $q->where('title=' . $db->Quote('POEcom Users'));
            $db->setQuery($q);
            JModel::addIncludePath (JPATH_ADMINISTRATOR . '/components/com_users/models');
            $model = JModel::getInstance('Group', 'UsersModel');

            if ((!$id = $db->loadResult())) {
                $userGroupData = array('id' => 0, 'parent_id' => 2, 'title' => 'POEcom Users');
                if (!$model->save($userGroupData)) {
                    echo '<p>' . JText::_('COM_POECOM_INSTALL_USER_GROUP_ERROR') . '</p>';
                }
            }

            //check for POE-com admin group
            $q2 = $db->getQuery(true);
            $q2->select('id');
            $q2->from('#__usergroups');
            $q2->where('title=' . $db->Quote('POEcom Users'));
            $db->setQuery($q2);

            if ((!$id = $db->loadResult())) {
                $adminGroupData = array('id' => 0, 'parent_id' => 2, 'title' => 'POEcom Admin');
                if (!$model->save($adminGroupData)) {
                    echo '<p>' . JText::_('COM_POECOM_INSTALL_ADMIN_GROUP_ERROR') . '</p>';
                }
            }
        }
    }

}