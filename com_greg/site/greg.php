<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Greg
 * @author     Joopiter.ru <support@joopiter.ru>
 * @copyright  2017 Joopiter.ru
 * @license    GNU General Public License версии 2 или более поздней; Смотрите LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Greg', JPATH_COMPONENT);
JLoader::register('GregController', JPATH_COMPONENT . '/controller.php');


// Execute the task.
$controller = JControllerLegacy::getInstance('Greg');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
