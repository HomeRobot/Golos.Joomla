<?php 

// No direct access to this file
defined('_JEXEC') or die;

$module = JModuleHelper::getModule('mod_glogin');
$getGlobalParams = new JRegistry($module->params);

$db = JFactory::getDbo();
$user = JFactory::getUser();

require JModuleHelper::getLayoutPath('mod_glogin', $params->get('layout', 'default'));
