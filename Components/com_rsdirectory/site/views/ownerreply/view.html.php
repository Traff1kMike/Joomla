<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Owner reply view.
 */
class RSDirectoryViewOwnerReply extends JViewLegacy
{
    /**
     * The display function.
     *
     * @access public
     * 
     * @param mixed $tpl
     */
    function display($tpl = null)
    {
		$app = JFactory::getApplication();
		$this->config = RSDirectoryConfig::getInstance();
			
		if ( $this->config->get('enable_owner_reply') )
		{
			$this->form = $this->get('Form');
			$this->item = trim( $this->get('Item') );
				
			if ( $app->input->getInt('success') )
			{
				$review_id = $app->input->getInt('review_id');
					
				if ( trim($this->item) )
				{
					$owner_reply = json_encode( RSDirectoryHelper::getOwnerReplyHTML( trim($this->item) ) );	
				}
				else
				{
					$owner_reply = json_encode('');
				}
					
				$script = "window.parent.updateOwnerReply($review_id, $owner_reply)";
				JFactory::getDocument()->addScriptDeclaration($script);
			}
		}
		else
		{
			$app->enqueueMessage( JText::_('COM_RSDIRECTORY_OWNER_REPLY_DISABLED'), 'error' );
		}
			
		JFactory::getDocument()->addStyleSheet( JURI::root(true) . '/media/com_rsdirectory/css/iframe.css?v=' . RSDirectoryVersion::$version );
			
		parent::display($tpl);
    }
}