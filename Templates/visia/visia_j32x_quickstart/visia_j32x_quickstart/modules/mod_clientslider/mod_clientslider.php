<?php
/**
* @version		$Id: mod_clientslider.php 10000 2013-10-29 03:35:53Z schro $
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
$base_url 	= ''.JURI::base().'modules/mod_clientslider/';

$loadjQuery = $params->get('jquery', 0);

// add stylesheets here
$doc->addStyleSheet( $base_url.'assets/style.css');

/* call jquery library */
JHtml::_('jquery.framework');

// add javascripts here
$doc->addScript($base_url . 'assets/jquery.bxslider.js');

$items = modClientSliderHelper::getItems($params);

require( JModuleHelper::getLayoutPath('mod_clientslider', $params->get('layout')) );
