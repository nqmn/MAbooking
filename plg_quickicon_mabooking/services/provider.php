<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Quickicon.mabooking
 */

\defined('_JEXEC') or die;

require_once dirname(__DIR__) . '/src/Extension/Mabooking.php';

use Icc\Plugin\Quickicon\Mabooking\Extension\Mabooking;
use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class () implements ServiceProviderInterface {
	public function register(Container $container): void
	{
		$container->set(
			PluginInterface::class,
			function (Container $container) {
				$plugin = new Mabooking((array) PluginHelper::getPlugin('quickicon', 'mabooking'));
				$plugin->setApplication(Factory::getApplication());

				return $plugin;
			}
		);
	}
};
