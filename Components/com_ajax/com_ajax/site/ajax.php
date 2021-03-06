<?php defined('_JEXEC') or die;

/**
 * File       ajax.php
 * Created    5/20/13 4:54 PM
 * Author     Matt Thomas | matt@betweenbrain.com | http://betweenbrain.com
 * Support    https://github.com/betweenbrain/Joomla-Ajax-Interface/issues
 * Copyright  Copyright (C) 2013 betweenbrain llc. All Rights Reserved.
 * License    GNU General Public License version 2, or later.
 */

// Reference global application object
$app = JFactory::getApplication();

// JInput object
$input = $app->input;

// Requested format passed via URL
$format = strtolower($input->getWord('format'));

// Initialized to prevent notices
$results = NULL;
$error   = NULL;

/*
 * Module support.
 *
 * modFooHelper::getAjax() is called where 'foo' is the value
 * of the 'module' variable passed via the URL 
 * (i.e. index.php?option=com_ajax&module=foo).
 *
 */
if ($input->get('module')) {

	$module       = $input->get('module');
	$moduleObject = JModuleHelper::getModule('mod_' . $module, NULL);

	/*
	 * As JModuleHelper::isEnabled always returns true, we check
	 * for an id other than 0 to see if it is published.
	 */
	if ($moduleObject->id != 0) {

		jimport('joomla.filesystem.file');
		$helperFile = JPATH_BASE . '/modules/mod_' . $module . '/helper.php';

		$class  = 'mod' . ucfirst($module) . 'Helper';
		$method = $input->get('method') ? $input->get('method') : 'get';

		if (JFile::exists($helperFile)) {
			require_once($helperFile);

			if (method_exists($class, $method . 'Ajax')) {
				$results = call_user_func($class . '::' . $method . 'Ajax');
			} else {
				$error = JText::sprintf('COM_AJAX_METHOD_DOES_NOT_EXIST', $method . 'Ajax');
			}
		} else {
			$error = JText::sprintf('COM_AJAX_HELPER_DOES_NOT_EXIST', 'mod_' . $module . '/helper.php');
		}
	} else {
		$error = JText::sprintf('COM_AJAX_MODULE_NOT_PUBLISHED', 'mod_' . $module);
	}
}

/*
 * Plugin support is based on the "Ajax" plugin group.
 *
 * The plugin event triggered is onAjaxFoo, where 'foo' is
 * the value of the 'plugin' variable passed via the URL
 * (i.e. index.php?option=com_ajax&plugin=foo)
 *
 */
if ($input->get('plugin')) {
	JPluginHelper::importPlugin('ajax');
	$plugin     = ucfirst($input->get('plugin'));
	$dispatcher = JDispatcher::getInstance();
	$response   = $dispatcher->trigger('onAjax' . $plugin);
	$results    = $response ? $response : ($error = JText::sprintf('COM_AJAX_NO_PLUGIN_RESPONSE', $plugin));
}

if (!is_null($error)) {
	echo $error;
	$app->close();
}

// Return the results in the desired format
switch ($format) {
	case 'json':
		JResponse::setHeader('Content-Type', 'application/json', TRUE);
		echo json_encode($results);
		$app->close();
		break;
	case 'debug':
		echo '<pre>' . print_r($results, TRUE) . '</pre>';
		$app->close();
		break;
	default:
		echo is_array($results) ? implode($results) : $results;
		// Emulates format=raw by closing $app
		$app->close();
		break;
}

/*
 * References
 *  Support plugins in your component
 * - http://docs.joomla.org/Supporting_plugins_in_your_component
 *
 * Best way for JSON output
 * - https://groups.google.com/d/msg/joomla-dev-cms/WsC0nA9Fixo/Ur-gPqpqh-EJ
 *
 */
