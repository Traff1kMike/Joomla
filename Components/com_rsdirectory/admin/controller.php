<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * The RSDirectory controller.
 */
class RSDirectoryController extends JControllerLegacy
{
    /**
     * The class constructor.
     *
     * @access public
     */
    public function __construct()
    {
		parent::__construct();
			
		JToolBarHelper::title('RSDirectory!', 'rsdirectory');
			
		if ( !RSDirectoryConfig::getInstance()->get('credit_cost') && RSDirectoryCredits::requiresCredits() )
		{
			JFactory::getApplication()->enqueueMessage( JText::_('COM_RSDIRECTORY_EMPTY_CREDIT_COST_VALUE_WARNING'), 'warning' );	
		}
    }
}