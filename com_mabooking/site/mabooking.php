<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_mabooking
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

$app = Factory::getApplication();
$app->bootComponent('com_mabooking')->getDispatcher($app)->dispatch();
