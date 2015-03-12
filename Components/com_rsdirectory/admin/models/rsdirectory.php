<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * The RSDirectory! model.
 */
class RSDirectoryModelRSDirectory extends JModelLegacy
{
    /**
     * Get buttons data array.
     *
     * @access public
     * 
     * @return array
     */
    public function getButtons()
    {
        return (object)array(
            'manage_directory_activity' => (object)array(
                (object)array(
                    'link' => JRoute::_('index.php?option=com_rsdirectory&view=entries'),
                    'image' => '../media/com_rsdirectory/images/icon-48-entries.png',
                    'width' => 48,
                    'height' => 48,
                    'text' => JText::_('COM_RSDIRECTORY_ENTRIES'),
                    'access' => true,
                ),
                (object)array(
                    'link' => JRoute::_('index.php?option=com_rsdirectory&view=reportedentries'),
                    'image' => '../media/com_rsdirectory/images/icon-48-reported-entries.png',
                    'width' => 48,
                    'height' => 48,
                    'text' => JText::_('COM_RSDIRECTORY_REPORTED_ENTRIES'),
                    'access' => true,
                ),
                (object)array(
                    'link' => JRoute::_('index.php?option=com_rsdirectory&view=ratings'),
                    'image' => '../media/com_rsdirectory/images/icon-48-ratings.png',
                    'width' => 48,
                    'height' => 48,
                    'text' => JText::_('COM_RSDIRECTORY_RATINGS_AND_REVIEWS'),
                    'access' => true,
                ),
                (object)array(
                    'link' => JRoute::_('index.php?option=com_rsdirectory&view=users'),
                    'image' => '../media/com_rsdirectory/images/icon-48-users.png',
                    'width' => 48,
                    'height' => 48,
                    'text' => JText::_('COM_RSDIRECTORY_USERS'),
                    'access' => true,
                ),
                (object)array(
                    'link' => JRoute::_('index.php?option=com_rsdirectory&view=transactions'),
                    'image' => '../media/com_rsdirectory/images/icon-48-transactions.png',
                    'width' => 48,
                    'height' => 48,
                    'text' => JText::_('COM_RSDIRECTORY_TRANSACTIONS'),
                    'access' => true,
                ),
                (object)array(
                    'link' => JRoute::_('index.php?option=com_rsdirectory&view=creditshistory'),
                    'image' => '../media/com_rsdirectory/images/icon-48-credits-history.png',
                    'width' => 48,
                    'height' => 48,
                    'text' => JText::_('COM_RSDIRECTORY_CREDITS_HISTORY'),
                    'access' => true,
                ),
            ),
            'set_up_directory' => (object)array(
                (object)array(
                    'link' => JRoute::_('index.php?option=com_rsdirectory&view=fields'),
                    'image' => '../media/com_rsdirectory/images/icon-48-fields.png',
                    'width' => 48,
                    'height' => 48,
                    'text' => JText::_('COM_RSDIRECTORY_FIELDS'),
                    'access' => true,
                ),
                (object)array(
                    'link' => JRoute::_('index.php?option=com_rsdirectory&view=forms'),
                    'image' => '../media/com_rsdirectory/images/icon-48-forms.png',
                    'width' => 48,
                    'height' => 48,
                    'text' => JText::_('COM_RSDIRECTORY_FORMS'),
                    'access' => true,
                ),
                (object)array(
                    'link' => JRoute::_('index.php?option=com_rsdirectory&view=categories'),
                    'image' => '../media/com_rsdirectory/images/icon-48-categories.png',
                    'width' => 48,
                    'height' => 48,
                    'text' => JText::_('COM_RSDIRECTORY_CATEGORIES'),
                    'access' => true,
                ),
                (object)array(
                    'link' => JRoute::_('index.php?option=com_rsdirectory&view=groups'),
                    'image' => '../media/com_rsdirectory/images/icon-48-groups.png',
                    'width' => 48,
                    'height' => 48,
                    'text' => JText::_('COM_RSDIRECTORY_GROUPS'),
                    'access' => true,
                ),
                (object)array(
                    'link' => JRoute::_('index.php?option=com_rsdirectory&view=creditpackages'),
                    'image' => '../media/com_rsdirectory/images/icon-48-credits.png',
                    'width' => 48,
                    'height' => 48,
                    'text' => JText::_('COM_RSDIRECTORY_CREDIT_PACKAGES'),
                    'access' => true,
                ),
                (object)array(
                    'link' => JRoute::_('index.php?option=com_rsdirectory&view=emailmessages'),
                    'image' => '../media/com_rsdirectory/images/icon-48-email-messages.png',
                    'width' => 48,
                    'height' => 48,
                    'text' => JText::_('COM_RSDIRECTORY_EMAIL_MESSAGES'),
                    'access' => true,
                ),
                (object)array(
                    'link' => JRoute::_('index.php?option=com_rsdirectory&view=configuration'),
                    'image' => '../media/com_rsdirectory/images/icon-48-configuration.png',
                    'width' => 48,
                    'height' => 48,
                    'text' => JText::_('COM_RSDIRECTORY_CONFIGURATION'),
                    'access' => true,
                ),
                (object)array(
                    'link' => JRoute::_('index.php?option=com_rsdirectory&view=tools'),
                    'image' => '../media/com_rsdirectory/images/icon-48-tools.png',
                    'width' => 48,
                    'height' => 48,
                    'text' => JText::_('COM_RSDIRECTORY_TOOLS'),
                    'access' => true,
                ),
                (object)array(
                    'link' => JRoute::_('index.php?option=com_rsdirectory&view=updates'),
                    'image' => '../media/com_rsdirectory/images/icon-48-updates.png',
                    'width' => 48,
                    'height' => 48,
                    'text' => JText::_('COM_RSDIRECTORY_UPDATES'),
                    'access' => true,
                ),
            ),
        );
    }
        
    /**
     * Get sidebar.
     *
     * @access public
     * 
     * @return string
     */
    public function getSideBar()
    {
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php';
            
        return RSDirectoryToolbarHelper::render();
    }
}