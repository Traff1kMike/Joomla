<?php
/**
* @version		$Id: mod_flickr.php 10000 2013-10-29 03:35:53Z schro $
* @package		Joomla 3.1.x
* @copyright	Copyright (C) 2005 - 2013 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

/// no direct access
defined('_JEXEC') or die('Restricted access');

$doc		= JFactory::getDocument();
$modbase 	= ''.JURI::base().'modules/mod_flickr/';

$count 	= $params->get('count');
$flickr_id	= $params->get('flickr_id');

// Load jQuery
JHtml::_('jquery.framework');

require( JModuleHelper::getLayoutPath('mod_flickr', $params->get('layout', 'default')) );
