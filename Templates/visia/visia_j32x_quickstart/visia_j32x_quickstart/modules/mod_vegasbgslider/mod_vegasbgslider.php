<?php
/**
* @version		$Id: mod_vegasbgslider.php 10000 2013-10-29 03:35:53Z schro $
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


// Include the syndicate functions only once
//require_once dirname(__FILE__).'/helper.php';

$doc		= JFactory::getDocument();
$base_url 	= ''.JURI::base().'modules/mod_vegasbgslider/';

$layout 	= $params->get('layout', 'default');
$loadjQuery = $params->get('jquery', 0);

/* call jquery  */
JHtml::_('jquery.framework');


//$items = modVegasBgSliderHelper::getItems($params);

require( JModuleHelper::getLayoutPath('mod_vegasbgslider', $layout) );
