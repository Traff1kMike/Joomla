<?php
//no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

ini_set('display_errors',0);

// Include the helper.
require_once dirname(__FILE__). '/helper.php';

// Instantiate global document object
$doc = JFactory::getDocument();
$format     = $params->get('format', 'debug');

// Path assignments
$path = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];


$modbase 	= JURI::base().'modules/mod_visiacontact/';

JHtml::_('jquery.framework');

$name_label 	= $params->get("name","Name");
$email_label 	= $params->get("email","Email");
$message_label 	= $params->get("message","Message");
$enable_captcha	= $params->get("enable_captcha", 1); // bool
$captcha_label 	= $params->get("captcha_label","Captcha");
$submit_label 	= $params->get("submit","Send");
$subject 		= $params->get("subject","Request a Quote");
$recipient 		= $params->get("recipient","");



require(JModuleHelper::getLayoutPath('mod_visiacontact', $params->get('layout', 'default')));
