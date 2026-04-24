<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_mabooking
 */

namespace Icc\Component\Mabooking\Site\Extension;

\defined('_JEXEC') or die;

use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Psr\Container\ContainerInterface;

final class MabookingComponent extends MVCComponent implements BootableExtensionInterface
{
	public function boot(ContainerInterface $container): void
	{
	}
}
