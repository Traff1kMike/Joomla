<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

// Load the component main helper.
require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/rsdirectory.php';

// Initialize main helper.
RSDirectoryHelper::init();

$controller = JControllerLegacy::getInstance('RSDirectory');
$controller->execute( JFactory::getApplication()->input->get('task') );
$controller->redirect();