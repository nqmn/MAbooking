<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_mabooking
 */

defined('_JEXEC') or die;

use Icc\Component\Mabooking\Site\Extension\MabookingComponent;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\ComponentRouterFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class () implements ServiceProviderInterface {
	public function register(Container $container): void
	{
		$container->registerServiceProvider(new ComponentDispatcherFactory('\\Icc\\Component\\Mabooking'));
		$container->registerServiceProvider(new MVCFactory('\\Icc\\Component\\Mabooking'));
		$container->registerServiceProvider(new ComponentRouterFactory('\\Icc\\Component\\Mabooking'));

		$container->set(
			ComponentInterface::class,
			function (Container $container) {
				$component = new MabookingComponent($container->get(ComponentDispatcherFactoryInterface::class));
				$component->setMVCFactory($container->get(MVCFactoryInterface::class));
				$component->setRouterFactory($container->get(RouterFactoryInterface::class));

				return $component;
			}
		);
	}
};
