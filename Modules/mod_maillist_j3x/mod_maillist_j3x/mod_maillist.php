<?php
/**
* @version		$Id: mod_maillist.php 10000 2013-10-29 03:35:53Z schro $
* @package		Joomla
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

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';

$doc		= JFactory::getDocument();
$base_url 	= ''.JURI::base().'modules/mod_maillist/';

$show_numthings = (bool)$params->get('show_numthings', 21); 
$count1_label 	= $params->get('countlabel_1', 'Projects');
$countnum_1		= $params->get('countnum_1', 21);
$count2_label 	= $params->get('countlabel_2', 'Coffee');
$countnum_2		= $params->get('countnum_2', 88);
$count3_label 	= $params->get('countlabel_3', 'Tweets');
$countnum_3		= $params->get('countnum_3', 320);

$maillist_email = $params->get('maillist_email');
$show_pretext	= $params->get('show_pretext');
$pretext	= $params->get('pretext');

// load jquery
JHtml::_('jquery.framework');


$counts = modMailListHelper::getItems($params);
require( JModuleHelper::getLayoutPath('mod_maillist', $params->get('layout', 'default')) );
