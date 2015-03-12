<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');


if ( !JFactory::getUser()->authorise('core.manage', 'com_rsdirectory') )
    return JError::raiseWarning( 404, JText::_('JERROR_ALERTNOAUTHOR') );


require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/rsdirectory.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/config.php';

// Initialize main helper.
RSDirectoryHelper::init();


// Require the base controller.
require JPATH_COMPONENT_ADMINISTRATOR . '/controller.php';

$controller = JControllerLegacy::getInstance('RSDirectory');
$controller->execute( JFactory::getApplication()->input->get('task') );
$controller->redirect();